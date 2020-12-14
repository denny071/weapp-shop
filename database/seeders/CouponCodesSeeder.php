<?php
namespace Database\Seeders;


use App\Models\Coupon;
use Illuminate\Database\Seeder;

class CouponCodesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Coupon::factory(20)->create();
    }
}
