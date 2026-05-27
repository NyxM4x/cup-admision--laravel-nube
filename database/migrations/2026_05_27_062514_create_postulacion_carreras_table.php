<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postulacion_carreras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_id')->constrained('inscripciones')->onDelete('cascade');
            $table->foreignId('carrera_id')->constrained('carreras')->onDelete('cascade');
            $table->unsignedTinyInteger('prioridad'); // 1 = primera opción, 2 = segunda opción
            $table->timestamps();

            // Un postulante no puede elegir la misma carrera dos veces en la misma inscripción
            $table->unique(['inscripcion_id', 'carrera_id']);
            // Tampoco puede tener dos veces la misma prioridad
            $table->unique(['inscripcion_id', 'prioridad']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postulacion_carreras');
    }
};