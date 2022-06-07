<?php

namespace App\Http\Controllers\Api;

use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Http\Requests\AddCartRequest;
use App\Models\ProductSku;
use App\Services\CartService;

class CartController extends Controller
{
    protected $cartService;

    // 利用 Laravel 的自动解析功能注入 CartService 类
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        $cartItems = $this->cartService->get();
        $addresses = $request->user()->addresses()->orderBy('last_used_at', 'desc')->get();

        return response()->json(['cartItems' => $cartItems, 'addresses' => $addresses]);
    }

    public function add(AddCartRequest $request)
    {
        $this->cartService->add($request->input('sku_id'), $request->input('amount'));

        return [];
    }

    public function remove(CartItem $cartItem, Request $request)
    {
        // 修改成策略
        $this->authorize('own', $cartItem);
        $result = $request->all();
        if(!empty($result['all'])){
            return $cartItem->where('user_id',$cartItem->user_id)->delete();
        }else{
            $cartItem->delete();
        }


        return [];
    }
}
