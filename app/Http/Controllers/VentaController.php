<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\CajaDiaria;
use App\Models\AsientoContable;
use App\Models\DetalleAsiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with(['cliente', 'usuario'])->orderBy('fecha_venta', 'desc')->get();
        $clientes = Cliente::where('estado', true)->orderBy('nombre_razon_social', 'asc')->get();
        $productos = Producto::where('estado', true)->orderBy('descripcion', 'asc')->get();
        
        // Obtener caja abierta
        $cajaAbierta = CajaDiaria::where('estado', 'ABIERTA')->first();

        return Inertia::render('Ventas/Index', [
            'ventas' => $ventas,
            'clientes' => $clientes,
            'productos' => $productos,
            'cajaAbierta' => $cajaAbierta
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_cliente' => 'required|integer|exists:clientes,id_cliente',
            'tipo_comprobante' => 'required|string|in:Factura,Boleta',
            'num_comprobante' => 'required|string|max:50|unique:ventas,num_comprobante',
            'detalles' => 'required|array|min:1',
            'detalles.*.id_producto' => 'required|integer|exists:productos,id_producto',
            'detalles.*.cantidad' => 'required|integer|min:1',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'detalles.*.descuento' => 'nullable|numeric|min:0',
        ]);

        // 1. Verificar Caja Abierta
        $caja = CajaDiaria::where('estado', 'ABIERTA')->first();
        if (!$caja) {
            return back()->withErrors(['error' => 'Debe abrir caja antes de registrar una venta.']);
        }

        $detalles = $request->input('detalles');
        $subtotal = 0;

        // 2. Verificar Stocks
        foreach ($detalles as $det) {
            $prod = Producto::findOrFail($det['id_producto']);
            if ($prod->stock_actual < $det['cantidad']) {
                return back()->withErrors(['error' => "Stock insuficiente para el producto: {$prod->descripcion} (Stock actual: {$prod->stock_actual})."]);
            }
            $descuento = $det['descuento'] ?? 0;
            $subtotal += ($det['cantidad'] * $det['precio_unitario']) - $descuento;
        }

        $igv = $subtotal * 0.18;
        $total = $subtotal + $igv;

        DB::beginTransaction();
        try {
            // 3. Crear Venta
            $venta = Venta::create([
                'id_cliente' => $request->input('id_cliente'),
                'id_usuario' => Auth::user()->id_usuario,
                'tipo_comprobante' => $request->input('tipo_comprobante'),
                'num_comprobante' => $request->input('num_comprobante'),
                'subtotal' => $subtotal,
                'igv' => $igv,
                'total' => $total,
                'estado' => 'COMPLETADO'
            ]);

            // 4. Detalle y Disminución de Stock
            foreach ($detalles as $det) {
                $descuento = $det['descuento'] ?? 0;
                $itemSubtotal = ($det['cantidad'] * $det['precio_unitario']) - $descuento;

                DetalleVenta::create([
                    'id_venta' => $venta->id_venta,
                    'id_producto' => $det['id_producto'],
                    'cantidad' => $det['cantidad'],
                    'precio_unitario' => $det['precio_unitario'],
                    'descuento' => $descuento,
                    'subtotal' => $itemSubtotal
                ]);

                $producto = Producto::findOrFail($det['id_producto']);
                $producto->stock_actual -= $det['cantidad'];
                $producto->save();
            }

            // 5. Acumular en Caja
            $caja->ingresos_ventas += $total;
            $caja->save();

            // 6. Generar Asiento Contable Automático (VENTA)
            $asiento = AsientoContable::create([
                'id_usuario' => Auth::user()->id_usuario,
                'glosa' => 'REGISTRO DE VENTA N° ' . $venta->num_comprobante,
                'tipo_operacion' => 'VENTA',
                'referencia_id' => $venta->id_venta,
                'estado' => 'ACTIVO'
            ]);

            // Detalle asiento:
            // Debe 121 (Facturas, boletas y otros comprobantes por cobrar) - Total
            DetalleAsiento::create([
                'id_asiento' => $asiento->id_asiento,
                'codigo_cuenta' => '121',
                'debe' => $total,
                'haber' => 0.00
            ]);

            // Haber 401 (Gobierno Central / IGV) - IGV
            DetalleAsiento::create([
                'id_asiento' => $asiento->id_asiento,
                'codigo_cuenta' => '401',
                'debe' => 0.00,
                'haber' => $igv
            ]);

            // Haber 701 (Ventas - Mercaderías) - Subtotal
            DetalleAsiento::create([
                'id_asiento' => $asiento->id_asiento,
                'codigo_cuenta' => '701',
                'debe' => 0.00,
                'haber' => $subtotal
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Venta procesada y contabilizada con éxito.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al procesar la venta: ' . $e->getMessage()]);
        }
    }
}
