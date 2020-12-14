<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpressCompanysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('express_companys', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment("快递公司编码");
            $table->string('name')->comment("快递公司名称");
            $table->integer('times')->default(0)->comment("使用次数");
            $table->boolean('status')->default(false)->comment("状态（0：禁用，1：启用）");
            $table->timestamps();
        });
        add_table_comment("express_companys","快递公司表");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('express_companys');
    }
}
