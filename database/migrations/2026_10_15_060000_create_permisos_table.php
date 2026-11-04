<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Catálogo de permisos (un registro por submódulo asignable).
 *
 * Los registros se sincronizan desde App\Support\PermisoCatalogo mediante
 * PermisoSeeder; el administrador no los crea ni los elimina, solo los asigna.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permisos', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 80)->unique();
            $table->string('nombre', 100);
            $table->string('modulo', 50);
            $table->string('descripcion')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permisos');
    }
};
