<?php

namespace App\Http\Controllers;

use App\Models\CuentaPcge;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PlanCuentasController extends Controller
{
    public function index()
    {
        $cuentas = CuentaPcge::orderBy('codigo_cuenta', 'asc')->get();

        return Inertia::render('PlanCuentas/Index', [
            'cuentas' => $cuentas
        ]);
    }

    public function storeSubcuenta(Request $request)
    {
        $request->validate([
            'codigo_cuenta' => 'required|string|max:10|unique:cuentas_pcge,codigo_cuenta',
            'denominacion' => 'required|string|max:150',
            'codigo_padre' => 'required|string|exists:cuentas_pcge,codigo_cuenta'
        ]);

        $codigo = $request->input('codigo_cuenta');
        $padre = $request->input('codigo_padre');

        // Validar jerarquía: el código debe comenzar con el código del padre
        if (!str_starts_with($codigo, $padre)) {
            return back()->withErrors(['codigo_cuenta' => "El código de la subcuenta ({$codigo}) debe comenzar con el código de la cuenta padre ({$padre})."]);
        }

        // Validar longitud: debe ser más largo que el padre
        if (strlen($codigo) <= strlen($padre)) {
            return back()->withErrors(['codigo_cuenta' => 'El código de la subcuenta debe tener mayor longitud que la cuenta padre.']);
        }

        CuentaPcge::create([
            'codigo_cuenta' => $codigo,
            'denominacion' => $request->input('denominacion'),
            'elemento' => intval(substr($codigo, 0, 1)),
            'estado' => true
        ]);

        return redirect()->back()->with('success', 'Subcuenta creada con éxito.');
    }
}
