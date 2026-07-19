<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';

import { 
    Wallet, 
    Plus, 
    ArrowUpRight, 
    ArrowDownLeft, 
    Lock, 
    Unlock, 
    AlertCircle, 
    Clock, 
    Receipt 
} from '@lucide/vue';

const props = defineProps<{
    cajaActiva: any | null;
    cajasPasadas: any[];
    errors: any;
}>();

const isEgresoModalOpen = ref(false);

const openForm = useForm({
    monto_inicial: 100.00
});

const egresoForm = useForm({
    monto: 0.00,
    glosa: ''
});

const cerrarForm = useForm({});

const submitAbrir = () => {
    openForm.post('/caja/abrir', {
        onSuccess: () => {
            openForm.reset();
        }
    });
};

const submitEgreso = () => {
    egresoForm.post('/caja/egreso', {
        onSuccess: () => {
            isEgresoModalOpen.value = false;
            egresoForm.reset();
        }
    });
};

const submitCerrar = () => {
    if (confirm('¿Está seguro de cerrar la caja y realizar el arqueo diario? Esta acción no se puede deshacer.')) {
        cerrarForm.post('/caja/cerrar');
    }
};

const formatCurrency = (val: number | string) => {
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

// Saldo actual en caja abierta
const saldoCalculado = () => {
    if (!props.cajaActiva) return 0;
    return Number(props.cajaActiva.monto_inicial) + Number(props.cajaActiva.ingresos_ventas) - Number(props.cajaActiva.egresos_varios);
};
defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Control de Caja',
                href: '/caja',
            },
        ],
    },
});
</script>

