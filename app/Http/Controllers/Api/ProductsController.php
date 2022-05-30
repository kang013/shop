<?php


namespace App\Http\Controllers\Api;

use App\Exceptions\InvalidRequestException;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()->where('on_sale', true)->paginate(5);

        return response()->json($products);
    }

    public function show(Product $product, Request $request)
    {
        // 判断商品是否已经上架，如果没有上架则抛出异常。
        if (!$product->on_sale) {
            throw new InvalidRequestException('商品未上架');
        }

        return response()->json($product);
    }

    /**
     * 收藏
     *
     * @param Product $product
     * @param Request $request
     * @return array
     */
    public function favor(Product $product, Request $request)
    {
        $user = $request->user();
        if ($user->favoriteProducts()->find($product->id)) {
            return [];
        }

        $user->favoriteProducts()->attach($product);

        return [];
    }

    /**
     * 取消收藏
     *
     * @param Product $product
     * @param Request $request
     * @return array
     */
    public function disfavor(Product $product, Request $request)
    {
        $user = $request->user();
        $user->favoriteProducts()->detach($product);

        return [];
    }

    /**
     * 收藏列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function favorites(Request $request)
    {
        $products = $request->user()->favoriteProducts()->paginate(16);

        return response()->json($products);
    }
}
