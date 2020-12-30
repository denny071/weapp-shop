<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable()->comment("用户名称");
            $table->string('mobile')->nullable()->unique()->comment("手机号");
            $table->string('email')->nullable()->unique()->comment("邮箱");
            $table->string('password')->nullable()->comment("密码");
            $table->string('avatar')->nullable()->comment("头像图片");
            $table->string('introduction')->nullable()->comment("简介");
            $table->unsignedInteger('notification_count')->unsigned()->default(0)->comment("通知信息");
            $table->string('weixin_openid')->unique()->nullable()->comment("微信openid");
            $table->string('weixin_avatar')->nullable()->comment("微信头像");
            $table->string('weixin_session_key')->unique()->nullable()->comment("微信session_key");
            $table->string('weixin_union_id')->unique()->nullable()->comment("微信union_id");
            $table->string('weapp_openid')->unique()->nullable()->comment("小程序openid");
            $table->tinyInteger('gender')->nullable()->comment("性别：1：男，2：女");
            $table->string('country')->nullable()->comment("所在国家");
            $table->string('province')->nullable()->comment("所在省份");
            $table->string('city')->nullable()->comment("所在城市");
            $table->string('language')->nullable()->comment("使用语言");
            $table->timestamp('last_login_at')->nullable()->comment("最近登录");
            $table->boolean('email_verified')->default(false)->comment("微信头像");
            $table->rememberToken();
            $table->timestamps();

        });
        add_table_comment("users","用户表");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
