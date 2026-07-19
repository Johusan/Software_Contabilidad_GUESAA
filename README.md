# GUESAA SIC - Sistema Integrado de Contabilidad

> **Desarrollado para GUESAA PERÚ E.I.R.L.**  
> Sistema de gestión contable, comercial y financiera adaptado a la normativa peruana (SUNAT y PCGE 2026).

---

## 📌 Descripción General

**GUESAA SIC** es una plataforma web moderna e integrada diseñada para automatizar la gestión contable y comercial de empresas en el Perú. Permite administrar clientes, proveedores, inventario mediante Kardex valorizado con promedio ponderado, registro de compras con automatización de asientos contables (naturaleza y destino), punto de venta (POS) interactivo, control de caja chica y el catálogo completo del Plan Contable General Empresarial (PCGE 2026).

---

## 🛠️ Stack Tecnológico

- **Backend:** Laravel 13 (PHP 8.3+)
- **Frontend:** Vue 3 (Composition API / TypeScript) + Inertia.js
- **Base de Datos:** PostgreSQL
- **Estilos & UI:** TailwindCSS + Shadcn Vue
- **Temas Visuales:** Claro, Oscuro y Semidark
- **Herramientas de Construcción:** Vite

---

## 🚀 Módulos del Sistema

### 1. 📊 Panel Principal (Dashboard)
- Resumen en tiempo real con Indicadores Clave de Rendimiento (KPIs).
- Estadísticas de ventas acumuladas, compras registradas, clientes y proveedores activos.
- Alertas de productos con stock bajo o crítico.
- Registro visual de los asientos contables más recientes.

### 2. 👥 Clientes y Proveedores (Terceros)
- Registro y gestión unificada de clientes (DNI / RUC) y proveedores comerciales.
- Control de estado (Activo / Inactivo).
- Integración automática con los módulos de Ventas y Compras.

### 3. 📦 Inventario / Kardex Valorizado
- Control de catálogo de productos organizados por categorías.
- Valorización de stock en tiempo real mediante el método de **Promedio Ponderado**.
- Movimientos automáticos de entrada (Compras) y salida (Ventas POS).
- Indicadores visuales de stock mínimo y reposición.

### 4. 🛍️ Registro de Compras
- Registro completo de comprobantes (Facturas, Boletas, Guías de Remisión).
- Desglose automático de Base Imponible, IGV (18%) y Total.
- **Generación Automática de Asientos Contables:**
  - *Asiento de Naturaleza:* Cuenta 6011 (Mercaderías) + 40111 (IGV) vs 4212 (Emitidas).
  - *Asiento de Destino:* Cuenta 20111 (Mercaderías) vs 6111 (Variación de existencias).

### 5. 🛒 Punto de Venta (POS)
- Interfaz rápida e interactiva para la atención y venta en mostrador.
- Búsqueda en tiempo real de productos y asignación de clientes.
- Validación de stock disponible antes de procesar el pago.
- Generación automática de comprobantes y cobro directo vinculado al flujo diario de caja chica.
- Asiento contable de diario automático (1212 vs 40111 + 70111).

### 6. 💼 Caja Chica / Gestión Flujo Diario
- Apertura diaria de caja con saldo inicial configurado.
- Registro de gastos operacionales y egresos menores.
- Acumulación automática por cobros procesados en el POS.
- Arqueo de caja y cierre diario con cálculo de saldos finales.

### 7. 📖 Plan de Cuentas (PCGE 2026)
- Estructura completa de cuentas contables adaptada a la actualización oficial del PCGE 2026.
- Clasificación jerárquica por Elementos (1 al 9), Cuentas, Subcuentas y Divisionarias.
- Registro dinámico de nuevas subcuentas.

### ⚙️ Configuración y Perfil de Usuario
- Actualización de perfil contable (Nombre y Correo).
- Cambio de contraseña segura.
- Selector de temas visuales corporativos (Modo Claro, Modo Oscuro y Modo Semidark).

---

## 💻 Guía de Instalación e Inicialización

### Requisitos Previos
- **PHP** >= 8.3 con extensiones `pdo_pgsql`, `pgsql`, `mbstring`, `openssl`, `tokenizer`, `xml`.
- **Composer** >= 2.0
- **Node.js** >= 18.x & **NPM** >= 9.x
- Servidor de base de datos **PostgreSQL** activo.

---

### Pasos de Instalación

1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/Johusan/Software_Contabilidad_GUESAA.git
   cd Software_Contabilidad_GUESAA
   ```

2. **Instalar dependencias de PHP (Composer):**
   ```bash
   composer install
   ```

3. **Instalar dependencias de JavaScript (NPM):**
   ```bash
   npm install
   ```

4. **Configurar el archivo de entorno `.env`:**
   Copia el archivo de ejemplo:
   ```bash
   cp .env.example .env
   ```
   Asegúrate de configurar los datos de acceso a tu base de datos PostgreSQL en `.env`:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=bd_guesaa_sic
   DB_USERNAME=postgres
   DB_PASSWORD=tu_contraseña
   ```

5. **Generar la clave de la aplicación:**
   ```bash
   php artisan key:generate
   ```

6. **Ejecutar las migraciones de base de datos:**
   ```bash
   php artisan migrate
   ```

7. **Compilar los activos estáticos del frontend:**
   ```bash
   npm run build
   ```

---

## ⚡ Ejecución del Proyecto en Desarrollo

Para ejecutar el sistema en entorno de desarrollo, inicia ambos servidores en paralelo:

1. **Iniciar el servidor backend de Laravel:**
   ```bash
   php artisan serve
   ```
   *El servidor estará disponible en:* `http://127.0.0.1:8000`

2. **Iniciar el servidor de compilación en caliente (Vite):**
   ```bash
   npm run dev
   ```

---

## 📄 Licencia

Este proyecto es de uso exclusivo y propiedad de **GUESAA PERÚ E.I.R.L.**. Todos los derechos reservados.
