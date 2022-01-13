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
        CREATE PROCEDURE listar_documentos(in sucursal_p text, in fecha_p date)
        begin
                            select dl.serie, dl.estado,dl.total ,dl.estado_pdf, dl.created_at, 1 tipo from documentos_electronicos dl where date(dl.created_at) = fecha_p
                            and if(sucursal_p != 'huaral', dl.sucursal = sucursal_p, true)
                            union 
                            select cs.serie , cs.estado , null total, null estado_pdf, cs.created_at, 2 tipo from credito_debito_sunat cs 
                            where date(cs.created_at) = fecha_p and if(sucursal_p != 'huaral', cs.sucursal = sucursal_p, true); 
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
