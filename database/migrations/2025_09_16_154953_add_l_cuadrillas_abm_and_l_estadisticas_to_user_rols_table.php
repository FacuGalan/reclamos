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
            $table->boolean('lCuadrillasAbm')->after('lPreguntasFrecAbm');
            $table->boolean('lEstadisticas')->after('lCuadrillasAbm');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_rols', function (Blueprint $table) {
            $table->boolean('lCuadrillasAbm')->after('lPreguntasFrecAbm');
            $table->boolean('lEstadisticas')->after('lCuadrillasAbm');  
        });
    }
};
