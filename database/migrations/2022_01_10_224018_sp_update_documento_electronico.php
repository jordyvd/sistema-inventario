<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SpUpdateDocumentoElectronico extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "
        CREATE PROCEDURE update_documento_electronico(
            in serie_p text, in qr_p text, in estado_p int, in estado_pdf_p int)
            begin
                update documentos_electronicos set qr = qr_p, estado = estado_p, estado_pdf = estado_pdf_p, updated_at = now()
                where serie = serie_p;
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
