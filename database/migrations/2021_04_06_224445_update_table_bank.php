<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableBank extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('banks', function (Blueprint $table) {
            //
            $table->dropColumn(['code']);
            $table->longText('logo')->nullable();
            // $table->decimal('price',12,0);
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
        Schema::table('banks', function (Blueprint $table) {
            //
            $table->dropColumn(['logo']);
            $table->string('code');
            // $table->decimal('price',12,0);
        });
    }
}
