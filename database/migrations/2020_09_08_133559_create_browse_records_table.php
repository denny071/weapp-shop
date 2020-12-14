<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrowseRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('browse_records', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->comment("用户ID");
            $table->enum('type',['product'])->comment("类型：product 商品");
            $table->integer('sub_id')->comment("关联ID");
            $table->integer('times')->default(1)->comment("浏览次数");
            $table->boolean('is_deleted')->default(false)->comment("是否被删除");
            $table->timestamps();
        });
        add_table_comment("browse_records","浏览记录表");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('browse_records');
    }
}
