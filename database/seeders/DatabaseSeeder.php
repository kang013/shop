<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminTablesSeeder::class);
        $this->call(SlideCategorySeeder::class);
        $this->call(SlideSeeder::class);
        $this->call(CategoriesSeeder::class);
        $this->call(DDRProductsSeeder::class);
        $this->call(CookerProductsSeeder::class);
        $this->call(LineProductsSeeder::class);
        $this->call(PcProductsSeeder::class);
        $this->call(PhoneProductsSeeder::class);
    }
}
