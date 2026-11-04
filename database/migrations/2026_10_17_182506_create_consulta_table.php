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
        Schema::create('consulta', function (Blueprint $table) {
            $table->id();
            $table->date('fecha'); // FECHA DEL SISTEMA
            $table->enum('tipo', ['Reserva', 'Emergencia'])->default('Reserva');
            $table->string('motivo')->nullable();
            $table->string('diagnostico')->nullable();
            $table->string('tratamiento')->nullable();
            $table->decimal('peso', 10, 2)->nullable();
            $table->enum('estado_clinico', ['En atención', 'Finalizada'])->default('En atención');

            // Agregar la columna finalizada_at para registrar la fecha y hora de finalización
            $table->timestamp('finalizada_at')->nullable();

            // $table->foreignId('mascota_id')->constrained('mascota')->cascadeOnDelete();
            $table->foreignId('reserva_id')->nullable()->constrained('reserva')->onDelete('restrict')->onUpdate('cascade');

            $table->foreignId('veterinario_id')->constrained('veterinario')->onDelete('restrict')->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consulta');
    }
};
