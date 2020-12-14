<?php
namespace Database\Seeders;


use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = \App\Models\Product::factory(30)->create();
        foreach ($products as $product) {

            $skus = \App\Models\ProductSku::factory(3)->create(['product_id' => $product->id]);
            \App\Models\ProductProperty::factory(3)->create(['product_id' => $product->id]);

            $product->update(['price' => $skus->min('price')]);
        }

    }
}
