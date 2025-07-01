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
        Schema::table('reclamos', function (Blueprint $table) {
            $table->tinyInteger('notificado')->default(false)->after('responsable_id');
            $table->tinyInteger('no_aplica')->default(false)->after('notificado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reclamos', function (Blueprint $table) {
             $table->dropColumn('notificado');
             $table->dropColumn('no_aplica');
        });
    }
};
