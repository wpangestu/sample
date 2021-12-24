<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrderStatusOrdersTabels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
            DB::statement("ALTER TABLE orders MODIFY COLUMN order_status ENUM('waiting_payment','waiting_payment_confirmation','payment_success','waiting_order', 'accepted', 'processed','extend','canceled','done') DEFAULT 'waiting_payment'");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
            DB::statement("ALTER TABLE orders MODIFY COLUMN order_status ENUM('waiting_payment','payment_success','waiting_order', 'accepted', 'processed','extend','canceled','done') DEFAULT 'waiting_payment'");
        });
    }
}
