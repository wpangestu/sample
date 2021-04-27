<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCategoryServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('category_services', function (Blueprint $table) {
            //
            $table->string('slug')->unique()->nullable();
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
        Schema::table('category_services', function (Blueprint $table) {
            //
            $table->dropColumn('slug');
        }); 
    }
}
