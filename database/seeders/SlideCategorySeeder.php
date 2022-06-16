<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SlideCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\SlideCategory::insert([
            'name' => 'test',
            'index_name' => 'index',
            'status' => true,
        ]);
    }
}
