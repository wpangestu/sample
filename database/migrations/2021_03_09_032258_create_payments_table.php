<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->decimal('amount',12,0);
            $table->string('paymentid');
            $table->enum('status',['pending','decline','success'])->default('pending');
            $table->longText('image');
            $table->integer('convenience_fee')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->datetime('verified_at')->nullabable();
            $table->string('verified_name')->nullabable();
            $table->string('type');
            $table->json('orders');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('users');
            $table->foreign('verified_by')->references('id')->on('users');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
