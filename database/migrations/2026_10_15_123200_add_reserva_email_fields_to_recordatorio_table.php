<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recordatorio', function (Blueprint $table) {
            $table->foreignId('reserva_id')
                ->nullable()
                ->after('mascota_id')
                ->constrained('reserva')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->dateTime('fecha_envio')->nullable()->after('fecha');
            $table->dateTime('enviado_at')->nullable()->after('fecha_envio');
            $table->string('canal', 30)->default('correo')->after('enviado_at');
            $table->text('error_envio')->nullable()->after('canal');

            $table->unique('reserva_id');
            $table->index(['fecha_envio', 'enviado_at', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::table('recordatorio', function (Blueprint $table) {
            $table->dropForeign(['reserva_id']);
            $table->dropUnique(['reserva_id']);
            $table->dropIndex(['fecha_envio', 'enviado_at', 'estado']);
            $table->dropColumn(['reserva_id', 'fecha_envio', 'enviado_at', 'canal', 'error_envio']);
        });
    }
};
