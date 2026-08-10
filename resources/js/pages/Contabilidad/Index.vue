<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { 
    BookOpen, 
    FileText, 
    Scale, 
    Plus, 
    Search, 
    Calendar, 
    CheckCircle2, 
    AlertCircle, 
    Trash2, 
    ArrowRightLeft,
    Filter,
    RotateCcw
} from '@lucide/vue';

const props = defineProps<{
    asientosDiario: any[];
    movimientosMayor: any[];
    balanza: any[];
    cuentasCatalogo: any[];
    filters: {
        fecha_desde?: string;
        fecha_hasta?: string;
    };
    errors: any;
}>();

// Estado de pestañas
const activeTab = ref<'diario' | 'mayor' | 'balanza'>('diario');

// Estado de filtros de fecha
const fechaDesde = ref(props.filters.fecha_desde || '');
const fechaHasta = ref(props.filters.fecha_hasta || '');

const applyFilters = () => {
    router.get('/contabilidad', {
        fecha_desde: fechaDesde.value,
        fecha_hasta: fechaHasta.value
    }, {
        preserveState: true,
        preserveScroll: true
    });
};

const clearFilters = () => {
    fechaDesde.value = '';
    fechaHasta.value = '';
    applyFilters();
};

// Formato de moneda soles
const formatCurrency = (val: number | string) => {
    return new Intl.NumberFormat('es-PE', { style: 'currency', currency: 'PEN' }).format(Number(val));
};

const formatDate = (dateStr: string) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('es-PE', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
};

// --- LIBRO MAYOR (LÓGICA) ---
const mayorSearchQuery = ref('');
const selectedCuentaCodigo = ref<string>('');

// Lista de cuentas con movimiento para el menú del mayor
const cuentasConMovimiento = computed(() => {
    const map = new Map<string, { codigo: string; nombre: string; count: number }>();
    
    props.movimientosMayor.forEach(m => {
        if (!map.has(m.codigo_cuenta)) {
            map.set(m.codigo_cuenta, {
                codigo: m.codigo_cuenta,
                nombre: m.nombre_cuenta,
                count: 0
            });
        }
        map.get(m.codigo_cuenta)!.count++;
    });

    const list = Array.from(map.values());
    
    // Auto-seleccionar la primera cuenta si ninguna está seleccionada
    if (list.length > 0 && !selectedCuentaCodigo.value) {
        selectedCuentaCodigo.value = list[0].codigo;
    }

    const q = mayorSearchQuery.value.trim().toLowerCase();
    if (!q) return list;
    return list.filter(c => c.codigo.toLowerCase().includes(q) || c.nombre.toLowerCase().includes(q));
});

// Movimientos de la cuenta seleccionada en el Libro Mayor
const mayorDetallesCuenta = computed(() => {
    if (!selectedCuentaCodigo.value) return [];
    
    const movs = props.movimientosMayor.filter(m => m.codigo_cuenta === selectedCuentaCodigo.value);
    
    let saldoAcumulado = 0;
    return movs.map(m => {
        const debe = Number(m.debe || 0);
        const haber = Number(m.haber || 0);
        
        // Regla contable: Cuentas de Activo (1,2,3) y Gastos (6) aumentan en Debe.
        // Cuentas de Pasivo (4), Patrimonio (5) e Ingresos (7) aumentan en Haber.
        const primerDigito = m.codigo_cuenta.charAt(0);
        if (['1', '2', '3', '6'].includes(primerDigito)) {
            saldoAcumulado += (debe - haber);
        } else {
            saldoAcumulado += (haber - debe);
        }

        return {
            ...m,
            saldo: saldoAcumulado
        };
    });
});

const cuentaMayorSeleccionadaObj = computed(() => {
    return props.cuentasCatalogo.find(c => c.codigo_cuenta === selectedCuentaCodigo.value);
});

// Totales acumulados Libro Diario
const totalDiarioDebe = computed(() => {
    let sum = 0;
    props.asientosDiario.forEach(a => {
        a.detalles?.forEach((d: any) => {
            sum += Number(d.debe || 0);
        });
    });
    return sum;
});

const totalDiarioHaber = computed(() => {
    let sum = 0;
    props.asientosDiario.forEach(a => {
        a.detalles?.forEach((d: any) => {
            sum += Number(d.haber || 0);
        });
    });
    return sum;
});

