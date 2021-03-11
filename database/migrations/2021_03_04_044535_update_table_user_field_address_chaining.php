<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableUserFieldAddressChaining extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            // $table->unsignedBigInteger('province_id')->nullable()->default(null);
            // $table->unsignedBigInteger('regency_id')->nullable()->default(null);
            // $table->unsignedBigInteger('district_id')->nullable()->default(null);
            // $table->unsignedBigInteger('village_id')->nullable()->default(null);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn(['province_id','regency_id','district_id','village_id']);
        });
    }
}
