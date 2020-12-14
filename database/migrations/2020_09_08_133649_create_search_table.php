<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_words', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment("商品ID");
            $table->string('word')->comment("查询单词");
            $table->unsignedInteger('times')->default(0)->comment("查询次数");
            $table->boolean('is_deleted')->default(false)->comment("是否被删除");
            $table->timestamps();
        });
        add_table_comment("search_words","搜索表");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('search_word');
    }
}
