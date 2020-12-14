<?php


namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CouponFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Coupon::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        // 首先随机获取一个类型
        $type = $this->faker->randomElement(array_keys(\App\Models\Coupon::$typeMap));
        // 根据取得的类型生成对应折扣
        $value = $type === \App\Models\Coupon::TYPE_FIXED?rand(1,200):random_int(1,50);

        // 如果是固定金额，则最低订单金额必须要比优惠金额高 0.01 员
        if ($type === \App\Models\Coupon::TYPE_FIXED) {
            $minAmount = $value + 0.01;
        } else {
            // 如果是百分比折扣，有50%概率不需要最低订单金额
            if (random_int(0,100) < 50) {
                $minAmount = 0;
            } else {
                $minAmount = random_int(100,1000);
            }
        }

        return [
            'name' => join( ' ', $this->faker->words), // 随机生成名称
            'code' => \App\Models\Coupon::findAvailableCode(),
            'type' => $type,
            'value' => $value,
            'total' => 1000,
            'used' => 0,
            'min_amount' => $minAmount,
            'not_before' => "2019-01-01 00:00:01",
            'not_after' =>  "2021-01-01 00:00:01",
            'enabled' => true
        ];

    }
}


