<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SpGetFilesVentas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "
        CREATE PROCEDURE `get_files_ventas`(in venta_id int, in folder int)
        begin
            select * from archivos_ventas v where v.ventas_id = venta_id and 
            if(folder is null, v.padre is null, v.padre = folder)
            order by v.created_at asc;
        END";
        DB::unprepared('DROP PROCEDURE IF EXISTS total_venta_dia');
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
