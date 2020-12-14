<?php

namespace Database\Factories;

use App\Models\ProductProperty;
use Illuminate\Database\Eloquent\Factories\Factory;



class ProductPropertyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductProperty::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        return [
            'name'         => $this->faker->word,
            'value'         => $this->faker->word,


        ];

    }
}

