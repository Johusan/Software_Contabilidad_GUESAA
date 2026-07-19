<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TerceroController extends Controller
{
    public function index()
    {
        $clientes = Cliente::orderBy('nombre_razon_social', 'asc')->get();
        $proveedores = Proveedor::orderBy('razon_social', 'asc')->get();

        return Inertia::render('Terceros/Index', [
            'clientes' => $clientes,
            'proveedores' => $proveedores
        ]);
    }

    public function storeCliente(Request $request)
    {
        $rules = [
            'tipo_documento' => 'required|string|in:DNI,RUC',
            'num_documento' => 'required|string|unique:clientes,num_documento',
            'nombre_razon_social' => 'required|string|max:150',
            'direccion' => 'nullable|string|max:200',
            'telefono' => 'nullable|string|max:20',
        ];

        $request->validate($rules);

        // Validaciones personalizadas de longitud y formato
        $tipo = $request->input('tipo_documento');
        $doc = $request->input('num_documento');

        if ($tipo === 'DNI') {
            if (strlen($doc) !== 8 || !ctype_digit($doc)) {
                return back()->withErrors(['num_documento' => 'El DNI debe contener exactamente 8 dígitos numéricos.']);
            }
        } elseif ($tipo === 'RUC') {
            if (strlen($doc) !== 11 || !ctype_digit($doc) || !(str_starts_with($doc, '10') || str_starts_with($doc, '20'))) {
                return back()->withErrors(['num_documento' => 'El RUC debe contener exactamente 11 dígitos numéricos y comenzar con 10 o 20.']);
            }
        }

        Cliente::create([
            'tipo_documento' => $tipo,
            'num_documento' => $doc,
            'nombre_razon_social' => $request->input('nombre_razon_social'),
            'direccion' => $request->input('direccion'),
            'telefono' => $request->input('telefono'),
            'estado' => true
        ]);

        return redirect()->back()->with('success', 'Cliente registrado con éxito.');
    }

    public function updateCliente(Request $request, $id)
    {
        $cliente = Cliente::findOrFail($id);

        $rules = [
            'tipo_documento' => 'required|string|in:DNI,RUC',
            'num_documento' => 'required|string|unique:clientes,num_documento,' . $id . ',id_cliente',
            'nombre_razon_social' => 'required|string|max:150',
            'direccion' => 'nullable|string|max:200',
            'telefono' => 'nullable|string|max:20',
        ];

        $request->validate($rules);

        $tipo = $request->input('tipo_documento');
        $doc = $request->input('num_documento');

        if ($tipo === 'DNI') {
            if (strlen($doc) !== 8 || !ctype_digit($doc)) {
                return back()->withErrors(['num_documento' => 'El DNI debe contener exactamente 8 dígitos numéricos.']);
            }
        } elseif ($tipo === 'RUC') {
            if (strlen($doc) !== 11 || !ctype_digit($doc) || !(str_starts_with($doc, '10') || str_starts_with($doc, '20'))) {
                return back()->withErrors(['num_documento' => 'El RUC debe contener exactamente 11 dígitos numéricos y comenzar con 10 o 20.']);
            }
        }

        $cliente->update([
            'tipo_documento' => $tipo,
            'num_documento' => $doc,
            'nombre_razon_social' => $request->input('nombre_razon_social'),
            'direccion' => $request->input('direccion'),
            'telefono' => $request->input('telefono'),
        ]);

        return redirect()->back()->with('success', 'Cliente actualizado con éxito.');
    }

    public function toggleClienteEstado($id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->estado = !$cliente->estado;
        $cliente->save();

        return redirect()->back()->with('success', 'Estado del cliente actualizado.');
    }

    public function storeProveedor(Request $request)
    {
        $request->validate([
            'ruc' => 'required|string|unique:proveedores,ruc',
            'razon_social' => 'required|string|max:150',
            'direccion' => 'nullable|string|max:200',
            'telefono' => 'nullable|string|max:20',
        ]);

        $ruc = $request->input('ruc');

        if (strlen($ruc) !== 11 || !ctype_digit($ruc) || !(str_starts_with($ruc, '10') || str_starts_with($ruc, '20'))) {
            return back()->withErrors(['ruc' => 'El RUC debe contener exactamente 11 dígitos numéricos y comenzar con 10 o 20.']);
        }

        Proveedor::create([
            'ruc' => $ruc,
            'razon_social' => $request->input('razon_social'),
            'direccion' => $request->input('direccion'),
            'telefono' => $request->input('telefono'),
            'estado' => true
        ]);

        return redirect()->back()->with('success', 'Proveedor registrado con éxito.');
    }

    public function updateProveedor(Request $request, $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $request->validate([
            'ruc' => 'required|string|unique:proveedores,ruc,' . $id . ',id_proveedor',
            'razon_social' => 'required|string|max:150',
            'direccion' => 'nullable|string|max:200',
            'telefono' => 'nullable|string|max:20',
        ]);

        $ruc = $request->input('ruc');

        if (strlen($ruc) !== 11 || !ctype_digit($ruc) || !(str_starts_with($ruc, '10') || str_starts_with($ruc, '20'))) {
            return back()->withErrors(['ruc' => 'El RUC debe contener exactamente 11 dígitos numéricos y comenzar con 10 o 20.']);
        }

        $proveedor->update([
            'ruc' => $ruc,
            'razon_social' => $request->input('razon_social'),
            'direccion' => $request->input('direccion'),
            'telefono' => $request->input('telefono'),
        ]);

        return redirect()->back()->with('success', 'Proveedor actualizado con éxito.');
    }

    public function toggleProveedorEstado($id)
    {
        $proveedor = Proveedor::findOrFail($id);
        $proveedor->estado = !$proveedor->estado;
        $proveedor->save();

        return redirect()->back()->with('success', 'Estado del proveedor actualizado.');
    }
}
