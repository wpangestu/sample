<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddAddressOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->text('address')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();

            $table->foreign('payment_id')->references('id')->on('payments');
        });
        
        DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('pending', 'check', 'decline', 'success') DEFAULT 'pending'");
        
        Schema::table('payments', function (Blueprint $table) {
            //
            $table->datetime('verified_at')->nullable()->change();
            $table->string('verified_name')->nullable()->change();
            $table->longText('image')->nullable()->change();

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
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->dropColumn(['address','payment_id']);

        });

        DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('pending', 'decline', 'success') DEFAULT 'pending'");
        
        Schema::table('payments', function (Blueprint $table) {
            //

            $table->datetime('verified_at')->change();
            $table->string('verified_name')->change();
            $table->longText('image')->change();
        });
    }
}
