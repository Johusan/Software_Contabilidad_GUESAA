<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::with('categoria')->orderBy('descripcion', 'asc')->get();
        $categorias = Categoria::orderBy('nombre', 'asc')->get();

        return Inertia::render('Inventario/Index', [
            'productos' => $productos,
            'categorias' => $categorias
        ]);
    }

    public function storeProducto(Request $request)
    {
        $request->validate([
            'id_categoria' => 'required|integer|exists:categorias,id_categoria',
            'codigo_barras' => 'nullable|string|max:50|unique:productos,codigo_barras',
            'descripcion' => 'required|string|max:150',
            'stock_actual' => 'nullable|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'precio_mayorista' => 'nullable|numeric|min:0',
            'cant_mayorista' => 'nullable|integer|min:1',
        ]);

        Producto::create([
            'id_categoria' => $request->input('id_categoria'),
            'codigo_barras' => $request->input('codigo_barras'),
            'descripcion' => $request->input('descripcion'),
            'stock_actual' => $request->input('stock_actual', 0),
            'stock_minimo' => $request->input('stock_minimo', 5),
            'precio_compra' => $request->input('precio_compra'),
            'precio_venta' => $request->input('precio_venta'),
            'precio_mayorista' => $request->input('precio_mayorista', 0.00),
            'cant_mayorista' => $request->input('cant_mayorista', 6),
            'estado' => true
        ]);

        return redirect()->back()->with('success', 'Producto registrado con éxito.');
    }

    public function updateProducto(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'id_categoria' => 'required|integer|exists:categorias,id_categoria',
            'codigo_barras' => 'nullable|string|max:50|unique:productos,codigo_barras,' . $id . ',id_producto',
            'descripcion' => 'required|string|max:150',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'precio_mayorista' => 'nullable|numeric|min:0',
            'cant_mayorista' => 'nullable|integer|min:1',
        ]);

        $producto->update([
            'id_categoria' => $request->input('id_categoria'),
            'codigo_barras' => $request->input('codigo_barras'),
            'descripcion' => $request->input('descripcion'),
            'stock_actual' => $request->input('stock_actual'),
            'stock_minimo' => $request->input('stock_minimo'),
            'precio_compra' => $request->input('precio_compra'),
            'precio_venta' => $request->input('precio_venta'),
            'precio_mayorista' => $request->input('precio_mayorista', 0.00),
            'cant_mayorista' => $request->input('cant_mayorista', 6),
        ]);

        return redirect()->back()->with('success', 'Producto actualizado con éxito.');
    }

    public function toggleProductoEstado($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->estado = !$producto->estado;
        $producto->save();

        return redirect()->back()->with('success', 'Estado del producto actualizado.');
    }

    public function storeCategoria(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias,nombre',
            'descripcion' => 'nullable|string|max:150'
        ]);

        Categoria::create([
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion')
        ]);

        return redirect()->back()->with('success', 'Categoría registrada con éxito.');
    }

    public function updateCategoria(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias,nombre,' . $id . ',id_categoria',
            'descripcion' => 'nullable|string|max:150'
        ]);

        $categoria->update([
            'nombre' => $request->input('nombre'),
            'descripcion' => $request->input('descripcion')
        ]);

        return redirect()->back()->with('success', 'Categoría actualizada con éxito.');
    }

    public function deleteProducto($id)
    {
        $producto = Producto::findOrFail($id);

        if ($producto->detallesCompra()->exists() || $producto->detallesVenta()->exists()) {
            return redirect()->back()->withErrors([
                'error' => 'No se puede eliminar el producto porque posee movimientos de compras o ventas registrados. Puede desactivarlo en su lugar.'
            ]);
        }

        $producto->delete();

        return redirect()->back()->with('success', 'Producto eliminado con éxito.');
    }

    public function deleteCategoria($id)
    {
        $categoria = Categoria::findOrFail($id);

        if ($categoria->productos()->exists()) {
            return redirect()->back()->withErrors([
                'error' => 'No se puede eliminar la categoría porque contiene productos asociados.'
            ]);
        }

        $categoria->delete();

        return redirect()->back()->with('success', 'Categoría eliminada con éxito.');
    }
}
