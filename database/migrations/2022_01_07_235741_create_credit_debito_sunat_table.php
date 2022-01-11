<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditDebitoSunatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credito_debito_sunat', function (Blueprint $table) {
            $table->id();
            $table->integer('venta_id')->nullable();
        //   $table->foreign('venta_id')->references('id')->on('ventas');
            $table->text('serie');
            $table->integer('correlativo')->nullable();
            $table->text('tipo');
            $table->integer('estado')->nullable();
            $table->text('sucursal');
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
        Schema::dropIfExists('credit_debito_sunat');
    }
}
