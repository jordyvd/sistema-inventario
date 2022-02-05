<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SpGetCajaTransfarencias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "
        CREATE PROCEDURE get_caja_transfarencias( in sucursal_p text, in fecha_p date)
        begin
            select sum(v.total_v) total_ventas from ventas v where v.fecha = fecha_p and v.sucursal = sucursal_p and v.estado NOT IN('1','0','credito','cambio','cancelado')
            union
            select sum(il.monto) total_ventas from ingresos_salidas il 
            join creditos_payments cp on il.credit_id = cp.id
            where il.fecha = fecha_p and il.sucursal = sucursal_p
           and cp.condicion = 2;
        END";
        DB::unprepared('DROP PROCEDURE IF EXISTS get_caja_transfarencias');
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
