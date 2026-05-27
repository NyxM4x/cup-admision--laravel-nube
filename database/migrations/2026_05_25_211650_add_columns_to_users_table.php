<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('activo')->default(true)->after('password');
            $table->foreignId('rol_id')->nullable()->after('activo')->constrained('roles')->nullOnDelete();
            $table->string('ci', 20)->nullable()->unique()->after('rol_id');
            $table->string('telefono', 20)->nullable()->after('ci');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rol_id');
            $table->dropColumn(['activo', 'ci', 'telefono']);
        });
    }
};
