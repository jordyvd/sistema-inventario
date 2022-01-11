<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SpInsertarCredito extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "
        CREATE PROCEDURE insertar_credito(in venta_id_p int, in serie_p text, in correlativo_p int,
        in tipo_p text, in estado_p int, in sucursal_p text, in res_sunat_p text,in cod_sunat_p text)
        begin
            if(estado_p is null) then
              insert into credito_debito_sunat(venta_id, serie, correlativo, tipo, sucursal, created_at, updated_at) 
             values(venta_id_p,serie_p, correlativo_p, tipo_p, sucursal_p, now(), now());
              update ventas set anulado_cod = correlativo_p where id = venta_id_p;
             else 
              update credito_debito_sunat set estado = estado_p , 
              res_sunat = res_sunat_p, cod_sunat = cod_sunat_p
              where serie = serie_p;
            end if;
        END";
        DB::unprepared('DROP PROCEDURE IF EXISTS insertar_credito');
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
