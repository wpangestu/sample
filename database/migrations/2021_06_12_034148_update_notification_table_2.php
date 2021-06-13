<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateNotificationTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('service_info','review','wallet', 'order', 'customer')");

        Schema::table('notifications', function (Blueprint $table) {
            //
            $table->string('subtitle')->nullable();
            $table->string('subtitle_color')->nullable();
            $table->string('caption')->nullable();
            $table->string('action')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('service_info','review','wallet', 'order')");
        
        Schema::table('notifications', function (Blueprint $table) {
            //
            $table->dropColumn(['subtitle','subtitle_color','caption','action']);
        });
    }
}
