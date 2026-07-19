<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TerceroController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\PlanCuentasController;
use App\Models\Cliente;
use App\Models\Proveedor;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\Compra;
use App\Models\AsientoContable;
use App\Http\Controllers\UsuarioController;
use Inertia\Inertia;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    
    // Dashboard con Estadísticas Integradas
    Route::get('/dashboard', function () {
        $stats = [
            'totalClientes' => Cliente::count(),
            'totalProveedores' => Proveedor::count(),
            'totalProductos' => Producto::count(),
            'productosBajoStock' => Producto::whereRaw('stock_actual <= stock_minimo')->count(),
            'totalVentas' => Venta::where('estado', 'COMPLETADO')->sum('total'),
            'totalCompras' => Compra::where('estado', 'COMPLETADO')->sum('total'),
            'ventasCount' => Venta::count(),
            'comprasCount' => Compra::count(),
            'asientosRecientes' => AsientoContable::with(['usuario', 'detalles.cuenta'])
                ->orderBy('fecha_asiento', 'desc')
                ->limit(6)
                ->get()
        ];

        return Inertia::render('Dashboard', $stats);
    })->name('dashboard');

    // Módulo Terceros
    Route::get('/terceros', [TerceroController::class, 'index'])->name('terceros.index');
    Route::post('/terceros/cliente', [TerceroController::class, 'storeCliente'])->name('clientes.store');
    Route::put('/terceros/cliente/{id}', [TerceroController::class, 'updateCliente'])->name('clientes.update');
    Route::post('/terceros/cliente/{id}/toggle', [TerceroController::class, 'toggleClienteEstado'])->name('clientes.toggle');
    Route::post('/terceros/proveedor', [TerceroController::class, 'storeProveedor'])->name('proveedores.store');
    Route::put('/terceros/proveedor/{id}', [TerceroController::class, 'updateProveedor'])->name('proveedores.update');
    Route::post('/terceros/proveedor/{id}/toggle', [TerceroController::class, 'toggleProveedorEstado'])->name('proveedores.toggle');

    // Módulo Inventario/Productos
    Route::get('/inventario', [ProductoController::class, 'index'])->name('inventario.index');
    Route::post('/inventario/producto', [ProductoController::class, 'storeProducto'])->name('productos.store');
    Route::put('/inventario/producto/{id}', [ProductoController::class, 'updateProducto'])->name('productos.update');
    Route::post('/inventario/producto/{id}/toggle', [ProductoController::class, 'toggleProductoEstado'])->name('productos.toggle');
    Route::post('/inventario/categoria', [ProductoController::class, 'storeCategoria'])->name('categorias.store');
    Route::put('/inventario/categoria/{id}', [ProductoController::class, 'updateCategoria'])->name('categorias.update');

    // Módulo Compras
    Route::get('/compras', [CompraController::class, 'index'])->name('compras.index');
    Route::post('/compras', [CompraController::class, 'store'])->name('compras.store');

    // Módulo Ventas (POS)
    Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
    Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');

    // Módulo Caja
    Route::get('/caja', [CajaController::class, 'index'])->name('caja.index');
    Route::post('/caja/abrir', [CajaController::class, 'abrir'])->name('caja.abrir');
    Route::post('/caja/egreso', [CajaController::class, 'registrarEgreso'])->name('caja.egreso');
    Route::post('/caja/cerrar', [CajaController::class, 'cerrar'])->name('caja.cerrar');

    // Módulo Plan de Cuentas
    Route::get('/plan-cuentas', [PlanCuentasController::class, 'index'])->name('plan-cuentas.index');
    Route::post('/plan-cuentas/subcuenta', [PlanCuentasController::class, 'storeSubcuenta'])->name('plan-cuentas.store-subcuenta');

    // Módulo Gestión de Usuarios y Roles (Administradores)
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{id}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::post('/usuarios/{id}/toggle', [UsuarioController::class, 'toggleEstado'])->name('usuarios.toggle');

});

require __DIR__.'/settings.php';
