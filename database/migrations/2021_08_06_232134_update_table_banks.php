<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableBanks extends Migration
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
            $table->boolean('is_active')->default(true);
        });

        Schema::table('bank_payments', function (Blueprint $table) {
            //
            $table->boolean('is_active')->default(true);
            $table->string('account_name')->nullable();
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
            $table->dropColumn(['is_active']);
        });

        Schema::table('bank_payments', function (Blueprint $table) {
            //
            $table->dropColumn(['is_active','account_name']);
        });
    }
}
