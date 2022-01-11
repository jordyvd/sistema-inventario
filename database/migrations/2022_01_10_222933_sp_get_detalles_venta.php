<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SpGetDetallesVenta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "
        CREATE PROCEDURE get_detalles_venta(in nrof_p int)
        begin
            select p.nompro producto,dv.precio,dv.cantidad ,dv.descuento,p.barra,p.marca,dv.sucursal from detalle_venta dv 
            join products p on dv.barra_detalles = p.barra where dv.nrof = nrof_p;
        END";
        DB::unprepared('DROP PROCEDURE IF EXISTS get_detalles_venta');
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
