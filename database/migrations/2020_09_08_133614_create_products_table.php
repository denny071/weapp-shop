<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {


        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type')->default("normal")->index()->comment("类型");
            $table->string('catalog_id')->comment("商品类型目ID");
            $table->string('title')->comment("商品名称");
            $table->string('long_title')->comment("商品长名称");
            $table->string('cover_image')->nullable()->comment("封面图片");
            $table->text('album_image')->nullable()->comment("相册图片");
            $table->text('content_image')->nullable()->comment("内容图片");
            $table->boolean('on_sale')->default(true)->comment("是否上架");
            $table->float('rating')->default(5)->comment("评价");
            $table->unsignedInteger('sold_count')->default(0)->comment("销售数量");
            $table->unsignedInteger('review_count')->default(0)->comment("评论数量");
            $table->decimal('price', 10, 2)->default(0)->comment("商品价格");
            $table->timestamps();
        });
        add_table_comment("products","商品表");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
