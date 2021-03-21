<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableReview extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('reviews', function (Blueprint $table) {
            //

            $table->unsignedBigInteger('service_order_id')->nullable()->change();
            $table->unsignedBigInteger('order_id');
            $table->text('liked')->nullable();

            $table->foreign('order_id')->references('id')->on('orders');
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
        Schema::table('reviews', function (Blueprint $table) {
            //

            $table->dropColumn(['order_id','liked']);
        });
    }
}
