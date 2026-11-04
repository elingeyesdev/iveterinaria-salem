<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * Class Categoria
 *
 * @property $id
 * @property $nombre
 * @property $created_at
 * @property $updated_at
 * @property Libro[] $libros
 * @mixin Builder
 * @property-read int|null $libros_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Categoria whereUpdatedAt($value)
 */
	class Categoria extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Cliente
 *
 * @property $id
 * @property $nombre
 * @property $apellido
 * @property $direccion
 * @property $correo
 * @property $telefono
 * @property $estado
 * @property $created_at
 * @property $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Mascotum> $mascotas
 * @property-read int|null $mascotas_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reserva> $reservas
 * @property-read int|null $reservas_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cliente newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cliente newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cliente query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cliente whereApellido($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cliente whereCorreo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cliente whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cliente whereDireccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cliente whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cliente whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cliente whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cliente whereTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cliente whereUpdatedAt($value)
 */
	class Cliente extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Libro
 *
 * @property $id
 * @property $nombre
 * @property $autor
 * @property $categoria_id
 * @property $created_at
 * @property $updated_at
 * @property Categoria $categoria
 * @mixin Builder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Libro newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Libro newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Libro query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Libro whereAutor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Libro whereCategoriaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Libro whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Libro whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Libro whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Libro whereUpdatedAt($value)
 */
	class Libro extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Mascotum
 *
 * @property $id
 * @property $cliente_id
 * @property $nombre
 * @property $especie
 * @property $fecha_nac
 * @property $peso
 * @property $sexo
 * @property $estado
 * @property $created_at
 * @property $updated_at
 * @property Cliente $cliente
 * @property CitaMascotum[] $citaMascotas
 * @property Consultum[] $consultas
 * @property Consultum[] $consultas
 * @property Consultum[] $consultas
 * @property Citum[] $citas
 * @property Citum[] $citas
 * @property CitaMascotum[] $citaMascotas
 * @property CitaMascotum[] $citaMascotas
 * @property CitaMascotum[] $citaMascotas
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @property string $raza
 * @property string|null $imagen
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mascotum newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mascotum newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mascotum query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mascotum whereClienteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mascotum whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mascotum whereEspecie($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mascotum whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mascotum whereFechaNac($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mascotum whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mascotum whereImagen($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mascotum whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mascotum wherePeso($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mascotum whereRaza($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mascotum whereSexo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Mascotum whereUpdatedAt($value)
 */
	class Mascotum extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $fecha
 * @property string $hora_inicio
 * @property string $hora_fin
 * @property string|null $motivo
 * @property int $cliente_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Cliente $cliente
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva whereClienteId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva whereFecha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva whereHoraFin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva whereHoraInicio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva whereMotivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reserva whereUpdatedAt($value)
 */
	class Reserva extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Role
 *
 * @property $id
 * @property $nombre
 * @property $estado
 * @property $created_at
 * @property $updated_at
 * @property RoleUser[] $roleUsers
 * @property ModelHasRole[] $modelHasRoles
 * @property RoleHasPermission[] $roleHasPermissions
 * @mixin Builder
 * @property-read int|null $role_users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * Class RoleUser
 *
 * @property $id
 * @property $user_id
 * @property $role_id
 * @property $created_at
 * @property $updated_at
 * @property Role $role
 * @property User $user
 * @mixin Builder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleUser whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|RoleUser whereUserId($value)
 */
	class RoleUser extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Servicio
 *
 * @property $id
 * @property $nombre
 * @property $descripcion
 * @property $precio
 * @property $estado
 * @property $created_at
 * @property $updated_at
 * @property CitaMascotaServicio[] $citaMascotaServicios
 * @property DetalleConsultaServicio[] $detalleConsultaServicios
 * @property Citaservicio[] $citaservicios
 * @property Citaservicio[] $citaservicios
 * @property CitaMascotaServicio[] $citaMascotaServicios
 * @property CitaMascotaServicio[] $citaMascotaServicios
 * @property CitaMascotaServicio[] $citaMascotaServicios
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Servicio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Servicio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Servicio query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Servicio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Servicio whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Servicio whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Servicio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Servicio whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Servicio wherePrecio($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Servicio whereUpdatedAt($value)
 */
	class Servicio extends \Eloquent {}
}

namespace App\Models{
/**
 * Class User
 *
 * @property $id
 * @property $name
 * @property $email
 * @property $email_verified_at
 * @property $password
 * @property $estado
 * @property $remember_token
 * @property $created_at
 * @property $updated_at
 * @property RoleUser[] $roleUsers
 * @mixin Builder
 * @property-read mixed $role
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Veterinario
 *
 * @mixin \Illuminate\Database\Eloquent\Builder
 * @property int $id
 * @property string $nombre
 * @property string $apellido
 * @property string $direccion
 * @property string $correo
 * @property string $telefono
 * @property int $estado
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Veterinario newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Veterinario newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Veterinario query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Veterinario whereApellido($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Veterinario whereCorreo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Veterinario whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Veterinario whereDireccion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Veterinario whereEstado($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Veterinario whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Veterinario whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Veterinario whereTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Veterinario whereUpdatedAt($value)
 */
	class Veterinario extends \Eloquent {}
}

