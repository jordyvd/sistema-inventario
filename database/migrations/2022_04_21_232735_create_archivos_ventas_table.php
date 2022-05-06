<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchivosVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('archivos_ventas');
        Schema::create('archivos_ventas', function (Blueprint $table) {
            $table->id();
            $table->integer('ventas_id')->nullable();
            $table->text('url')->nullable();
            $table->text('nombre')->nullable();
            $table->text('extension')->nullable();
            $table->text('tipo');
            $table->text('padre')->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('archivos_ventas');
    }
}
