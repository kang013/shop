<?php

namespace Database\Seeders;

use App\Models\Slide;
use App\Models\SlideCategory;
use Illuminate\Database\Seeder;

class SlideSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 1,
                'image' => 'https://yanxuan.nosdn.127.net/static-union/165519718437b102.jpg?type=webp&imageView&quality=75&thumbnail=750x0',
            ],
            [
                'name' => 2,
                'image' => 'https://yanxuan.nosdn.127.net/static-union/165398333137b102.jpg?type=webp&imageView&quality=75&thumbnail=750x0',
            ]
        ];

        $category = SlideCategory::where(['index_name' => 'index'])->first();

        foreach ($data as $value){
            $slide = new Slide([
                'name' => $value['name'],
                'image' => $value['image'],
                'status' => 1,
                'order' => 100,
            ]);
            $slide->category()->associate($category);
            $slide->save();
        }
    }
}
