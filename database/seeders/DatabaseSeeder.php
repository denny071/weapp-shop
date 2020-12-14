<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(\Database\Seeders\AdminSeeder::class);
        $this->call(\Database\Seeders\UsersSeeder::class);
        $this->call(\Database\Seeders\UserAddressesSeeder::class);
        $this->call(\Database\Seeders\CatalogsSeeder::class);
        $this->call(\Database\Seeders\ProductsSeeder::class);
        $this->call(\Database\Seeders\CouponCodesSeeder::class);
        $this->call(\Database\Seeders\ExpressCompanySeeder::class);
        $this->call(\Database\Seeders\ExpressCostSeeder::class);
        $this->call(\Database\Seeders\OrdersSeeder::class);



    }
}
