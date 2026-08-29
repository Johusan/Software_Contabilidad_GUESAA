<script setup lang="ts">
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
    RotateCcw,
    TrendingUp,
    PieChart,
    Landmark,
    Activity,
    Printer
} from '@lucide/vue';
import { ref, computed } from 'vue';

const props = defineProps<{
    asientosDiario: any[];
    movimientosMayor: any[];
    balanza: any[];
    estadoResultados?: {
        cuentas: any[];
        total_debe: number;
        total_haber: number;
        utilidad_neta: number;
    };
    estadoPatrimonio?: {
        saldos_iniciales: any;
        utilidad_neta_fila: any;
        dividendos_fila: any;
        saldos_finales: any;
        total_patrimonio: number;
        capital_social: number;
        utilidades_retenidas: number;
    };
    balanceGeneral?: {
        activo_circulante: any[];
        total_activo_circulante: number;
        activo_no_circulante: any[];
        total_activo_no_circulante: number;
        total_activo: number;
        total_inventarios: number;
        pasivo_corto_plazo: any[];
        total_pasivo_cp: number;
        pasivo_largo_plazo: any[];
        total_pasivo_lp: number;
        total_pasivo: number;
        total_patrimonio: number;
        total_pasivo_patrimonio: number;
        cuadrado: boolean;
    };
    ratios?: {
        liquidez: Record<string, any>;
        solvencia: Record<string, any>;
        rentabilidad: Record<string, any>;
    };
    cuentasCatalogo: any[];
    filters: {
        fecha_desde?: string;
        fecha_hasta?: string;
    };
    errors: any;
}>();

// Estado de pestañas
const activeTab = ref<'diario' | 'mayor' | 'balanza' | 'resultados' | 'patrimonio' | 'balance' | 'ratios'>('diario');

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
    if (!dateStr) {
return '-';
}

    return new Date(dateStr).toLocaleDateString('es-PE', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    });
};

// Imprimir reporte activo
const printReport = () => {
    window.print();
};

// Totales Generales del Libro Diario
const totalDiarioDebe = computed(() => {
    return props.asientosDiario.reduce((acc, asiento) => {
        const sum = (asiento.detalles || []).reduce((s: number, d: any) => s + Number(d.debe || 0), 0);

        return acc + sum;
    }, 0);
});

const totalDiarioHaber = computed(() => {
    return props.asientosDiario.reduce((acc, asiento) => {
        const sum = (asiento.detalles || []).reduce((s: number, d: any) => s + Number(d.haber || 0), 0);

        return acc + sum;
    }, 0);
});

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

    let list = Array.from(map.values());

    if (mayorSearchQuery.value) {
        const q = mayorSearchQuery.value.toLowerCase();
        list = list.filter(c => c.codigo.includes(q) || c.nombre.toLowerCase().includes(q));
    }

    return list;
});

// Si no hay cuenta seleccionada, seleccionar la primera automáticamente
if (cuentasConMovimiento.value.length > 0 && !selectedCuentaCodigo.value) {
    selectedCuentaCodigo.value = cuentasConMovimiento.value[0].codigo;
}

// Movimientos de la cuenta seleccionada en el Mayor
const movimientosCuentaSeleccionada = computed(() => {
    if (!selectedCuentaCodigo.value) {
return [];
}

    return props.movimientosMayor.filter(m => m.codigo_cuenta === selectedCuentaCodigo.value);
});

// Cuenta seleccionada objeto
const cuentaSeleccionadaInfo = computed(() => {
    return props.cuentasCatalogo.find(c => c.codigo_cuenta === selectedCuentaCodigo.value) || {
        codigo_cuenta: selectedCuentaCodigo.value,
        denominacion: 'Cuenta Contable'
    };
});

// Totales de la T Contable seleccionada
const totalMayorDebe = computed(() => {
    return movimientosCuentaSeleccionada.value.reduce((acc, cur) => acc + Number(cur.debe || 0), 0);
});

const totalMayorHaber = computed(() => {
    return movimientosCuentaSeleccionada.value.reduce((acc, cur) => acc + Number(cur.haber || 0), 0);
});

const saldoMayor = computed(() => {
    const d = totalMayorDebe.value;
    const h = totalMayorHaber.value;

    if (d >= h) {
        return { tipo: 'DEUDOR', monto: d - h };
    } else {
        return { tipo: 'ACREEDOR', monto: h - d };
    }
});

// --- BALANZA DE COMPROBACIÓN (LÓGICA) ---
const balanzaSearch = ref('');

const balanzaFiltrada = computed(() => {
    if (!balanzaSearch.value) {
return props.balanza;
}

    const q = balanzaSearch.value.toLowerCase();

    return props.balanza.filter(b => b.codigo_cuenta.includes(q) || b.nombre_cuenta.toLowerCase().includes(q));
});

const totalBalanzaSaldoDeudor = computed(() => {
    return props.balanza.reduce((acc, cur) => acc + Number(cur.saldo_deudor || 0), 0);
});

const totalBalanzaSaldoAcreedor = computed(() => {
    return props.balanza.reduce((acc, cur) => acc + Number(cur.saldo_acreedor || 0), 0);
});

// --- ASIENTO MANUAL (MODAL) ---
const isManualModalOpen = ref(false);

const manualForm = useForm({
    glosa: '',
    fecha_asiento: new Date().toISOString().split('T')[0],
    detalles: [
        { codigo_cuenta: '1011', debe: 0, haber: 0 },
        { codigo_cuenta: '5011', debe: 0, haber: 0 }
    ]
});

const openManualModal = () => {
    manualForm.reset();
    manualForm.glosa = '';
    manualForm.fecha_asiento = new Date().toISOString().split('T')[0];
    manualForm.detalles = [
        { codigo_cuenta: '1011', debe: 0, haber: 0 },
        { codigo_cuenta: '5011', debe: 0, haber: 0 }
    ];
    isManualModalOpen.value = true;
};

const addManualLine = () => {
    manualForm.detalles.push({
        codigo_cuenta: '1011',
        debe: 0,
        haber: 0
    });
};

const removeManualLine = (index: number) => {
    if (manualForm.detalles.length > 2) {
        manualForm.detalles.splice(index, 1);
    }
};

const manualTotalDebe = computed(() => {
    return manualForm.detalles.reduce((acc, cur) => acc + (Number(cur.debe) || 0), 0);
});

