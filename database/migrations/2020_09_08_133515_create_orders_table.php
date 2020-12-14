<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {


            $table->increments('id');
            $table->string('type')->default("normal")->index()->comment("订单类型");
            $table->string('no')->unique()->comment("订单编号");
            $table->unsignedInteger('user_id')->comment("用户ID");
            $table->text('address')->comment("订单地址");

            $table->decimal("product_amount",10,2)->comment("商品总额");
            $table->decimal('total_amount',12,2)->comment("订单总额");
            $table->text('remark')->nullable()->comment("备足");

            $table->boolean('is_paid')->default(false)->comment("是否支付");
            $table->dateTime('paid_at')->nullable()->comment("支付时间");
            $table->dateTime('pay_deadline')->nullable()->comment("支付截止时间");
            $table->unsignedInteger('coupon_code_id')->nullable()->comment("优惠券ID");
            $table->string('payment_method')->nullable()->comment("支付方式");
            $table->string('payment_no')->nullable()->comment("支付单号");



            $table->boolean('is_reviewed')->default(false)->comment("是否退款审核");
            $table->string('review')->nullable()->comment("审核内容");
            $table->dateTime('reviewed_at')->nullable()->comment("退款审核时间");

            $table->string('refund_no')->nullable()->comment("退款单号");
            $table->string('refund_status')->default("pending")->comment("退款状态");

            $table->string('express_company')->nullable()->comment("快递公司");
            $table->float("express_freight",8,2)->default(0)->comment("快递费用");
            $table->string('ship_status')->default("pending")->comment("物流状态");
            $table->text('ship_data')->nullable()->comment("物流数据");
            $table->text('ship_info')->nullable()->comment("物流状态");

            $table->text('extra')->nullable()->comment("扩展信息");
            $table->string('status',20)->nullable()->comment("订单状态");

            $table->boolean('is_closed')->default(false)->comment("是否关闭");
            $table->dateTime('closed_at')->nullable()->comment("关闭时间");
            $table->timestamps();
        });
        add_table_comment("orders","订单表");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
