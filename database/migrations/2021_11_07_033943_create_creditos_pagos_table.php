<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditosPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creditos_payments', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('credito_id');
            // $table->foreign('credito_id')->references('id')->on('ventas');
            $table->integer('credito_id');
            $table->decimal('monto',11,2);
            $table->integer('created_by');
            // $table->unsignedBigInteger('created_by');
            // $table->foreign('created_by')->references('id')->on('users');
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
        Schema::dropIfExists('creditos_pagos');
    }
}
