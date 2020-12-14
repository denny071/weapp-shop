<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminTables extends Migration
{


    public function config($key)
    {
        return config('admin.'.$key);
    }
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->config('database.users_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username', 120)->unique();
            $table->string('password', 80);
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
        });

        add_table_comment($this->config('database.users_table'),"管理员表");

        Schema::create($this->config('database.roles_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->string('slug', 50)->unique();
            $table->timestamps();
        });
        add_table_comment($this->config('database.roles_table'),"角色表");

        Schema::create($this->config('database.permissions_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 50);
            $table->string('slug', 50)->unique();
            $table->string('http_method')->nullable();
            $table->text('http_path')->nullable();
            $table->integer('order')->default(0);
            $table->bigInteger('parent_id')->default(0);
            $table->timestamps();
        });
        add_table_comment($this->config('database.permissions_table'),"权限表");

        Schema::create($this->config('database.menu_table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('parent_id')->default(0);
            $table->integer('order')->default(0);
            $table->string('title', 50);
            $table->string('icon', 50)->nullable();
            $table->string('uri', 50)->nullable();
            $table->tinyInteger('show')->default(1);
            $table->string('extension', 50)->default('');

            $table->timestamps();
        });
        add_table_comment($this->config('database.menu_table'),"菜单表");

        Schema::create($this->config('database.role_users_table'), function (Blueprint $table) {
            $table->bigInteger('role_id');
            $table->bigInteger('user_id');
            $table->unique(['role_id', 'user_id']);
            $table->timestamps();
        });
        add_table_comment($this->config('database.role_users_table'),"角色用户关联表");

        Schema::create($this->config('database.role_permissions_table'), function (Blueprint $table) {
            $table->bigInteger('role_id');
            $table->bigInteger('permission_id');
            $table->unique(['role_id', 'permission_id']);
            $table->timestamps();
        });
        add_table_comment($this->config('database.role_permissions_table'),"角色权限关联表");

        Schema::create($this->config('database.role_menu_table'), function (Blueprint $table) {
            $table->bigInteger('role_id');
            $table->bigInteger('menu_id');
            $table->unique(['role_id', 'menu_id']);
            $table->timestamps();
        });
        add_table_comment($this->config('database.role_menu_table'),"角色菜单关联表");

        Schema::create($this->config('database.permission_menu_table'), function (Blueprint $table) {
            $table->bigInteger('permission_id');
            $table->bigInteger('menu_id');
            $table->unique(['permission_id', 'menu_id']);
            $table->timestamps();
        });
        add_table_comment($this->config('database.permission_menu_table'),"权限菜单关联表");


        Schema::create($this->config('database.settings_table'), function (Blueprint $table) {
            $table->string('slug', 100)->primary();
            $table->longText('value');
            $table->timestamps();
        });
        add_table_comment($this->config('database.settings_table'),"后台配置表");


        Schema::create( $this->config('database.extensions_table'), function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name', 100)->unique();
            $table->string('version', 20)->default('');
            $table->tinyInteger('is_enabled')->default(0);
            $table->text('options')->nullable();
            $table->timestamps();

            $table->engine = 'InnoDB';
        });
        add_table_comment($this->config('database.extensions_table'),"后台扩展表");

        Schema::create($this->config('database.extension_histories_table'), function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('name', 100);
            $table->tinyInteger('type')->default(1);
            $table->string('version', 20)->default(0);
            $table->text('detail')->nullable();

            $table->index('name');
            $table->timestamps();
            $table->engine = 'InnoDB';
        });
        add_table_comment($this->config('database.extension_histories_table'),"后台扩展历史表");


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->config('database.users_table'));
        Schema::dropIfExists($this->config('database.roles_table'));
        Schema::dropIfExists($this->config('database.permissions_table'));
        Schema::dropIfExists($this->config('database.menu_table'));
        Schema::dropIfExists($this->config('database.role_users_table'));
        Schema::dropIfExists($this->config('database.role_permissions_table'));
        Schema::dropIfExists($this->config('database.role_menu_table'));
        Schema::dropIfExists($this->config('database.permission_menu_table'));
        Schema::dropIfExists($this->config('database.settings_table'));
        Schema::dropIfExists($this->config('database.extensions_table'));
        Schema::dropIfExists($this->config('database.extension_histories_table'));
    }
}
