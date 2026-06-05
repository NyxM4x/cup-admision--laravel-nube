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
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();          // ej: M1, T2, N1
            $table->enum('turno', ['Mañana', 'Tarde', 'Noche']);
            $table->string('dias', 50);                       // ej: "Lunes,Miércoles,Viernes"
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('descripcion', 200)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
