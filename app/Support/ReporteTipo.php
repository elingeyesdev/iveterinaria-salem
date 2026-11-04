<?php

namespace App\Support;

/**
 * Catálogo único de reportes del sistema.
 *
 * Agregar un reporte nuevo = una entrada aquí + un método en ReporteService.
 * Rutas, menú, permisos e índice se alimentan de este catálogo.
 */
final class ReporteTipo
{
    public const INGRESOS = 'ingresos';

    public const SERVICIOS = 'servicios';

    public const VETERINARIOS = 'veterinarios';

    public const RESERVAS = 'reservas';

    public const POR_COBRAR = 'cuentas-por-cobrar';

    public const CLIENTES_MASCOTAS = 'clientes-mascotas';

    /**
     * Gate de "Cuentas por cobrar": no está en el catálogo de permisos
     * asignables, por lo que solo el administrador puede verlo.
     */
    public const PERMISO_POR_COBRAR = 'permiso.reportes.cuentas-por-cobrar';

    /**
     * @return array<string, array{titulo:string, descripcion:string, icono:string, permiso:string, filtros:array<int,string>}>
     */
    public static function catalogo(): array
    {
        return [
            self::INGRESOS => [
                'titulo' => 'Ingresos',
                'descripcion' => 'Cobros vigentes en efectivo, anulaciones y comparación con los servicios facturados.',
                'icono' => 'fas fa-cash-register',
                'permiso' => PermisoCatalogo::gate(PermisoCatalogo::REPORTE_INGRESOS),
                'filtros' => ['veterinario'],
            ],
            self::POR_COBRAR => [
                'titulo' => 'Cuentas por cobrar',
                'descripcion' => 'Consultas con saldo pendiente, ordenadas por antigüedad, para gestionar la cobranza.',
                'icono' => 'fas fa-file-invoice-dollar',
                'permiso' => self::PERMISO_POR_COBRAR,
                'filtros' => ['veterinario'],
            ],
            self::SERVICIOS => [
                'titulo' => 'Servicios más solicitados',
                'descripcion' => 'Ranking de servicios por cantidad e ingresos facturados, agrupado por categoría.',
                'icono' => 'fas fa-syringe',
                'permiso' => PermisoCatalogo::gate(PermisoCatalogo::REPORTE_SERVICIOS),
                'filtros' => ['veterinario'],
            ],
            self::VETERINARIOS => [
                'titulo' => 'Productividad por veterinario',
                'descripcion' => 'Consultas, emergencias e ingresos facturados por cada veterinario.',
                'icono' => 'fas fa-user-md',
                'permiso' => PermisoCatalogo::gate(PermisoCatalogo::REPORTE_VETERINARIOS),
                'filtros' => ['veterinario'],
            ],
            self::CLIENTES_MASCOTAS => [
                'titulo' => 'Clientes y mascotas',
                'descripcion' => 'Evolución de nuevos registros y distribución de mascotas por especie.',
                'icono' => 'fas fa-paw',
                'permiso' => PermisoCatalogo::gate(PermisoCatalogo::REPORTE_RESERVAS),
                'filtros' => [],
            ],
            self::RESERVAS => [
                'titulo' => 'Reservas y asistencia',
                'descripcion' => 'Reservas por estado, tasa de asistencia, cancelaciones y horas de mayor demanda.',
                'icono' => 'fas fa-calendar-check',
                'permiso' => PermisoCatalogo::gate(PermisoCatalogo::REPORTE_RESERVAS),
                'filtros' => [],
            ],
        ];
    }

    /** Patrón para ->where('tipo', ...) en las rutas. */
    public static function patronRuta(): string
    {
        return implode('|', array_keys(self::catalogo()));
    }
}
