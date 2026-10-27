<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agenda de contactos (electricistas, plomeros, etc.) que se pueden asignar
     * a los reclamos internos como responsables, a modo informativo.
     */
    public function up(): void
    {
        Schema::create('contactos_agenda', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('telefono')->nullable();
            $table->string('ocupacion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Sin foreign key a propósito: agregarla obliga a MySQL a revalidar las FKs
        // existentes de reclamos y falla si hay datos viejos inconsistentes
        // (p. ej. domicilio_id huérfanos). Los contactos se borran con soft delete.
        Schema::table('reclamos', function (Blueprint $table) {
            $table->unsignedBigInteger('contacto_agenda_id')->nullable()->after('responsable_id');
            $table->index('contacto_agenda_id');
        });

        // Contactos iniciales (teléfono y ocupación se completan luego desde el ABM)
        $ahora = now();
        DB::table('contactos_agenda')->insert(collect([
            '(Sario) Torres, Diego',
            'Bernárdez, Daniel',
            'Defelippe, Julio',
            'Cortessi, Juan Pablo',
            'Tarulla, Carlos Gabriel',
            'Otros',
        ])->map(fn ($nombre) => [
            'nombre' => $nombre,
            'created_at' => $ahora,
            'updated_at' => $ahora,
        ])->all());
    }

    public function down(): void
    {
        Schema::table('reclamos', function (Blueprint $table) {
            $table->dropIndex(['contacto_agenda_id']);
            $table->dropColumn('contacto_agenda_id');
        });

        Schema::dropIfExists('contactos_agenda');
    }
};
