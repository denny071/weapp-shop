<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalogs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment("目录名称");
            $table->unsignedInteger('parent_id')->nullable()->comment("父ID");
            $table->boolean('is_directory')->comment("是否目录");
            $table->unsignedInteger('level')->comment("目录级别");
            $table->string('path')->comment("目录信息");
            $table->string('cover_image')->comment("封面图片");
            $table->timestamps();
//            $table->foreign('parent_id')->references('id')->on('catalogs')->onDelete('cascade');

        });
        add_table_comment("catalogs","商品类目表");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catalogs');
    }
}
