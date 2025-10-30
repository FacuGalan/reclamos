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
        Schema::create('modelos_exportacion_reclamos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('area_id')->constrained('areas')->onDelete('cascade');
            $table->text('campos');
            $table->foreignId('usuario_creador_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['area_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modelos_exportacion_reclamos');
    }
};