// Totales acumulados Balanza de Comprobación
const balanzaTotales = computed(() => {
    let sumDebe = 0;
    let sumHaber = 0;
    let sumSaldoDeudor = 0;
    let sumSaldoAcreedor = 0;

    props.balanza.forEach(b => {
        sumDebe += Number(b.suma_debe || 0);
        sumHaber += Number(b.suma_haber || 0);
        sumSaldoDeudor += Number(b.saldo_deudor || 0);
        sumSaldoAcreedor += Number(b.saldo_acreedor || 0);
    });

    return {
        sumDebe,
        sumHaber,
        sumSaldoDeudor,
        sumSaldoAcreedor,
        cuadrado: Math.abs(sumDebe - sumHaber) < 0.01 && Math.abs(sumSaldoDeudor - sumSaldoAcreedor) < 0.01
    };
});

// --- MODAL ASIENTO MANUAL (LÓGICA INSPIRADA EN IMAGEN 2) ---
const isManualModalOpen = ref(false);

const manualForm = useForm({
    glosa: '',
    fecha_asiento: new Date().toISOString().substring(0, 10),
    detalles: [
        { codigo_cuenta: '', debe: 0.00, haber: 0.00 },
        { codigo_cuenta: '', debe: 0.00, haber: 0.00 }
    ]
});

const openManualModal = () => {
    manualForm.reset();
    manualForm.fecha_asiento = new Date().toISOString().substring(0, 10);
    if (props.cuentasCatalogo.length >= 2) {
        manualForm.detalles = [
            { codigo_cuenta: props.cuentasCatalogo[0].codigo_cuenta, debe: 0.00, haber: 0.00 },
            { codigo_cuenta: props.cuentasCatalogo[1].codigo_cuenta, debe: 0.00, haber: 0.00 }
        ];
    }
    isManualModalOpen.value = true;
};

const addManualLine = () => {
    const defaultCuenta = props.cuentasCatalogo.length > 0 ? props.cuentasCatalogo[0].codigo_cuenta : '';
    manualForm.detalles.push({
        codigo_cuenta: defaultCuenta,
        debe: 0.00,
        haber: 0.00
    });
};

const removeManualLine = (index: number) => {
    if (manualForm.detalles.length > 2) {
        manualForm.detalles.splice(index, 1);
    }
};

const manualTotalDebe = computed(() => {
    return manualForm.detalles.reduce((acc, line) => acc + Number(line.debe || 0), 0);
});

const manualTotalHaber = computed(() => {
    return manualForm.detalles.reduce((acc, line) => acc + Number(line.haber || 0), 0);
});

const manualDiferencia = computed(() => {
    return Math.abs(manualTotalDebe.value - manualTotalHaber.value);
});

const isManualCuadrado = computed(() => {
    return manualTotalDebe.value > 0 && manualTotalHaber.value > 0 && manualDiferencia.value < 0.01;
});

// Función "Igualar Saldos" (Inspirada en el botón de la Imagen 2)
const igualarSaldos = () => {
    const diff = manualTotalDebe.value - manualTotalHaber.value;
    if (diff === 0) return;

    const lastIndex = manualForm.detalles.length - 1;
    if (lastIndex >= 0) {
        if (diff > 0) {
            // Debe es mayor -> ponemos la diferencia en Haber
            manualForm.detalles[lastIndex].haber = Number((Number(manualForm.detalles[lastIndex].haber) + diff).toFixed(2));
        } else {
            // Haber es mayor -> ponemos la diferencia en Debe
            manualForm.detalles[lastIndex].debe = Number((Number(manualForm.detalles[lastIndex].debe) + Math.abs(diff)).toFixed(2));
        }
    }
};

const submitAsientoManual = () => {
    manualForm.post('/contabilidad/asientos', {
        onSuccess: () => {
            isManualModalOpen.value = false;
            manualForm.reset();
        }
    });
};

const getCuentaNombre = (codigo: string) => {
    const found = props.cuentasCatalogo.find(c => c.codigo_cuenta === codigo);
    return found ? found.denominacion : 'Cuenta Desconocida';
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Contabilidad',
                href: '/contabilidad',
            },
        ],
    },
});
</script>

