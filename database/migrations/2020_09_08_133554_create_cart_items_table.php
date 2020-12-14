<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment("用户ID");
            $table->unsignedInteger('product_sku_id')->comment("商品SKU ID");
            $table->unsignedInteger('amount')->comment("商品总额");
            $table->boolean('checked')->nullable()->default(true)->comment("是否选择");
            $table->timestamps();

//            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
//            $table->foreign('product_sku_id')->references('id')->on('product_skus')->onDelete('cascade');
        });
        add_table_comment("cart_items","购车表");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
}
