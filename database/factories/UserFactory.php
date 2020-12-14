<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $avatar = get_test_image("avatar","jpg");
        return [
            'name' => $this->faker->name,
            'mobile' => $this->faker->e164PhoneNumber,
            'introduction' => $this->faker->text(),
            'email' => $this->faker->unique()->safeEmail,
            'avatar' => $avatar,
            'weixin_avatar' => $avatar,
            'last_login_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
        ];

    }
}
