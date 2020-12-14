<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFavroiteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_favorites', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment("用户ID");
            $table->unsignedInteger('product_id')->comment("商品ID");
            $table->timestamps();
//            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
//            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
        add_table_comment("user_favorites","用户收藏表");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_favorites');
    }
}
