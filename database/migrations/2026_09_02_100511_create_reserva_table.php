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
        Schema::create('reserva', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->unique(['fecha', 'hora_inicio']); // Evita duplicados en el mismo día

            $table->foreignId('mascota_id')->constrained('mascota')->onDelete('restrict')->onUpdate('cascade');

            $table->enum('estado', ['Pendiente', 'Atendida', 'Cancelado', 'No asistio'])->default('Pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reserva');
    }
};
