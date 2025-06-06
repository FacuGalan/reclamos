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
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('observaciones')->nullable();
            $table->foreignId('tipo_movimiento_id')
                ->constrained('tipo_movimientos')
                ->onDelete('cascade');
            $table->foreignId('estado_id')
                ->constrained('estados')
                ->onDelete('cascade');
            $table->foreignId('usuario_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
