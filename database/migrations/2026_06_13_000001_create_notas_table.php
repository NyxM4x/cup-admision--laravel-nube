<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CU21 — Registrar notas de exámenes
 * CU22 — Calcular nota final y resultado
 *
 * Un postulante tiene 3 exámenes por cada materia del CUP.
 * La nota final se calcula como promedio de los 3 exámenes.
 * El resultado es APROBADO (≥51) o REPROBADO (<51).
 *
 * Estructura:
 *   notas (grupo_materia_id, postulante_id, examen1, examen2, examen3,
 *           nota_final, resultado, registrado_por, observacion)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->id();

            // Bloque de materia dentro del grupo (liga con materia + docente + horario)
            $table->foreignId('grupo_materia_id')
                ->constrained('grupo_materias')
                ->onDelete('cascade');

            // Postulante evaluado
            $table->foreignId('postulante_id')
                ->constrained('postulantes')
                ->onDelete('cascade');

            // Las 3 notas de examen (0-100)
            $table->decimal('examen1', 5, 2)->nullable()->comment('Primer parcial');
            $table->decimal('examen2', 5, 2)->nullable()->comment('Segundo parcial');
            $table->decimal('examen3', 5, 2)->nullable()->comment('Examen final');

            // CU22: calculados automáticamente
            $table->decimal('nota_final', 5, 2)->nullable()->comment('Promedio de los 3 exámenes');
            $table->enum('resultado', ['aprobado', 'reprobado', 'pendiente'])
                ->default('pendiente');

            // Quién registró la nota (docente o admin)
            $table->foreignId('registrado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('observacion')->nullable();
            $table->timestamps();

            // Un postulante solo puede tener UNA nota por bloque de materia
            $table->unique(['grupo_materia_id', 'postulante_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};