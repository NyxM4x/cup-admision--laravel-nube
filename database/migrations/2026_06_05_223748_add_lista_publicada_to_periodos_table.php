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
        Schema::table('periodos', function (Blueprint $table) {
            if (! Schema::hasColumn('periodos', 'lista_publicada')) {
                $table->boolean('lista_publicada')->default(false)->after('activo');
                $table->timestamp('fecha_publicacion')->nullable()->after('lista_publicada');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('periodos', function (Blueprint $table) {
            if (Schema::hasColumn('periodos', 'lista_publicada')) {
                $table->dropColumn(['lista_publicada', 'fecha_publicacion']);
            }
        });
    }
};
