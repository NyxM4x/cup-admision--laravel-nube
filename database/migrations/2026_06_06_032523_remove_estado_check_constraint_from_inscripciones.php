<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar el CHECK constraint de PostgreSQL que limita los estados
        DB::statement('ALTER TABLE inscripciones DROP CONSTRAINT IF EXISTS inscripciones_estado_check');

        // Asegurarnos que el campo sea string libre sin restricciones
        DB::statement("ALTER TABLE inscripciones ALTER COLUMN estado TYPE VARCHAR(30)");
        DB::statement("ALTER TABLE inscripciones ALTER COLUMN estado SET DEFAULT 'activa'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE inscripciones DROP CONSTRAINT IF EXISTS inscripciones_estado_check");
        DB::statement("ALTER TABLE inscripciones ADD CONSTRAINT inscripciones_estado_check 
            CHECK (estado IN ('activa', 'anulada'))");
    }
};