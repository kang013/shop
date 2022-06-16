<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class CookerProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $productData = [
            [
                "title"       => "美的（Midea）电磁炉 家用2200W",
                "long_title"  => "美的（Midea）电磁炉 家用2200W大功率 火锅炉汉森面板 电磁灶 智能定时 旋风防堵风机 C22-RT22E01 ",
                "description" => '<p><img src="https://img30.360buyimg.com/sku/jfs/t1/200496/25/24146/330311/62844c67Eb2e4aed7/d1c24c261143be40.jpg" /></p>',
                "image"       => "https://img14.360buyimg.com/n2/s240x240_jfs/t1/214102/25/20086/179183/62a9fc65E56aaddaf/9e84a874ddd0c2a5.jpg!q70.jpg.webp",
                "price"       => "155.00",
                "skus"        => [
                    ["title" => "2200W 大功率", "description" => "2200W 大功率", "price" => "178.00"],
                    ["title" => "1800W 大功率", "description" => "1800W 大功率", "price" => "155.00"],
                ],
                "properties"  => [
                    ["name" => "品牌名称", "value" => "美的"],
                    ["name" => "功率", "value" => "2200W"],
                    ["name" => "功率", "value" => "1800W"],
                ],
                "images"  => [
                    "https://m.360buyimg.com/mobilecms/s750x750_jfs/t1/214102/25/20086/179183/62a9fc65E56aaddaf/9e84a874ddd0c2a5.jpg!q80.dpg",
                    "https://m.360buyimg.com/mobilecms/s1265x1265_jfs/t1/179916/27/2980/424443/60965ec2E3047f9c0/d6fe59d265818958.jpg!q70.dpg.webp",
                ],
            ],
        ];

        // 查找名为『内存』的商品类目
        $category = Category::where('name', '电磁炉')->first();

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
