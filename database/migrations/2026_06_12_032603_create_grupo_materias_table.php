<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Crear tabla grupo_materias (bloques de materia dentro del grupo-turno)
        Schema::create('grupo_materias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grupo_id')->constrained('grupos')->onDelete('cascade');
            $table->foreignId('materia_id')->constrained('materias');
            $table->foreignId('docente_id')->nullable()->constrained('docentes')->nullOnDelete();
            $table->foreignId('aula_id')->nullable()->constrained('aulas')->nullOnDelete();
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('dia_semana', 20); // Lunes, Martes, etc.
            $table->timestamps();

            $table->unique(['grupo_id', 'materia_id']);
        });

        // Eliminar columnas viejas de la tabla grupos
        Schema::table('grupos', function (Blueprint $table) {
            if (Schema::hasColumn('grupos', 'materia_id')) {
                $table->dropForeign(['materia_id']);
                $table->dropColumn('materia_id');
            }
            if (Schema::hasColumn('grupos', 'docente_id')) {
                $table->dropForeign(['docente_id']);
                $table->dropColumn('docente_id');
            }
            if (Schema::hasColumn('grupos', 'aula_id')) {
                $table->dropForeign(['aula_id']);
                $table->dropColumn('aula_id');
            }
            if (Schema::hasColumn('grupos', 'hora_inicio')) {
                $table->dropColumn('hora_inicio');
            }
            if (Schema::hasColumn('grupos', 'hora_fin')) {
                $table->dropColumn('hora_fin');
            }
            if (Schema::hasColumn('grupos', 'dia_semana')) {
                $table->dropColumn('dia_semana');
            }
        });
    }

    public function down(): void
    {
        // Revertir: eliminar tabla grupo_materias
        Schema::dropIfExists('grupo_materias');

        // Restaurar columnas viejas en grupos (opcional, para rollback)
        Schema::table('grupos', function (Blueprint $table) {
            $table->foreignId('materia_id')->nullable()->constrained('materias');
            $table->foreignId('docente_id')->nullable()->constrained('docentes');
            $table->foreignId('aula_id')->nullable()->constrained('aulas');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->string('dia_semana', 20)->nullable();
        });
    }
};