<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\OrderRequest;
use App\Models\UserAddress;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Exceptions\InvalidRequestException;
use Carbon\Carbon;
use App\Http\Requests\SendReviewRequest;
use App\Events\OrderReviewed;
use App\Http\Requests\ApplyRefundRequest;
use App\Exceptions\CouponCodeUnavailableException;
use App\Models\CouponCode;
use App\Http\Requests\CrowdFundingOrderRequest;
use App\Models\ProductSku;
use App\Http\Requests\SeckillOrderRequest;
use Illuminate\Support\Facades\Redis;

class OrdersController extends Controller
{
    public function seckill(SeckillOrderRequest $request, OrderService $orderService)
    {
        $user = $request->user();
        $sku  = ProductSku::find($request->input('sku_id'));

        return $orderService->seckill($user, $request->input('address'), $sku);
    }

    public function store(OrderRequest $request, OrderService $orderService)
    {
        $user    = $request->user();
        $address = UserAddress::find($request->input('address_id'));
        $coupon  = null;

        // 如果用户提交了优惠码
        if ($code = $request->input('coupon_code')) {
            $coupon = CouponCode::where('code', $code)->first();
            if (!$coupon) {
                throw new CouponCodeUnavailableException('优惠券不存在');
            }
        }
        // 参数中加入 $coupon 变量
        return response()->json($orderService->store($user, $address, $request->input('remark'), $request->input('items'), $coupon));
    }

    public function index(Request $request)
    {
        $status = $request->get('status','');
        $shipStatus = $request->get('shipStatus','');
        $where['user_id'] = $request->user()->id;
        $whereIn = ['pending','applied','processing','success','failed'];
        $where['is_del'] = false;
        if(!empty($status)){
            if($status == 'applied'){
                // 售後
                $whereIn = ['applied','processing','success','failed'];
            }
            $where['refund_status'] = $status;
            $where['closed'] = false;
        }
        if(!empty($shipStatus)){
            $where['ship_status'] = $shipStatus;
            $where['closed'] = false;
        }
        $orders = Order::query()
            // 使用 with 方法预加载，避免N + 1问题
            ->with(['items.product', 'items.productSku'])
            ->where($where)
            ->whereIn('refund_status',$whereIn)
            ->orderBy('created_at', 'desc')
            ->paginate();


        return response()->json($orders);
    }

    public function show(Order $order, Request $request)
    {
        $this->authorize('own', $order);
        // 判断订单是否已删除
        if ($order->is_del) {
            throw new InvalidRequestException('订单不存在');
        }

        return response()->json(['order' => $order->load(['items.productSku', 'items.product'])]);
    }

    /**
     * 确认收货
     *
     * @param Order $order
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function received(Order $order, Request $request)
    {
        // 校验权限
        $this->authorize('own', $order);

        // 判断订单的发货状态是否为已发货
        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED) {
            throw new InvalidRequestException('发货状态不正确');
        }

        // 更新发货状态为已收到
        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);

        // 返回订单信息
        return response()->json($order);
    }

    /**
     * 评价商品页面
     *
     * @param Order $order
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function review(Order $order)
    {
        // 校验权限
        $this->authorize('own', $order);
        // 判断是否已经支付
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }
        // 使用 load 方法加载关联数据，避免 N + 1 性能问题
        return response()->json(['order' => $order->load(['items.productSku', 'items.product'])]);
    }

    /**
     * 评价商品
     *
     * @param Order $order
     * @param SendReviewRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function sendReview(Order $order, SendReviewRequest $request)
    {
        // 校验权限
        $this->authorize('own', $order);
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可评价');
        }
        // 判断是否已经评价
        if ($order->reviewed) {
            throw new InvalidRequestException('该订单已评价，不可重复提交');
        }
        $reviews = $request->input();

        // 开启事务
        \DB::transaction(function () use ($reviews, $order) {
            $orderItem = $order->items()->find($reviews['id']);
            // 保存评分和评价
            $orderItem->update([
                'rating'      => $reviews['rating'],
                'review'      => $reviews['review'],
                'reviewed_at' => Carbon::now(),
            ]);
            // 将订单标记为已评价
            $order->update(['reviewed' => true]);
        });

        event(new OrderReviewed($order));

        return response()->json();
    }

    /**
     * 退款申请
     *
     * @param Order $order
     * @param ApplyRefundRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function applyRefund(Order $order, ApplyRefundRequest $request)
    {
        // 校验订单是否属于当前用户
        $this->authorize('own', $order);
        // 判断订单是否已付款
        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付，不可退款');
        }
        // 众筹订单不允许申请退款
        if ($order->type === Order::TYPE_CROWDFUNDING) {
            throw new InvalidRequestException('众筹订单不支持退款');
        }
        // 判断订单退款状态是否正确
        if ($order->refund_status !== Order::REFUND_STATUS_PENDING) {
            throw new InvalidRequestException('该订单已经申请过退款，请勿重复申请');
        }
        // 将用户输入的退款理由放到订单的 extra 字段中
        $extra                  = $order->extra ?: [];
        $extra['refund_reason'] = $request->input('reason');
        // 将订单退款状态改为已申请退款
        $order->update([
            'refund_status' => Order::REFUND_STATUS_APPLIED,
            'extra'         => $extra,
        ]);

        return response()->json($order);
    }

    // 创建一个新的方法用于接受众筹商品下单请求
    public function crowdfunding(CrowdFundingOrderRequest $request, OrderService $orderService)
    {
        $user    = $request->user();
        $sku     = ProductSku::find($request->input('sku_id'));
        $address = UserAddress::find($request->input('address_id'));
        $amount  = $request->input('amount');

        return $orderService->crowdfunding($user, $address, $sku, $amount);
    }

    /**
     * 取消订单
     *
     * @param Order $order
     * @return void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function cancel(Order $order)
    {
        // 校验订单是否属于当前用户
        $this->authorize('own', $order);
        // 判断对应的订单是否已经被支付
        // 如果已经支付则不需要关闭订单，直接退出
        if ($order->paid_at) {
            return;
        }
        if ($order->closed) {
            return;
        }
        // 通过事务执行 sql
        \DB::transaction(function () use ($order) {
            $order->update(['closed' => true]);
            foreach ($order->items as $item) {
                $item->productSku->addStock($item->amount);
                // 当前订单类型是秒杀订单，并且对应商品是上架且尚未到截止时间
                if ($item->order->type === Order::TYPE_SECKILL
                    && $item->product->on_sale
                    && !$item->product->seckill->is_after_end) {
                    // 将 Redis 中的库存 +1
                    Redis::incr('seckill_sku_'.$item->productSku->id);
                }
            }
            if ($order->couponCode) {
                $order->couponCode->changeUsed(false);
            }
        });
    }

    /**
     * 删除订单
     *
     * @param Order $order
     * @return void
     * @throws InvalidRequestException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(Order $order)
    {
        // 校验订单是否属于当前用户
        $this->authorize('own', $order);
        // 只有关闭的订单才可以删除
        if (!$order->closed) {
            throw new InvalidRequestException('该订单正常，不可删除');
        }
        $order->update([
            'is_del' => true,
        ]);
    }
}
