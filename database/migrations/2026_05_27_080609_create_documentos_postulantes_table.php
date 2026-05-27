<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos_postulantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscripcion_id')->constrained('inscripciones')->onDelete('cascade');
            $table->foreignId('requisito_id')->constrained('requisitos')->onDelete('cascade');
            $table->string('archivo', 255);
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->text('comentario')->nullable();
            $table->timestamp('fecha_subida')->nullable();
            $table->timestamps();

            $table->unique(['inscripcion_id', 'requisito_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos_postulantes');
    }
};