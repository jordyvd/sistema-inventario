<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SpGetMarcasResult extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "
        CREATE PROCEDURE bd_sistema get_marcas_result(in search_p text)
        begin
            select p.*, m.nombre marca,pf.url from products p join marcas m on p.marcas_id = m.id 
            join products_files pf on p.id = pf.products_id
            where (search_p is null or match(p.filtros) against(search_p IN BOOLEAN MODE));
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
