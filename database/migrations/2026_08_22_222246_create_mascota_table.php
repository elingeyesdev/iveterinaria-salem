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
        Schema::create('mascota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('cliente')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('especie_id')->constrained('especie')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('raza_id')->constrained('raza')->onDelete('restrict')->onUpdate('cascade');
            $table->string('nombre');
            $table->unique(['nombre', 'cliente_id']);
            $table->date('fecha_nac');
            $table->double('peso');
            $table->enum('sexo', ['Macho', 'Hembra', 'No especificado'])->default('Macho');
            $table->string('imagen')->nullable();
            // =====================================================================
            // A08:2021 — Fallos de Integridad de Software y Datos
            // Columna para almacenar el checksum SHA-256 del archivo de imagen
            // subido. Permite verificar posteriormente que la imagen no fue
            // alterada (ni en disco ni en una restauración de backup) y
            // detectar archivos poliglota o suplantaciones maliciosas.
            // =====================================================================
            $table->string('imagen_hash', 64)->nullable()
                ->comment('A08: Checksum SHA-256 del archivo de imagen para verificación de integridad');
            $table->tinyInteger('estado')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mascota');
    }
};
