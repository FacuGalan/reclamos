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
        // Modificar tabla reclamos
        Schema::table('reclamos', function (Blueprint $table) {
            $table->foreignId('barrio_id')
                ->nullable()
                ->constrained('barrios')
                ->onDelete('set null');
        });

        // Modificar tabla domicilios
        Schema::table('domicilios', function (Blueprint $table) {
            $table->foreignId('barrio_id')
                ->nullable()
                ->constrained('barrios')
                ->onDelete('set null');
        });
    }

    // Método DOWN:
    public function down(): void
    {
        // Eliminar foreign key de tabla reclamos
        Schema::table('reclamos', function (Blueprint $table) {
            $table->dropForeign(['barrio_id']);
            $table->dropColumn('barrio_id');
        });

        // Eliminar foreign key de tabla domicilios
        Schema::table('domicilios', function (Blueprint $table) {
            $table->dropForeign(['barrio_id']);
            $table->dropColumn('barrio_id');
        });
    }
};
