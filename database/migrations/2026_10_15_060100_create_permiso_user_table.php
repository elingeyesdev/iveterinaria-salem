<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Asignación de permisos a usuarios (relación muchos a muchos).
 *
 * Solo se agrega esta tabla: no se modifica ninguna tabla existente.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permiso_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('permiso_id')->constrained('permisos')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'permiso_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permiso_user');
    }
};
