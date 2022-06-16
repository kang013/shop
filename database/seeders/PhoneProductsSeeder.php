<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class PhoneProductsSeeder extends Seeder
{
    public function run()
    {
        $productData = [
            [
                "title"       => "Apple iPhone 13 (A2634)",
                "long_title"  => "Apple iPhone 13 (A2634) 128GB 星光色 支持移动联通电信5G 双卡双待手机【快充套装】",
                "description" => '<p><img src="https://img30.360buyimg.com/sku/jfs/t1/149802/22/20835/730681/61e14530Ead8ebf40/58fa174ca397a311.jpg" /></p>',
                "image"       => "https://img10.360buyimg.com/n2/s270x270_jfs/t1/134784/35/23156/97133/6212fc17E780035a3/0dd82913e51e4d8f.jpg!q70.webp",
                "price"       => "4999.00",
                "skus"        => [
                    ["title" => "128GB 黑色", "description" => "128GB 黑色", "price" => "4999.00"],
                    ["title" => "256GB 绿色", "description" => "256GB 绿色", "price" => "5299.00"],
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
            ]));
            $product->category()->associate($category);
            $product->save();

            // 遍历商品数据中的 SKU 字段
            foreach ($data['skus'] as $sku) {
                $product->skus()->create(array_merge($sku, ['stock' => 999]));
            }
            // 遍历商品数据中的 properties 字段
            foreach ($data['properties'] as $attribute) {
                $product->properties()->create($attribute);
            }
        }
    }
}
