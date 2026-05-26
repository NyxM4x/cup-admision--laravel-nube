<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cupo_carreras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrera_id')->constrained('carreras')->onDelete('cascade');
            $table->foreignId('periodo_id')->constrained('periodos')->onDelete('cascade');
            $table->unsignedInteger('cupo_max');
            $table->date('fecha_cofi')->nullable();
            $table->timestamps();

            // Una carrera solo puede tener un cupo por periodo
            $table->unique(['carrera_id', 'periodo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cupo_carreras');
    }
};