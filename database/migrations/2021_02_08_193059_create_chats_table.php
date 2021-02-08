<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chatrooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_1');
            $table->unsignedBigInteger('user_2');
            $table->boolean('pinned_user_1')->default(false);
            $table->boolean('pinned_user_2')->default(false);
            $table->timestamps();

            $table->foreign('user_1')->references('id')->on('users');            
            $table->foreign('user_2')->references('id')->on('users');
        });
        
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('to');
            $table->unsignedBigInteger('from');
            $table->text('message');
            $table->unsignedBigInteger('chatroom_id');
            $table->longText('media')->nullable();
            $table->boolean('read')->default(false);
            $table->timestamps();

            $table->foreign('to')->references('id')->on('users');            
            $table->foreign('from')->references('id')->on('users');
            $table->foreign('chatroom_id')->references('id')->on('chatrooms');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chats');
        Schema::dropIfExists('chatrooms');
    }
}
