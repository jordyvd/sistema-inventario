<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSerieAndCorrelativoAndDocSunatInVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->text('serie')->nullable()->after('id');
            $table->integer('correlativo')->nullable()->after('id');
            $table->text('doc_sunat')->nullable()->after('id');
            $table->text('anulado_cod')->nullable()->after('nrof');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ventas', function (Blueprint $table) {
            //
        });
    }
}
