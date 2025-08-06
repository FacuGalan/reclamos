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
        Schema::table('user_rols', function (Blueprint $table) {
            // Permisos para Reclamos
            $table->boolean('lReclamosAbm')->default(false);
            $table->boolean('lReclamosAlta')->default(false);
            $table->boolean('lReclamosModifica')->default(false);
            $table->boolean('lReclamosBaja')->default(false);
            $table->boolean('lReclamosDeriva')->default(false);
            $table->boolean('lReclamosFinaliza')->default(false);
            
            // Permisos para módulos administrativos
            $table->boolean('lReportesAbm')->default(false);
            $table->boolean('lSecretariaAbm')->default(false);
            $table->boolean('lAreasAbm')->default(false);
            $table->boolean('lMotivosAbm')->default(false);
            $table->boolean('lTiposMovAbm')->default(false);
            $table->boolean('lEstadosAbm')->default(false);
            $table->boolean('lUsuariosAbm')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_rols', function (Blueprint $table) {
            $table->dropColumn([
                'lReclamosAbm',
                'lReclamosAlta',
                'lReclamosModifica',
                'lReclamosBaja',
                'lReclamosDeriva',
                'lReclamosFinaliza',
                'lReportesAbm',
                'lSecretariaAbm',
                'lAreasAbm',
                'lMotivosAbm',
                'lTiposMovAbm',
                'lEstadosAbm',
                'lUsuariosAbm'
            ]);
        });
    }
};