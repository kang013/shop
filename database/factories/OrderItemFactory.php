<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition()
    {
        // 从数据库随机取一条商品
        $product = Product::query()->where('on_sale', true)->inRandomOrder()->first();
        // 从该商品的 SKU 中随机取一条
        $sku = $product->skus()->inRandomOrder()->first();

        return [
            'amount'         => random_int(1, 5), // 购买数量随机 1 - 5 份
            'price'          => $sku->price,
            'rating'         => null,
            'review'         => null,
            'reviewed_at'    => null,
            'product_id'     => $product->id,
            'product_sku_id' => $sku->id,
        ];
    }
}
