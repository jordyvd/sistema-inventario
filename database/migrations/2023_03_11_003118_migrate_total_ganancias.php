<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrateTotalGanancias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $ventas = DB::select("
            select v.id, v.cod_sucursal from ventas v 
            where date(v.fecha) between '2023-04-26' and '2023-07-18';
        ");

        foreach($ventas as $venta){
            $ganancia = DB::select("
                select sum(ganancia) ganancia from (
                select (dv.precio - dv.descuento) pv, p.precio, ((dv.precio - p.precio) * dv.cantidad) - dv.descuento ganancia, dv.nrof from detalle_venta dv
                join products p on dv.barra_detalles = p.barra 
                where dv.nrof = ?
                ) x group by nrof;", [$venta->cod_sucursal]
            );

            if(count($ganancia) > 0){
                $ganancia = $ganancia[0]->ganancia;
            }else{
                $ganancia = 0;
            }

            if($ganancia > 0){
                DB::statement("update ventas set total_ganancia = ? where id = ?", [$ganancia, $venta->id]);
            }
        }
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