const manualTotalHaber = computed(() => {
    return manualForm.detalles.reduce((acc, cur) => acc + (Number(cur.haber) || 0), 0);
});

const manualDiferencia = computed(() => {
    return Math.abs(manualTotalDebe.value - manualTotalHaber.value);
});

const isManualCuadrado = computed(() => {
    return manualTotalDebe.value > 0 && manualDiferencia.value < 0.01;
});

// Función de autocuadre / igualar saldos
const igualarSaldos = () => {
    const diff = manualTotalDebe.value - manualTotalHaber.value;

    if (Math.abs(diff) < 0.01) {
return;
}

    if (diff > 0) {
        manualForm.detalles.push({
            codigo_cuenta: '5011',
            debe: 0,
            haber: Number(diff.toFixed(2))
        });
    } else {
        manualForm.detalles.push({
            codigo_cuenta: '1011',
            debe: Number(Math.abs(diff).toFixed(2)),
            haber: 0
        });
    }
};

const submitManualAsiento = () => {
    if (!isManualCuadrado.value) {
return;
}

    manualForm.post('/contabilidad/asiento-manual', {
        onSuccess: () => {
            isManualModalOpen.value = false;
            manualForm.reset();
        }
    });
};

const getCuentaNombre = (codigo: string) => {
    const c = props.cuentasCatalogo.find(item => item.codigo_cuenta === codigo);

    return c ? c.denominacion : 'Cuenta Desconocida';
};
</script>

