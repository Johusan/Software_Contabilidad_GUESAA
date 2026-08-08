<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. roles
        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id_rol');
            $table->string('nombre_rol', 50)->unique();
            $table->string('descripcion', 150)->nullable();
        });

        // 2. usuarios
        Schema::create('usuarios', function (Blueprint $table) {
            $table->increments('id_usuario');
            $table->unsignedInteger('id_rol');
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->boolean('estado')->default(true);
            $table->foreign('id_rol')->references('id_rol')->on('roles');
        });

        // 3. categorias
        Schema::create('categorias', function (Blueprint $table) {
            $table->increments('id_categoria');
            $table->string('nombre', 100);
            $table->string('descripcion', 150)->nullable();
        });

        // 4. productos
        Schema::create('productos', function (Blueprint $table) {
            $table->increments('id_producto');
            $table->unsignedInteger('id_categoria');
            $table->string('codigo_barras', 50)->unique()->nullable();
            $table->string('descripcion', 150);
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->decimal('precio_compra', 10, 2);
            $table->decimal('precio_venta', 10, 2);
            $table->boolean('estado')->default(true);
            $table->foreign('id_categoria')->references('id_categoria')->on('categorias');
        });

        // 5. proveedores
        Schema::create('proveedores', function (Blueprint $table) {
            $table->increments('id_proveedor');
            $table->string('ruc', 11)->unique();
            $table->string('razon_social', 150);
            $table->string('direccion', 200)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->boolean('estado')->default(true);
        });

        // 6. clientes
        Schema::create('clientes', function (Blueprint $table) {
            $table->increments('id_cliente');
            $table->string('tipo_documento', 20);
            $table->string('num_documento', 15)->unique();
            $table->string('nombre_razon_social', 150);
            $table->string('direccion', 200)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->boolean('estado')->default(true);
        });

        // 7. compras
        Schema::create('compras', function (Blueprint $table) {
            $table->increments('id_compra');
            $table->unsignedInteger('id_proveedor');
            $table->unsignedInteger('id_usuario');
            $table->string('tipo_comprobante', 20);
            $table->string('num_comprobante', 50);
            $table->timestamp('fecha_compra')->useCurrent();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('igv', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('estado', 20)->default('COMPLETADO');
            $table->foreign('id_proveedor')->references('id_proveedor')->on('proveedores');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios');
        });

        // 8. detalle_compras
        Schema::create('detalle_compras', function (Blueprint $table) {
            $table->increments('id_detalle_compra');
            $table->unsignedInteger('id_compra');
            $table->unsignedInteger('id_producto');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->foreign('id_compra')->references('id_compra')->on('compras')->onDelete('cascade');
            $table->foreign('id_producto')->references('id_producto')->on('productos');
        });

        // 9. ventas
        Schema::create('ventas', function (Blueprint $table) {
            $table->increments('id_venta');
            $table->unsignedInteger('id_cliente');
            $table->unsignedInteger('id_usuario');
            $table->string('tipo_comprobante', 20);
            $table->string('num_comprobante', 50)->unique();
            $table->timestamp('fecha_venta')->useCurrent();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('igv', 10, 2);
            $table->decimal('total', 10, 2);
            $table->string('estado', 20)->default('COMPLETADO');
            $table->foreign('id_cliente')->references('id_cliente')->on('clientes');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios');
        });

        // 10. detalle_ventas
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->increments('id_detalle_venta');
            $table->unsignedInteger('id_venta');
            $table->unsignedInteger('id_producto');
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0.00);
            $table->decimal('subtotal', 10, 2);
            $table->foreign('id_venta')->references('id_venta')->on('ventas')->onDelete('cascade');
            $table->foreign('id_producto')->references('id_producto')->on('productos');
        });

        // 11. caja_diaria
        Schema::create('caja_diaria', function (Blueprint $table) {
            $table->increments('id_caja');
            $table->unsignedInteger('id_usuario');
            $table->timestamp('fecha_apertura')->useCurrent();
            $table->timestamp('fecha_cierre')->nullable();
            $table->decimal('monto_inicial', 10, 2);
            $table->decimal('ingresos_ventas', 10, 2)->default(0.00);
            $table->decimal('egresos_varios', 10, 2)->default(0.00);
            $table->decimal('monto_final', 10, 2)->nullable();
            $table->string('estado', 20)->default('ABIERTA');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios');
        });

        // 12. cuentas_pcge
        Schema::create('cuentas_pcge', function (Blueprint $table) {
            $table->string('codigo_cuenta', 10)->primary();
            $table->string('denominacion', 150);
            $table->integer('elemento');
            $table->boolean('estado')->default(true);
        });

        // 13. asientos_contables
        Schema::create('asientos_contables', function (Blueprint $table) {
            $table->increments('id_asiento');
            $table->unsignedInteger('id_usuario');
            $table->timestamp('fecha_asiento')->useCurrent();
            $table->string('glosa', 255);
            $table->string('tipo_operacion', 50);
            $table->integer('referencia_id')->nullable();
            $table->string('estado', 20)->default('ACTIVO');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios');
        });

        // 14. detalle_asientos
        Schema::create('detalle_asientos', function (Blueprint $table) {
            $table->increments('id_detalle_asiento');
            $table->unsignedInteger('id_asiento');
            $table->string('codigo_cuenta', 10);
            $table->decimal('debe', 10, 2)->default(0.00);
            $table->decimal('haber', 10, 2)->default(0.00);
            $table->foreign('id_asiento')->references('id_asiento')->on('asientos_contables')->onDelete('cascade');
            $table->foreign('codigo_cuenta')->references('codigo_cuenta')->on('cuentas_pcge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_asientos');
        Schema::dropIfExists('asientos_contables');
        Schema::dropIfExists('cuentas_pcge');
        Schema::dropIfExists('caja_diaria');
        Schema::dropIfExists('detalle_ventas');
        Schema::dropIfExists('ventas');
        Schema::dropIfExists('detalle_compras');
        Schema::dropIfExists('compras');
        Schema::dropIfExists('clientes');
        Schema::dropIfExists('proveedores');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('categorias');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('roles');
    }
};
