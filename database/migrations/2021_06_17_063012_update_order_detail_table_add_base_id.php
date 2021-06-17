<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateOrderDetailTableAddBaseId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('order_details', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('base_id')->nullable();
            $table->longText('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('order_details', function (Blueprint $table) {
            //
            $table->dropColumn(['base_id','image']);
        });
    }
}
