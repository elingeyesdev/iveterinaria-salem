<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Contraseña temporal de los usuarios iniciales. Debe cambiarse después
     * del primer ingreso.
     */
    public const CLAVE_INICIAL = 'Veterinaria2026';

    /**
     * Datos iniciales del sistema: roles, usuario administrador, usuario de
     * prueba y catálogo de permisos.
     *
     * Es idempotente: si se ejecuta otra vez no duplica registros ni cambia
     * la contraseña de usuarios que ya existen.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(
            ['nombre' => 'admin'],
            ['estado' => 1]
        );

        $usuarioRole = Role::firstOrCreate(
            ['nombre' => 'user'],
            ['estado' => 1]
        );

        Role::firstOrCreate(
            ['nombre' => 'veterinario'],
            ['estado' => 1]
        );

        $admin = $this->usuarioInicial('admin', 'susej.co2016@gmail.com');
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        $usuario = $this->usuarioInicial('user', 'user@example.com');
        $usuario->roles()->syncWithoutDetaching([$usuarioRole->id]);

        $this->call(PermisoSeeder::class);
    }

    private function usuarioInicial(string $nombre, string $email): User
    {
        return User::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => $nombre,
                'password' => Hash::make(self::CLAVE_INICIAL),
                'estado' => 1,
            ]
        );
    }
}
