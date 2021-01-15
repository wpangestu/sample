<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableEngineers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('engineers', function (Blueprint $table) {
            $table->id();
            $table->string('id_card_number');
            $table->string('name');
            $table->string('phone');
            $table->string('address');
            $table->string('email');
            $table->boolean('is_varified_email')->default(false);
            $table->datetime('varified_email_at')->nullable();
            $table->boolean('is_verified_data')->default(false);
            $table->datetime('verified_data_at')->nullable();
            $table->integer('verified_by')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('id_card_image')->nullable(); 
            $table->string('id_card_selfie_image')->nullable(); 

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_engineers');
    }
}
