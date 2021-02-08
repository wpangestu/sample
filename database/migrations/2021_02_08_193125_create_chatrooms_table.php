<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatroomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::create('chatrooms', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('user_1');
        //     $table->unsignedBigInteger('user_2');
        //     $table->boolean('pinned_user_1')->default(false);
        //     $table->boolean('pinned_user_2')->default(false);
        //     $table->timestamps();

        //     $table->foreign('user_1')->references('id')->on('users');            
        //     $table->foreign('user_2')->references('id')->on('users');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('chatrooms');
    }
}
