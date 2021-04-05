<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateOrderTableAgain extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_status ENUM('waiting-order', 'denied', 'accepted', 'processed','take-away','canceled','done') DEFAULT 'waiting-order'");

        Schema::table('orders', function (Blueprint $table) {
            //
            $table->text('custom_order')->nullable();
            // $table->decimal('price',12,0);
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
            $table->dropColumn(['custom_order']);
            // $table->decimal('price',12,0);
        });
    }
}
