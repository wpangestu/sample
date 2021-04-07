<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableHistoryBalanceCreateWithdrawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('history_balances', function (Blueprint $table) {
            //
            $table->dropColumn(['type','time','status','image','verified_by','verified_at']);
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('created_by')->default(0);
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
        Schema::table('history_balances', function (Blueprint $table) {
            $table->datetime('time');
            $table->enum('type',['in','out']);
            $table->enum('status',['pending','success','decline'])->default('pending');
            $table->string('image')->nullable();
            $table->integer('verified_by')->nullable();
            $table->datetime('verified_at')->nullable();

            $table->dropColumn(['description','created_by']);
        });


    }
}
