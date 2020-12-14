<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_properties', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id')->comment("商品ID");
            $table->string('name')->comment("名称");
            $table->string('value')->comment("值");
            $table->timestamps();
//            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
        add_table_comment("product_properties","商品属性表");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_properties');
    }
}
