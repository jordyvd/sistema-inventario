<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SpGetMontoCajaEfectivo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "
        CREATE PROCEDURE get_monto_caja_efectivo(in sucursal_p text, in fecha_p date)
        begin
             select il.monto , il.condicion, cp.condicion condicion_cp from ingresos_salidas il 
                left join creditos_payments cp on il.credit_id = cp.id
                where il.fecha = fecha_p and il.sucursal = sucursal_p;
        END";
        DB::unprepared('DROP PROCEDURE IF EXISTS get_monto_caja_efectivo');
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
