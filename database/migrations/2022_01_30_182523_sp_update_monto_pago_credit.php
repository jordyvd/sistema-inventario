<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SpUpdateMontoPagoCredit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $procedure = "
        CREATE PROCEDURE update_monto_pago_credit(in monto_p decimal(11, 2), in user_id_p int, in id_p int)
        begin
            update creditos_payments set monto = monto_p, edit_user = user_id_p where id = id_p;
            update ingresos_salidas set monto = monto_p where credit_id = id_p;
        END";
        DB::unprepared('DROP PROCEDURE IF EXISTS update_monto_pago_credit');
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
