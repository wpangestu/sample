<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->longText('description')->nullable();
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();
            $table->enum('type',['presentation','fixed'])->default('fixed');
            $table->decimal('value',12,2);
            $table->boolean('multiple')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('promo_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('promo_id');
            $table->unsignedBigInteger('order_id');
            $table->timestamps();

            $table->foreign('promo_id')->references('id')->on('promos');            
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
        Schema::dropIfExists('promos');
        Schema::dropIfExists('promo_orders');
    }
}
