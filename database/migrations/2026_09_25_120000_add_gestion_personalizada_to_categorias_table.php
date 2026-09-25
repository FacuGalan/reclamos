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
        Schema::table('categorias', function (Blueprint $table) {
            // Motivos que requieren presencialidad: no se ofrecen al ciudadano
            // en el alta pública, solo se cargan desde el panel logueado.
            $table->boolean('gestion_personalizada')->default(false)->after('urgente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn('gestion_personalizada');
        });
    }
};
