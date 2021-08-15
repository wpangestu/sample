<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateWithdrawTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('withdraws', function (Blueprint $table) {
            $table->string('account_number')->nullable();
            $table->string('account_holder')->nullable();
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
        Schema::table('withdraws', function (Blueprint $table) {
            $table->dropColumn(['account_number','account_holder']);
        });
    }
}
