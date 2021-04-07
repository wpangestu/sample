<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWithdrawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdraws', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('engineer_id');
            $table->decimal('amount',12,0);
            $table->string('withdraw_id')->nullable();
            $table->longText('note')->nullable();
            $table->enum('status',['pending','decline','success'])->default('pending');
            $table->longText('description')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->datetime('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('withdraws');
    }
}
