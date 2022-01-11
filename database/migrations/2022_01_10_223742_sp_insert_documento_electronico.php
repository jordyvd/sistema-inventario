<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SpInsertDocumentoElectronico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "
        CREATE PROCEDURE insert_documento_electronico(
            in sucursal_p text, in serie_p text, in tipo_p text, in total_p decimal(11,2), 
            in qr_p text,in estado_p int,in estado_pdf_p int)
            begin
                insert into documentos_electronicos(sucursal,serie, tipo, total, qr, estado, estado_pdf, created_at) 
                values(sucursal_p,serie_p, tipo_p,total_p, qr_p,estado_p, estado_pdf_p, now());
        END";
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
