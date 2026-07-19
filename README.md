# GUESAA SIC - Sistema de Información Contable

Sistema de Información Contable (SIC) desarrollado para la empresa **GUESAA PERÚ E.I.R.L.** Diseñado bajo arquitectura **MVC** con **Laravel 11/12**, **Inertia.js (Vue 3 + TypeScript)** y base de datos **PostgreSQL**.

El software integra las operaciones comerciales de la empresa (Ventas POS, Compras, Inventarios y Caja Chica) con la **generación automática de asientos contables en el Libro Diario** bajo el catálogo del **Plan Contable General Empresarial (PCGE 2026)**.

---

## 🚀 Módulos del Sistema

1. **Dashboard (Panel Principal)**: Métricas KPI en tiempo real (clientes, proveedores, stock crítico, total de ventas y compras) y visualización de los últimos asientos contables generados.
2. **Clientes y Proveedores (Terceros)**: Gestión unificada de personas naturales y jurídicas (RUC/DNI), con control de estado (Activo/Inactivo).
3. **Inventario / Kardex**: Catálogo de productos, control de precios (compra/venta), gestión de categorías y sistema de alertas automáticas para productos con stock menor o igual al mínimo.
4. **Registro de Compras**: Ingreso de facturas/boletas de compras de mercadería con incremento automático de stock en inventario y contabilización automática por naturaleza (`601-401-421`) y destino (`201-611`).
5. **Punto de Venta (POS)**: Módulo interactivo para ventas en mostrador, emisión de comprobantes, cálculo automático de IGV (18%), cobro inmediato en Caja Chica y contabilización de venta (`121-401-701`).
6. **Caja Chica**: Control de caja diaria con saldo inicial, aperturas, cierres, recepción automática de cobros POS y contabilización de egresos menores (`659-101`).
7. **Plan de Cuentas (PCGE 2026)**: Catálogo oficial a 3 dígitos (423 cuentas precargadas) conforme al PCGE 2026, con funcionalidad para que los Administradores agreguen subcuentas personalizadas.
8. **Gestión de Usuarios y Roles**: Panel administrativo para registrar personal, asignar roles (*Administrador*, *Cajero*, *Almacenero*) y controlar los accesos.

---

## 🔐 Matriz de Permisos por Rol

El sistema cuenta con un middleware dinámico (`CheckRole`) que restringe tanto la navegación lateral como el acceso por URL backend:

| Módulo | Ruta | Administrador (`id_rol = 1`) | Cajero (`id_rol = 2`) | Almacenero (`id_rol = 3`) |
|---|---|:---:|:---:|:---:|
| **Dashboard** | `/dashboard` | ✅ Acceso | ✅ Acceso | ✅ Acceso |
| **Clientes y Prov.** | `/terceros` | ✅ Acceso | ✅ Acceso | ✅ Acceso |
| **Inventario / Kardex** | `/inventario` | ✅ Acceso | ❌ Oculto | ✅ Acceso |
| **Registro Compras** | `/compras` | ✅ Acceso | ❌ Oculto | ✅ Acceso |
| **Punto de Venta (POS)** | `/ventas` | ✅ Acceso | ✅ Acceso | ❌ Oculto |
| **Caja Chica** | `/caja` | ✅ Acceso | ✅ Acceso | ❌ Oculto |
| **Plan de Cuentas** | `/plan-cuentas` | ✅ Acceso | ❌ Oculto | ❌ Oculto |
| **Usuarios y Roles** | `/usuarios` | ✅ Acceso | ❌ Oculto | ❌ Oculto |

---

## 📁 Estructura del Proyecto y Leyenda de Componentes

La siguiente estructura detalla los componentes del proyecto, clasificando entre los **generados por el framework/starter kit** y los **desarrollados a medida** para GUESAA PERÚ E.I.R.L.:

