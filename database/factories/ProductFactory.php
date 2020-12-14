<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;


class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {


        // 从数据库中随机取一个类目
        $catalog = \App\Models\Catalog::query()->where('is_directory', false)->inRandomOrder()->first();

        return [
            'title'        => $this->faker->word,
            'long_title'   => $this->faker->sentence,
            'on_sale'      => true,
            'cover_image'  => get_test_image("product_cover","webp"),
            'album_image'  => get_test_image("product_album","jpg",5),
            'content_image'=> get_test_image("product_content","jpg",10),
            'rating'       => $this->faker->numberBetween(0, 5),
            'sold_count'   => 0,
            'review_count' => 0,
            'price'        => 0,
            // 将取出的类目 ID 赋给 category_id 字段
            // 如果数据库中没有类目则 $category 为 null，同样 category_id 也设成 null
            'catalog_id'  => $catalog ? $catalog->id : null,
        ];

    }
}
