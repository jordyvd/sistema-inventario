<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVentasXmayor extends Migration
{
    public function up()
    {
        Schema::table('ventas', function ($table) {
            // $table->integer('xmayor')->after('anulado');
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
    }
}
