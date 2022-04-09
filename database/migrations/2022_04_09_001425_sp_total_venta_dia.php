<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SpTotalVentaDia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "
        CREATE PROCEDURE total_venta_dia(in fecha date, in sucursal_p text)
        begin
            select sum(v.total_v) total_venta from ventas v where if(fecha is null, v.fecha = curdate(), v.fecha = fecha) 
            and v.sucursal = sucursal_p and v.estado = 1
            group by v.fecha;
        end";
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
