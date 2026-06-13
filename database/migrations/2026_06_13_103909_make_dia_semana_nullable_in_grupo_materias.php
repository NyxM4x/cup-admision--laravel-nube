<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Hacer dia_semana nullable
        DB::statement('ALTER TABLE grupo_materias ALTER COLUMN dia_semana DROP NOT NULL');

        // 2) Rellenar los existentes con un valor por defecto (opcional, por seguridad)
        DB::statement("UPDATE grupo_materias SET dia_semana = 'LUN' WHERE dia_semana IS NULL");
    }

    public function down(): void
    {
        DB::statement("UPDATE grupo_materias SET dia_semana = 'LUN' WHERE dia_semana IS NULL");
        DB::statement('ALTER TABLE grupo_materias ALTER COLUMN dia_semana SET NOT NULL');
    }
};