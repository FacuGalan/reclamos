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
        Schema::table('domicilios', function (Blueprint $table) {
            $table->string('direccion_rural')->nullable()->after('entre_calles');
        });

        Schema::table('reclamos', function (Blueprint $table) {
            $table->string('direccion_rural')->nullable()->after('entre_calles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('domicilios', function (Blueprint $table) {
            $table->dropColumn('direccion_rural');
        });

        Schema::table('reclamos', function (Blueprint $table) {
            $table->dropColumn('direccion_rural');
        });
    }
};
