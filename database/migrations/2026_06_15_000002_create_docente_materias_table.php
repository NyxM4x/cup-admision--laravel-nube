<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('docente_materias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('docente_id')->constrained('docentes')->cascadeOnDelete();
            $table->string('materia_sigla', 10);
            $table->timestamps();

            $table->unique(['docente_id', 'materia_sigla']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docente_materias');
    }
};
