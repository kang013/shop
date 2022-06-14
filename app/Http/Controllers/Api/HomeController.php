<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Slide;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * 顶部轮播图
     *
     * @param Request $request
     * @param Slide $slide
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, Slide $slide)
    {
        $categoryId = $slide->getCategoryId($request->get('index'));
        $slide = $slide->where(['status'=>1,'category_id'=>$categoryId])->orderByDesc('order')->get();
        return response()->json($slide);
    }

    /**
     * 首页秒杀
     *
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function seckill(product $product)
    {
        $product = $product->where(['type'=>'seckill','on_sale'=>true])->limit(8)->get();
        return response()->json($product);
    }

    public function likeProduct(product $product)
    {
        // 随机排序
        $product = $product->where(['type'=>'normal','on_sale'=>true])->inRandomOrder()->paginate(10);
        return response()->json(['products' => $product]);
    }
}