```text
Software_Contabilidad_GUESAA/
├── app/                                    # Código fuente principal de la aplicación Backend (PHP)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── [CREADO] CajaController.php         # Gestión de aperturas, cierres y egresos de caja chica
│   │   │   ├── [CREADO] CompraController.php       # Registro de compras e integración de asientos 60-40-42 y 20-61
│   │   │   ├── [CREADO] PlanCuentasController.php   # Consulta del PCGE 2026 y creación de subcuentas
│   │   │   ├── [CREADO] ProductoController.php     # Kardex, stock mínimo y categorías
│   │   │   ├── [CREADO] TerceroController.php      # Clientes y Proveedores
│   │   │   ├── [CREADO] UsuarioController.php      # Administración de usuarios y asignación de roles
│   │   │   └── [CREADO] VentaController.php        # Punto de venta POS e integración de asientos 12-40-70
│   │   ├── Middleware/
│   │   │   ├── [CREADO] CheckRole.php              # Middleware de autorización backend por Matriz de Roles
│   │   │   └── [FRAMEWORK] HandleInertiaRequests.php # Middleware para compartir props de usuario y rol a Vue
│   ├── Models/
│   │   ├── [CREADO] AsientoContable.php            # Modelo de Cabecera del Libro Diario
│   │   ├── [CREADO] CajaChica.php                  # Modelo de Cajas Diarias
│   │   ├── [CREADO] Categoria.php                  # Modelo de Categorías de productos
│   │   ├── [CREADO] Cliente.php                    # Helpers/Scopes para Terceros tipo Cliente
│   │   ├── [CREADO] Compra.php                     # Modelo de Cabecera de Compras
│   │   ├── [CREADO] CuentaPcge.php                 # Modelo del Plan Contable PCGE
│   │   ├── [CREADO] DetalleAsiento.php             # Modelo de Líneas de Debe/Haber
│   │   ├── [CREADO] DetalleCompra.php              # Modelo de Detalle de ítems de compra
│   │   ├── [CREADO] DetalleVenta.php               # Modelo de Detalle de ítems de venta
│   │   ├── [CREADO] MovimientoCaja.php             # Modelo de Ingresos y Egresos de efectivo
│   │   ├── [CREADO] Producto.php                   # Modelo de Productos e Inventario
│   │   ├── [CREADO] Proveedor.php                  # Helpers/Scopes para Terceros tipo Proveedor
│   │   ├── [CREADO] Rol.php                        # Modelo de Roles (Administrador, Cajero, Almacenero)
│   │   ├── [CREADO] Tercero.php                    # Modelo de Clientes / Proveedores
│   │   ├── [CREADO] Usuario.php                    # Modelo de Usuario personalizado adaptado a id_usuario e id_rol
│   │   ├── [CREADO] Venta.php                      # Modelo de Cabecera de Ventas POS
│   │   └── [FRAMEWORK] User.php                    # Modelo predeterminado de Laravel
│   └── Providers/                                  # [FRAMEWORK] Proveedores de servicios de Laravel
├── config/                                         # [FRAMEWORK] Archivos de configuración de Laravel
├── database/                                       # Migraciones y datos iniciales de PostgreSQL
│   ├── migrations/                                 # [CREADO / FRAMEWORK] Migraciones de tablas contables y del sistema
│   └── seeders/
│       └── [CREADO] DatabaseSeeder.php             # Sembrador inicial (Roles, Usuarios, 423 cuentas PCGE, productos)
├── public/                                         # [FRAMEWORK / CREATED] Punto de entrada web y compilados de Vite
├── resources/
│   ├── js/                                         # Código fuente Frontend (Vue 3 + TypeScript)
│   │   ├── components/
│   │   │   ├── [CREADO] AppSidebar.vue             # Barra lateral con filtrado dinámico según id_rol
│   │   │   ├── [CREADO] AppSidebarHeader.vue       # Header con avatar, datos y nombre de rol dinámico
│   │   │   └── ui/                                 # [FRAMEWORK] Componentes Shadcn Vue (Button, Dialog, Dropdown, etc.)
│   │   ├── composables/                            # [FRAMEWORK] Helpers de UI (useAppearance, useInitials)
│   │   ├── layouts/                                # [FRAMEWORK] Layouts de la aplicación (AppLayout, AuthLayout)
│   │   ├── pages/
│   │   │   ├── [CREADO] Dashboard.vue              # Vista principal con métricas KPI y asientos recientes
│   │   │   ├── [CREADO] Caja/Index.vue             # Vista de Caja Chica (Apertura, Egresos, Cierre)
│   │   │   ├── [CREADO] Compras/Index.vue          # Vista de Registro de Compras y pre-asiento contable
│   │   │   ├── [CREADO] Inventario/Index.vue       # Vista de Control de Stock y Categorías
│   │   │   ├── [CREADO] PlanCuentas/Index.vue      # Vista del Catálogo PCGE 2026 a 3 dígitos
│   │   │   ├── [CREADO] Terceros/Index.vue         # Vista de Gestión de Clientes y Proveedores
│   │   │   ├── [CREADO] Usuarios/Index.vue         # Vista de Administración de Usuarios y Roles
│   │   │   ├── [CREADO] Ventas/Index.vue           # Vista del Punto de Venta (POS) y pre-asiento contable
│   │   │   ├── auth/                               # [FRAMEWORK] Vistas de Login, Registro y Autenticación
│   │   │   └── settings/                           # [FRAMEWORK] Vistas de Configuración de Perfil
│   │   ├── app.ts                                  # [FRAMEWORK] Punto de entrada Frontend de Inertia.js
│   │   └── ssr.ts                                  # [FRAMEWORK] Servidor Render Side Server de Inertia
│   └── views/
│       └── app.blade.php                           # [FRAMEWORK] Plantilla HTML raíz de Laravel Inertia
├── routes/
│   ├── [CREADO] web.php                            # Rutas web del sistema con grupos de middleware CheckRole
│   ├── [FRAMEWORK] console.php                     # Rutas de comandos Artisan
│   └── [FRAMEWORK] settings.php                    # Rutas de configuración de perfil y seguridad
├── .env.example                                    # [FRAMEWORK] Plantilla de variables de entorno
├── .gitignore                                      # [CREADO / FRAMEWORK] Reglas de exclusión de Git
├── package.json                                    # [FRAMEWORK] Dependencias de Node.js (Vue, Inertia, Tailwind, Lucide)
├── composer.json                                   # [FRAMEWORK] Dependencias de PHP (Laravel, Fortify, Inertia)
├── vite.config.ts                                  # [FRAMEWORK] Configuración del empaquetador Vite
└── README.md                                       # [CREADO] Manual técnico y comercial del proyecto
```

