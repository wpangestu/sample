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
            $table->id();
            $table->string('order_number');
            $table->enum('order_type',['reguler','custom'])->default('reguler');
            $table->enum('order_status',['pending','waiting-order','denied','processed','take-away','canceled','done'])->default('pending');
            $table->boolean('is_take_away')->default(false);
            $table->unsignedBigInteger('customer_id')->length(20);
            $table->unsignedBigInteger('engineer_id')->length(20);
            $table->decimal('deposit',12,0)->default(0);
            $table->decimal('shipping',10,0)->default(0);
            $table->integer('convenience_fee')->nullable();
            $table->decimal('total_payment',12,0)->default(0);
            $table->decimal('total_payment_receive',12,0)->default(0);
            $table->longText('note')->nullable();
            $table->longText('photo')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('users');            
            $table->foreign('engineer_id')->references('id')->on('users');
        });
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
