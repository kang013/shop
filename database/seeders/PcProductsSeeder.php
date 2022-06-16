<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class PcProductsSeeder extends Seeder
{
    public function run()
    {
        $productData = [
            [
                "title"       => "华为笔记本电脑MateBook D 14",
                "long_title"  => "华为笔记本电脑MateBook D 14 SE版 14英寸 11代酷睿 i5 锐炬显卡 8G+512G 轻薄本/高清护眼防眩光屏",
                "description" => '<p><img src="https://img30.360buyimg.com/sku/jfs/t1/149802/22/20835/730681/61e14530Ead8ebf40/58fa174ca397a311.jpg" /></p>',
                "image"       => "https://img12.360buyimg.com/n2/s270x270_jfs/t1/197255/26/24988/217671/62a9413fE67cb513b/ae71850fd3e4f595.jpg!q70.webp",
                "price"       => "3999.00",
                "skus"        => [
                    ["title" => "128GB 黑色", "description" => "128GB 黑色", "price" => "4999.00"],
                    ["title" => "256GB 绿色", "description" => "256GB 绿色", "price" => "5299.00"],
                ],
                "properties"  => [
                    ["name" => "品牌名称", "value" => "华为"],
                    ["name" => "内存容量", "value" => "128GB"],
                    ["name" => "内存容量", "value" => "256GB"],
                ],
                "images"  => [
                    "https://m.360buyimg.com/mobilecms/s750x750_jfs/t1/117071/21/27443/202618/62a15b84E0d698aec/d1417cca5bfec9c7.jpg!q80.dpg",
                    "https://m.360buyimg.com/mobilecms/s1265x1265_jfs/t1/69468/27/17416/141766/626baaecE202ac55d/d65dd608458c8c0b.jpg!q70.dpg.webp",
                ],
            ],
        ];

        // 查找名为『内存』的商品类目
        $category = Category::where('name', '笔记本')->first();

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
