<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrowdfundingProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crowdfunding_products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id')->comment("商品ID");
            $table->decimal('target_amount', 10, 2)->comment("目标价格");
            $table->decimal('total_amount', 10, 2)->default(0)->comment("总价格");
            $table->unsignedInteger('user_count')->default(0)->comment("用户数量");
            $table->dateTime('end_at')->comment("结束时间");
            $table->string('status')->default("funding")->comment("状态");
            $table->timestamps();

//            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
        add_table_comment("crowdfunding_products","众筹商品表");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crowdfunding_products');
    }
}
