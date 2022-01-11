<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SpInsertVenta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "
        CREATE PROCEDURE insert_venta(
            in doc_sunat_p text,
            in cod_p text,
            in nrof_p text, 
            in estado_p text,
            in estado_pago_p int,
            in tipo_v_p text,
            in total_v_p decimal(11,2), 
            in total_ganancia_p decimal(11,2),
            in ruc_dni_v_p text,
            in nombre_cliente_p text,
            in sucursal_p text,
            in cod_sucursal_p text,
            in usuario_p text, 
            in xmayor_p int)
            begin
                if(tipo_v_p != \"ticked\") then
                    set @correlativo = (select v.correlativo from ventas v where v.doc_sunat = doc_sunat_p order by v.id desc limit 1); 
                    set @correlativo = (if(@correlativo is null, 1, @correlativo + 1));
                    set @serie = (select concat(cod_p,\"-\",LPAD(@correlativo,8,'0')));
                else 
                    set @correlativo = null;
                    set @serie = null;
                end if;
                insert into ventas(doc_sunat, correlativo, serie, nrof, estado,estado_pago, tipo_v,total_v, total_ganancia, ruc_dni_v, nombre_cliente, sucursal, cod_sucursal,
                usuario,anulado,fecha,fecha_t,xmayor,created_at,updated_at) values(doc_sunat_p,@correlativo,@serie,nrof_p,estado_p,estado_pago_p,tipo_v_p,total_v_p,total_ganancia_p,ruc_dni_v_p,
                nombre_cliente_p,sucursal_p, cod_sucursal_p,usuario_p,0,curdate(), now(), xmayor_p,now(),now());
                select @serie serie;
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
