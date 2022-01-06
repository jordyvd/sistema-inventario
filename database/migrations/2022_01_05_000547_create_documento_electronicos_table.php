<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentoElectronicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos_electronicos', function (Blueprint $table) {
            $table->id();
            $table->text('serie');
            $table->text('tipo');
            $table->decimal('total', 11,2);
            $table->text('qr')->nullable();
            $table->integer('estado')->nullable();
            $table->integer('estado_pdf')->nullable();
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
        Schema::dropIfExists('documento_electronicos');
    }
}
