<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            // Agregar aula_id si no existe
            if (!Schema::hasColumn('grupos', 'aula_id')) {
                $table->foreignId('aula_id')
                    ->nullable()
                    ->after('horario_id')
                    ->constrained('aulas')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('grupos', function (Blueprint $table) {
            if (Schema::hasColumn('grupos', 'aula_id')) {
                $table->dropForeign(['aula_id']);
                $table->dropColumn('aula_id');
            }
        });
    }
};