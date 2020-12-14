<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_skus', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id')->comment("商品ID");
            $table->string('title')->comment("商品标题");
            $table->string('description')->comment("描述");
            $table->decimal('price', 10, 2)->comment("商品价格");
            $table->unsignedInteger('stock')->comment("库存数量");
            $table->timestamps();
//            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
        add_table_comment("product_skus","商品SKU表");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_skus');
    }
}
