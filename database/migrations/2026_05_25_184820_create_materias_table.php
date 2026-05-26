<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materias', function (Blueprint $table) {
            $table->id();
            $table->string('sigla', 20)->unique();
            $table->string('nombre', 100);
            $table->string('dias', 20); // 'LMV' o 'MJ'
            $table->unsignedTinyInteger('cant_examenes')->default(3);
            $table->decimal('peso_examen1', 5, 2)->default(30.00);
            $table->decimal('peso_examen2', 5, 2)->default(30.00);
            $table->decimal('peso_examen3', 5, 2)->default(40.00);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materias');
    }
};