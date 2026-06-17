<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profesiones', function (Blueprint $table) {
            $table->string('materia_sigla', 10)->nullable()->after('nivel_jerarquico');
        });
    }

    public function down(): void
    {
        Schema::table('profesiones', function (Blueprint $table) {
            $table->dropColumn('materia_sigla');
        });
    }
};
