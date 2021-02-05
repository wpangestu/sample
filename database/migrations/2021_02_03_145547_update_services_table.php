<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('engineer_id');
            $table->json('skill');
            $table->longText('sertification_image');
            $table->enum('status',['active','non_active','danied','review'])->default('review');
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->datetime('verified_at')->nullable();

            $table->foreign('engineer_id')->references('id')->on('users');
            $table->foreign('verified_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            //
            $table->dropForeign('services_engineer_id_foreign');
            $table->dropForeign('services_verified_by_foreign');

            $table->dropColumn([
                'engineer_id',
                'skill',
                'sertification_image',
                'status',
                'verified_by',
                'verified_at'
            ]);
        });
    }
}
