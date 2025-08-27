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
        Schema::table('categorias', function (Blueprint $table) {
            $table->string('nombre_publico')->nullable()->after('nombre');
        });

        // Actualizar registros existentes con el valor del campo 'nombre'
        DB::table('categorias')->whereNull('nombre_publico')->update([
            'nombre_publico' => DB::raw('nombre')
        ]);

        // Opcional: hacer el campo no nulo después de actualizar los datos
        // Schema::table('categorias', function (Blueprint $table) {
        //     $table->string('nombre_publico')->nullable(false)->change();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn('nombre_publico');
        });
    }
};