<template>
    <Head title="Contabilidad y Libros Oficiales - GUESAA SIC" />

    <div class="p-6 max-w-7xl mx-auto space-y-6">
        
        <!-- Encabezado y Barra Principal -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b pb-5 border-zinc-200 dark:border-zinc-800">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-zinc-950 dark:text-zinc-50">Libros Contables</h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                    Libro Diario General, Libro Mayor de Cuentas y Balance de Comprobación (PCGE 2026).
                </p>
            </div>
            
            <div class="flex items-center gap-2">
                <button @click="openManualModal" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">
                    <Plus class="h-4 w-4" />
                    Nuevo Asiento Manual
                </button>
            </div>
        </div>

        <!-- Filtros de Rango de Fechas -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3 flex-wrap text-xs">
                <div class="flex items-center gap-1.5 font-semibold text-zinc-700 dark:text-zinc-300">
                    <Filter class="h-4 w-4 text-indigo-500" />
                    <span>Filtrar por Rango:</span>
                </div>
                
                <div class="flex items-center gap-1">
                    <span class="text-zinc-400">Desde:</span>
                    <input v-model="fechaDesde" type="date" class="rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none" />
                </div>

                <div class="flex items-center gap-1">
                    <span class="text-zinc-400">Hasta:</span>
                    <input v-model="fechaHasta" type="date" class="rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none" />
                </div>

                <button @click="applyFilters" class="px-3 py-1 bg-zinc-850 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white rounded font-medium transition-colors">
                    Aplicar
                </button>
                <button v-if="fechaDesde || fechaHasta" @click="clearFilters" class="px-2 py-1 text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors flex items-center gap-1">
                    <RotateCcw class="h-3 w-3" />
                    Limpiar
                </button>
            </div>

            <!-- Pestañero (Tabs) -->
            <div class="inline-flex rounded-lg p-1 bg-zinc-100 dark:bg-zinc-800/80 text-xs font-semibold">
                <button @click="activeTab = 'diario'" 
                    class="px-4 py-1.5 rounded-md transition-all flex items-center gap-1.5"
                    :class="activeTab === 'diario' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100'">
                    <FileText class="h-4 w-4" />
                    Libro Diario
                </button>
                
                <button @click="activeTab = 'mayor'" 
                    class="px-4 py-1.5 rounded-md transition-all flex items-center gap-1.5"
                    :class="activeTab === 'mayor' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100'">
                    <BookOpen class="h-4 w-4" />
                    Libro Mayor
                </button>

                <button @click="activeTab = 'balanza'" 
                    class="px-4 py-1.5 rounded-md transition-all flex items-center gap-1.5"
                    :class="activeTab === 'balanza' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100'">
                    <Scale class="h-4 w-4" />
                    Balanza de Comprobaciones
                </button>
            </div>
        </div>

        <!-- Mensajes de Error de Laravel -->
        <div v-if="Object.keys(errors).length > 0" class="rounded-lg bg-red-50 p-4 dark:bg-red-950/20 border border-red-200 dark:border-red-800/40">
            <div class="flex">
                <AlertCircle class="h-5 w-5 text-red-600 dark:text-red-400" />
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Hubo un problema al procesar</h3>
                    <ul class="mt-1 list-disc list-inside text-xs text-red-700 dark:text-red-400 space-y-1">
                        <li v-for="err in errors" :key="err">{{ err }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- ==================== PESTAÑA 1: LIBRO DIARIO ==================== -->
        <div v-if="activeTab === 'diario'" class="space-y-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-xs uppercase text-zinc-700 dark:text-zinc-300 border-b border-zinc-200 dark:border-zinc-800">
                            <tr>
                                <th class="p-3">N° Asiento</th>
                                <th class="p-3">Fecha</th>
                                <th class="p-3">Cód. Cuenta</th>
                                <th class="p-3">Nombre de Cuenta (PCGE)</th>
                                <th class="p-3">Glosa / Concepto</th>
                                <th class="p-3 text-right">Debe (S/.)</th>
                                <th class="p-3 text-right">Haber (S/.)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            <template v-if="asientosDiario.length === 0">
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-zinc-400">No hay asientos contables en el período seleccionado.</td>
                                </tr>
                            </template>

                            <template v-for="asiento in asientosDiario" :key="asiento.id_asiento">
                                <!-- Filas de Detalle de cada Asiento -->
                                <tr v-for="(det, idx) in asiento.detalles" :key="det.id_detalle_asiento" class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                    
                                    <!-- N° Asiento y Fecha (solo en la primera línea del asiento) -->
                                    <td v-if="idx === 0" :rowspan="asiento.detalles.length" class="p-3 font-mono text-xs font-bold text-indigo-600 dark:text-indigo-400 bg-zinc-50/30 dark:bg-zinc-950/20 border-r border-zinc-100 dark:border-zinc-800 align-top">
                                        #{{ asiento.id_asiento }}
                                    </td>
                                    
                                    <td v-if="idx === 0" :rowspan="asiento.detalles.length" class="p-3 text-xs text-zinc-600 dark:text-zinc-300 bg-zinc-50/30 dark:bg-zinc-950/20 border-r border-zinc-100 dark:border-zinc-800 align-top">
                                        {{ formatDate(asiento.fecha_asiento) }}
                                    </td>

                                    <!-- Código y Denominación Cuenta -->
                                    <td class="p-3 font-mono text-xs font-semibold text-zinc-900 dark:text-zinc-100">
                                        {{ det.codigo_cuenta }}
                                    </td>

                                    <td class="p-3 text-xs font-medium text-zinc-800 dark:text-zinc-200">
                                        {{ det.cuenta?.denominacion || getCuentaNombre(det.codigo_cuenta) }}
                                    </td>

                                    <!-- Glosa del Asiento (solo en la primera línea) -->
                                    <td v-if="idx === 0" :rowspan="asiento.detalles.length" class="p-3 text-xs text-zinc-500 dark:text-zinc-400 bg-zinc-50/10 dark:bg-zinc-950/10 border-l border-r border-zinc-100 dark:border-zinc-800 align-top">
                                        <div class="font-medium text-zinc-700 dark:text-zinc-300">{{ asiento.glosa }}</div>
                                        <span class="text-[10px] text-zinc-400 uppercase tracking-wider block mt-0.5">Operación: {{ asiento.tipo_operacion }}</span>
                                    </td>

                                    <!-- Debe / Haber -->
                                    <td class="p-3 text-right font-mono text-xs" :class="Number(det.debe) > 0 ? 'font-bold text-emerald-600 dark:text-emerald-400' : 'text-zinc-300 dark:text-zinc-700'">
                                        {{ Number(det.debe) > 0 ? formatCurrency(det.debe) : '-' }}
                                    </td>

                                    <td class="p-3 text-right font-mono text-xs" :class="Number(det.haber) > 0 ? 'font-bold text-blue-600 dark:text-blue-400' : 'text-zinc-300 dark:text-zinc-700'">
                                        {{ Number(det.haber) > 0 ? formatCurrency(det.haber) : '-' }}
                                    </td>
                                </tr>
                                <!-- Separador visual entre asientos -->
                                <tr class="bg-zinc-100/50 dark:bg-zinc-800/40 h-1">
                                    <td colspan="7" class="p-0"></td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-zinc-50 dark:bg-zinc-800/80 font-semibold text-xs text-zinc-900 dark:text-zinc-100 border-t-2 border-zinc-200 dark:border-zinc-700">
                            <tr>
                                <td colspan="5" class="p-3 text-right uppercase tracking-wider">Totales Generales Libro Diario:</td>
                                <td class="p-3 text-right font-mono text-emerald-600 dark:text-emerald-400 font-bold">{{ formatCurrency(totalDiarioDebe) }}</td>
                                <td class="p-3 text-right font-mono text-blue-600 dark:text-blue-400 font-bold">{{ formatCurrency(totalDiarioHaber) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <div class="flex justify-between items-center text-xs text-zinc-400 px-1">
                <span>Total de asientos registrados: {{ asientosDiario.length }}</span>
                <span v-if="Math.abs(totalDiarioDebe - totalDiarioHaber) < 0.01" class="inline-flex items-center text-emerald-600 dark:text-emerald-400 font-medium gap-1">
                    <CheckCircle2 class="h-3.5 w-3.5" />
                    Partida Doble Cuadrada (Debe = Haber)
                </span>
            </div>
        </div>

        <!-- ==================== PESTAÑA 2: LIBRO MAYOR (INSPIRADO EN IMAGEN 1) ==================== -->
        <div v-else-if="activeTab === 'mayor'" class="flex flex-col md:flex-row gap-6 items-start w-full">
            
            <!-- Panel Izquierdo: Lista de Cuentas PCGE con Movimientos -->
            <div class="w-full md:w-80 lg:w-96 shrink-0 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm flex flex-col h-[650px]">
                <div class="mb-3">
                    <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">Buscar Cuenta PCGE</label>
                    <div class="relative">
                        <input v-model="mayorSearchQuery" type="text" placeholder="Código o Nombre de cuenta..." class="w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 pl-8 pr-3 py-1.5 text-xs text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none" />
                        <Search class="absolute left-2.5 top-2 h-3.5 w-3.5 text-zinc-400" />
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto space-y-1 pr-1 border-t pt-2 border-zinc-100 dark:border-zinc-800">
                    <div v-if="cuentasConMovimiento.length === 0" class="p-4 text-center text-xs text-zinc-400">
                        No hay cuentas con movimiento.
                    </div>

                    <button v-for="c in cuentasConMovimiento" :key="c.codigo" 
                        @click="selectedCuentaCodigo = c.codigo"
                        class="w-full text-left p-2.5 rounded-lg text-xs transition-colors flex items-center justify-between"
                        :class="selectedCuentaCodigo === c.codigo ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-700 dark:text-indigo-300 font-bold border border-indigo-200 dark:border-indigo-800 shadow-sm' : 'hover:bg-zinc-50 dark:hover:bg-zinc-800/50 text-zinc-700 dark:text-zinc-300'">
                        <div class="truncate pr-2">
                            <span class="font-mono text-zinc-950 dark:text-zinc-100 block font-bold">{{ c.codigo }}</span>
                            <span class="text-[11px] font-normal truncate block text-zinc-500 dark:text-zinc-400">{{ c.nombre }}</span>
                        </div>
                        <span class="px-1.5 py-0.5 text-[10px] rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-mono shrink-0">
                            {{ c.count }} movs
                        </span>
                    </button>
                </div>
            </div>

            <!-- Panel Derecho: Tabla de Movimientos del Mayor de la Cuenta Seleccionada -->
            <div class="flex-1 min-w-0 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm flex flex-col h-[650px]">
                <div v-if="cuentaMayorSeleccionadaObj" class="border-b pb-3 border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
                    <div>
                        <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 font-mono">CUENTA # {{ cuentaMayorSeleccionadaObj.codigo_cuenta }}</span>
                        <h3 class="text-lg font-bold text-zinc-950 dark:text-zinc-50">{{ cuentaMayorSeleccionadaObj.denominacion }}</h3>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-zinc-400 uppercase tracking-wider block">Saldo Final Acumulado</span>
                        <span class="text-lg font-black text-emerald-600 dark:text-emerald-400 font-mono">
                            {{ formatCurrency(mayorDetallesCuenta.length > 0 ? mayorDetallesCuenta[mayorDetallesCuenta.length - 1].saldo : 0) }}
                        </span>
                    </div>
                </div>

                <div class="flex-1 overflow-x-auto overflow-y-auto mt-3">
                    <table class="w-full text-left text-xs text-zinc-500 dark:text-zinc-400">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-[10px] uppercase text-zinc-700 dark:text-zinc-300 sticky top-0">
                            <tr>
                                <th class="p-2.5">Asiento</th>
                                <th class="p-2.5">Fecha</th>
                                <th class="p-2.5">Tipo</th>
                                <th class="p-2.5">Concepto / Glosa</th>
                                <th class="p-2.5 text-right">Debe (S/.)</th>
                                <th class="p-2.5 text-right">Haber (S/.)</th>
                                <th class="p-2.5 text-right">Saldo (S/.)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            <tr v-if="mayorDetallesCuenta.length === 0">
                                <td colspan="7" class="p-8 text-center text-zinc-400">Seleccione una cuenta contable del menú izquierdo.</td>
                            </tr>
                            <tr v-for="m in mayorDetallesCuenta" :key="m.id_detalle_asiento" class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                                <td class="p-2.5 font-mono font-semibold text-indigo-600 dark:text-indigo-400">#{{ m.id_asiento }}</td>
                                <td class="p-2.5">{{ formatDate(m.fecha_asiento) }}</td>
                                <td class="p-2.5 font-medium uppercase text-[10px] text-zinc-400">{{ m.tipo_operacion }}</td>
                                <td class="p-2.5 font-medium text-zinc-900 dark:text-zinc-100 max-w-xs truncate">{{ m.glosa }}</td>
                                <td class="p-2.5 text-right font-mono text-emerald-600 font-semibold">{{ Number(m.debe) > 0 ? formatCurrency(m.debe) : '-' }}</td>
                                <td class="p-2.5 text-right font-mono text-blue-600 font-semibold">{{ Number(m.haber) > 0 ? formatCurrency(m.haber) : '-' }}</td>
                                <td class="p-2.5 text-right font-mono font-bold text-zinc-900 dark:text-zinc-100 bg-zinc-50/50 dark:bg-zinc-950/20">{{ formatCurrency(m.saldo) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ==================== PESTAÑA 3: BALANCE DE COMPROBACIÓN (BALANZA) ==================== -->
        <div v-else-if="activeTab === 'balanza'" class="space-y-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-xs uppercase text-zinc-700 dark:text-zinc-300 border-b border-zinc-200 dark:border-zinc-800">
                            <tr>
                                <th class="p-3">N°</th>
                                <th class="p-3">Código Cuenta</th>
                                <th class="p-3">Nombre de Cuenta (PCGE)</th>
                                <th class="p-3 text-right">Sumas Debe (S/.)</th>
                                <th class="p-3 text-right">Sumas Haber (S/.)</th>
                                <th class="p-3 text-right text-emerald-600">Saldo Deudor (S/.)</th>
                                <th class="p-3 text-right text-blue-600">Saldo Acreedor (S/.)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            <tr v-if="balanza.length === 0">
                                <td colspan="7" class="p-8 text-center text-zinc-400">No se registraron cuentas con movimiento en el período.</td>
                            </tr>
                            <tr v-for="(b, idx) in balanza" :key="b.codigo_cuenta" class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="p-3 text-xs text-zinc-400 font-mono">{{ idx + 1 }}</td>
                                <td class="p-3 font-mono font-bold text-xs text-zinc-900 dark:text-zinc-100">{{ b.codigo_cuenta }}</td>
                                <td class="p-3 text-xs font-medium text-zinc-800 dark:text-zinc-200">{{ b.nombre_cuenta }}</td>
                                <td class="p-3 text-right font-mono text-xs font-medium">{{ formatCurrency(b.suma_debe) }}</td>
                                <td class="p-3 text-right font-mono text-xs font-medium">{{ formatCurrency(b.suma_haber) }}</td>
                                <td class="p-3 text-right font-mono text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50/10">{{ b.saldo_deudor > 0 ? formatCurrency(b.saldo_deudor) : '-' }}</td>
                                <td class="p-3 text-right font-mono text-xs font-bold text-blue-600 dark:text-blue-400 bg-blue-50/10">{{ b.saldo_acreedor > 0 ? formatCurrency(b.saldo_acreedor) : '-' }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-zinc-100 dark:bg-zinc-800 font-bold text-xs text-zinc-950 dark:text-zinc-50 border-t-2 border-zinc-300 dark:border-zinc-700">
                            <tr>
                                <td colspan="3" class="p-3 text-right uppercase tracking-wider">Totales de la Balanza de Comprobación:</td>
                                <td class="p-3 text-right font-mono font-black text-indigo-600 dark:text-indigo-400">{{ formatCurrency(balanzaTotales.sumDebe) }}</td>
                                <td class="p-3 text-right font-mono font-black text-indigo-600 dark:text-indigo-400">{{ formatCurrency(balanzaTotales.sumHaber) }}</td>
                                <td class="p-3 text-right font-mono font-black text-emerald-600 dark:text-emerald-400">{{ formatCurrency(balanzaTotales.sumSaldoDeudor) }}</td>
                                <td class="p-3 text-right font-mono font-black text-blue-600 dark:text-blue-400">{{ formatCurrency(balanzaTotales.sumSaldoAcreedor) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="flex justify-between items-center text-xs text-zinc-400 px-1">
                <span>Cuentas contables procesadas: {{ balanza.length }}</span>
                <span v-if="balanzaTotales.cuadrado" class="inline-flex items-center text-emerald-600 dark:text-emerald-400 font-bold gap-1">
                    <CheckCircle2 class="h-4 w-4" />
                    Balanza de Comprobaciones Perfectamente Cuadrada
                </span>
            </div>
        </div>

        <!-- ==================== MODAL ASIENTO MANUAL (INSPIRADO EN IMAGEN 2) ==================== -->
        <div v-if="isManualModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="w-full max-w-4xl rounded-xl border bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 p-6 shadow-xl animate-in fade-in zoom-in-95 duration-150 flex flex-col max-h-[90vh]">
                
                <div class="flex justify-between items-center border-b pb-3 border-zinc-100 dark:border-zinc-800">
                    <h3 class="text-lg font-bold text-zinc-950 dark:text-zinc-50 flex items-center gap-2">
                        <FileText class="h-5 w-5 text-indigo-600" />
                        Modificación / Registro de Asiento Contable Manual
                    </h3>
                </div>
                
                <form @submit.prevent="submitAsientoManual" class="mt-4 flex-1 overflow-y-auto space-y-4 pr-1">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Fecha Asiento</label>
                            <input v-model="manualForm.fecha_asiento" type="date" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Glosa / Descripción General del Asiento</label>
                            <input v-model="manualForm.glosa" type="text" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Ej: Registro de provisión de servicios de mantenimiento, Pago de alquileres" />
                        </div>
                    </div>

                    <!-- Tabla de Líneas Contables -->
                    <div class="border rounded-lg border-zinc-200 dark:border-zinc-800 overflow-hidden">
                        <table class="w-full text-left text-xs text-zinc-500 dark:text-zinc-400">
                            <thead class="bg-zinc-50 dark:bg-zinc-800/50 uppercase text-zinc-700 dark:text-zinc-300">
                                <tr>
                                    <th class="p-2.5 w-1/3">Cuenta PCGE</th>
                                    <th class="p-2.5 w-1/3">Nombre de la Cuenta</th>
                                    <th class="p-2.5 w-1/6 text-right">Debe (S/.)</th>
                                    <th class="p-2.5 w-1/6 text-right">Haber (S/.)</th>
                                    <th class="p-2.5 text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                <tr v-for="(line, index) in manualForm.detalles" :key="index">
                                    <td class="p-2">
                                        <select v-model="line.codigo_cuenta" required class="w-full rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-2 py-1.5 text-xs text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none">
                                            <option v-for="c in cuentasCatalogo" :key="c.codigo_cuenta" :value="c.codigo_cuenta">
                                                {{ c.codigo_cuenta }} - {{ c.denominacion }}
                                            </option>
                                        </select>
                                    </td>
                                    <td class="p-2 font-medium text-zinc-800 dark:text-zinc-200 truncate">
                                        {{ getCuentaNombre(line.codigo_cuenta) }}
                                    </td>
                                    <td class="p-2">
                                        <input v-model="line.debe" type="number" step="0.01" min="0" class="w-full text-right rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none" />
                                    </td>
                                    <td class="p-2">
                                        <input v-model="line.haber" type="number" step="0.01" min="0" class="w-full text-right rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none" />
                                    </td>
                                    <td class="p-2 text-center">
                                        <button type="button" @click="removeManualLine(index)" :disabled="manualForm.detalles.length <= 2" class="p-1 text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded disabled:opacity-30">
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-between items-center">
                        <button type="button" @click="addManualLine" class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                            <Plus class="h-4 w-4" />
                            + Agregar Fila de Cuenta
                        </button>

                        <!-- Botón de Igualar Saldos (Inspirado en la Imagen 2) -->
                        <button type="button" @click="igualarSaldos" class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 dark:bg-amber-950/30 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800/40 rounded text-xs font-semibold hover:bg-amber-100 transition-colors">
                            <ArrowRightLeft class="h-3.5 w-3.5 text-amber-600" />
                            Igualar Saldos
                        </button>
                    </div>

                    <!-- Resumen Totales Asiento -->
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-800/40 rounded-lg border border-zinc-150 dark:border-zinc-800 flex justify-between items-center text-xs">
                        <div class="flex items-center gap-4">
                            <span>Total Débito (Debe): <strong class="text-emerald-600 font-mono text-sm font-bold">{{ formatCurrency(manualTotalDebe) }}</strong></span>
                            <span>Total Crédito (Haber): <strong class="text-blue-600 font-mono text-sm font-bold">{{ formatCurrency(manualTotalHaber) }}</strong></span>
                        </div>

                        <div>
                            <span v-if="isManualCuadrado" class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-bold">
                                <CheckCircle2 class="h-4 w-4" />
                                Asiento Cuadrado
                            </span>
                            <span v-else class="text-red-600 dark:text-red-400 font-semibold">
                                Descuadre: {{ formatCurrency(manualDiferencia) }}
                            </span>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 border-t pt-4 border-zinc-100 dark:border-zinc-800 mt-6">
                        <button type="button" @click="isManualModalOpen = false" class="px-4 py-2 border border-zinc-200 dark:border-zinc-850 rounded-lg text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-850/50 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="manualForm.processing || !isManualCuadrado" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-sm transition-colors disabled:opacity-50">
                            Grabar Asiento
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>
