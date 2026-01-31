<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoPagoSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        DB::table('tipo_pagos')->insert([
            [
                'descripcion' => 'Efectivo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Transferencia Bancaria',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Cheque',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'descripcion' => 'Crédito',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
