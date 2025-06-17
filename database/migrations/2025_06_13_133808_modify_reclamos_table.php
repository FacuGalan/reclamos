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
        Schema::table('reclamos', function (Blueprint $table) {
            // Agregar campo persona_id que referencia a la tabla personas
            $table->foreignId('persona_id')
                ->nullable() // Opcional: permite valores nulos si es necesario
                ->after('responsable_id') // Ubicar después del campo responsable_id
                ->constrained('personas')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Agregar campo domicilio_id que referencia a la tabla domicilios
            $table->foreignId('domicilio_id')
                ->nullable() // Opcional: permite valores nulos si es necesario
                ->after('persona_id') // Ubicar después del campo persona_id
                ->constrained('domicilios')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reclamos', function (Blueprint $table) {
            // Primero eliminar las foreign keys
            $table->dropForeign(['persona_id']);
            $table->dropForeign(['domicilio_id']);
            
            // Luego eliminar las columnas
            $table->dropColumn(['persona_id', 'domicilio_id']);
        });
    }
};