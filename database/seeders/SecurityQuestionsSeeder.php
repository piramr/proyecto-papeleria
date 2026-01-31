<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SecurityQuestionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            '¿Cuál es el nombre de tu primera mascota?',
            '¿Cuál es el apellido de soltera de tu madre?',
            '¿En qué ciudad naciste?',
            '¿Cuál fue tu primer auto?',
            '¿Cuál es tu comida favorita?',
            '¿Cómo se llamaba tu escuela primaria?',
        ];

        foreach ($questions as $q) {
            DB::table('security_questions')->insertOrIgnore(['question' => $q, 'created_at' => now(), 'updated_at' => now()]);
        }
    }
}
