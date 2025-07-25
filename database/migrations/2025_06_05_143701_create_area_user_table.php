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
        Schema::create('area_user', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('area_id')
                ->constrained('areas')
                ->onDelete('cascade')
                ->onUpdate('cascade');
                
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->timestamps();
            
            // Evitar duplicados
            $table->unique(['area_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('area_user');
    }
};