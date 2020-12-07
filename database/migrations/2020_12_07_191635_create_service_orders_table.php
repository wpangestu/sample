<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->string('serviceorder_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('engineer_id');
            $table->unsignedBigInteger('service_id');
            $table->text('description')->nullable(true);
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('engineer_id')->references('id')->on('users');
            $table->foreign('service_id')->references('id')->on('services');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_orders');
    }
}
