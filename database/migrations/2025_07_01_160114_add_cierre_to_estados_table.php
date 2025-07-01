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
            $table->tinyInteger('de_cierre')->default(false)->after('color_texto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estados', function (Blueprint $table) {
            $table->dropColumn('de_cierre');
        });
    }
};
