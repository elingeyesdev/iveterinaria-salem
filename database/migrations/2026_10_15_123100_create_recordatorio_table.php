<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recordatorio', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mascota_id')
                ->constrained('mascota')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->enum('tipo', [
                'Vacunación',
                'Desparasitación',
                'Control veterinario',
                'Baño/Peluquería',
                'Limpieza dental',
                'Otro',
            ]);

            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->date('fecha');
            $table->enum('estado', [
                'Pendiente',
                'Completado',
                'Cancelado',
            ])->default('Pendiente');

            $table->timestamps();
            $table->index(['fecha', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recordatorio');
    }
};
