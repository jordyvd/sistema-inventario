<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SpGetMovimientos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "
        CREATE PROCEDURE get_movimientos(in desde_p date, in hasta_p date, in tipo_p text, in sucursal_p text, in npage int, in perpage int)
        begin
            
            set @count = (select count(*) from movimientos m
            where m.sucursal = sucursal_p and m.tipo = tipo_p and m.fecha between desde_p and hasta_p);
        
            set npage = perpage*(npage-1);
            if(tipo_p = 'venta') then
                select dv.descuento, p.nompro , p.marca, m.precio , m.cantidad , m.fecha, m.condicion , m.detalle,m.sucursal,m.nro_documento, @count count
                from movimientos m 
                join detalle_venta dv on m.barra_mov = dv.barra_detalles and m.nro_documento = dv.nrof 
                join products p on dv.barra_detalles = p.barra
                where m.sucursal = sucursal_p and m.tipo = tipo_p and m.fecha between desde_p and hasta_p limit perpage offset npage;
            else 
                select null descuento, p.nompro , p.marca, m.precio , m.cantidad , m.fecha, m.condicion , m.detalle,m.sucursal,m.nro_documento, @count count
                from movimientos m 
                join products p on m.barra_mov = p.barra
                where m.sucursal = sucursal_p and m.tipo = tipo_p and m.fecha between desde_p and hasta_p limit perpage offset npage;
            end if;
            
        END";
        DB::unprepared('DROP PROCEDURE IF EXISTS get_movimientos');
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
