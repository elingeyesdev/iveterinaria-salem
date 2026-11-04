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
        Schema::create('consulta_servicio', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_actual');
            $table->date('fecha_prox')->nullable();
            $table->decimal('peso', 10, 2);
            $table->decimal('precio', 10, 2);

            $table->foreignId('servicio_id')->constrained('servicio')->onDelete('restrict')->onUpdate('cascade');
            $table->foreignId('consulta_id')->constrained('consulta')->onDelete('restrict')->onUpdate('cascade');

            $table->unique(['servicio_id', 'consulta_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consulta_servicio');
    }
};
