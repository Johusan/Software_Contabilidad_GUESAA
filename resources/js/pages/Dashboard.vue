<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { 
    Users, 
    Package, 
    TrendingUp, 
    TrendingDown, 
    FileText, 
    ArrowRight,
    CircleDollarSign,
    BadgeAlert,
    Clock,
    Calendar,
    Activity
} from '@lucide/vue';

defineProps<{
    totalClientes: number;
    totalProveedores: number;
    totalProductos: number;
    productosBajoStock: number;
    totalVentas: string | number;
    totalCompras: string | number;
    ventasCount: number;
    comprasCount: number;
    asientosRecientes: any[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: '/dashboard',
            },
        ],
    },
});

const page = usePage();
const user = computed(() => (page.props.auth as any).user);

const currentTime = ref('');
const currentDate = ref('');
const greeting = ref('');

const updateTime = () => {
    const now = new Date();
    currentTime.value = now.toLocaleTimeString('es-PE', { hour: '2-digit', minute: '2-digit' });
    currentDate.value = now.toLocaleDateString('es-PE', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    
    const hrs = now.getHours();
    if (hrs < 12) greeting.value = '¡Buenos días!';
    else if (hrs < 19) greeting.value = '¡Buenas tardes!';
    else greeting.value = '¡Buenas noches!';
};

let timer: any;
onMounted(() => {
    updateTime();
    timer = setInterval(updateTime, 60000); // Actualizar cada minuto
});

onUnmounted(() => {
    clearInterval(timer);
});

const formatCurrency = (val: string | number) => {
    return new Intl.NumberFormat('es-PE', { style: 'currency', currency: 'PEN' }).format(Number(val));
};

const formatDate = (dateStr: string) => {
    return new Date(dateStr).toLocaleDateString('es-PE', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Panel de Control - GUESAA SIC" />

    <div class="flex h-full flex-1 flex-col gap-6 p-4 sm:p-6 lg:p-8 bg-gray-50/50 dark:bg-gray-950/20">
        
        <!-- Fila Superior: Tarjeta de Bienvenida Estilo VOTREX y Métricas Clave -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            
            <!-- Tarjeta de Bienvenida GUESAA (Spans 2 cols on lg) -->
            <div class="lg:col-span-2 bg-gradient-to-br from-white to-indigo-50/30 dark:from-gray-900 dark:to-gray-800 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-sm relative overflow-hidden flex flex-col justify-between min-h-[220px] transition-all hover:shadow-md">
                <!-- Blobs Decorativos de Fondo -->
                <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-indigo-100 dark:bg-indigo-900/20 rounded-full blur-2xl opacity-50"></div>
                <div class="absolute bottom-0 left-1/3 w-24 h-24 bg-purple-100 dark:bg-purple-900/20 rounded-full blur-2xl opacity-50"></div>
                
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center text-gray-500 dark:text-gray-400 text-sm font-medium">
                            <Clock class="w-4 h-4 mr-2 text-indigo-500" />
                            {{ currentTime }}
                        </div>
                        <div class="flex items-center text-gray-500 dark:text-gray-400 text-xs font-medium capitalize">
                            <Calendar class="w-3.5 h-3.5 mr-1.5 text-indigo-500" />
                            {{ currentDate }}
                        </div>
                    </div>
                    
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-1 tracking-tight">
                        {{ greeting }}
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300 text-lg">
                        Bienvenido de nuevo, <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ user?.nombres }} {{ user?.apellidos }}</span>
                    </p>
                </div>
                
                <div class="relative z-10 flex items-end justify-between mt-6">
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-1">Estado del Servidor Contable</p>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-semibold text-gray-600 dark:text-gray-300">Base de Datos PostgreSQL Conectada</span>
                        </div>
                    </div>
                    <span class="text-[10px] bg-indigo-100 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-400 px-2 py-0.5 rounded font-mono font-medium">SIC v2.1</span>
                </div>
            </div>

            <!-- Métrica 1: Ventas -->
            <div class="bg-gradient-to-br from-indigo-50/50 to-white dark:from-indigo-950/10 dark:to-gray-900 border border-indigo-100 dark:border-indigo-900/30 rounded-2xl p-6 shadow-sm relative overflow-hidden hover:shadow-md transition-all flex flex-col justify-between">
                <div class="absolute top-3 right-3 w-1.5 h-1.5 bg-emerald-400 rounded-full"></div>
                <div>
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center mb-4 shadow-sm">
                        <TrendingUp class="w-5 h-5" />
                    </div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block">Ventas al Contado (POS)</span>
                    <h3 class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">
                        {{ formatCurrency(totalVentas) }}
                    </h3>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-400 mt-4 border-t pt-2 border-gray-100 dark:border-gray-800">
                    <span class="font-medium text-indigo-600 dark:text-indigo-400">{{ ventasCount }} boletas/facturas</span>
                    <span>Hoy</span>
                </div>
            </div>

            <!-- Métrica 2: Compras -->
            <div class="bg-gradient-to-br from-purple-50/50 to-white dark:from-purple-950/10 dark:to-gray-900 border border-purple-100 dark:border-purple-900/30 rounded-2xl p-6 shadow-sm relative overflow-hidden hover:shadow-md transition-all flex flex-col justify-between">
                <div class="absolute top-3 right-3 w-1.5 h-1.5 bg-indigo-400 rounded-full"></div>
                <div>
                    <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center mb-4 shadow-sm">
                        <TrendingDown class="w-5 h-5" />
                    </div>
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400 block">Compras del Periodo</span>
                    <h3 class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">
                        {{ formatCurrency(totalCompras) }}
                    </h3>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-400 mt-4 border-t pt-2 border-gray-100 dark:border-gray-800">
                    <span class="font-medium text-purple-600 dark:text-purple-400">{{ comprasCount }} facturas</span>
                    <span>Proveedores</span>
                </div>
            </div>
        </div>

        <!-- Fila de Tarjetas Secundarias: Catálogo & Stock -->
        <div class="grid gap-6 md:grid-cols-4">
            
            <!-- Productos en Catálogo -->
            <div class="rounded-xl border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-800 p-5 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Productos en Catálogo</span>
                    <div class="p-2 bg-indigo-50 text-indigo-600 dark:bg-indigo-950/30 dark:text-indigo-400 rounded-lg">
                        <Package class="w-4 h-4" />
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ totalProductos }}</span>
                    <span class="text-xs text-gray-450">ítems activos</span>
                </div>
            </div>

            <!-- Clientes -->
            <div class="rounded-xl border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-800 p-5 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Clientes</span>
                    <div class="p-2 bg-indigo-50 text-indigo-600 dark:bg-indigo-950/30 dark:text-indigo-400 rounded-lg">
                        <Users class="w-4 h-4" />
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ totalClientes }}</span>
                    <span class="text-xs text-gray-450">registrados</span>
                </div>
            </div>

            <!-- Proveedores -->
            <div class="rounded-xl border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-800 p-5 shadow-sm hover:shadow-md transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Proveedores</span>
                    <div class="p-2 bg-indigo-50 text-indigo-600 dark:bg-indigo-950/30 dark:text-indigo-400 rounded-lg">
                        <Users class="w-4 h-4" />
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ totalProveedores }}</span>
                    <span class="text-xs text-gray-450">asociados</span>
                </div>
            </div>

            <!-- Alerta Stock -->
            <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-5 shadow-sm hover:shadow-md transition-all"
                :class="productosBajoStock > 0 ? 'bg-amber-50/50 border-amber-200 dark:bg-amber-950/10 dark:border-amber-900/40' : 'bg-white dark:bg-gray-900'">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider" :class="productosBajoStock > 0 ? 'text-amber-700 dark:text-amber-350' : 'text-gray-400'">
                        Stock Mínimo
                    </span>
                    <div class="p-2 rounded-lg" :class="productosBajoStock > 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-gray-50 text-gray-400'">
                        <BadgeAlert class="w-4 h-4" />
                    </div>
                </div>
                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-2xl font-bold" :class="productosBajoStock > 0 ? 'text-amber-700 dark:text-amber-400' : 'text-gray-900 dark:text-white'">
                        {{ productosBajoStock }}
                    </span>
                    <span class="text-xs" :class="productosBajoStock > 0 ? 'text-amber-600/80 dark:text-amber-450/80' : 'text-gray-450'">
                        requieren compra
                    </span>
                </div>
            </div>
        </div>

        <!-- Fila de Paneles de Operación y Libro Diario -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            
            <!-- Panel Izquierdo: Acceso Rápido y Resumen -->
            <div class="rounded-xl border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-800 p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white border-b pb-3 border-gray-100 dark:border-gray-800 flex items-center gap-2">
                        <Activity class="w-4.5 h-4.5 text-indigo-600" />
                        Accesos Rápidos a Módulos
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 leading-relaxed">
                        Gestiona de forma directa las operaciones comerciales diarias integradas con el Plan Contable General Empresarial (PCGE).
                    </p>
                    
                    <div class="mt-4 space-y-2.5">
                        <Link href="/ventas" class="w-full flex items-center justify-between p-3 rounded-lg border border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 text-sm font-semibold text-gray-700 dark:text-gray-300 transition-colors">
                            <span>Cobros y Facturación POS</span>
                            <ArrowRight class="w-4 h-4 text-indigo-500" />
                        </Link>
                        <Link href="/compras" class="w-full flex items-center justify-between p-3 rounded-lg border border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 text-sm font-semibold text-gray-700 dark:text-gray-300 transition-colors">
                            <span>Ingreso de Compras</span>
                            <ArrowRight class="w-4 h-4 text-indigo-500" />
                        </Link>
                        <Link href="/caja" class="w-full flex items-center justify-between p-3 rounded-lg border border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 text-sm font-semibold text-gray-700 dark:text-gray-300 transition-colors">
                            <span>Control de Caja Chica</span>
                            <ArrowRight class="w-4 h-4 text-indigo-500" />
                        </Link>
                    </div>
                </div>

                <div class="mt-6 border-t pt-4 border-gray-100 dark:border-gray-800">
                    <Link href="/terceros" class="w-full inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors gap-2">
                        Gestionar Directorio de Terceros
                    </Link>
                </div>
            </div>

            <!-- Panel Derecho: Asientos Contables Generados Automáticamente (Spans 2 cols) -->
            <div class="rounded-xl border bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-800 p-6 xl:col-span-2 shadow-sm">
                <div class="flex justify-between items-center border-b pb-3 border-gray-100 dark:border-gray-800">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <CircleDollarSign class="w-4.5 h-4.5 text-indigo-600" />
                        Libro Diario: Últimos Asientos Generados
                    </h3>
                </div>

                <!-- Tabla de Asientos -->
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                        <thead class="bg-gray-50/50 dark:bg-gray-800/40 text-[11px] uppercase font-bold text-gray-750 dark:text-gray-300 border-b border-gray-100 dark:border-gray-800">
                            <tr>
                                <th class="p-3">Concepto / Glosa</th>
                                <th class="p-3">Origen</th>
                                <th class="p-3">Fecha</th>
                                <th class="p-3 text-right">Detalle Contable (Debe / Haber)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <tr v-if="asientosRecientes.length === 0">
                                <td colspan="4" class="p-8 text-center text-gray-400">
                                    No se registran asientos en el periodo. Realice compras o ventas para generarlos de manera automática.
                                </td>
                            </tr>
                            <tr v-for="asiento in asientosRecientes" :key="asiento.id_asiento" class="hover:bg-gray-50/30 dark:hover:bg-gray-800/20 transition-colors">
                                <td class="p-3">
                                    <p class="font-semibold text-gray-950 dark:text-gray-100 text-xs">{{ asiento.glosa }}</p>
                                    <p class="text-[10px] text-gray-450 mt-0.5">ID Asiento: {{ asiento.id_asiento }} • Usuario: {{ asiento.usuario?.nombres }}</p>
                                </td>
                                <td class="p-3">
                                    <span class="inline-flex items-center rounded px-2 py-0.5 text-[10px] font-bold ring-1 ring-inset"
                                        :class="asiento.tipo_operacion === 'VENTA' ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : (asiento.tipo_operacion === 'COMPRA' ? 'bg-blue-50 text-blue-700 ring-blue-600/20' : 'bg-amber-50 text-amber-700 ring-amber-600/20')">
                                        {{ asiento.tipo_operacion }}
                                    </span>
                                </td>
                                <td class="p-3 text-xs text-gray-400">
                                    {{ formatDate(asiento.fecha_asiento) }}
                                </td>
                                <td class="p-3 text-xs text-right">
                                    <div class="inline-block text-left space-y-1">
                                        <div v-for="det in asiento.detalles" :key="det.id_detalle_asiento" class="flex justify-between gap-4 font-mono text-[11px]">
                                            <span class="bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded text-gray-600 dark:text-gray-450">
                                                {{ det.codigo_cuenta }}
                                            </span>
                                            <span v-if="Number(det.debe) > 0" class="text-emerald-600 dark:text-emerald-450 font-semibold">
                                                D: {{ formatCurrency(det.debe) }}
                                            </span>
                                            <span v-else class="text-indigo-600 dark:text-indigo-400 font-semibold pl-4">
                                                H: {{ formatCurrency(det.haber) }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>