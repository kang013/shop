<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class LineProductsSeeder extends Seeder
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
                "title" => "罗马仕 数据线三合一苹果Type-c",
                "long_title" => "罗马仕 数据线三合一苹果Type-c安卓手机充电线一拖三适用iPhone12/11小米/oppo华为vivo 1.5米加长蓝色",
                "description" => '<p><img src="https://img30.360buyimg.com/sku/jfs/t1/138042/36/26515/327022/61e4d1cdEbee4e6d9/534fa10d59ebfa53.jpg" /></p>',
                "image" => "https://img13.360buyimg.com/n2/s240x240_jfs/t1/219806/22/18453/153285/62a9e6edE337f5ec8/fdaa527b7bf6f8f1.jpg!q70.jpg.webp",
                "price" => "14.90",
                "skus" => [
                    ["title" => "i5 8GB 512GB 银色", "description" => "i5 8GB 512GB 银色", "price" => "3999"],
                    ["title" => "i7 16GB 512GB 黑色", "description" => "i5 8GB 512GB 黑色", "price" => "4299"],
                ],
                "properties" => [
                    ["name" => "品牌名称", "value" => "华为"],
                    ["name" => "内存", "value" => "8GB"],
                    ["name" => "内存", "value" => "16GB"],
                    ["name" => "处理器", "value" => "i5"],
                    ["name" => "处理器", "value" => "i7"],
                ],
                "images" => [
                    "https://m.360buyimg.com/mobilecms/s750x750_jfs/t1/219806/22/18453/153285/62a9e6edE337f5ec8/fdaa527b7bf6f8f1.jpg!q80.dpg",
                    "https://m.360buyimg.com/mobilecms/s1265x1265_jfs/t1/202491/24/13309/453499/617bd3c2E12c06218/8f9a4824dd6c2664.jpg!q70.dpg.webp",
                ],
            ],
        ];

        // 查找名为『内存』的商品类目
        $category = Category::where('name', '数据线')->first();

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
                'rating' => 5,
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
