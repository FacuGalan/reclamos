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
            $table->foreignId('cuadrilla_id')
                ->nullable()
                ->constrained('cuadrillas')
                ->onDelete('restrict')
                ->onUpdate('restrict')
                ->after('ver_privada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['cuadrilla_id']);
            $table->dropColumn('cuadrilla_id');
        });
    }
};
