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
        Schema::table('users', function (Blueprint $table) {
            // Agregar campo DNI único
            $table->string('dni', 11)->unique()->after('id');
            
            // Agregar campo teléfono
            $table->string('telefono', 10)->nullable()->after('email');
            
            // Quitar la restricción unique del email
            $table->dropUnique(['email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eliminar campos agregados
            $table->dropColumn(['dni', 'telefono']);
            
            // Restaurar unique del email
            $table->unique('email');
        });
    }
};