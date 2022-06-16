<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSku;
use App\Models\SeckillProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redis;

class SeckillProductsSeeder extends Seeder
{
    public function run()
    {
        $productData = [
            [
                "title"       => "Apple iPhone 12 (A2634)",
                "long_title"  => "Apple iPhone 12 (A2634) 128GB 星光色 支持移动联通电信5G 双卡双待手机【快充套装】",
                "description" => '<p><img src="https://img30.360buyimg.com/sku/jfs/t1/149802/22/20835/730681/61e14530Ead8ebf40/58fa174ca397a311.jpg" /></p>',
                "image"       => "https://img10.360buyimg.com/n2/s270x270_jfs/t1/113960/3/20088/56974/5f861926E5153a0ef/1831cb31ecb63f24.jpg!q70.webp",
                "price"       => "2999.00",
                "skus"        => [
                    ["title" => "128GB 黑色", "description" => "128GB 黑色", "price" => "3999.00"],
                    ["title" => "256GB 绿色", "description" => "256GB 绿色", "price" => "2299.00"],
                ],
                "properties"  => [
                    ["name" => "品牌名称", "value" => "苹果"],
                    ["name" => "内存容量", "value" => "128GB"],
                    ["name" => "内存容量", "value" => "256GB"],
                ],
                "images"  => [
                    "https://m.360buyimg.com/mobilecms/s1265x1265_jfs/t1/86427/14/19271/36243/61411717E77fe56b3/6772d7b278344d28.jpg!q70.dpg.webp",
                    "https://m.360buyimg.com/mobilecms/s1265x1265_jfs/t1/210382/3/569/32745/61411717E369a0d11/c86d359347ae7008.jpg!q70.dpg.webp",
                ],
            ],
        ];

        // 查找名为『内存』的商品类目
        $category = Category::where('name', '手机')->first();

        // 遍历上面的商品数据
        foreach ($productData as $data) {
            // 创建一个新商品
            $product = new Product(array_merge(Arr::only($data, [
                'title',
                'long_title',
                'description',
                'image',
                'price',
                'images',
            ]), [
                'on_sale' => true,
                'rating'  => 5,
                'type'  => 'seckill',
            ]));
            $product->category()->associate($category);
            $product->save();

            // 遍历商品数据中的 SKU 字段
            foreach ($data['skus'] as $sku) {
                $product->skus()->create(array_merge($sku, ['stock' => 10]));
            }
            // 遍历商品数据中的 properties 字段
            foreach ($data['properties'] as $attribute) {
                $product->properties()->create($attribute);
            }

            SeckillProduct::query()->insert(
                [
                    'product_id' => $product->id,
                    'start_at' => date('Y-m-d H:i:s',time()),
                    'end_at' => date('Y-m-d H:i:s',time() + 86400 * 3),
                ]
            );

            // 获取当前时间与秒杀结束时间的差值
            $diff = $product->seckill->end_at->getTimestamp() - time();
            // 遍历商品 SKU
            $product->skus->each(function (ProductSku $sku) use ($diff, $product) {
                // 如果秒杀商品是上架并且尚未到结束时间
                if ($product->on_sale && $diff > 0) {
                    // 将剩余库存写入到 Redis 中，并设置该值过期时间为秒杀截止时间
                    Redis::setex('seckill_sku_'.$sku->id, $diff, $sku->stock);
                } else {
                    // 否则将该 SKU 的库存值从 Redis 中删除
                    Redis::del('seckill_sku_'.$sku->id);
                }
            });
        }
    }
}
