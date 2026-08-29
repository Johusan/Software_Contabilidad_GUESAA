# GUESAA SIC - Sistema de Información Contable y Comercial

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-2.x-9553E9?style=for-the-badge&logo=inertia&logoColor=white)](https://inertiajs.com)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16.x-4169E1?style=for-the-badge&logo=postgresql&logoColor=white)](https://www.postgresql.org)
[![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com)

Sistema de Información Contable y Comercial web (GUESAA SIC) desarrollado para la empresa **GUESAA PERÚ E.I.R.L.** Diseñado bajo una moderna arquitectura monolítica SPA (Single Page Application) impulsada por **Laravel 11**, **Inertia.js**, **Vue 3 con TypeScript** y motor de base de datos **PostgreSQL 16**.

El software automatiza e integra el ciclo operativo comercial completo (Ventas POS con tarifario mayorista, Compras a proveedores, Control de existencias e inventario valorizado, Apertura/Cierre de Caja Diaria y Gastos de Caja Chica) con la **generación contable automática de asientos de Partida Doble en el Libro Diario**, **Libro Mayor interactivo**, **Balance de Comprobación**, **Estados Financieros oficiales** y **Ratios Gerenciales** bajo el marco del **Plan Contable General Empresarial (PCGE 2026)**.

---

## 🚀 Módulos del Sistema

1. **Dashboard Principal (Panel de Control)**:
   - Resumen ejecutivo de KPIs en tiempo real (total de ventas, compras, saldo en caja y alertas de stock bajo).
   - Acceso rápido a las operaciones frecuentes y visualización de los últimos asientos contables generados.

2. **Gestión de Terceros (Clientes y Proveedores)**:
   - Mantenimiento CRUD unificado de clientes y proveedores con validación de tipo de documento (DNI, RUC, CE).
   - Búsqueda predictiva y control de estado (Activo/Inactivo).

3. **Inventario de Mercaderías y Kardex**:
   - Catálogo de productos organizado por categorías con código de barras único.
   - Configuración de tarifas duales: **Precio Minorista** y **Precio Mayorista** con activación automática por cuota de volumen (`cant_mayorista`).
   - Alertas visuales destacadas para artículos con existencias menores o iguales al stock mínimo programado.

4. **Registro de Compras a Proveedores**:
   - Registro de facturas y boletas de compra con desglose de Base Imponible e IGV (18%).
   - Incremento automático de existencias en el inventario e inserción inmediata de asientos contables de compra por naturaleza (`601-401-421`) y destino (`201-611`).

5. **Punto de Venta POS (Registro de Ventas)**:
   - Modal interactivo de facturación rápida con selector de productos y cliente.
   - Aplicación reactiva de precios al por mayor con etiqueta destacada *"¡Mayorista!"*.
   - Generador automático de numeración correlativa (`B001-XXXXXXXX` y `F001-XXXXXXXX`).
   - Descuento de stock en tiempo real y contabilización de venta (`121-401-701`) y cobro (`101-121`).

6. **Control de Caja Diaria y Caja Chica**:
   - Gestión de turnos de caja comercial con registro de monto inicial para vuelto.
   - Acumulación en tiempo real de los cobros en efectivo provenientes del POS.
   - Panel independiente **Caja Chica & Gastos Menores** para registrar salidas imprevistas (`659-101`) con validación de saldo disponible.
   - Arqueo final y cierre de turno.

7. **Plan Contable General Empresarial (PCGE 2026)**:
   - Árbol jerárquico navegable con más de 420 cuentas y subcuentas oficiales adaptadas al PCGE 2026 peruano.
   - Módulo para que el Administrador o Contador incorpore subcuentas contables personalizadas.

8. **Módulo de Contabilidad Oficial y Estados Financieros**:
   - **Libro Diario General:** Grilla cronológica con número de asiento, fecha, código PCGE, glosa, Debe y Haber con verificación de Partida Doble y filtros avanzados por rango de fechas.
   - **Libro Mayor Interactivo:** Layout dual incompresible *side-by-side* (selector de cuentas a la izquierda y extracto de débitos, créditos y saldo acumulado dinámico a la derecha).
   - **Balance de Comprobación:** Balanza de 8 columnas que totaliza las sumas del mayor y clasifica los saldos deudores y acreedores con comprobación de balance.
   - **Estado de Resultados:** Reporte financiero que enfrenta ingresos (Cuentas 70) vs. costos/gastos (Cuentas 60, 61, 65) determinando la Utilidad Neta del Ejercicio.
   - **Estado de Cambios en el Patrimonio Neto:** Conciliación de saldos iniciales, capital social, utilidades retenidas y resultado del ejercicio.
   - **Balance General (Estado de Situación Financiera):** Presentación clasificada en Activo, Pasivo y Patrimonio Neto con validación matemática de la igualdad `Activo = Pasivo + Patrimonio`.
   - **Panel de Ratios Financieros:** Indicadores en tiempo real de Liquidez (Razón Corriente, Prueba Ácida), Solvencia (Endeudamiento) y Rentabilidad (Margen Neto, ROE).
   - **Asiento Contable Manual:** Formulario interactivo para asientos de ajuste y reclasificación con botón inteligente **Igualar Saldos**.

9. **Gestión de Usuarios y Roles (RBAC)**:
   - Control de accesos y seguridad con contraseñas encriptadas mediante **Bcrypt**.
   - Asignación de roles operativos: *Administrador*, *Contador*, *Cajero* y *Almacenero*.

---

## 🔐 Matriz de Permisos por Rol

El sistema incorpora el middleware `CheckRole` que restringe el menú lateral y las rutas backend según el perfil autenticado:

| Módulo | Ruta | Administrador (`id_rol=1`) | Contador (`id_rol=4`) | Cajero (`id_rol=2`) | Almacenero (`id_rol=3`) |
|:---|:---|:---:|:---:|:---:|:---:|
| **Dashboard** | `/dashboard` | ✅ Acceso | ✅ Acceso | ✅ Acceso | ✅ Acceso |
| **Contactos (Terceros)** | `/terceros` | ✅ Acceso | ✅ Acceso | ✅ Acceso | ✅ Acceso |
| **Inventario / Kardex** | `/inventario` | ✅ Acceso | 👁️ Consulta | ❌ Oculto | ✅ Acceso |
| **Registro Compras** | `/compras` | ✅ Acceso | 👁️ Consulta | ❌ Oculto | ✅ Acceso |
| **Punto de Venta POS** | `/ventas` | ✅ Acceso | ❌ Oculto | ✅ Acceso | ❌ Oculto |
| **Control de Caja** | `/caja` | ✅ Acceso | ❌ Oculto | ✅ Acceso | ❌ Oculto |
| **Plan de Cuentas** | `/plan-cuentas` | ✅ Acceso | ✅ Acceso | ❌ Oculto | ❌ Oculto |
| **Contabilidad Oficial** | `/contabilidad` | ✅ Acceso | ✅ Acceso | ❌ Oculto | ❌ Oculto |
| **Usuarios y Roles** | `/usuarios` | ✅ Acceso | ❌ Oculto | ❌ Oculto | ❌ Oculto |

---

## 📁 Estructura del Proyecto

```text
Software_Contabilidad_GUESAA/
├── app/                                    # Lógica Backend (PHP / Laravel 11)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── CajaController.php         # Control de Caja Diaria y Caja Chica
│   │   │   ├── CompraController.php       # Compras e integración contable (60-40-42 / 20-61)
│   │   │   ├── ContabilidadController.php # Diario, Mayor, Balanza, Estados Financieros y Ratios
│   │   │   ├── PlanCuentasController.php   # Catálogo del PCGE 2026
│   │   │   ├── ProductoController.php     # Inventario, precios mayoristas y categorías
│   │   │   ├── TerceroController.php      # Clientes y Proveedores
│   │   │   ├── UsuarioController.php      # Gestión de usuarios y roles
│   │   │   └── VentaController.php        # POS, correlativos SUNAT y asientos de venta
│   │   └── Middleware/
│   │       ├── CheckRole.php              # Control de acceso por roles (RBAC)
│   │       └── HandleInertiaRequests.php  # Compartición de props reactivos con Vue 3
│   └── Models/
│       ├── AsientoContable.php            # Cabecera de Libro Diario
│       ├── CajaDiaria.php                 # Sesiones de Caja Diaria
│       ├── Categoria.php                  # Categorías de productos
│       ├── Cliente.php                    # Catálogo de Clientes
│       ├── Compra.php                     # Comprobantes de compra
│       ├── CuentaPcge.php                 # Cuentas contables PCGE 2026
│       ├── DetalleAsiento.php             # Apuntes contables Debe / Haber
│       ├── DetalleCompra.php              # Ítems de compra
│       ├── DetalleVenta.php               # Ítems de venta
│       ├── Producto.php                   # Mercaderías y tarifarios
│       ├── Proveedor.php                  # Catálogo de Proveedores
│       ├── Rol.php                        # Roles del sistema
│       ├── Usuario.php                    # Usuarios autenticados
│       └── Venta.php                      # Comprobantes de venta POS
├── database/
│   ├── migrations/                        # Definición de las 14 tablas en PostgreSQL
│   └── seeders/                           # Sembradores de Roles, Usuarios y PCGE 2026
├── resources/
│   ├── js/                                # Frontend (Vue 3 + TypeScript + Tailwind CSS)
│   │   ├── components/                    # Componentes reutilizables (Sidebar, Modales, UI)
│   │   └── pages/                         # Vistas de la aplicación
│   │       ├── Dashboard.vue              # Panel central con métricas comerciales
│   │       ├── Caja/Index.vue             # Panel de Caja Diaria y Caja Chica
│   │       ├── Compras/Index.vue          # Registro de Compras
│   │       ├── Contabilidad/Index.vue     # Módulo Contable (Diario, Mayor, Balanza, EEFF, Ratios)
│   │       ├── Inventario/Index.vue       # Catálogo de Productos y Precios Mayoristas
│   │       ├── PlanCuentas/Index.vue      # Árbol de cuentas del PCGE 2026
│   │       ├── Terceros/Index.vue         # Clientes y Proveedores
│   │       ├── Usuarios/Index.vue         # Administración de Usuarios y Roles
│   │       └── Ventas/Index.vue           # Punto de Venta POS
├── docker-compose.yml                     # Orquestación de contenedores Docker (App + PostgreSQL)
├── Dockerfile                             # Imagen optimizada de PHP 8.2 + Nginx
└── README.md                              # Documentación oficial del proyecto
```

---

## 🛠️ Guías de Instalación y Despliegue

### Opción 1: Despliegue con Docker (Recomendado para Producción)

1. Asegúrate de tener **Docker Desktop** o **Docker Engine** en ejecución.
2. Abre la terminal en la raíz del proyecto y ejecuta:
   ```bash
   docker compose up -d --build
   ```
3. Inicializa las tablas y el catálogo del PCGE 2026:
   ```bash
   docker compose exec app php artisan migrate --seed
   ```
4. Abre tu navegador en: `http://localhost:8000`

---

### Opción 2: Instalación Manual en Servidor o Entorno Local

#### Requisitos Previos:
- **PHP** >= 8.2 con extensiones `pdo_pgsql`, `pgsql`, `mbstring`, `openssl`, `bcmath`, `curl`, `xml`, `fileinfo`.
- **Composer** >= 2.x
- **Node.js** >= 20.x y **NPM**
- **PostgreSQL** 15/16 corriendo en el puerto `5432`.
- **Git**

#### Pasos de Instalación:

1. **Clonar el Repositorio:**
   ```bash
   git clone https://github.com/Johusan/Software_Contabilidad_GUESAA.git
   cd Software_Contabilidad_GUESAA
   ```

2. **Instalar Dependencias:**
   ```bash
   composer install
   npm install
   ```

3. **Configurar el Archivo de Entorno (`.env`):**
   ```bash
   cp .env.example .env
   ```
   Configura las credenciales de tu base de datos PostgreSQL:
   ```ini
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=bd_guesaa_sic
   DB_USERNAME=postgres
   DB_PASSWORD=tu_password_postgres
   ```

4. **Generar Clave de Cifrado y Migrar Base de Datos:**
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```

5. **Compilar Activos de Frontend:**
   ```bash
   npm run build
   ```

6. **Iniciar el Servidor de la Aplicación:**
   ```bash
   php artisan serve --port=8000
   ```
   Ingresa en tu navegador a: `http://localhost:8000`

---

## 🔑 Credenciales de Acceso de Prueba

El sistema incluye las siguientes cuentas predeterminadas para pruebas de validación por rol:

| Rol | Correo Electrónico | Contraseña | Alcance de Permisos |
|:---|:---|:---:|:---|
| **Administrador** | `admin@guesaa.com` | `password` | Acceso total al sistema y configuración general. |
| **Contador** | `admin@guesaa.com` | `password` | Acceso a Libros Contables, Balanza, Estados Financieros y Ratios. |
| **Cajero** | `cajero@guesaa.com` | `password` | Punto de Venta POS, Clientes y Control de Caja. |
| **Almacenero** | `almacenero@guesaa.com` | `password` | Inventario de Mercaderías, Stock y Registro de Compras. |

---

## 👨‍💻 Créditos
Desarrollado para la empresa **GUESAA PERÚ E.I.R.L.**  
Sistema de Información Contable bajo el marco del **PCGE 2026**.
