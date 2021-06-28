<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateWithdrawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('withdraws', function (Blueprint $table) {
            //
            $table->renameColumn('engineer_id', 'user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('withdraws', function (Blueprint $table) {
            //
            $table->renameColumn('user_id', 'engineer_id');
        });
    }
}
