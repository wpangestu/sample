<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTableOrdersAddStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_status ENUM('waiting_payment','payment_success','waiting_order', 'accepted', 'processed','extend','canceled','done') DEFAULT 'waiting_payment'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_status ENUM('waiting-order', 'denied', 'accepted', 'processed','take-away','canceled','done') DEFAULT 'waiting-order'");
    }
}
