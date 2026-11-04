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
        Schema::table('recordatorio', function (Blueprint $table) {
            $table->foreignId('consulta_id')
                ->nullable()
                ->after('reserva_id')
                ->constrained('consulta')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('servicio_id')
                ->nullable()
                ->after('consulta_id')
                ->constrained('servicio')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->index(['consulta_id', 'servicio_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recordatorio', function (Blueprint $table) {
            $table->dropForeign(['consulta_id']);
            $table->dropForeign(['servicio_id']);
            $table->dropIndex(['consulta_id', 'servicio_id']);
            $table->dropColumn(['consulta_id', 'servicio_id']);
        });
    }
};
