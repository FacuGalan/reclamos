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
        Schema::table('estados', function (Blueprint $table) {
            $table->string('codigo_color', 20)->default('#6B7280')->after('nombre');
            $table->string('color_texto', 20)->nullable()->after('codigo_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estados', function (Blueprint $table) {
            $table->dropColumn('codigo_color');
            $table->dropColumn('color_texto');
        });
    }
};