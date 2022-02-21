<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SpListarDocumentos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "
        CREATE PROCEDURE listar_documentos(in sucursal_p text, in fecha_p date, in fecha_end date)
        begin
            select dl.serie, dl.estado,dl.total ,dl.estado_pdf, dl.created_at, CAST(dl.tipo AS int) tipo,'--' afectado,
            v.nombre_cliente , v.ruc_dni_v 
            from documentos_electronicos dl 
            join ventas v on dl.serie = v.serie
            where date(dl.created_at) between fecha_p and fecha_end
            and if(sucursal_p != 'huaral' and sucursal_p != 'ALMACEN HUARAL', dl.sucursal = sucursal_p, true)
            union 
            select cs.serie , cs.estado ,dl.total total, null estado_pdf, cs.created_at, 7 tipo, v.serie afectado,
            v.nombre_cliente , v.ruc_dni_v 
            from credito_debito_sunat cs 
            join ventas v on cs.venta_id = v.id
            join documentos_electronicos dl on v.serie = dl.serie 
            where date(cs.created_at) between fecha_p and fecha_end and if(sucursal_p != 'huaral' and sucursal_p != 'ALMACEN HUARAL', cs.sucursal = sucursal_p, true); 
        END";
        DB::unprepared('DROP PROCEDURE IF EXISTS listar_documentos');
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
