<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableReview123 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reviews', function (Blueprint $table) {
            //
            $table->dropForeign('ratings_service_order_id_foreign');
            $table->dropForeign('reviews_order_id_foreign');
            $table->dropColumn(['order_id','service_order_id']);
            $table->string('order_number_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reviews', function (Blueprint $table) {
            //
            $table->string('order_id')->nullable();
            $table->string('service_order_id')->nullable();
            $table->dropColumn(['order_number_id']);
        });
    }
}
