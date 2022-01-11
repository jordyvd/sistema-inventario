<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SpGetVentaNotaCredito extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "
        CREATE PROCEDURE get_venta_nota_credito(in id_p int, in tipo text,in credito_t text)
        begin
            set @correlativo = (select cs.correlativo from credito_debito_sunat cs where cs.tipo = credito_t order by cs.id desc limit 1); 
            set @correlativo = (if(@correlativo is null, 1, @correlativo + 1));
            set @serie = (select concat(\"07\",\"-\",tipo,\"-\",LPAD(@correlativo,8,'0')));
            select *,@serie code,@correlativo correlativo from ventas v where v.id = id_p;
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
