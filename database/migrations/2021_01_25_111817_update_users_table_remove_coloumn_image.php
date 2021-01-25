<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTableRemoveColoumnImage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn(['idcard_image','idcard_selfie_image','formal_image']);
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
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('idcard_image')->nullable(); 
            $table->string('idcard_selfie_image')->nullable(); 
            $table->string('formal_image')->nullable(); 
        });
    }
}