<template>
    <Head title="Caja y Bancos - GUESAA SIC" />

        <div class="p-6 max-w-7xl mx-auto space-y-6">
            
            <!-- Encabezado -->
            <div class="flex justify-between items-center border-b pb-5 border-zinc-200 dark:border-zinc-800">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-zinc-950 dark:text-zinc-50">Control de Caja Chica</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        Control de ingresos por ventas y egresos varios de caja chica para el arqueo diario.
                    </p>
                </div>
            </div>

            <!-- Alertas globales de Laravel -->
            <div v-if="Object.keys(errors).length > 0" class="rounded-lg bg-red-50 p-4 dark:bg-red-950/20 border border-red-200 dark:border-red-800/40">
                <div class="flex">
                    <AlertCircle class="h-5 w-5 text-red-600 dark:text-red-400" />
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Hubo errores en la operación</h3>
                        <ul class="mt-2 list-disc list-inside text-xs text-red-700 dark:text-red-400 space-y-1">
                            <li v-for="err in errors" :key="err">{{ err }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-3">
                
                <!-- Columna Izquierda: Estado Caja Chica -->
                <div class="md:col-span-2 space-y-6">
                    
                    <!-- CAJA ACTIVA (ABIERTA) -->
                    <div v-if="cajaActiva" class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 shadow-sm space-y-6">
                        <div class="flex justify-between items-center border-b pb-4 border-zinc-100 dark:border-zinc-800">
                            <div class="flex items-center gap-2">
                                <span class="p-2 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-lg">
                                    <Unlock class="h-5 w-5" />
                                </span>
                                <div>
                                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-50">Caja Diaria Abierta</h3>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Abierto por: {{ cajaActiva.usuario?.nombres }}</p>
                                </div>
                            </div>
                            
                            <div class="flex gap-2">
                                <button @click="isEgresoModalOpen = true" class="inline-flex items-center gap-1 text-xs font-semibold bg-zinc-100 dark:bg-zinc-850 border border-zinc-200 dark:border-zinc-700 px-3 py-2 rounded-lg text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-800 transition-colors">
                                    <ArrowUpRight class="h-4 w-4 text-red-500" />
                                    Registrar Egreso
                                </button>
                                <button @click="submitCerrar" class="inline-flex items-center gap-1 text-xs font-semibold bg-red-600 text-white px-3 py-2 rounded-lg hover:bg-red-500 transition-colors">
                                    <Lock class="h-4 w-4" />
                                    Cerrar Caja (Arqueo)
                                </button>
                            </div>
                        </div>

                        <!-- Métricas del Flujo de Efectivo -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            
                            <div class="p-4 rounded-lg bg-zinc-50 dark:bg-zinc-800/40 border">
                                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider block">Monto Inicial</span>
                                <span class="text-lg font-bold text-zinc-900 dark:text-zinc-50 block mt-2">
                                    {{ formatCurrency(cajaActiva.monto_inicial) }}
                                </span>
                            </div>

                            <div class="p-4 rounded-lg bg-zinc-50 dark:bg-zinc-800/40 border">
                                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider block flex items-center gap-1">
                                    Ventas Efectivo
                                    <ArrowDownLeft class="h-3 w-3 text-emerald-500" />
                                </span>
                                <span class="text-lg font-bold text-emerald-600 dark:text-emerald-400 block mt-2">
                                    +{{ formatCurrency(cajaActiva.ingresos_ventas) }}
                                </span>
                            </div>

                            <div class="p-4 rounded-lg bg-zinc-50 dark:bg-zinc-800/40 border">
                                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider block flex items-center gap-1">
                                    Egresos Varios
                                    <ArrowUpRight class="h-3 w-3 text-red-500" />
                                </span>
                                <span class="text-lg font-bold text-red-600 dark:text-red-400 block mt-2">
                                    -{{ formatCurrency(cajaActiva.egresos_varios) }}
                                </span>
                            </div>

                            <div class="p-4 rounded-lg bg-indigo-50/50 dark:bg-indigo-950/10 border border-indigo-100 dark:border-indigo-900/40">
                                <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block">Saldo Actual</span>
                                <span class="text-xl font-black text-indigo-700 dark:text-indigo-300 block mt-1.5">
                                    {{ formatCurrency(saldoCalculado()) }}
                                </span>
                            </div>
                        </div>

                        <!-- Información Contable -->
                        <div class="p-4 rounded-lg bg-zinc-50 dark:bg-zinc-850/50 border border-zinc-150 dark:border-zinc-800 text-xs text-zinc-500">
                            <p class="font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Información de Arqueo para Auditoría:</p>
                            <p>El saldo actual de <strong class="text-zinc-800 dark:text-zinc-200">{{ formatCurrency(saldoCalculado()) }}</strong> representa la suma de los cobros comerciales registrados vía POS más el capital de apertura de caja chico, restando los gastos menores contabilizados. Al momento de cerrar caja, este saldo pasará a consolidarse como el <strong class="text-zinc-850 dark:text-zinc-250">Monto Final</strong> del día.</p>
                        </div>
                    </div>

                    <!-- CAJA CERRADA (FORMULARIO APERTURA) -->
                    <div v-else class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 shadow-sm text-center py-12 space-y-6">
                        <div class="mx-auto w-12 h-12 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-400">
                            <Lock class="h-6 w-6" />
                        </div>
                        <div class="max-w-md mx-auto">
                            <h3 class="text-xl font-bold text-zinc-950 dark:text-zinc-50">Caja Chica Cerrada</h3>
                            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-2">
                                Para poder operar el punto de venta (POS) y cobrar a clientes, debes aperturar un nuevo turno de caja chica ingresando un monto inicial para vuelto (sencillo).
                            </p>
                        </div>

                        <!-- Formulario Apertura -->
                        <form @submit.prevent="submitAbrir" class="max-w-xs mx-auto flex gap-2 justify-center items-end">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider text-left">Monto Inicial (S/.)</label>
                                <input v-model="openForm.monto_inicial" type="number" step="0.01" min="0" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                            </div>
                            <button type="submit" :disabled="openForm.processing" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-sm transition-colors">
                                <Unlock class="h-4 w-4" />
                                Abrir Caja
                            </button>
                        </form>
                    </div>

                </div>

                <!-- Columna Derecha: Resumen Informativo de Caja -->
                <div class="md:col-span-1 space-y-6">
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 shadow-sm space-y-4">
                        <h3 class="text-base font-bold text-zinc-950 dark:text-zinc-50 border-b pb-3 border-zinc-100 dark:border-zinc-800">
                            Arqueo e Integración
                        </h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                            El módulo de caja se vincula de manera directa con las operaciones de ventas. Cada venta registrada al contado suma automáticamente su total a la caja abierta.
                        </p>
                        <div class="space-y-2 border-t pt-3 border-zinc-100 dark:border-zinc-800">
                            <h4 class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Asientos Contables por Egresos:</h4>
                            <p class="text-[11px] text-zinc-500">Cada salida manual por "Caja Chica" genera automáticamente un asiento:</p>
                            <div class="font-mono text-[9px] bg-zinc-50 dark:bg-zinc-950 p-2 rounded border">
                                <div>659 Gastos diversos • DEBE</div>
                                <div>1011 Caja M/N • HABER</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- HISTORIAL DE CAJAS PASADAS -->
            <div class="space-y-4 pt-4">
                <h3 class="text-lg font-bold text-zinc-950 dark:text-zinc-50 flex items-center gap-1.5">
                    <Clock class="h-5 w-5 text-zinc-400" />
                    Historial de Cierres y Arqueos
                </h3>
                
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                            <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-xs uppercase text-zinc-700 dark:text-zinc-300">
                                <tr>
                                    <th class="p-4">Cajero</th>
                                    <th class="p-4">Apertura</th>
                                    <th class="p-4">Cierre</th>
                                    <th class="p-4 text-right">M. Inicial</th>
                                    <th class="p-4 text-right">Ingreso POS</th>
                                    <th class="p-4 text-right">Egreso Chica</th>
                                    <th class="p-4 text-right">Monto Final</th>
                                    <th class="p-4 text-center">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                <tr v-if="cajasPasadas.length === 0">
                                    <td colspan="8" class="p-8 text-center text-zinc-400">No se registran arqueos previos de caja.</td>
                                </tr>
                                <tr v-for="c in cajasPasadas" :key="c.id_caja" class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                                    <td class="p-4 font-medium text-zinc-900 dark:text-zinc-100">{{ c.usuario?.nombres }}</td>
                                    <td class="p-4 text-xs">{{ formatDate(c.fecha_apertura) }}</td>
                                    <td class="p-4 text-xs">{{ c.fecha_cierre ? formatDate(c.fecha_cierre) : '-' }}</td>
                                    <td class="p-4 text-right">{{ formatCurrency(c.monto_inicial) }}</td>
                                    <td class="p-4 text-right text-emerald-600">+{{ formatCurrency(c.ingresos_ventas) }}</td>
                                    <td class="p-4 text-right text-red-600">-{{ formatCurrency(c.egresos_varios) }}</td>
                                    <td class="p-4 text-right font-semibold text-zinc-900 dark:text-zinc-50">{{ formatCurrency(c.monto_final) }}</td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-semibold text-zinc-800 dark:bg-zinc-800 dark:text-zinc-300">
                                            {{ c.estado }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- MODAL REGISTRAR EGRESO -->
            <div v-if="isEgresoModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                <div class="w-full max-w-md rounded-xl border bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 p-6 shadow-xl animate-in fade-in zoom-in-95 duration-150">
                    <h3 class="text-lg font-semibold text-zinc-950 dark:text-zinc-50 border-b pb-3 border-zinc-100 dark:border-zinc-800 flex items-center gap-1">
                        <ArrowUpRight class="h-5 w-5 text-red-500" />
                        Registrar Egreso (Caja Chica)
                    </h3>
                    
                    <form @submit.prevent="submitEgreso" class="mt-4 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Monto del Egreso (S/.)</label>
                            <input v-model="egresoForm.monto" type="number" step="0.01" min="0.01" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-955 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Glosa / Concepto del Gasto</label>
                            <input v-model="egresoForm.glosa" type="text" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Ej: Compra de útiles de limpieza, Pasajes de mensajería" />
                        </div>

                        <div class="flex justify-end gap-2 border-t pt-4 border-zinc-100 dark:border-zinc-800 mt-6">
                            <button type="button" @click="isEgresoModalOpen = false" class="px-4 py-2 border border-zinc-200 dark:border-zinc-850 rounded-lg text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-850/50 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="egresoForm.processing" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-sm transition-colors disabled:opacity-50">
                                Registrar y Contabilizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

    </div>
</template>
