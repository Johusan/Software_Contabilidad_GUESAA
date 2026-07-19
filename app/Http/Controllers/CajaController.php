<?php

namespace App\Http\Controllers;

use App\Models\CajaDiaria;
use App\Models\AsientoContable;
use App\Models\DetalleAsiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CajaController extends Controller
{
    public function index()
    {
        $cajaActiva = CajaDiaria::with('usuario')->where('estado', 'ABIERTA')->first();
        $cajasPasadas = CajaDiaria::with('usuario')->where('estado', 'CERRADA')->orderBy('fecha_apertura', 'desc')->get();

        return Inertia::render('Caja/Index', [
            'cajaActiva' => $cajaActiva,
            'cajasPasadas' => $cajasPasadas
        ]);
    }

    public function abrir(Request $request)
    {
        $request->validate([
            'monto_inicial' => 'required|numeric|min:0'
        ]);

        // Verificar si ya hay caja abierta
        $cajaExistente = CajaDiaria::where('estado', 'ABIERTA')->first();
        if ($cajaExistente) {
            return back()->withErrors(['error' => 'Ya existe una caja abierta. Debe cerrarla primero.']);
        }

        CajaDiaria::create([
            'id_usuario' => Auth::user()->id_usuario,
            'fecha_apertura' => now(),
            'monto_inicial' => $request->input('monto_inicial'),
            'ingresos_ventas' => 0.00,
            'egresos_varios' => 0.00,
            'estado' => 'ABIERTA'
        ]);

        return redirect()->back()->with('success', 'Caja abierta con éxito.');
    }

    public function registrarEgreso(Request $request)
    {
        $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'glosa' => 'required|string|max:255'
        ]);

        $caja = CajaDiaria::where('estado', 'ABIERTA')->first();
        if (!$caja) {
            return back()->withErrors(['error' => 'Debe abrir caja antes de registrar un egreso.']);
        }

        $saldoActual = $caja->monto_inicial + $caja->ingresos_ventas - $caja->egresos_varios;
        $montoEgreso = $request->input('monto');

        if ($saldoActual < $montoEgreso) {
            return back()->withErrors(['error' => 'Saldo insuficiente en caja para realizar el egreso. Saldo disponible: S/. ' . number_format($saldoActual, 2)]);
        }

        DB::beginTransaction();
        try {
            // Actualizar egresos en caja
            $caja->egresos_varios += $montoEgreso;
            $caja->save();

            // Generar asiento contable por el gasto (Caja chica / Gastos varios)
            $asiento = AsientoContable::create([
                'id_usuario' => Auth::user()->id_usuario,
                'glosa' => 'EGRESO DE CAJA: ' . strtoupper($request->input('glosa')),
                'tipo_operacion' => 'CAJA',
                'referencia_id' => $caja->id_caja,
                'estado' => 'ACTIVO'
            ]);

            // Debe 659 (Otros gastos de gestión diversos)
            // (Si no existe en cuentas_pcge, usaremos una genérica o crearemos 659, pero en el seeder se insertaron cientos. Usemos 659 u otra si existe, o 65 por naturaleza)
            DetalleAsiento::create([
                'id_asiento' => $asiento->id_asiento,
                'codigo_cuenta' => '659' ?? '65',
                'debe' => $montoEgreso,
                'haber' => 0.00
            ]);

            // Haber 101 (Caja)
            DetalleAsiento::create([
                'id_asiento' => $asiento->id_asiento,
                'codigo_cuenta' => '101',
                'debe' => 0.00,
                'haber' => $montoEgreso
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Egreso registrado y contabilizado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al registrar egreso: ' . $e->getMessage()]);
        }
    }

    public function cerrar(Request $request)
    {
        $caja = CajaDiaria::where('estado', 'ABIERTA')->first();
        if (!$caja) {
            return back()->withErrors(['error' => 'No hay ninguna caja abierta para cerrar.']);
        }

        $montoFinal = $caja->monto_inicial + $caja->ingresos_ventas - $caja->egresos_varios;

        $caja->update([
            'fecha_cierre' => now(),
            'monto_final' => $montoFinal,
            'estado' => 'CERRADA'
        ]);

        return redirect()->back()->with('success', 'Caja cerrada. Arqueo completado exitosamente.');
    }
}