<template>
    <Head title="Contabilidad & Estados Financieros - GUESAA SIC" />

    <div class="p-4 sm:p-6 space-y-6 max-w-7xl mx-auto pb-12 w-full min-w-0 max-w-full">
        
        <!-- Encabezado de Página -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-5">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 flex items-center gap-2.5">
                    <Scale class="h-7 w-7 text-indigo-600 dark:text-indigo-400" />
                    Contabilidad Oficial & Estados Financieros
                </h1>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                    Libros electrónicos PCGE 2026, Estados Financieros bajo NIIF y Ratios de Liquidez, Solvencia y Rentabilidad.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button 
                    v-if="['resultados', 'patrimonio', 'balance'].includes(activeTab)"
                    @click="printReport"
                    class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-lg bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 shadow-sm transition-all cursor-pointer">
                    <Printer class="h-4 w-4" />
                    <span>Imprimir / PDF</span>
                </button>

                <button 
                    @click="openManualModal" 
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white shadow-sm transition-all cursor-pointer">
                    <Plus class="h-4 w-4" />
                    <span>+ Asiento Manual (Partida Doble)</span>
                </button>
            </div>
        </div>

        <!-- Barra de Herramientas: Filtros de Fecha & Pestañas de Navegación -->
        <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-4 bg-white dark:bg-zinc-900/60 p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm w-full min-w-0 max-w-full overflow-hidden">
            
            <!-- Filtros de Fecha -->
            <div class="flex flex-wrap items-center gap-2 text-xs w-full sm:w-auto">
                <div class="flex items-center gap-1.5 font-semibold text-zinc-700 dark:text-zinc-300 mr-1">
                    <Filter class="h-3.5 w-3.5 text-indigo-500" />
                    Periodo:
                </div>
                
                <div class="flex items-center gap-1">
                    <span class="text-zinc-400">Desde:</span>
                    <input v-model="fechaDesde" type="date" class="rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none" />
                </div>

                <div class="flex items-center gap-1">
                    <span class="text-zinc-400">Hasta:</span>
                    <input v-model="fechaHasta" type="date" class="rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none" />
                </div>

                <button @click="applyFilters" class="px-3 py-1 bg-zinc-800 hover:bg-zinc-700 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white rounded font-medium transition-colors cursor-pointer">
                    Filtrar
                </button>
                <button v-if="fechaDesde || fechaHasta" @click="clearFilters" class="px-2 py-1 text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors flex items-center gap-1 cursor-pointer">
                    <RotateCcw class="h-3 w-3" />
                    Limpiar
                </button>
            </div>

            <!-- Pestañero en 2 Filas Organizadas (Cambios en Patrimonio debajo de Libro Diario) -->
            <div class="flex flex-col gap-1.5 text-xs font-semibold overflow-x-auto max-w-full pb-1 w-full xl:w-auto">
                
                <!-- Fila 1: Libros Principales y Resultados -->
                <div class="inline-flex rounded-lg p-1 bg-zinc-100 dark:bg-zinc-800/80 gap-1 min-w-max">
                    <button @click="activeTab = 'diario'" 
                        class="px-3 py-1.5 rounded-md transition-all flex items-center gap-1.5 cursor-pointer"
                        :class="activeTab === 'diario' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100'">
                        <FileText class="h-3.5 w-3.5" />
                        Libro Diario
                    </button>
                    
                    <button @click="activeTab = 'mayor'" 
                        class="px-3 py-1.5 rounded-md transition-all flex items-center gap-1.5 cursor-pointer"
                        :class="activeTab === 'mayor' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100'">
                        <BookOpen class="h-3.5 w-3.5" />
                        Libro Mayor
                    </button>

                    <button @click="activeTab = 'balanza'" 
                        class="px-3 py-1.5 rounded-md transition-all flex items-center gap-1.5 cursor-pointer"
                        :class="activeTab === 'balanza' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100'">
                        <Scale class="h-3.5 w-3.5" />
                        Balanza
                    </button>

                    <button @click="activeTab = 'resultados'" 
                        class="px-3 py-1.5 rounded-md transition-all flex items-center gap-1.5 cursor-pointer"
                        :class="activeTab === 'resultados' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100'">
                        <TrendingUp class="h-3.5 w-3.5" />
                        Estado de Resultados
                    </button>
                </div>

                <!-- Fila 2: Estados Financieros y Ratios -->
                <div class="inline-flex rounded-lg p-1 bg-zinc-100 dark:bg-zinc-800/80 gap-1 min-w-max">
                    <button @click="activeTab = 'patrimonio'" 
                        class="px-3 py-1.5 rounded-md transition-all flex items-center gap-1.5 cursor-pointer"
                        :class="activeTab === 'patrimonio' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100'">
                        <PieChart class="h-3.5 w-3.5" />
                        Cambios en Patrimonio
                    </button>

                    <button @click="activeTab = 'balance'" 
                        class="px-3 py-1.5 rounded-md transition-all flex items-center gap-1.5 cursor-pointer"
                        :class="activeTab === 'balance' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100'">
                        <Landmark class="h-3.5 w-3.5" />
                        Balance General
                    </button>

                    <button @click="activeTab = 'ratios'" 
                        class="px-3 py-1.5 rounded-md transition-all flex items-center gap-1.5 cursor-pointer"
                        :class="activeTab === 'ratios' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100'">
                        <Activity class="h-3.5 w-3.5" />
                        Ratios Financieros
                    </button>
                </div>

            </div>
        </div>

        <!-- ==================== PESTAÑA 1: LIBRO DIARIO ==================== -->
        <div v-if="activeTab === 'diario'" class="space-y-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[780px] text-left text-sm text-zinc-500 dark:text-zinc-400">
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
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 font-mono text-xs">
                            <template v-for="asiento in asientosDiario" :key="asiento.id_asiento">
                                <tr v-for="(det, idx) in asiento.detalles" :key="det.id_detalle_asiento" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30">
                                    <td class="p-3 font-sans font-semibold text-zinc-900 dark:text-zinc-100" v-if="idx === 0" :rowspan="asiento.detalles.length">
                                        #{{ String(asiento.id_asiento).padStart(5, '0') }}
                                        <span class="block text-[10px] text-zinc-400 font-normal">{{ asiento.tipo_operacion }}</span>
                                    </td>
                                    <td class="p-3 font-sans" v-if="idx === 0" :rowspan="asiento.detalles.length">
                                        {{ formatDate(asiento.fecha_asiento) }}
                                    </td>
                                    <td class="p-3 font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ det.codigo_cuenta }}
                                    </td>
                                    <td class="p-3 font-sans text-zinc-800 dark:text-zinc-200">
                                        {{ det.cuenta ? det.cuenta.denominacion : '-' }}
                                    </td>
                                    <td class="p-3 font-sans text-zinc-600 dark:text-zinc-400 max-w-xs truncate" v-if="idx === 0" :rowspan="asiento.detalles.length">
                                        {{ asiento.glosa }}
                                    </td>
                                    <td class="p-3 text-right text-emerald-600 dark:text-emerald-400">
                                        {{ Number(det.debe) > 0 ? formatCurrency(det.debe) : '-' }}
                                    </td>
                                    <td class="p-3 text-right text-blue-600 dark:text-blue-400">
                                        {{ Number(det.haber) > 0 ? formatCurrency(det.haber) : '-' }}
                                    </td>
                                </tr>
                            </template>
                            <tr v-if="asientosDiario.length === 0">
                                <td colspan="7" class="p-8 text-center text-zinc-500 font-sans">
                                    No se encontraron asientos contables registrados en el periodo seleccionado.
                                </td>
                            </tr>
                        </tbody>
                        <tfoot v-if="asientosDiario.length > 0" class="bg-zinc-100 dark:bg-zinc-800/80 font-bold border-t-2 border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100">
                            <tr>
                                <td colspan="5" class="p-3 font-sans text-right uppercase">
                                    Total General del Diario:
                                </td>
                                <td class="p-3 text-right text-emerald-600 dark:text-emerald-400 font-mono">
                                    {{ formatCurrency(totalDiarioDebe) }}
                                </td>
                                <td class="p-3 text-right text-blue-600 dark:text-blue-400 font-mono">
                                    {{ formatCurrency(totalDiarioHaber) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- ==================== PESTAÑA 2: LIBRO MAYOR ==================== -->
        <div v-else-if="activeTab === 'mayor'" class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            
            <!-- Selector lateral de Cuentas T -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-4 shadow-sm">
                <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 mb-3 flex items-center justify-between">
                    <span>Cuentas con Movimiento</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400">{{ cuentasConMovimiento.length }}</span>
                </h3>

                <div class="relative mb-3">
                    <Search class="absolute left-2.5 top-2.5 h-3.5 w-3.5 text-zinc-400" />
                    <input v-model="mayorSearchQuery" type="text" placeholder="Buscar cuenta PCGE..." class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 focus:outline-none focus:border-indigo-500" />
                </div>

                <div class="space-y-1 max-h-[500px] overflow-y-auto pr-1">
                    <button 
                        v-for="c in cuentasConMovimiento" 
                        :key="c.codigo"
                        @click="selectedCuentaCodigo = c.codigo"
                        class="w-full text-left p-2.5 rounded-lg text-xs transition-all flex items-center justify-between"
                        :class="selectedCuentaCodigo === c.codigo ? 'bg-indigo-50 dark:bg-indigo-950/50 text-indigo-700 dark:text-indigo-300 font-semibold border border-indigo-200 dark:border-indigo-800/40' : 'hover:bg-zinc-50 dark:hover:bg-zinc-800/40 text-zinc-700 dark:text-zinc-300'">
                        <div class="truncate mr-2">
                            <span class="font-mono font-bold mr-1.5 text-indigo-600 dark:text-indigo-400">{{ c.codigo }}</span>
                            <span>{{ c.nombre }}</span>
                        </div>
                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-500 font-mono">{{ c.count }}</span>
                    </button>
                </div>
            </div>

            <!-- Esquema de Cuenta "T" Mayorizada -->
            <div class="lg:col-span-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
                <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6 flex flex-col md:flex-row md:items-center justify-between gap-2">
                    <div>
                        <span class="text-xs font-mono font-bold px-2 py-0.5 rounded bg-indigo-100 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 mr-2">CUENTA {{ cuentaSeleccionadaInfo.codigo_cuenta }}</span>
                        <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-50 inline">{{ cuentaSeleccionadaInfo.denominacion }}</h2>
                    </div>

                    <div class="flex items-center gap-3 font-mono text-xs">
                        <span class="px-2.5 py-1 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                            Saldo: <strong :class="saldoMayor.tipo === 'DEUDOR' ? 'text-emerald-600' : 'text-blue-600'">{{ formatCurrency(saldoMayor.monto) }} ({{ saldoMayor.tipo }})</strong>
                        </span>
                    </div>
                </div>

                <!-- Grilla T Mayor -->
                <div class="grid grid-cols-2 gap-4">
                    
                    <!-- Columna DEBE (Izquierda) -->
                    <div class="border-r border-zinc-200 dark:border-zinc-800 pr-4">
                        <div class="p-2 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-400 font-bold text-xs uppercase text-center rounded-lg mb-3">
                            DEBE (Cargos)
                        </div>

                        <div class="space-y-2 min-h-[220px]">
                            <div 
                                v-for="m in movimientosCuentaSeleccionada.filter(item => Number(item.debe) > 0)" 
                                :key="m.id_detalle_asiento" 
                                class="p-2 rounded bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-100 dark:border-zinc-800 text-xs flex justify-between items-center">
                                <div class="truncate mr-2">
                                    <span class="text-[10px] text-zinc-400 block font-mono">{{ formatDate(m.fecha_asiento) }} - Asiento #{{ m.id_asiento }}</span>
                                    <span class="text-zinc-700 dark:text-zinc-300 truncate block">{{ m.glosa }}</span>
                                </div>
                                <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400 shrink-0">{{ formatCurrency(m.debe) }}</span>
                            </div>
                        </div>

                        <div class="border-t-2 border-zinc-900 dark:border-zinc-100 pt-3 mt-4 flex justify-between items-center font-mono font-bold text-sm">
                            <span>Total Debe:</span>
                            <span class="text-emerald-600 dark:text-emerald-400">{{ formatCurrency(totalMayorDebe) }}</span>
                        </div>
                    </div>

                    <!-- Columna HABER (Derecha) -->
                    <div class="pl-4">
                        <div class="p-2 bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-400 font-bold text-xs uppercase text-center rounded-lg mb-3">
                            HABER (Abonos)
                        </div>

                        <div class="space-y-2 min-h-[220px]">
                            <div 
                                v-for="m in movimientosCuentaSeleccionada.filter(item => Number(item.haber) > 0)" 
                                :key="m.id_detalle_asiento" 
                                class="p-2 rounded bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-100 dark:border-zinc-800 text-xs flex justify-between items-center">
                                <div class="truncate mr-2">
                                    <span class="text-[10px] text-zinc-400 block font-mono">{{ formatDate(m.fecha_asiento) }} - Asiento #{{ m.id_asiento }}</span>
                                    <span class="text-zinc-700 dark:text-zinc-300 truncate block">{{ m.glosa }}</span>
                                </div>
                                <span class="font-mono font-bold text-blue-600 dark:text-blue-400 shrink-0">{{ formatCurrency(m.haber) }}</span>
                            </div>
                        </div>

                        <div class="border-t-2 border-zinc-900 dark:border-zinc-100 pt-3 mt-4 flex justify-between items-center font-mono font-bold text-sm">
                            <span>Total Haber:</span>
                            <span class="text-blue-600 dark:text-blue-400">{{ formatCurrency(totalMayorHaber) }}</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ==================== PESTAÑA 3: BALANZA DE COMPROBACIÓN ==================== -->
        <div v-else-if="activeTab === 'balanza'" class="space-y-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[650px] text-left text-sm text-zinc-500 dark:text-zinc-400 font-mono text-xs">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/60 text-xs uppercase text-zinc-700 dark:text-zinc-300 border-b border-zinc-200 dark:border-zinc-800">
                            <tr>
                                <th class="p-3 border-r border-zinc-200 dark:border-zinc-800 w-24">Código</th>
                                <th class="p-3 border-r border-zinc-200 dark:border-zinc-800 font-sans">Cuenta Contable</th>
                                <th class="p-3 text-right border-r border-zinc-200 dark:border-zinc-800 text-emerald-700 dark:text-emerald-400 w-44">Saldo Deudor (S/.)</th>
                                <th class="p-3 text-right text-blue-700 dark:text-blue-400 w-44">Saldo Acreedor (S/.)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            <tr v-for="b in balanzaFiltrada" :key="b.codigo_cuenta" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30">
                                <td class="p-2.5 font-bold text-indigo-600 dark:text-indigo-400 border-r border-zinc-200 dark:border-zinc-800">
                                    {{ b.codigo_cuenta }}
                                </td>
                                <td class="p-2.5 font-sans text-zinc-800 dark:text-zinc-200 border-r border-zinc-200 dark:border-zinc-800">
                                    {{ b.nombre_cuenta }}
                                </td>
                                <td class="p-2.5 text-right font-bold text-emerald-600 border-r border-zinc-200 dark:border-zinc-800">
                                    {{ Number(b.saldo_deudor) > 0 ? formatCurrency(b.saldo_deudor) : '-' }}
                                </td>
                                <td class="p-2.5 text-right font-bold text-blue-600">
                                    {{ Number(b.saldo_acreedor) > 0 ? formatCurrency(b.saldo_acreedor) : '-' }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-zinc-100 dark:bg-zinc-800/80 font-bold border-t-2 border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100">
                            <tr>
                                <td colspan="2" class="p-3 font-sans text-right border-r border-zinc-200 dark:border-zinc-800 uppercase">
                                    Sumas Iguales:
                                </td>
                                <td class="p-3 text-right text-emerald-600 border-r border-zinc-200 dark:border-zinc-800">{{ formatCurrency(totalBalanzaSaldoDeudor) }}</td>
                                <td class="p-3 text-right text-blue-600">{{ formatCurrency(totalBalanzaSaldoAcreedor) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- ==================== PESTAÑA 4: ESTADO DE RESULTADOS (DISEÑO IMAGEN 1) ==================== -->
        <div v-else-if="activeTab === 'resultados'" class="max-w-4xl mx-auto space-y-4">
            <div class="bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-zinc-100 rounded-xl overflow-hidden shadow-md">
                
                <!-- Encabezado Oficial -->
                <div class="p-6 text-center border-b-2 border-zinc-900 dark:border-zinc-100 bg-zinc-50/50 dark:bg-zinc-800/30">
                    <h2 class="text-base font-semibold tracking-wide text-zinc-800 dark:text-zinc-200">GUESAA PERÚ E.I.R.L.</h2>
                    <h1 class="text-xl font-extrabold uppercase tracking-tight text-zinc-900 dark:text-zinc-50 mt-0.5">ESTADO DE RESULTADOS</h1>
                    <p v-if="fechaHasta" class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                        Al {{ formatDate(fechaHasta) }}
                    </p>
                </div>

                <!-- Tabla de Resultados Directa por Cuentas (Elemento 6 y 7) -->
                <div class="overflow-x-auto p-4">
                    <table class="w-full min-w-[500px] text-left text-sm border-collapse">
                        <thead>
                            <tr class="border-b-2 border-zinc-900 dark:border-zinc-100 font-bold text-xs uppercase text-zinc-900 dark:text-zinc-100">
                                <th class="py-2.5 px-4 w-28">Código</th>
                                <th class="py-2.5 px-4">Cuenta (Elemento 6 y 7)</th>
                                <th class="py-2.5 px-4 text-right w-36">Debe (S/.)</th>
                                <th class="py-2.5 px-4 text-right w-36">Haber (S/.)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 text-xs font-mono">
                            <tr v-for="c in (estadoResultados?.cuentas || [])" :key="c.codigo" class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30">
                                <td class="py-2.5 px-4 font-bold text-indigo-600 dark:text-indigo-400">{{ c.codigo }}</td>
                                <td class="py-2.5 px-4 font-sans text-zinc-800 dark:text-zinc-200">{{ c.cuenta }}</td>
                                <td class="py-2.5 px-4 text-right text-zinc-900 dark:text-zinc-100">
                                    {{ Number(c.debe) > 0 ? formatCurrency(c.debe) : '-' }}
                                </td>
                                <td class="py-2.5 px-4 text-right text-zinc-900 dark:text-zinc-100">
                                    {{ Number(c.haber) > 0 ? formatCurrency(c.haber) : '-' }}
                                </td>
                            </tr>
                            <tr v-if="(estadoResultados?.cuentas || []).length === 0">
                                <td colspan="4" class="p-6 text-center text-zinc-500 font-sans">
                                    No se encontraron cuentas de Elemento 6 o 7 registradas en el periodo.
                                </td>
                            </tr>

                            <!-- Totales -->
                            <tr class="border-t-2 border-zinc-900 dark:border-zinc-100 font-bold text-zinc-900 dark:text-zinc-100 bg-zinc-50 dark:bg-zinc-800/50">
                                <td colspan="2" class="py-3 px-4 font-sans uppercase text-right">Totales:</td>
                                <td class="py-3 px-4 text-right text-emerald-600 dark:text-emerald-400">{{ formatCurrency(estadoResultados?.total_debe || 0) }}</td>
                                <td class="py-3 px-4 text-right text-blue-600 dark:text-blue-400">{{ formatCurrency(estadoResultados?.total_haber || 0) }}</td>
                            </tr>

                            <!-- Utilidad Neta -->
                            <tr class="border-t border-zinc-300 dark:border-zinc-700 bg-emerald-50/60 dark:bg-emerald-950/20 font-bold text-sm">
                                <td colspan="2" class="py-3.5 px-4 font-sans text-emerald-800 dark:text-emerald-300 uppercase">Utilidad Neta del Ejercicio:</td>
                                <td class="py-3.5 px-4 text-right"></td>
                                <td class="py-3.5 px-4 text-right text-emerald-700 dark:text-emerald-400 font-mono">
                                    {{ formatCurrency(estadoResultados?.utilidad_neta || 0) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

        <!-- ==================== PESTAÑA 5: ESTADO DE CAMBIOS EN PATRIMONIO (DISEÑO IMAGEN 2) ==================== -->
        <div v-else-if="activeTab === 'patrimonio'" class="max-w-5xl mx-auto space-y-4">
            <div class="bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-zinc-100 rounded-xl overflow-hidden shadow-md">
                
                <!-- Encabezado Oficial -->
                <div class="p-6 text-center border-b-2 border-zinc-900 dark:border-zinc-100 bg-zinc-50/50 dark:bg-zinc-800/30">
                    <span class="text-xs italic text-zinc-500 dark:text-zinc-400 block mb-0.5">Estados Financieros: Estado de Cambios en Capital Contable</span>
                    <h2 class="text-base font-semibold tracking-wide text-zinc-800 dark:text-zinc-200">GUESAA PERÚ E.I.R.L.</h2>
                    <h1 class="text-xl font-extrabold uppercase tracking-tight text-zinc-900 dark:text-zinc-50 mt-0.5">ESTADO DE CAMBIOS EN PATRIMONIO NETO</h1>
                    <p v-if="fechaHasta" class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                        Al {{ formatDate(fechaHasta) }}
                    </p>
                </div>

                <!-- Matriz de Cambios en Patrimonio -->
                <div class="overflow-x-auto p-4">
                    <table class="w-full min-w-[750px] text-left text-sm border-collapse font-mono text-xs">
                        <thead>
                            <tr class="border-b-2 border-zinc-900 dark:border-zinc-100 font-bold uppercase text-zinc-900 dark:text-zinc-100 text-center">
                                <th class="py-3 px-3 text-left w-1/4 font-sans">Concepto</th>
                                <th class="py-3 px-3">Capital Social</th>
                                <th class="py-3 px-3">Donaciones / Reservas</th>
                                <th class="py-3 px-3">Utilidades Retenidas</th>
                                <th class="py-3 px-3">Exceso o Insuficiencia</th>
                                <th class="py-3 px-3 font-sans bg-zinc-100 dark:bg-zinc-800/60">TOTAL Capital Contable</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                            
                            <!-- Saldos Iniciales -->
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30">
                                <td class="py-2.5 px-3 font-sans font-medium text-zinc-800 dark:text-zinc-200">Saldos iniciales</td>
                                <td class="py-2.5 px-3 text-right">{{ formatCurrency(estadoPatrimonio?.saldos_iniciales.capital_social || 0) }}</td>
                                <td class="py-2.5 px-3 text-right">{{ formatCurrency(estadoPatrimonio?.saldos_iniciales.donaciones || 0) }}</td>
                                <td class="py-2.5 px-3 text-right">{{ formatCurrency(estadoPatrimonio?.saldos_iniciales.utilidades_retenidas || 0) }}</td>
                                <td class="py-2.5 px-3 text-right">{{ formatCurrency(0) }}</td>
                                <td class="py-2.5 px-3 text-right font-bold bg-zinc-50 dark:bg-zinc-800/30">{{ formatCurrency(estadoPatrimonio?.saldos_iniciales.total || 0) }}</td>
                            </tr>

                            <!-- Utilidad Neta -->
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30">
                                <td class="py-2.5 px-3 font-sans font-medium text-zinc-800 dark:text-zinc-200">Utilidad Neta</td>
                                <td class="py-2.5 px-3 text-right">{{ formatCurrency(0) }}</td>
                                <td class="py-2.5 px-3 text-right">{{ formatCurrency(0) }}</td>
                                <td class="py-2.5 px-3 text-right text-emerald-600 font-bold">{{ formatCurrency(estadoPatrimonio?.utilidad_neta_fila.utilidades_retenidas || 0) }}</td>
                                <td class="py-2.5 px-3 text-right">{{ formatCurrency(0) }}</td>
                                <td class="py-2.5 px-3 text-right font-bold text-emerald-600 bg-zinc-50 dark:bg-zinc-800/30">{{ formatCurrency(estadoPatrimonio?.utilidad_neta_fila.total || 0) }}</td>
                            </tr>

                            <!-- Dividendos -->
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30">
                                <td class="py-2.5 px-3 font-sans font-medium text-zinc-800 dark:text-zinc-200">Dividendos / Retiros</td>
                                <td class="py-2.5 px-3 text-right">{{ formatCurrency(0) }}</td>
                                <td class="py-2.5 px-3 text-right">{{ formatCurrency(0) }}</td>
                                <td class="py-2.5 px-3 text-right text-red-500 font-bold">{{ formatCurrency(estadoPatrimonio?.dividendos_fila.utilidades_retenidas || 0) }}</td>
                                <td class="py-2.5 px-3 text-right">{{ formatCurrency(0) }}</td>
                                <td class="py-2.5 px-3 text-right font-bold text-red-500 bg-zinc-50 dark:bg-zinc-800/30">{{ formatCurrency(estadoPatrimonio?.dividendos_fila.total || 0) }}</td>
                            </tr>

                            <!-- Saldos Finales -->
                            <tr class="border-t-2 border-zinc-900 dark:border-zinc-100 font-bold text-sm bg-yellow-100/80 dark:bg-yellow-950/40 text-zinc-900 dark:text-zinc-100">
                                <td class="py-3 px-3 font-sans uppercase">Saldos</td>
                                <td class="py-3 px-3 text-right">{{ formatCurrency(estadoPatrimonio?.saldos_finales.capital_social || 0) }}</td>
                                <td class="py-3 px-3 text-right">{{ formatCurrency(estadoPatrimonio?.saldos_finales.donaciones || 0) }}</td>
                                <td class="py-3 px-3 text-right">{{ formatCurrency(estadoPatrimonio?.saldos_finales.utilidades_retenidas || 0) }}</td>
                                <td class="py-3 px-3 text-right">{{ formatCurrency(0) }}</td>
                                <td class="py-3 px-3 text-right font-extrabold bg-yellow-200 dark:bg-yellow-900/60 text-yellow-900 dark:text-yellow-200">
                                    {{ formatCurrency(estadoPatrimonio?.saldos_finales.total || 0) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-4 bg-zinc-50 dark:bg-zinc-800/40 border-t border-zinc-200 dark:border-zinc-800 flex justify-between items-center font-sans font-bold">
                    <span class="text-sm uppercase text-zinc-700 dark:text-zinc-300">Total Capital Contable / Patrimonio Neto:</span>
                    <span class="text-lg font-mono text-indigo-600 dark:text-indigo-400">{{ formatCurrency(estadoPatrimonio?.total_patrimonio || 0) }}</span>
                </div>

            </div>
        </div>

        <!-- ==================== PESTAÑA 6: BALANCE GENERAL (DISEÑO IMAGEN 3) ==================== -->
        <div v-else-if="activeTab === 'balance'" class="max-w-5xl mx-auto space-y-4">
            <div class="bg-white dark:bg-zinc-900 border-2 border-zinc-900 dark:border-zinc-100 rounded-xl overflow-hidden shadow-md">
                
                <!-- Encabezado Oficial -->
                <div class="p-6 text-center border-b-2 border-zinc-900 dark:border-zinc-100 bg-zinc-50/50 dark:bg-zinc-800/30">
                    <h2 class="text-base font-semibold tracking-wide text-zinc-800 dark:text-zinc-200">GUESAA PERÚ E.I.R.L.</h2>
                    <h1 class="text-xl font-extrabold uppercase tracking-tight text-zinc-900 dark:text-zinc-50 mt-0.5">BALANCE GENERAL</h1>
                    <p v-if="fechaHasta" class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                        Al {{ formatDate(fechaHasta) }}
                    </p>
                </div>

                <!-- Grilla a Dos Columnas (Activo a la Izquierda vs Pasivo + Patrimonio a la Derecha) -->
                <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x-2 divide-zinc-900 dark:divide-zinc-100 text-xs font-mono">
                    
                    <!-- LADO IZQUIERDO: ACTIVO -->
                    <div class="p-4 space-y-6">
                        <div class="font-bold font-sans text-sm uppercase text-zinc-900 dark:text-zinc-100 border-b border-zinc-200 dark:border-zinc-800 pb-1.5">
                            ACTIVO
                        </div>

                        <!-- Activo Circulante -->
                        <div>
                            <div class="font-bold text-xs uppercase text-zinc-700 dark:text-zinc-300 mb-2">
                                Act. CIRCULANTE
                            </div>
                            <div class="space-y-1.5 pl-2">
                                <div v-for="a in (balanceGeneral?.activo_circulante || [])" :key="a.codigo" class="flex justify-between items-center">
                                    <span class="font-sans text-zinc-700 dark:text-zinc-300">{{ a.cuenta }}</span>
                                    <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ formatCurrency(a.monto) }}</span>
                                </div>
                            </div>
                            <div class="border-t border-zinc-300 dark:border-zinc-700 pt-1.5 mt-2 flex justify-between items-center font-bold text-zinc-900 dark:text-zinc-100">
                                <span class="font-sans uppercase">Total ACTIVO CIRCULANTE</span>
                                <span>{{ formatCurrency(balanceGeneral?.total_activo_circulante || 0) }}</span>
                            </div>
                        </div>

                        <!-- Activo No Circulante -->
                        <div>
                            <div class="font-bold text-xs uppercase text-zinc-700 dark:text-zinc-300 mb-2">
                                Act. NO CIRCULANTE
                            </div>
                            <div class="space-y-1.5 pl-2">
                                <div v-for="a in (balanceGeneral?.activo_no_circulante || [])" :key="a.codigo" class="flex justify-between items-center">
                                    <span class="font-sans text-zinc-700 dark:text-zinc-300">{{ a.cuenta }}</span>
                                    <span :class="Number(a.monto) < 0 ? 'text-red-500 font-bold' : 'text-zinc-900 dark:text-zinc-100 font-bold'">
                                        {{ formatCurrency(a.monto) }}
                                    </span>
                                </div>
                            </div>
                            <div class="border-t border-zinc-300 dark:border-zinc-700 pt-1.5 mt-2 flex justify-between items-center font-bold text-zinc-900 dark:text-zinc-100">
                                <span class="font-sans uppercase">Total ACTIVO NO CIRCULANTE</span>
                                <span>{{ formatCurrency(balanceGeneral?.total_activo_no_circulante || 0) }}</span>
                            </div>
                        </div>

                        <!-- TOTAL ACTIVO -->
                        <div class="border-t-2 border-zinc-900 dark:border-zinc-100 pt-3 mt-8 flex justify-between items-center font-extrabold text-sm text-zinc-900 dark:text-zinc-100 bg-zinc-100 dark:bg-zinc-800 p-2.5 rounded-lg">
                            <span class="font-sans uppercase">TOTAL ACTIVO</span>
                            <span class="text-indigo-600 dark:text-indigo-400">{{ formatCurrency(balanceGeneral?.total_activo || 0) }}</span>
                        </div>
                    </div>

                    <!-- LADO DERECHO: PASIVO Y PATRIMONIO -->
                    <div class="p-4 space-y-6">
                        <div class="font-bold font-sans text-sm uppercase text-zinc-900 dark:text-zinc-100 border-b border-zinc-200 dark:border-zinc-800 pb-1.5">
                            PASIVO - PATRIMONIO
                        </div>

                        <!-- Pasivo Corto Plazo -->
                        <div>
                            <div class="font-bold text-xs uppercase text-zinc-700 dark:text-zinc-300 mb-2">
                                Pas. CORTO PLAZO
                            </div>
                            <div class="space-y-1.5 pl-2">
                                <div v-for="p in (balanceGeneral?.pasivo_corto_plazo || [])" :key="p.codigo" class="flex justify-between items-center">
                                    <span class="font-sans text-zinc-700 dark:text-zinc-300">{{ p.cuenta }}</span>
                                    <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ formatCurrency(p.monto) }}</span>
                                </div>
                            </div>
                            <div class="border-t border-zinc-300 dark:border-zinc-700 pt-1.5 mt-2 flex justify-between items-center font-bold text-zinc-900 dark:text-zinc-100">
                                <span class="font-sans uppercase">Total PASIVO C.P.</span>
                                <span>{{ formatCurrency(balanceGeneral?.total_pasivo_cp || 0) }}</span>
                            </div>
                        </div>

                        <!-- Pasivo Largo Plazo -->
                        <div>
                            <div class="font-bold text-xs uppercase text-zinc-700 dark:text-zinc-300 mb-2">
                                Pas. LARGO PLAZO
                            </div>
                            <div class="space-y-1.5 pl-2">
                                <div v-for="p in (balanceGeneral?.pasivo_largo_plazo || [])" :key="p.codigo" class="flex justify-between items-center">
                                    <span class="font-sans text-zinc-700 dark:text-zinc-300">{{ p.cuenta }}</span>
                                    <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ formatCurrency(p.monto) }}</span>
                                </div>
                            </div>
                            <div class="border-t border-zinc-300 dark:border-zinc-700 pt-1.5 mt-2 flex justify-between items-center font-bold text-zinc-900 dark:text-zinc-100">
                                <span class="font-sans uppercase">Total PASIVO L.P.</span>
                                <span>{{ formatCurrency(balanceGeneral?.total_pasivo_lp || 0) }}</span>
                            </div>
                        </div>

                        <!-- TOTAL PASIVO -->
                        <div class="border-t border-zinc-400 dark:border-zinc-600 pt-1.5 flex justify-between items-center font-bold text-zinc-900 dark:text-zinc-100">
                            <span class="font-sans uppercase">TOTAL PASIVO</span>
                            <span>{{ formatCurrency(balanceGeneral?.total_pasivo || 0) }}</span>
                        </div>

                        <!-- Patrimonio -->
                        <div>
                            <div class="font-bold text-xs uppercase text-zinc-700 dark:text-zinc-300 mb-2">
                                Patrimonio
                            </div>
                            <div class="space-y-1.5 pl-2">
                                <div class="flex justify-between items-center">
                                    <span class="font-sans text-zinc-700 dark:text-zinc-300">Capital social</span>
                                    <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ formatCurrency(estadoPatrimonio?.capital_social || 0) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="font-sans text-zinc-700 dark:text-zinc-300">Utilidades retenidas</span>
                                    <span class="font-bold text-zinc-900 dark:text-zinc-100">{{ formatCurrency(estadoPatrimonio?.utilidades_retenidas || 0) }}</span>
                                </div>
                            </div>
                            <div class="border-t border-zinc-300 dark:border-zinc-700 pt-1.5 mt-2 flex justify-between items-center font-bold text-zinc-900 dark:text-zinc-100">
                                <span class="font-sans uppercase">Total Patrimonio</span>
                                <span>{{ formatCurrency(balanceGeneral?.total_patrimonio || 0) }}</span>
                            </div>
                        </div>

                        <!-- TOTAL PASIVO + PATRIMONIO -->
                        <div class="border-t-2 border-zinc-900 dark:border-zinc-100 pt-3 mt-4 flex justify-between items-center font-extrabold text-sm text-zinc-900 dark:text-zinc-100 bg-zinc-100 dark:bg-zinc-800 p-2.5 rounded-lg">
                            <span class="font-sans uppercase">TOTAL PASIVO + PATRIMONIO</span>
                            <span class="text-indigo-600 dark:text-indigo-400">{{ formatCurrency(balanceGeneral?.total_pasivo_patrimonio || 0) }}</span>
                        </div>
                    </div>

                </div>

                <!-- Banner de Confirmación de Cuadre -->
                <div class="p-3 text-center text-xs font-bold font-sans border-t border-zinc-200 dark:border-zinc-800"
                    :class="balanceGeneral?.cuadrado ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300' : 'bg-red-50 dark:bg-red-950/30 text-red-700 dark:text-red-300'">
                    <span v-if="balanceGeneral?.cuadrado">
                        ✓ Ecuación Contable Balanceada: Activo ({{ formatCurrency(balanceGeneral?.total_activo || 0) }}) = Pasivo + Patrimonio ({{ formatCurrency(balanceGeneral?.total_pasivo_patrimonio || 0) }})
                    </span>
                    <span v-else>
                        ⚠️ Descuadre en Balance General: Diferencia de {{ formatCurrency(Math.abs((balanceGeneral?.total_activo || 0) - (balanceGeneral?.total_pasivo_patrimonio || 0))) }}
                    </span>
                </div>

            </div>
        </div>

        <!-- ==================== PESTAÑA 7: RATIOS FINANCIEROS (DASHBOARD KPIS) ==================== -->
        <div v-else-if="activeTab === 'ratios'" class="space-y-8">
            
            <!-- Grupo 1: Ratios de Liquidez -->
            <div class="space-y-3">
                <div class="flex items-center gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-2">
                    <div class="p-1.5 rounded-lg bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 font-bold">
                        <Scale class="h-4 w-4" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50">1. Ratios de Liquidez</h3>
                        <p class="text-xs text-zinc-500">Miden la capacidad de GUESAA para cumplir sus obligaciones financieras de corto plazo.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div v-for="(r, key) in (ratios?.liquidez || {})" :key="key" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 shadow-sm space-y-3">
                        <div class="flex justify-between items-start">
                            <span class="text-xs font-semibold text-zinc-500 uppercase">{{ r.nombre }}</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase"
                                :class="r.estado === 'EXCELENTE' || r.estado === 'POSITIVO' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : (r.estado === 'ACEPTABLE' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-950/60 dark:text-yellow-300' : 'bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-300')">
                                {{ r.estado }}
                            </span>
                        </div>

                        <div class="text-2xl font-black font-mono text-zinc-900 dark:text-zinc-50">
                            {{ key === 'capital_trabajo' ? formatCurrency(r.valor) : r.valor }}
                        </div>

                        <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800 text-[11px] text-zinc-500 space-y-1">
                            <p class="font-mono text-indigo-600 dark:text-indigo-400 font-medium">📐 {{ r.formula }}</p>
                            <p class="text-zinc-400">{{ r.descripcion }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grupo 2: Ratios de Solvencia / Endeudamiento -->
            <div class="space-y-3">
                <div class="flex items-center gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-2">
                    <div class="p-1.5 rounded-lg bg-blue-100 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 font-bold">
                        <Landmark class="h-4 w-4" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50">2. Ratios de Solvencia y Apalancamiento</h3>
                        <p class="text-xs text-zinc-500">Evalúan el respaldo patrimonial y el grado de financiamiento con terceros.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="(r, key) in (ratios?.solvencia || {})" :key="key" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 shadow-sm space-y-3">
                        <div class="flex justify-between items-start">
                            <span class="text-xs font-semibold text-zinc-500 uppercase">{{ r.nombre }}</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase"
                                :class="r.estado === 'SALUDABLE' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : (r.estado === 'MODERADO' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-950/60 dark:text-yellow-300' : 'bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-300')">
                                {{ r.estado }}
                            </span>
                        </div>

                        <div class="text-2xl font-black font-mono text-zinc-900 dark:text-zinc-50">
                            {{ r.valor }}{{ r.unidad || '' }}
                        </div>

                        <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800 text-[11px] text-zinc-500 space-y-1">
                            <p class="font-mono text-indigo-600 dark:text-indigo-400 font-medium">📐 {{ r.formula }}</p>
                            <p class="text-zinc-400">{{ r.descripcion }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grupo 3: Ratios de Rentabilidad -->
            <div class="space-y-3">
                <div class="flex items-center gap-2 border-b border-zinc-200 dark:border-zinc-800 pb-2">
                    <div class="p-1.5 rounded-lg bg-purple-100 dark:bg-purple-950/50 text-purple-600 dark:text-purple-400 font-bold">
                        <TrendingUp class="h-4 w-4" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-50">3. Ratios de Rentabilidad</h3>
                        <p class="text-xs text-zinc-500">Miden la efectividad del negocio para generar utilidades sobre las ventas, activos y patrimonio.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div v-for="(r, key) in (ratios?.rentabilidad || {})" :key="key" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 shadow-sm space-y-3">
                        <div class="flex justify-between items-start">
                            <span class="text-xs font-semibold text-zinc-500 uppercase">{{ r.nombre }}</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold uppercase"
                                :class="r.estado === 'ALTO' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300' : (r.estado === 'MODERADO' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-950/60 dark:text-yellow-300' : 'bg-red-100 text-red-700 dark:bg-red-950/60 dark:text-red-300')">
                                {{ r.estado }}
                            </span>
                        </div>

                        <div class="text-2xl font-black font-mono text-zinc-900 dark:text-zinc-50">
                            {{ r.valor }}{{ r.unidad || '' }}
                        </div>

                        <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800 text-[11px] text-zinc-500 space-y-1">
                            <p class="font-mono text-indigo-600 dark:text-indigo-400 font-medium">📐 {{ r.formula }}</p>
                            <p class="text-zinc-400">{{ r.descripcion }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ==================== MODAL DE ASIENTO MANUAL ==================== -->
        <div v-if="isManualModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl animate-in fade-in zoom-in-95 duration-150">
                <div class="p-5 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center bg-zinc-50/50 dark:bg-zinc-800/30">
                    <div>
                        <h3 class="font-bold text-base text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                            <Plus class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                            Nuevo Asiento Manual (Partida Doble)
                        </h3>
                        <p class="text-xs text-zinc-500">Registra un asiento contable con verificación en tiempo real de partida doble.</p>
                    </div>
                    <button @click="isManualModalOpen = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 p-1">
                        ✕
                    </button>
                </div>

                <form @submit.prevent="submitManualAsiento" class="p-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Glosa / Concepto del Asiento *</label>
                            <input v-model="manualForm.glosa" required type="text" placeholder="Ej: Registro de aportes, ajuste de depreciación..." class="w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-3 py-2 text-xs text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Fecha Contable *</label>
                            <input v-model="manualForm.fecha_asiento" required type="date" class="w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-3 py-2 text-xs text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none" />
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

<style scoped>
@media print {
    body {
        background-color: white !important;
        color: black !important;
    }
    button, input, select, .no-print {
        display: none !important;
    }
}
</style>
