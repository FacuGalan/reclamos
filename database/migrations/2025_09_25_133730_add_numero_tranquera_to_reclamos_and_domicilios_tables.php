<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reclamos', function (Blueprint $table) {
            $table->string('numero_tranquera')->nullable()->after('direccion_rural');
        });

        Schema::table('domicilios', function (Blueprint $table) {
            $table->string('numero_tranquera')->nullable()->after('direccion_rural');
        });
    }

    public function down(): void
    {
        Schema::table('reclamos', function (Blueprint $table) {
            $table->dropColumn('numero_tranquera');
        });

        Schema::table('domicilios', function (Blueprint $table) {
            $table->dropColumn('numero_tranquera');
        });
    }
};