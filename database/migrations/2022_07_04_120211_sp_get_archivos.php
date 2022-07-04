<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SpGetArchivos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "
        CREATE PROCEDURE get_archivos(in id_p int, in folder int)
        begin
            select * from archivos a where a.foreign_id = id_p and 
            if(folder is null, a.padre is null, a.padre = folder)
            order by a.created_at asc;
        END";
        DB::unprepared('DROP PROCEDURE IF EXISTS get_archivos');
        DB::unprepared($procedure);
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
