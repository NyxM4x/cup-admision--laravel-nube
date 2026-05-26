<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('docentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')->constrained('personas')->onDelete('cascade');
            $table->foreignId('profesion_id')->nullable()->constrained('profesiones')->nullOnDelete();
            $table->integer('anios_experiencia')->default(0);
            $table->string('certif_docente', 255)->nullable();    // ruta archivo
            $table->string('certif_profesional', 255)->nullable(); // ruta archivo
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docentes');
    }
};