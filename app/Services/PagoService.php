<?php

namespace App\Services;

use App\Models\Consulta;
use App\Models\Pago;
use DomainException;
use Illuminate\Support\Facades\DB;

class PagoService
{
    public function prepararPendiente(Consulta $consulta): void
    {
        DB::transaction(function () use ($consulta): void {
            $actual = Consulta::query()->lockForUpdate()->findOrFail($consulta->id);
            if ($actual->pagos()->where('estado', Pago::PAGADO)->exists()) {
                return;
            }

            $total = $this->calcularTotalServicios($actual);
            if ($total <= 0) {
                return;
            }
            if ($total > 99999999.99) {
                throw new DomainException('El total supera el monto permitido para un pago.');
            }

            $pendientes = $actual->pagos()->where('estado', Pago::PENDIENTE)->lockForUpdate()->get();
            if ($pendientes->count() > 1) {
                throw new DomainException('Existen varios pagos pendientes para esta consulta. Revise los movimientos antes de finalizar.');
            }

            $pago = $pendientes->first() ?? new Pago;
            $pago->fill([
                'consulta_id' => $actual->id,
                'monto' => number_format($total, 2, '.', ''),
                'estado' => Pago::PENDIENTE,
            ])->save();
        }, 3);
    }

    /** @param array{monto: int|float|string, pago_id?: int|string|null} $datos */
    public function registrar(Consulta $consulta, array $datos): void
    {
        DB::transaction(function () use ($consulta, $datos): void {
            $bloqueada = Consulta::query()->lockForUpdate()->findOrFail($consulta->id);
            $total = (int) round((float) DB::table('consulta_servicio')->where('consulta_id', $bloqueada->id)->sum('precio') * 100);
            $tieneCobro = $bloqueada->pagos()->where('estado', Pago::PAGADO)->exists();
            if ($total <= 0) {
                throw new DomainException('La consulta no tiene servicios cobrables.');
            }
            if ($tieneCobro) {
                throw new DomainException('La consulta tiene un cobro vigente. No se admiten abonos ni cobros duplicados.');
            }
            $monto = (int) round(((float) $datos['monto']) * 100);
            if ($monto !== $total) {
                throw new DomainException('El pago en efectivo debe ser exactamente Bs '.number_format($total / 100, 2, '.', '').'.');
            }
            $pendientes = Pago::where('consulta_id', $bloqueada->id)
                ->where('estado', Pago::PENDIENTE)->lockForUpdate()->get();
            if ($pendientes->count() > 1) {
                throw new DomainException('Existen varios pagos pendientes. Revise y anule los duplicados antes de cobrar.');
            }

            if (isset($datos['pago_id']) && (int) $pendientes->first()?->id !== (int) $datos['pago_id']) {
                throw new DomainException('El pago seleccionado ya no está pendiente o no pertenece a esta consulta. Actualice la cuenta antes de cobrar.');
            }

            $pago = $pendientes->first() ?? new Pago;
            $pago->fill([
                'consulta_id' => $bloqueada->id,
                'monto' => number_format($total / 100, 2, '.', ''),
                'estado' => Pago::PAGADO,
            ])->save();
        }, 3);
    }

    public function anular(Consulta $consulta, Pago $pago): void
    {
        DB::transaction(function () use ($consulta, $pago): void {
            Consulta::query()->lockForUpdate()->findOrFail($consulta->id);
            $actual = Pago::whereKey($pago->id)->where('consulta_id', $consulta->id)->lockForUpdate()->firstOrFail();
            if ($actual->estado === Pago::ANULADO) {
                throw new DomainException('El pago ya está anulado.');
            }
            $actual->update(['estado' => Pago::ANULADO]);
        }, 3);
    }

    public function calcularTotalServicios(Consulta $consulta): float
    {
        return round((float) DB::table('consulta_servicio')->where('consulta_id', $consulta->id)->sum('precio'), 2);
    }

    public function calcularTotalPagado(Consulta $consulta): float
    {
        return round((float) Pago::where('consulta_id', $consulta->id)->where('estado', Pago::PAGADO)->sum('monto'), 2);
    }

    public function calcularSaldo(Consulta $consulta): float
    {
        return max(0, round($this->calcularTotalServicios($consulta) - $this->calcularTotalPagado($consulta), 2));
    }
}