### 🏷️ Resumen de Origen de Componentes:
- **`[FRAMEWORK]`**: Archivos base generados por Laravel 11/12, Inertia.js starter kit, Shadcn/UI, Fortify y Tailwind CSS.
- **`[CREADO]`**: Archivos desarrollados 100% a medida para implementar las reglas de negocio contables y operativas de **GUESAA PERÚ E.I.R.L.**

---

## 🛠️ Guía de Instalación y Requisitos

### Requisitos Previos
- **PHP** >= 8.2 con extensiones `pdo_pgsql`, `pgsql`, `bcmath`, `mbstring`.
- **Composer** >= 2.x
- **Node.js** >= 18.x y **npm** >= 9.x
- Servidor de Base de Datos **PostgreSQL** instalado y corriendo localmente en el puerto `5432`.

---

### Pasos de Instalación

1. **Clonar el Repositorio Privado**:
   ```bash
   git clone https://github.com/Johusan/Software_Contabilidad_GUESAA.git
   cd Software_Contabilidad_GUESAA
   ```

2. **Instalar Dependencias de Backend (PHP)**:
   ```bash
   composer install
   ```

3. **Instalar Dependencias de Frontend (Node.js)**:
   ```bash
   npm install
   ```

4. **Configurar el Archivo de Entorno (`.env`)**:
   Crea una copia de `.env.example` con el nombre `.env`:
   ```bash
   cp .env.example .env
   ```
   Asegúrate de configurar la conexión a PostgreSQL:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=bd_guesaa_sic
   DB_USERNAME=postgres
   DB_PASSWORD=tu_contraseña
   ```

5. **Generar la Clave de Aplicación**:
   ```bash
   php artisan key:generate
   ```

6. **Ejecutar Migraciones y Sembrar la Base de Datos**:
   Este comando creará las tablas en PostgreSQL e insertará automáticamente los roles, usuarios de prueba, las 423 cuentas del PCGE 2026 a 3 dígitos, categorías y productos iniciales:
   ```bash
   php artisan migrate:fresh --seed
   ```

7. **Compilar los Archivos Frontend**:
   ```bash
   npm run build
   ```

---

## ⚙️ Ejecución en Entorno de Desarrollo

Para iniciar el sistema en modo desarrollo, ejecuta los dos servidores en terminales independientes:

1. **Servidor Backend (Laravel)**:
   ```bash
   php artisan serve
   ```
   *El backend estará escuchando en: `http://127.0.0.1:8000`*

2. **Servidor Frontend / HMR (Vite)**:
   ```bash
   npm run dev
   ```

---

## 🔑 Cuentas de Acceso de Prueba

El seeder precarga los siguientes usuarios de prueba para verificar los permisos por rol:

| Rol | Correo Electrónico | Contraseña | Permisos / Acceso |
|---|---|---|---|
| **Administrador** | `admin@guesaa.com` | `password` | Acceso Total (8 Módulos + Creación de Subcuentas PCGE) |
| **Cajero** | `cajero@guesaa.com` | `password` | Dashboard, Clientes/Prov., POS y Caja Chica |
| **Almacenero** | `almacenero@guesaa.com` | `password` | Dashboard, Clientes/Prov., Inventario/Kardex y Compras |

---

## 👨‍💻 Créditos
Desarrollado para la empresa **GUESAA PERÚ E.I.R.L.**
