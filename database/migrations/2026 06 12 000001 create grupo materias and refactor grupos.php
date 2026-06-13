<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * MIGRACIÓN: Refactorizar grupos para modelo de TURNO COMPLETO
 *
 * ANTES: grupos tiene materia_id → un grupo = una materia
 * DESPUÉS: grupos representa un TURNO (Mañana/Tarde)
 *          grupo_materias tiene los 4 bloques (materia + docente + horario propio + aula)
 *
 * La tabla grupos CONSERVA horario_id como referencia al turno principal
 * y aula_id como aula por defecto (puede ser sobreescrita por materia en grupo_materias)
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Crear tabla grupo_materias
        Schema::create('grupo_materias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('grupo_id')
                ->constrained('grupos')
                ->onDelete('cascade');

            $table->foreignId('materia_id')
                ->constrained('materias')
                ->onDelete('cascade');

            // Docente asignado a esta materia dentro del grupo
            $table->foreignId('docente_id')
                ->nullable()
                ->constrained('docentes')
                ->nullOnDelete();

            // Horario específico del bloque (hora_inicio / hora_fin dentro del turno)
            // Ej: Matemáticas 07:00-08:30, Física 08:30-10:00, etc.
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();

            // Aula específica para esta materia (opcional; si null hereda la del grupo)
            $table->foreignId('aula_id')
                ->nullable()
                ->constrained('aulas')
                ->nullOnDelete();

            // Orden de dictado dentro del turno (1=primera materia, 2=segunda, etc.)
            $table->unsignedTinyInteger('orden')->default(1);

            $table->timestamps();

            // Un grupo no puede tener la misma materia dos veces
            $table->unique(['grupo_id', 'materia_id']);
        });

        // 2. Quitar materia_id y docente_id de grupos
        //    (docente_id se mueve a grupo_materias; materia_id ya no aplica)
        //    NOTA: Si hay datos, hacer migrate sin fresh los pierde — OK porque tabla está vacía
        Schema::table('grupos', function (Blueprint $table) {
            // Quitar FK materia_id
            $table->dropForeign(['materia_id']);
            $table->dropColumn('materia_id');

            // Quitar FK docente_id (se mueve a grupo_materias)
            $table->dropForeign(['docente_id']);
            $table->dropColumn('docente_id');
        });
    }

    public function down(): void
    {
        // Revertir: quitar grupo_materias y restaurar columnas en grupos
        Schema::dropIfExists('grupo_materias');

        Schema::table('grupos', function (Blueprint $table) {
            $table->foreignId('materia_id')
                ->nullable()
                ->after('periodo_id')
                ->constrained('materias')
                ->nullOnDelete();

            $table->foreignId('docente_id')
                ->nullable()
                ->after('aula_id')
                ->constrained('docentes')
                ->nullOnDelete();
        });
    }
};