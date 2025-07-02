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
        // Modificar tabla tipo_movimientos
        Schema::table('tipo_movimientos', function (Blueprint $table) {
            // Quitar la restricción unique del campo nombre
            $table->dropUnique(['nombre']);
            
            // Permitir que area_id sea nullable manteniendo la foreign key
            $table->foreignId('area_id')->nullable()->change();
        });

        // Modificar tabla reclamos
        Schema::table('reclamos', function (Blueprint $table) {
            // Permitir que responsable_id sea nullable manteniendo la foreign key
            $table->foreignId('responsable_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir cambios en tabla tipo_movimientos
        Schema::table('tipo_movimientos', function (Blueprint $table) {
            // Restaurar la restricción unique del campo nombre
            $table->unique('nombre');
            
            // Quitar el nullable del campo area_id
            $table->foreignId('area_id')->nullable(false)->change();
        });

        // Revertir cambios en tabla reclamos
        Schema::table('reclamos', function (Blueprint $table) {
            // Quitar el nullable del campo responsable_id
            $table->foreignId('responsable_id')->nullable(false)->change();
        });
    }
};