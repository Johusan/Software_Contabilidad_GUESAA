<?php

namespace App\Http\Controllers;

use App\Models\AsientoContable;
use App\Models\DetalleAsiento;
use App\Models\CuentaPcge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ContabilidadController extends Controller
{
    public function index(Request $request)
    {
        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');

        // 1. Libro Diario
        $queryDiario = AsientoContable::with(['usuario', 'detalles.cuenta'])
            ->where('estado', 'ACTIVO');

        if ($fechaDesde) {
            $queryDiario->whereDate('fecha_asiento', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $queryDiario->whereDate('fecha_asiento', '<=', $fechaHasta);
        }

        $asientosDiario = $queryDiario->orderBy('fecha_asiento', 'desc')
            ->orderBy('id_asiento', 'desc')
            ->get();

        // 2. Libro Mayor
        // Obtener todas las cuentas PCGE que tienen movimientos registrados
        $queryDetalles = DB::table('detalle_asientos')
            ->join('asientos_contables', 'detalle_asientos.id_asiento', '=', 'asientos_contables.id_asiento')
            ->join('cuentas_pcge', 'detalle_asientos.codigo_cuenta', '=', 'cuentas_pcge.codigo_cuenta')
            ->where('asientos_contables.estado', 'ACTIVO');

        if ($fechaDesde) {
            $queryDetalles->whereDate('asientos_contables.fecha_asiento', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $queryDetalles->whereDate('asientos_contables.fecha_asiento', '<=', $fechaHasta);
        }

        $movimientos = $queryDetalles->select(
            'detalle_asientos.id_detalle_asiento',
            'detalle_asientos.id_asiento',
            'asientos_contables.fecha_asiento',
            'asientos_contables.glosa',
            'asientos_contables.tipo_operacion',
            'detalle_asientos.codigo_cuenta',
            'cuentas_pcge.denominacion as nombre_cuenta',
            'detalle_asientos.debe',
            'detalle_asientos.haber'
        )
        ->orderBy('detalle_asientos.codigo_cuenta')
        ->orderBy('asientos_contables.fecha_asiento', 'asc')
        ->orderBy('asientos_contables.id_asiento', 'asc')
        ->get();

        // 3. Balance de Comprobación (Balanza)
        $queryBal = DB::table('detalle_asientos')
            ->join('asientos_contables', 'detalle_asientos.id_asiento', '=', 'asientos_contables.id_asiento')
            ->join('cuentas_pcge', 'detalle_asientos.codigo_cuenta', '=', 'cuentas_pcge.codigo_cuenta')
            ->where('asientos_contables.estado', 'ACTIVO');

        if ($fechaDesde) {
            $queryBal->whereDate('asientos_contables.fecha_asiento', '>=', $fechaDesde);
        }
        if ($fechaHasta) {
            $queryBal->whereDate('asientos_contables.fecha_asiento', '<=', $fechaHasta);
        }

        $balanza = $queryBal->select(
            'detalle_asientos.codigo_cuenta',
            'cuentas_pcge.denominacion as nombre_cuenta',
            DB::raw('SUM(detalle_asientos.debe) as suma_debe'),
            DB::raw('SUM(detalle_asientos.haber) as suma_haber')
        )
        ->groupBy('detalle_asientos.codigo_cuenta', 'cuentas_pcge.denominacion')
        ->orderBy('detalle_asientos.codigo_cuenta')
        ->get()
        ->map(function ($row) {
            $debe = (float)$row->suma_debe;
            $haber = (float)$row->suma_haber;
            $saldoDeudor = max(0, $debe - $haber);
            $saldoAcreedor = max(0, $haber - $debe);

            return [
                'codigo_cuenta' => $row->codigo_cuenta,
                'nombre_cuenta' => $row->nombre_cuenta,
                'suma_debe' => $debe,
                'suma_haber' => $haber,
                'saldo_deudor' => $saldoDeudor,
                'saldo_acreedor' => $saldoAcreedor
            ];
        });

        // Cuentas del catálogo para el selector de asientos manuales
        $cuentasCatalogo = CuentaPcge::where('estado', true)
            ->orderBy('codigo_cuenta')
            ->get();

        return Inertia::render('Contabilidad/Index', [
            'asientosDiario' => $asientosDiario,
            'movimientosMayor' => $movimientos,
            'balanza' => $balanza,
            'cuentasCatalogo' => $cuentasCatalogo,
            'filters' => [
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta
            ]
        ]);
    }

    public function storeAsientoManual(Request $request)
    {
        $request->validate([
            'glosa' => 'required|string|max:255',
            'fecha_asiento' => 'nullable|date',
            'detalles' => 'required|array|min:2',
            'detalles.*.codigo_cuenta' => 'required|exists:cuentas_pcge,codigo_cuenta',
            'detalles.*.debe' => 'required|numeric|min:0',
            'detalles.*.haber' => 'required|numeric|min:0'
        ]);

        $detalles = $request->input('detalles');
        $totalDebe = 0;
        $totalHaber = 0;

        foreach ($detalles as $det) {
            $totalDebe += (float)$det['debe'];
            $totalHaber += (float)$det['haber'];
        }

        if (abs($totalDebe - $totalHaber) > 0.01) {
            return back()->withErrors([
                'error' => 'El asiento contable no está cuadrado. Total Debe: S/. ' . number_format($totalDebe, 2) . ' | Total Haber: S/. ' . number_format($totalHaber, 2)
            ]);
        }

        DB::beginTransaction();
        try {
            $asiento = AsientoContable::create([
                'id_usuario' => Auth::user()->id_usuario,
                'fecha_asiento' => $request->input('fecha_asiento') ? $request->input('fecha_asiento') : now(),
                'glosa' => 'MANUAL: ' . strtoupper($request->input('glosa')),
                'tipo_operacion' => 'MANUAL',
                'estado' => 'ACTIVO'
            ]);

            foreach ($detalles as $det) {
                if ((float)$det['debe'] > 0 || (float)$det['haber'] > 0) {
                    DetalleAsiento::create([
                        'id_asiento' => $asiento->id_asiento,
                        'codigo_cuenta' => $det['codigo_cuenta'],
                        'debe' => $det['debe'],
                        'haber' => $det['haber']
                    ]);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Asiento contable manual registrado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar asiento: ' . $e->getMessage()]);
        }
    }
}
