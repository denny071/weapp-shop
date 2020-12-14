<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeckillProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seckill_products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id')->comment("商品ID");
            $table->dateTime('start_at')->comment("开始时间");
            $table->dateTime('end_at')->comment("结束时间");
            $table->timestamps();
//            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
        add_table_comment("seckill_products","秒杀商品表");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seckill_products');
    }
}
