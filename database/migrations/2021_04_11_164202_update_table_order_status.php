<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableOrderStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_status ENUM('waiting-order', 'denied', 'accepted', 'processed','take-away','extend','canceled','done') DEFAULT 'waiting-order'");
        Schema::table('orders', function (Blueprint $table) {
            //
            $table->boolean('is_extend')->default(false);
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
        DB::statement("ALTER TABLE orders MODIFY COLUMN order_status ENUM('waiting-order', 'denied', 'accepted', 'processed','take-away','canceled','done') DEFAULT 'waiting-order'");

        Schema::table('orders', function (Blueprint $table) {
            //
            $table->dropColumn(['is_extend']);
            // $table->decimal('price',12,0);
        }); 
    }
}
