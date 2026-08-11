# Diagrama Entidad-Relación (DBML - dbdiagram.io)

Este archivo contiene el código fuente en **DBML** (Database Markup Language) que define el diagrama de entidad-relación completo del sistema contable y comercial **GUESAA SIC** (`bd_guesaa_sic`).

---

## 🛠️ Cómo Usar este Código

1. Ingresa a la plataforma web oficial: **[https://dbdiagram.io](https://dbdiagram.io)**.
2. Copia todo el bloque de código encerrado dentro del recuadro `dbml` que se muestra a continuación.
3. Pégalo en el editor de la izquierda en **dbdiagram.io**.
4. ¡Listo! El diagrama interactivo con todas las tablas, claves primarias, foráneas y relaciones se generará automáticamente.

---

## 📐 Código DBML

```dbml
// ==========================================
// PROYECTO: GUESAA SIC - Sistema Contable Comercial
// DATABASE: bd_guesaa_sic (PostgreSQL)
// ESTÁNDAR: DBML (https://dbml.dbdiagram.io/docs/)
// ==========================================

Project GUESAA_SIC {
  database_type: 'PostgreSQL'
  Note: 'Diagrama Entidad-Relación completo para el Software Contabilidad GUESAA SIC'
}

// ------------------------------------------
// MÓDULO: SEGURIDAD Y USUARIOS
// ------------------------------------------

Table roles {
  id_rol integer [pk, increment, note: 'Identificador único del rol']
  nombre_rol varchar(50) [unique, not null, note: 'Nombre del rol: Administrador, Contador, Cajero, Almacenero']
  descripcion varchar(150) [note: 'Descripción opcional de los permisos']
  
  Note: 'Tabla de roles del sistema para control de acceso basado en roles (RBAC)'
}

Table usuarios {
  id_usuario integer [pk, increment, note: 'Identificador único del usuario']
  id_rol integer [not null, ref: > roles.id_rol, note: 'Clave foránea hacia la tabla roles']
  nombres varchar(100) [not null]
  apellidos varchar(100) [not null]
  email varchar(100) [unique, not null]
  password varchar(255) [not null, note: 'Hash de la contraseña']
  estado boolean [not null, default: true, note: 'Estado del usuario: true (activo), false (inactivo)']
  remember_token varchar(100) [note: 'Token de sesión de Laravel']
  
  Note: 'Usuarios registrados que operan la plataforma'
}

// ------------------------------------------
// MÓDULO: INVENTARIO Y PRODUCTOS
// ------------------------------------------

Table categorias {
  id_categoria integer [pk, increment, note: 'Identificador de categoría']
  nombre varchar(100) [not null]
  descripcion varchar(150)
  
  Note: 'Categorías de clasificación de productos'
}

Table productos {
  id_producto integer [pk, increment, note: 'Identificador único del producto']
  id_categoria integer [not null, ref: > categorias.id_categoria, note: 'Categoría a la que pertenece']
  codigo_barras varchar(50) [unique, note: 'Código de barras para lector POS']
  descripcion varchar(150) [not null, note: 'Nombre / Descripción comercial del producto']
  stock_actual integer [not null, default: 0]
  stock_minimo integer [not null, default: 5]
  precio_compra decimal(10,2) [not null, note: 'Costo unitario de compra']
  precio_venta decimal(10,2) [not null, note: 'Precio de venta al por menor']
  precio_mayorista decimal(10,2) [default: 0.00, note: 'Precio unitario por volumen / al por mayor']
  cant_mayorista integer [default: 6, note: 'Cantidad mínima para aplicar precio mayorista']
  estado boolean [not null, default: true]
  
  Note: 'Catálogo de productos con inventario y tarifarios minoristas y mayoristas'
}

// ------------------------------------------
// MÓDULO: CONTACTOS Y TERCEROS
// ------------------------------------------

Table proveedores {
  id_proveedor integer [pk, increment, note: 'Identificador del proveedor']
  ruc varchar(11) [unique, not null, note: 'RUC de 11 dígitos']
  razon_social varchar(150) [not null]
  direccion varchar(200)
  telefono varchar(20)
  estado boolean [not null, default: true]
  
  Note: 'Registro de proveedores para órdenes de compra'
}

Table clientes {
  id_cliente integer [pk, increment, note: 'Identificador del cliente']
  tipo_documento varchar(20) [not null, note: 'DNI, RUC, CE, PASAPORTE']
  num_documento varchar(15) [unique, not null]
  nombre_razon_social varchar(150) [not null]
  direccion varchar(200)
  telefono varchar(20)
  estado boolean [not null, default: true]
  
  Note: 'Clientes para facturación y registro de ventas'
}

// ------------------------------------------
// MÓDULO: COMPRAS Y LOGÍSTICA
// ------------------------------------------

Table compras {
  id_compra integer [pk, increment]
  id_proveedor integer [not null, ref: > proveedores.id_proveedor]
  id_usuario integer [not null, ref: > usuarios.id_usuario]
  tipo_comprobante varchar(20) [not null, note: 'Factura, Boleta, Guía']
  num_comprobante varchar(50) [not null]
  fecha_compra timestamp [not null, default: `now()`]
  subtotal decimal(10,2) [not null]
  igv decimal(10,2) [not null]
  total decimal(10,2) [not null]
  estado varchar(20) [not null, default: 'COMPLETADO']
  
  Note: 'Cabecera de registro de compras de mercadería'
}

Table detalle_compras {
  id_detalle_compra integer [pk, increment]
  id_compra integer [not null, ref: > compras.id_compra]
  id_producto integer [not null, ref: > productos.id_producto]
  cantidad integer [not null]
  precio_unitario decimal(10,2) [not null]
  subtotal decimal(10,2) [not null]
  
  Note: 'Líneas de detalle de ítems comprados'
}

// ------------------------------------------
// MÓDULO: VENTAS Y CAJA DIARIA
// ------------------------------------------

Table ventas {
  id_venta integer [pk, increment]
  id_cliente integer [not null, ref: > clientes.id_cliente]
  id_usuario integer [not null, ref: > usuarios.id_usuario]
  tipo_comprobante varchar(20) [not null, note: 'Boleta, Factura']
  num_comprobante varchar(50) [unique, not null, note: 'Formato correlativo: B001-00000001 / F001-00000001']
  fecha_venta timestamp [not null, default: `now()`]
  subtotal decimal(10,2) [not null]
  igv decimal(10,2) [not null]
  total decimal(10,2) [not null]
  estado varchar(20) [not null, default: 'COMPLETADO']
  
  Note: 'Cabecera de registro de ventas al contado'
}

Table detalle_ventas {
  id_detalle_venta integer [pk, increment]
  id_venta integer [not null, ref: > ventas.id_venta]
  id_producto integer [not null, ref: > productos.id_producto]
  cantidad integer [not null]
  precio_unitario decimal(10,2) [not null]
  descuento decimal(10,2) [not null, default: 0.00]
  subtotal decimal(10,2) [not null]
  
  Note: 'Líneas de detalle de productos vendidos'
}

Table caja_diaria {
  id_caja integer [pk, increment]
  id_usuario integer [not null, ref: > usuarios.id_usuario]
  fecha_apertura timestamp [not null, default: `now()`]
  fecha_cierre timestamp [null]
  monto_inicial decimal(10,2) [not null, note: 'Monto de vuelto inicial']
  ingresos_ventas decimal(10,2) [not null, default: 0.00]
  egresos_varios decimal(10,2) [not null, default: 0.00, note: 'Gastos de caja chica']
  monto_final decimal(10,2) [null, note: 'Monto total en arqueo al cerrar turno']
  estado varchar(20) [not null, default: 'ABIERTA', note: 'ABIERTA / CERRADA']
  
  Note: 'Control de aperturas, arqueos y caja chica por turno'
}

// ------------------------------------------
// MÓDULO: CONTABILIDAD (PCGE 2026)
// ------------------------------------------

Table cuentas_pcge {
  codigo_cuenta varchar(10) [pk, note: 'Código contable PCGE (Ej: 101, 121, 401, 601, 701)']
  denominacion varchar(150) [not null]
  elemento integer [not null, note: 'Elemento contable 1 a 9']
  estado boolean [not null, default: true]
  
  Note: 'Catálogo de Cuentas del Plan Contable General Empresarial (PCGE)'
}

Table asientos_contables {
  id_asiento integer [pk, increment]
  id_usuario integer [not null, ref: > usuarios.id_usuario]
  fecha_asiento timestamp [not null, default: `now()`]
  glosa varchar(255) [not null, note: 'Concepto o explicación del asiento contable']
  tipo_operacion varchar(50) [not null, note: 'VENTA, COMPRA, CAJA, MANUAL']
  referencia_id integer [null, note: 'ID de la venta, compra o caja asociada']
  estado varchar(20) [not null, default: 'ACTIVO']
  
  Note: 'Libro Diario General - Cabecera de Asientos Contables'
}

Table detalle_asientos {
  id_detalle_asiento integer [pk, increment]
  id_asiento integer [not null, ref: > asientos_contables.id_asiento]
  codigo_cuenta varchar(10) [not null, ref: > cuentas_pcge.codigo_cuenta]
  debe decimal(10,2) [not null, default: 0.00]
  haber decimal(10,2) [not null, default: 0.00]
  
  Note: 'Líneas de apunte contable del asiento (Partida Doble: Debe = Haber)'
}

// ------------------------------------------
// GRUPOS DE TABLAS (ORGANIZACIÓN VISUAL)
// ------------------------------------------

TableGroup Seguridad {
  roles
  usuarios
}

TableGroup Catalogo_Productos {
  categorias
  productos
}

TableGroup Contactos {
  proveedores
  clientes
}

TableGroup Operaciones_Comerciales {
  compras
  detalle_compras
  ventas
  detalle_ventas
  caja_diaria
}

TableGroup Contabilidad_Oficial {
  cuentas_pcge
  asientos_contables
  detalle_asientos
}
```
