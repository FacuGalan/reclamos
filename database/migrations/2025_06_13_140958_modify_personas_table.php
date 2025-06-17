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
        Schema::table('personas', function (Blueprint $table) {

                // Cambiar el campo telefono de integer a bigInteger
                $table->bigInteger('telefono')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personas', function (Blueprint $table) {

                // Revertir el campo telefono de bigInteger a integer
                $table->integer('telefono')->nullable()->change();

        });
    }
};