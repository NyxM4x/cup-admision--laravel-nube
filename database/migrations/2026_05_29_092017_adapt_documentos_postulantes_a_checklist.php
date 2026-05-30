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
        // El flujo nuevo es presencial: el admin solo tilda checkboxes,
        // ya no se suben archivos. 'archivo' deja de ser obligatorio.
        Schema::table('documentos_postulantes', function (Blueprint $table) {
            $table->string('archivo')->nullable()->change();
        });

        Schema::table('documentos_postulantes', function (Blueprint $table) {
            if (! Schema::hasColumn('documentos_postulantes', 'cumplido')) {
                $table->boolean('cumplido')->default(false)->after('requisito_id');
            }
            if (! Schema::hasColumn('documentos_postulantes', 'fecha_validacion')) {
                $table->timestamp('fecha_validacion')->nullable()->after('cumplido');
            }
            if (! Schema::hasColumn('documentos_postulantes', 'validado_por')) {
                $table->unsignedBigInteger('validado_por')->nullable()->after('fecha_validacion');
                $table->foreign('validado_por')->references('id')->on('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documentos_postulantes', function (Blueprint $table) {
            $table->dropForeign(['validado_por']);
            $table->dropColumn(['cumplido', 'fecha_validacion', 'validado_por']);
        });
    }
};
