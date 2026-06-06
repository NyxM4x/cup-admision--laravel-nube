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
        Schema::create('resultados_admision', function (Blueprint $table) {
            $table->id();
            $table->foreignId('postulante_id')->constrained('postulantes')->cascadeOnDelete();
            $table->foreignId('periodo_id')->constrained('periodos')->cascadeOnDelete();
            $table->decimal('promedio_final', 5, 2);
            $table->integer('posicion_ranking_general')->nullable();
            $table->foreignId('carrera_asignada_id')->nullable()
                  ->constrained('carreras')->nullOnDelete();
            $table->enum('estado_admision', [
                'aprobado',
                'reprobado',
                'admitido_primera',
                'admitido_segunda',
                'no_admitido_sin_cupo',
                'lista_espera',
            ]);
            $table->timestamp('fecha_asignacion')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->index('estado_admision');
            $table->unique(['postulante_id', 'periodo_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultados_admision');
    }
};
