<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login } from '@/routes';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { 
    CreditCard, 
    Wallet, 
    Package, 
    Users, 
    BookOpen,
    ArrowRight,
    TrendingUp,
    Shield
} from '@lucide/vue';
</script>

<template>
    <Head title="GUESAA SIC - Sistema Integrado de Contabilidad">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="min-h-screen flex flex-col bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 transition-colors duration-300 font-sans">
        
        <!-- Header / Barra de Navegación -->
        <header class="sticky top-0 z-50 backdrop-blur-md bg-white/70 dark:bg-zinc-950/70 border-b border-zinc-200/80 dark:border-zinc-900/80">
            <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
                
                <!-- Logo & Marca -->
                <div class="flex items-center gap-2.5">
                    <AppLogoIcon class="h-8 w-8 text-indigo-600 dark:text-indigo-400" />
                    <div class="leading-none">
                        <span class="text-md font-bold tracking-tight bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">
                            GUESAA SIC
                        </span>
                        <span class="block text-[9px] text-zinc-500 font-medium tracking-wider uppercase mt-0.5">
                            Sistema Contable
                        </span>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <nav class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="dashboard()"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white shadow-sm transition-colors cursor-pointer"
                    >
                        <span>Ir al Panel</span>
                        <ArrowRight class="h-4 w-4" />
                    </Link>
                    <template v-else>
                        <Link
                            :href="login()"
                            class="px-4 py-2 text-sm font-semibold text-zinc-600 dark:text-zinc-300 hover:text-zinc-900 dark:hover:text-white transition-colors cursor-pointer"
                        >
                            Iniciar Sesión
                        </Link>
                        <Link
                            :href="register()"
                            class="px-4 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-lg shadow-sm transition-colors cursor-pointer"
                        >
                            Registrarse
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Sección Hero -->
        <main class="flex-1">
            <section class="relative overflow-hidden py-20 lg:py-28">
                <!-- Decoración de Luces traseras -->
                <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-indigo-500/10 dark:bg-indigo-500/5 blur-[120px] rounded-full pointer-events-none"></div>
                <div class="absolute top-1/3 left-1/3 w-[300px] h-[300px] bg-purple-500/10 dark:bg-purple-500/5 blur-[100px] rounded-full pointer-events-none"></div>

                <div class="max-w-5xl mx-auto px-6 text-center space-y-8 relative">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-indigo-100 dark:border-indigo-900/60 bg-indigo-50/50 dark:bg-indigo-950/20 text-xs font-semibold text-indigo-600 dark:text-indigo-400">
                        <Shield class="h-3.5 w-3.5" />
                        <span>Software Exclusivo para GUESAA PERÚ E.I.R.L.</span>
                    </div>

                    <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight text-zinc-900 dark:text-white max-w-4xl mx-auto leading-[1.15]">
                        El control financiero de tu empresa, 
                        <span class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 bg-clip-text text-transparent">
                            simplificado
                        </span>
                    </h1>

                    <p class="text-md sm:text-xl text-zinc-500 dark:text-zinc-400 max-w-2xl mx-auto leading-relaxed">
                        Administra facturación POS, control de caja diaria, registro de compras y asientos contables automáticos integrados al Plan de Cuentas PCGE 2026.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                        <Link
                            :href="$page.props.auth.user ? dashboard() : login()"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 text-base font-bold rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white shadow-md shadow-indigo-200 dark:shadow-none transition-all transform hover:-translate-y-0.5 cursor-pointer"
                        >
                            <span>Ingresar al Sistema</span>
                            <ArrowRight class="h-5 w-5" />
                        </Link>
                        <a
                            href="#features"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 text-base font-semibold rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-850/50 transition-colors"
                        >
                            Conocer Módulos
                        </a>
                    </div>
                </div>
            </section>

            <!-- Sección de Características / Módulos -->
            <section id="features" class="py-20 bg-zinc-100/50 dark:bg-zinc-950/40 border-t border-zinc-200/50 dark:border-zinc-900/50">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="text-center max-w-2xl mx-auto space-y-3 mb-16">
                        <h2 class="text-3xl font-bold tracking-tight text-zinc-900 dark:text-white">
                            Todo lo que necesitas para tu contabilidad comercial
                        </h2>
                        <p class="text-zinc-500 dark:text-zinc-400">
                            Módulos diseñados a la medida de los flujos de almacén, caja y asientos contables de la empresa.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        
                        <!-- Caja 1: POS -->
                        <div class="group relative p-6 rounded-2xl border border-zinc-200 dark:border-zinc-900 bg-white dark:bg-zinc-900 hover:border-indigo-500/40 dark:hover:border-indigo-500/30 transition-all duration-300 shadow-sm hover:shadow-md">
                            <div class="h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
                                <CreditCard class="h-5.5 w-5.5" />
                            </div>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-white mt-4 mb-2">Punto de Venta POS</h3>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                Registra facturas y boletas rápidamente con control estricto de stock comercial y validación reactiva de existencias.
                            </p>
                        </div>

                        <!-- Caja 2: Kardex -->
                        <div class="group relative p-6 rounded-2xl border border-zinc-200 dark:border-zinc-900 bg-white dark:bg-zinc-900 hover:border-indigo-500/40 dark:hover:border-indigo-500/30 transition-all duration-300 shadow-sm hover:shadow-md">
                            <div class="h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
                                <Package class="h-5.5 w-5.5" />
                            </div>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-white mt-4 mb-2">Control de Inventario</h3>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                Gestión de productos por categorías con alertas automáticas de stock mínimo para evitar roturas de inventario.
                            </p>
                        </div>

                        <!-- Caja 3: Caja Chica -->
                        <div class="group relative p-6 rounded-2xl border border-zinc-200 dark:border-zinc-900 bg-white dark:bg-zinc-900 hover:border-indigo-500/40 dark:hover:border-indigo-500/30 transition-all duration-300 shadow-sm hover:shadow-md">
                            <div class="h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
                                <Wallet class="h-5.5 w-5.5" />
                            </div>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-white mt-4 mb-2">Caja Diaria y Bancos</h3>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                Administra aperturas y cierres de caja diaria. Registro automático de cobros de ventas y control de egresos menores.
                            </p>
                        </div>

                        <!-- Caja 4: Clientes y Proveedores -->
                        <div class="group relative p-6 rounded-2xl border border-zinc-200 dark:border-zinc-900 bg-white dark:bg-zinc-900 hover:border-indigo-500/40 dark:hover:border-indigo-500/30 transition-all duration-300 shadow-sm hover:shadow-md">
                            <div class="h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
                                <Users class="h-5.5 w-5.5" />
                            </div>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-white mt-4 mb-2">Catálogo de Terceros</h3>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                Registro unificado de clientes y proveedores con validación de tipo de documento (DNI y RUC de 11 dígitos).
                            </p>
                        </div>

                        <!-- Caja 5: Asientos automáticos -->
                        <div class="group relative p-6 rounded-2xl border border-zinc-200 dark:border-zinc-900 bg-white dark:bg-zinc-900 hover:border-indigo-500/40 dark:hover:border-indigo-500/30 transition-all duration-300 shadow-sm hover:shadow-md">
                            <div class="h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
                                <TrendingUp class="h-5.5 w-5.5" />
                            </div>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-white mt-4 mb-2">Integración Contable Automática</h3>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                Cada compra y venta genera de forma automatizada asientos contables de doble entrada por el método de partida doble.
                            </p>
                        </div>

                        <!-- Caja 6: Plan PCGE -->
                        <div class="group relative p-6 rounded-2xl border border-zinc-200 dark:border-zinc-900 bg-white dark:bg-zinc-900 hover:border-indigo-500/40 dark:hover:border-indigo-500/30 transition-all duration-300 shadow-sm hover:shadow-md">
                            <div class="h-10 w-10 rounded-xl bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
                                <BookOpen class="h-5.5 w-5.5" />
                            </div>
                            <h3 class="text-lg font-bold text-zinc-900 dark:text-white mt-4 mb-2">Plan de Cuentas Básico</h3>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                Catálogo estructurado de las cuentas bajo el estándar PCGE 2026. Creación y validación estricta de subcuentas.
                            </p>
                        </div>

                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="py-8 border-t border-zinc-200 dark:border-zinc-900 bg-white dark:bg-zinc-950">
            <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-zinc-500 dark:text-zinc-400">
                <p>&copy; 2026 GUESAA PERÚ E.I.R.L. Todos los derechos reservados.</p>
                <div class="flex items-center gap-2">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span>Prototipo de Sistema de Información Contable - Versión 1.0</span>
                </div>
            </div>
        </footer>

    </div>
</template>
