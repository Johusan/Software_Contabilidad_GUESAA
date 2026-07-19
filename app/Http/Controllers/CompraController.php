<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Producto;
use App\Models\Proveedor;
use App\Models\AsientoContable;
use App\Models\DetalleAsiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CompraController extends Controller
{
    public function index()
    {
        $compras = Compra::with(['proveedor', 'usuario'])->orderBy('fecha_compra', 'desc')->get();
        $proveedores = Proveedor::where('estado', true)->orderBy('razon_social', 'asc')->get();
        $productos = Producto::where('estado', true)->orderBy('descripcion', 'asc')->get();

        return Inertia::render('Compras/Index', [
            'compras' => $compras,
            'proveedores' => $proveedores,
            'productos' => $productos
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_proveedor' => 'required|integer|exists:proveedores,id_proveedor',
            'tipo_comprobante' => 'required|string|in:Factura,Boleta,Guia',
            'num_comprobante' => 'required|string|max:50',
            'detalles' => 'required|array|min:1',
            'detalles.*.id_producto' => 'required|integer|exists:productos,id_producto',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        $detalles = $request->input('detalles');
        $subtotal = 0;

        foreach ($detalles as $det) {
            $subtotal += $det['cantidad'] * $det['precio_unitario'];
        }

        $igv = $subtotal * 0.18;
        $total = $subtotal + $igv;

        DB::beginTransaction();
        try {
            // 1. Crear Compra
            $compra = Compra::create([
                'id_proveedor' => $request->input('id_proveedor'),
                'id_usuario' => Auth::user()->id_usuario,
                'tipo_comprobante' => $request->input('tipo_comprobante'),
                'num_comprobante' => $request->input('num_comprobante'),
                'subtotal' => $subtotal,
                'igv' => $igv,
                'total' => $total,
                'estado' => 'COMPLETADO'
            ]);

            // 2. Crear Detalle y Actualizar Stock
            foreach ($detalles as $det) {
                $itemSubtotal = $det['cantidad'] * $det['precio_unitario'];
                DetalleCompra::create([
                    'id_compra' => $compra->id_compra,
                    'id_producto' => $det['id_producto'],
                    'cantidad' => $det['cantidad'],
                    'precio_unitario' => $det['precio_unitario'],
                    'subtotal' => $itemSubtotal
                ]);

                $producto = Producto::findOrFail($det['id_producto']);
                $producto->stock_actual += $det['cantidad'];
                // Opcionalmente actualizar el precio de compra si varía
                $producto->precio_compra = $det['precio_unitario'];
                $producto->save();
            }

            // 3. Generar Asiento Contable Automático (COMPRA)
            $asiento = AsientoContable::create([
                'id_usuario' => Auth::user()->id_usuario,
                'glosa' => 'REGISTRO DE COMPRA N° ' . $compra->num_comprobante,
                'tipo_operacion' => 'COMPRA',
                'referencia_id' => $compra->id_compra,
                'estado' => 'ACTIVO'
            ]);

            // Detalle asiento:
            // Debe 6011 (Mercaderías manufacturadas) - Subtotal
            DetalleAsiento::create([
                'id_asiento' => $asiento->id_asiento,
                'codigo_cuenta' => '6011',
                'debe' => $subtotal,
                'haber' => 0.00
            ]);

            // Debe 40111 (IGV) - IGV
            DetalleAsiento::create([
                'id_asiento' => $asiento->id_asiento,
                'codigo_cuenta' => '40111',
                'debe' => $igv,
                'haber' => 0.00
            ]);

            // Haber 4212 (Facturas por pagar emitidas) - Total
            DetalleAsiento::create([
                'id_asiento' => $asiento->id_asiento,
                'codigo_cuenta' => '4212',
                'debe' => 0.00,
                'haber' => $total
            ]);

            // 4. Asiento de Destino (Mercadería entra a Almacén)
            $asientoDestino = AsientoContable::create([
                'id_usuario' => Auth::user()->id_usuario,
                'glosa' => 'DESTINO DE COMPRA N° ' . $compra->num_comprobante,
                'tipo_operacion' => 'COMPRA',
                'referencia_id' => $compra->id_compra,
                'estado' => 'ACTIVO'
            ]);

            // Debe 20111 (Mercaderías manufacturadas) - Subtotal
            DetalleAsiento::create([
                'id_asiento' => $asientoDestino->id_asiento,
                'codigo_cuenta' => '20111',
                'debe' => $subtotal,
                'haber' => 0.00
            ]);

            // Haber 6111 (Variación de existencias - Mercaderías) - Subtotal
            DetalleAsiento::create([
                'id_asiento' => $asientoDestino->id_asiento,
                'codigo_cuenta' => '6111',
                'debe' => 0.00,
                'haber' => $subtotal
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Compra procesada y contabilizada con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al procesar la compra: ' . $e->getMessage()]);
        }
    }
}
