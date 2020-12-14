<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment("用户ID");
            $table->string('province')->comment("省份");
            $table->string('province_code')->nullable()->comment("省份code");
            $table->string('city')->comment("城市");
            $table->string('city_code')->nullable()->comment("城市code");
            $table->string('district')->comment("地区");
            $table->string('district_code')->nullable()->comment("地区code");
            $table->string('address')->comment("详细地址");
            $table->string('zip')->nullable()->comment("邮编");
            $table->string('contact_name')->comment("联系人");
            $table->string('contact_mobile')->comment("联系人手机号");
            $table->boolean('is_default')->default(false)->comment("是否默认");
            $table->dateTime('last_used_at')->nullable()->comment("最近登录");
            $table->timestamps();
//            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        add_table_comment("user_addresses","用户地址表");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_addresses');
    }
}
