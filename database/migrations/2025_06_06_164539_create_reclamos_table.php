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
        Schema::create('reclamos', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->string('descripcion', 500);
            $table->string('direccion')->nullable();
            $table->string('entre_calles')->nullable();
            $table->string('coordenadas');
            $table->foreignId('area_id')
                ->constrained('areas')
                ->onDelete('cascade');
            $table->foreignId('estado_id')
                ->constrained('estados')
                ->onDelete('cascade');
            $table->foreignId('categoria_id')
                ->constrained('categorias')
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
        Schema::dropIfExists('reclamos');
    }
};
