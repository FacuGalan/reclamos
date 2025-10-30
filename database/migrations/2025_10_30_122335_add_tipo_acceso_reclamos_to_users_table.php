<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('tipo_acceso_reclamos')->default(1)->after('ver_privada')
                ->comment('1=Públicos, 2=Privados, 3=Ambos');
        });

        // Migrar datos existentes
        DB::statement("UPDATE users SET tipo_acceso_reclamos = CASE WHEN ver_privada = 1 THEN 2 ELSE 1 END");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tipo_acceso_reclamos');
        });
    }
};
