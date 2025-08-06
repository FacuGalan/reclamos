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
        Schema::table('domicilios', function (Blueprint $table) {
            // Eliminar la foreign key constraint
            $table->dropForeign(['persona_id']);
            
            // Modificar la columna para que sea nullable
            $table->unsignedBigInteger('persona_id')->nullable()->change();
            
            // Volver a crear la foreign key
            $table->foreign('persona_id')->references('id')->on('personas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('domicilios', function (Blueprint $table) {
            // Eliminar la foreign key
            $table->dropForeign(['persona_id']);
            
            // Revertir la columna para que no sea nullable
            $table->unsignedBigInteger('persona_id')->nullable(false)->change();
            
            // Recrear la foreign key
            $table->foreign('persona_id')->references('id')->on('personas');
        });
    }
};