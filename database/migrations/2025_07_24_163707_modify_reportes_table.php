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
        //
          Schema::table('reportes', function (Blueprint $table) {
            $table->foreignId('categoría_id')
                ->constrained('reporte_categorias')
                ->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('reportes', function (Blueprint $table) {
        $table->dropForeign(['categoría_id']);
        $table->dropColumn('categoría_id');
});
}
};
