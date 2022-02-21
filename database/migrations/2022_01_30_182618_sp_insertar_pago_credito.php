<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SpInsertarPagoCredito extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "
        CREATE PROCEDURE insertar_pago_credito(in credito_id_p int, in monto_p decimal(11,2),
        in condicion_p int,
        in descripcion_p text, in caja_p int, in user_id_p int, in fecha datetime)
        begin
            insert into creditos_payments(credito_id, monto,condicion, descripcion, caja, created_by, created_at, updated_at)
            values(credito_id_p,monto_p,condicion_p,descripcion_p,caja_p,user_id_p,fecha, fecha);
            select @@identity id;
        END";
        DB::unprepared('DROP PROCEDURE IF EXISTS insertar_pago_credito');
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
