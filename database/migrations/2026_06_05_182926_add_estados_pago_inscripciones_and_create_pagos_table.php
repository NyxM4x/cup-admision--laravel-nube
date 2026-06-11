<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Modificar campo estado de inscripciones
        Schema::table('inscripciones', function (Blueprint $table) {
            $table->string('estado', 30)->default('activa')->change();
        });

        // Crear tabla pagos SOLO SI NO EXISTE
        if (!Schema::hasTable('pagos')) {
            Schema::create('pagos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inscripcion_id')->constrained('inscripciones')->onDelete('cascade');
                $table->decimal('monto', 8, 2)->default(50.00);
                $table->string('metodo', 50)->default('QR');
                $table->string('referencia_qr', 100)->nullable();
                $table->string('estado', 20)->default('pendiente');
                $table->text('observacion')->nullable();
                $table->foreignId('revisado_por')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('fecha_pago')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
        Schema::table('inscripciones', function (Blueprint $table) {
            $table->string('estado', 10)->default('activa')->change();
        });
    }
};