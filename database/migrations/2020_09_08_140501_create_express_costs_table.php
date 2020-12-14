<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpressCostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('express_costs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("express_id")->comment("快递公司ID");
            $table->string('province')->comment("目的省");
            $table->float("weight",6,2)->default(2)->comment("重量 kg");
            $table->float("freight",6,2)->comment("快递费用");
            $table->timestamps();
        });
        add_table_comment("express_costs","快递费用表");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('express_costs');
    }
}
