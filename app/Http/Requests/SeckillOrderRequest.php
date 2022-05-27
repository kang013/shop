<?php
namespace App\Http\Requests;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSku;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Redis;
use Illuminate\Auth\AuthenticationException;
use App\Exceptions\InvalidRequestException;

class SeckillOrderRequest extends Request
{
    public function rules()
    {
        return [
            // 将原本的 address_id 删除
            'address.province'      => 'required',
            'address.city'          => 'required',
            'address.district'      => 'required',
            'address.address'       => 'required',
            'address.zip'           => 'required',
            'address.contact_name'  => 'required',
            'address.contact_phone' => 'required',
            'sku_id'                => [
                'required',
                function ($attribute, $value, $fail) {
                    // 从 Redis 中读取数据
                    $stock = Redis::get('seckill_sku_'.$value);
                    // 如果是 null 代表这个 SKU 不是秒杀商品
                    if (is_null($stock)) {
                        return $fail('该商品不存在');
                    }
                    // 判断库存
                    if ($stock < 1) {
                        return $fail('该商品已售完');
                    }

                    // 大多数用户在上面的逻辑里就被拒绝了
                    // 因此下方的 SQL 查询不会对整体性能有太大影响
                    $sku = ProductSku::find($value);
                    if ($sku->product->seckill->is_before_start) {
                        return $fail('秒杀尚未开始');
                    }
                    if ($sku->product->seckill->is_after_end) {
                        return $fail('秒杀已经结束');
                    }

                    if (!$user = \Auth::user()) {
                        throw new AuthenticationException('请先登录');
                    }
                    if (!$user->email_verified_at) {
                        throw new InvalidRequestException('请先验证邮箱');
                    }
                    if ($order = Order::query()
                        .
                        .
                        .
                    }
                },
            ],
        ];
    }
}
