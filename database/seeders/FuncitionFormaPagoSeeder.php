<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FuncitionFormaPagoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared('
        DROP FUNCTION IF EXISTS obtenerFormaPagoPorCaja;
    ');
        DB::unprepared('
                   CREATE DEFINER=`root`@`localhost` FUNCTION `obtenerFormaPagoPorCaja`(`caja_id` INT)
            RETURNS varchar(255) CHARSET utf8mb4 COLLATE utf8mb4_general_ci
            READS SQL DATA
            BEGIN
                DECLARE forma_pago VARCHAR(255);

                SELECT CONCAT_WS(\',\',
                    CASE WHEN cash > 0 THEN \'Efectivo\' END,
                    CASE WHEN card > 0 THEN \'Tarjeta\' END,
                    CASE WHEN yape > 0 THEN \'Yape\' END,
                    CASE WHEN deposit > 0 THEN \'Depósito\' END,
                    CASE WHEN plin > 0 THEN \'Plin\' END
                ) INTO forma_pago
                FROM moviments
                WHERE id = caja_id;

                RETURN forma_pago;
            END;
    ');
    }
}
