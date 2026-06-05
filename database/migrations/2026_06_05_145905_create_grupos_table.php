<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('grupos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20);                     // ej: G-1-MAT-1
            $table->foreignId('periodo_id')->constrained('periodos')->cascadeOnDelete();
            $table->foreignId('materia_id')->constrained('materias')->cascadeOnDelete();
            $table->foreignId('horario_id')->nullable()->constrained('horarios')->nullOnDelete();
            $table->foreignId('aula_id')->nullable()->constrained('aulas')->nullOnDelete();
            $table->foreignId('docente_id')->nullable()->constrained('docentes')->nullOnDelete();
            $table->integer('cupo_max')->default(80);
            $table->integer('inscritos_actuales')->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->unique(['periodo_id', 'materia_id', 'codigo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
