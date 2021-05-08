<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaseServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('base_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_service_id');
            $table->string('name');
            $table->decimal('price', 12,0);
            $table->decimal('price_receive',12,0);
            $table->text('description')->nullable();
            $table->boolean('guarantee')->default(true);
            $table->text('long_guarantee')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('category_service_id')->references('id')->on('category_services');            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('base_services');
    }
}
