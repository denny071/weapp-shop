<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id')->comment("订单ID");
            $table->unsignedInteger('product_id')->comment("商品ID");
            $table->unsignedInteger('product_sku_id')->comment("商品SKU ID");
            $table->unsignedInteger('amount')->comment("数量");
            $table->decimal('price',10,2)->comment("售价");
            $table->float('rating')->nullable()->comment("评分");
            $table->text('review')->nullable()->comment("评论");
            $table->timestamp('reviewed_at')->nullable()->comment("评论时间");
            $table->timestamps();

//            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
//            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
//            $table->foreign('product_sku_id')->references('id')->on('product_skus')->onDelete('cascade');


        });
        add_table_comment("order_items","订单项目表");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}
