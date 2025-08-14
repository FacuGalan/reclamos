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
        // Crear tabla edificios
        Schema::create('edificios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('direccion')->nullable(); // Puede estar vacía
            $table->unsignedBigInteger('area_id')->nullable(); // Puede estar vacía
            $table->timestamps();
            
            // Índice para area_id (asumiendo que existe una tabla areas)
            $table->index('area_id');
            
            // Clave foránea para area_id (descomenta si tienes tabla areas)
            // $table->foreign('area_id')->references('id')->on('areas')->onDelete('set null');
        });

        // Modificar tabla reclamos para agregar edificio_id
        Schema::table('reclamos', function (Blueprint $table) {
            $table->unsignedBigInteger('edificio_id')->nullable()->after('id'); // Puede estar vacío
            
            // Índice para mejor rendimiento
            $table->index('edificio_id');
            
            // Clave foránea que apunta a edificios
            $table->foreign('edificio_id')->references('id')->on('edificios')->onDelete('set null');
        });
    }

    /**
     * Revertir las migraciones.
     */
    public function down(): void
    {
        // Eliminar la columna edificio_id de reclamos
        Schema::table('reclamos', function (Blueprint $table) {
            $table->dropForeign(['edificio_id']);
            $table->dropIndex(['edificio_id']);
            $table->dropColumn('edificio_id');
        });

        // Eliminar tabla edificios
        Schema::dropIfExists('edificios');
    }
};
