<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('优惠券标题');
            $table->string('code')->unique()->comment('优惠码，用户下单时输入');
            $table->string('type')->comment('优惠卷类型，支持固定金额和百分比折扣');
            $table->decimal('value',12,2)->comment('折扣值，根据不同类型含义不同');
            $table->unsignedInteger('total')->comment('全站可兑换的数量');
            $table->unsignedInteger('used')->default(0)->comment('当前已兑换的数量');
            $table->decimal('min_amount',10,2)->comment('使用该优惠券的最低订单金额');
            $table->dateTime('not_before')->nullable()->comment('在这个时间之前不可用');
            $table->dateTime('not_after')->nullable()->comment('在这个时间之后不可用');
            $table->tinyInteger('enabled')->comment('优惠券是否生效');
            $table->timestamps();
//            $table->foreign('coupon_code_id')->references('id')->on('coupon_codes')->onDelete('set null');
        });
        add_table_comment("coupons","优惠券表");
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupons');
    }
}
