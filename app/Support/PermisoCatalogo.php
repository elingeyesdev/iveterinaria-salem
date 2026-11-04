<?php

namespace App\Support;

/**
 * Catálogo único de permisos asignables por el administrador.
 *
 * Cada permiso habilita un submódulo completo del sistema. Este catálogo es la
 * fuente de verdad: el seeder y el panel sincronizan la tabla "permisos" a
 * partir de aquí, y AppServiceProvider registra un Gate por cada clave.
 *
 * El módulo "Acceso y seguridad" (usuarios, roles y este panel) NO es
 * asignable: queda reservado al administrador para evitar que un usuario
 * pueda otorgarse privilegios a sí mismo (escalamiento de privilegios).
 */
final class PermisoCatalogo
{
    public const MODULO_PARAMETRIZACION = 'Parametrización';

    public const MODULO_TRANSACCIONAL = 'Transaccional';

    public const MODULO_REPORTES = 'Reportes';

    public const CLIENTES = 'clientes';

    public const VETERINARIOS = 'veterinarios';

    public const ESPECIES = 'especies';

    public const RAZAS = 'razas';

    public const MASCOTAS = 'mascotas';

    public const SUBSERVICIOS = 'subservicios';

    public const SERVICIOS = 'servicios';

    public const RESERVAS = 'reservas';

    public const CONSULTAS = 'consultas';

    public const PAGOS = 'pagos';

    public const RECORDATORIOS = 'recordatorios';

    public const REPORTE_KPI = 'reportes.kpi';

    public const REPORTE_INGRESOS = 'reportes.ingresos';

    public const REPORTE_SERVICIOS = 'reportes.servicios';

    public const REPORTE_VETERINARIOS = 'reportes.veterinarios';

    public const REPORTE_RESERVAS = 'reportes.reservas';

    /** Gate que habilita la entrada al módulo de reportes (al menos un reporte). */
    public const GATE_REPORTES = 'permiso.reportes';

    /**
     * @return array<string, array{nombre: string, modulo: string, descripcion: string, requiere: array<int, string>}>
     */
    public static function definiciones(): array
    {
        return [
            self::CLIENTES => [
                'nombre' => 'Administrar clientes',
                'modulo' => self::MODULO_PARAMETRIZACION,
                'descripcion' => 'Ver, registrar y editar clientes.',
                'requiere' => [],
            ],
            self::VETERINARIOS => [
                'nombre' => 'Administrar veterinarios',
                'modulo' => self::MODULO_PARAMETRIZACION,
                'descripcion' => 'Ver, registrar y editar veterinarios.',
                'requiere' => [],
            ],
            self::ESPECIES => [
                'nombre' => 'Administrar especies',
                'modulo' => self::MODULO_PARAMETRIZACION,
                'descripcion' => 'Ver, registrar y editar especies.',
                'requiere' => [],
            ],
            self::RAZAS => [
                'nombre' => 'Administrar razas',
                'modulo' => self::MODULO_PARAMETRIZACION,
                'descripcion' => 'Ver, registrar y editar razas.',
                'requiere' => [],
            ],
            self::MASCOTAS => [
                'nombre' => 'Administrar mascotas',
                'modulo' => self::MODULO_PARAMETRIZACION,
                'descripcion' => 'Ver, registrar y editar mascotas.',
                'requiere' => [],
            ],
            self::SUBSERVICIOS => [
                'nombre' => 'Administrar subservicios',
                'modulo' => self::MODULO_PARAMETRIZACION,
                'descripcion' => 'Ver, registrar y editar subservicios.',
                'requiere' => [],
            ],
            self::SERVICIOS => [
                'nombre' => 'Administrar servicios y precios',
                'modulo' => self::MODULO_PARAMETRIZACION,
                'descripcion' => 'Ver, registrar y editar servicios, incluido su precio.',
                'requiere' => [],
            ],
            self::RESERVAS => [
                'nombre' => 'Administrar reservas',
                'modulo' => self::MODULO_TRANSACCIONAL,
                'descripcion' => 'Ver el calendario, registrar, editar y cambiar el estado de reservas.',
                'requiere' => [],
            ],
            self::CONSULTAS => [
                'nombre' => 'Consultas y emergencias',
                'modulo' => self::MODULO_TRANSACCIONAL,
                'descripcion' => 'Registrar y editar consultas clínicas.',
                'requiere' => [],
            ],
            self::PAGOS => [
                'nombre' => 'Cobrar consultas',
                'modulo' => self::MODULO_TRANSACCIONAL,
                'descripcion' => 'Registrar pagos de una consulta. Anular pagos sigue siendo exclusivo del administrador.',
                'requiere' => [self::CONSULTAS],
            ],
            self::RECORDATORIOS => [
                'nombre' => 'Recordatorios',
                'modulo' => self::MODULO_TRANSACCIONAL,
                'descripcion' => 'Ver y gestionar recordatorios de vacunas y controles.',
                'requiere' => [],
            ],
            self::REPORTE_KPI => [
                'nombre' => 'Indicadores KPI',
                'modulo' => self::MODULO_REPORTES,
                'descripcion' => 'Indicadores operativos y financieros agregados.',
                'requiere' => [],
            ],
            self::REPORTE_INGRESOS => [
                'nombre' => 'Reporte de ingresos',
                'modulo' => self::MODULO_REPORTES,
                'descripcion' => 'Dinero cobrado por período y método de pago (información financiera).',
                'requiere' => [],
            ],
            self::REPORTE_SERVICIOS => [
                'nombre' => 'Reporte de servicios',
                'modulo' => self::MODULO_REPORTES,
                'descripcion' => 'Servicios más solicitados.',
                'requiere' => [],
            ],
            self::REPORTE_VETERINARIOS => [
                'nombre' => 'Reporte de veterinarios',
                'modulo' => self::MODULO_REPORTES,
                'descripcion' => 'Productividad por veterinario.',
                'requiere' => [],
            ],
            self::REPORTE_RESERVAS => [
                'nombre' => 'Reporte de reservas y asistencia',
                'modulo' => self::MODULO_REPORTES,
                'descripcion' => 'Reservas por estado, asistencia y horas de mayor demanda.',
                'requiere' => [],
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function claves(): array
    {
        return array_keys(self::definiciones());
    }

    /**
     * @return array<int, string>
     */
    public static function clavesDeReportes(): array
    {
        return array_keys(array_filter(
            self::definiciones(),
            fn (array $definicion): bool => $definicion['modulo'] === self::MODULO_REPORTES
        ));
    }

    /**
     * Nombre del Gate asociado a un permiso, por ejemplo "permiso.clientes".
     */
    public static function gate(string $clave): string
    {
        return 'permiso.'.$clave;
    }

    /**
     * Gates de todos los permisos de un módulo (para el menú lateral).
     *
     * @return array<int, string>
     */
    public static function gatesDelModulo(string $modulo): array
    {
        return array_values(array_map(
            fn (string $clave): string => self::gate($clave),
            array_keys(array_filter(
                self::definiciones(),
                fn (array $definicion): bool => $definicion['modulo'] === $modulo
            ))
        ));
    }
}
