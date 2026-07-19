<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

import { 
    Plus, 
    Trash2, 
    CreditCard, 
    AlertCircle, 
    Check, 
    FileText, 
    AlertTriangle,
    Wallet 
} from '@lucide/vue';

const props = defineProps<{
    ventas: any[];
    clientes: any[];
    productos: any[];
    cajaAbierta: any | null;
    errors: any;
}>();

const isModalOpen = ref(false);

const form = useForm({
    id_cliente: '',
    tipo_comprobante: 'Boleta',
    num_comprobante: '',
    detalles: [] as Array<{ id_producto: string; cantidad: number; precio_unitario: number; descuento: number }>,
});

const openNewVentaModal = () => {
    form.reset();
    if (props.clientes.length > 0) {
        form.id_cliente = props.clientes[0].id_cliente.toString();
    }
    form.detalles = [];
    addLine();
    form.clearErrors();
    isModalOpen.value = true;
};

const addLine = () => {
    if (props.productos.length > 0) {
        // Seleccionar primer producto con stock si es posible
        const prod = props.productos.find(p => p.stock_actual > 0) || props.productos[0];
        form.detalles.push({
            id_producto: prod.id_producto.toString(),
            cantidad: 1,
            precio_unitario: Number(prod.precio_venta),
            descuento: 0
        });
    }
};

const removeLine = (index: number) => {
    form.detalles.splice(index, 1);
};

const onProductChange = (index: number) => {
    const selectedId = form.detalles[index].id_producto;
    const prod = props.productos.find(p => p.id_producto.toString() === selectedId);
    if (prod) {
        form.detalles[index].precio_unitario = Number(prod.precio_venta);
        form.detalles[index].cantidad = 1;
        form.detalles[index].descuento = 0;
    }
};

// Validar stock disponible reactivamente para cada línea
const getProductStock = (idStr: string) => {
    const prod = props.productos.find(p => p.id_producto.toString() === idStr);
    return prod ? prod.stock_actual : 0;
};

const isStockInsufficient = (line: { id_producto: string; cantidad: number }) => {
    const stock = getProductStock(line.id_producto);
    return line.cantidad > stock;
};

// Totales calculados
const computedSubtotal = computed(() => {
    return form.detalles.reduce((acc, line) => {
        const lineTotal = (line.cantidad * line.precio_unitario) - Number(line.descuento || 0);
        return acc + Math.max(0, lineTotal);
    }, 0);
});

const computedIgv = computed(() => {
    return computedSubtotal.value * 0.18;
});

const computedTotal = computed(() => {
    return computedSubtotal.value + computedIgv.value;
});

// Comprobar si hay alguna línea con stock insuficiente
const hasStockError = computed(() => {
    return form.detalles.some(line => isStockInsufficient(line));
});

const submitVenta = () => {
    if (!props.cajaAbierta) {
        alert('Debe abrir caja para poder vender.');
        return;
    }
    if (form.detalles.length === 0) {
        alert('Debe agregar al menos un producto.');
        return;
    }
    if (hasStockError.value) {
        alert('Hay productos con cantidades que superan el stock disponible. Por favor corrijalos.');
        return;
    }
    form.post('/ventas', {
        onSuccess: () => {
            isModalOpen.value = false;
            form.reset();
        }
    });
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
defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Punto de Venta',
                href: '/ventas',
            },
        ],
    },
});
</script>

<template>
    <Head title="Punto de Venta POS - GUESAA SIC" />

        <div class="p-6 max-w-7xl mx-auto space-y-6">
            
            <!-- Encabezado -->
            <div class="flex justify-between items-center border-b pb-5 border-zinc-200 dark:border-zinc-800">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-zinc-950 dark:text-zinc-50">Punto de Venta (POS)</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        Cobra al contado, verifica stocks de forma reactiva y genera automáticamente asientos contables y reportes.
                    </p>
                </div>
                <button
                    @click="openNewVentaModal"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors"
                >
                    <Plus class="h-4 w-4" />
                    Registrar Venta (POS)
                </button>
            </div>

            <!-- Caja Cerrada Warning -->
            <div v-if="!cajaAbierta" class="rounded-xl border border-amber-200 bg-amber-50/50 p-6 dark:border-amber-900/40 dark:bg-amber-950/10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="flex gap-3">
                    <div class="p-2 bg-amber-500/10 text-amber-600 dark:text-amber-400 rounded-lg h-fit">
                        <Wallet class="h-6 w-6" />
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-amber-800 dark:text-amber-300">Caja Diaria Cerrada</h3>
                        <p class="text-sm text-amber-700 dark:text-amber-400 mt-0.5">
                            Para poder registrar ventas, primero debes aperturar la caja chica con un monto inicial.
                        </p>
                    </div>
                </div>
                <Link href="/caja" class="inline-flex items-center justify-center rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-500 transition-colors">
                    Ir a Caja Chica
                </Link>
            </div>

            <!-- Alertas globales de Laravel -->
            <div v-if="Object.keys(errors).length > 0" class="rounded-lg bg-red-50 p-4 dark:bg-red-950/20 border border-red-200 dark:border-red-800/40">
                <div class="flex">
                    <AlertCircle class="h-5 w-5 text-red-600 dark:text-red-400" />
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Hubo errores al procesar la venta</h3>
                        <ul class="mt-2 list-disc list-inside text-xs text-red-700 dark:text-red-400 space-y-1">
                            <li v-for="err in errors" :key="err">{{ err }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tabla de Ventas Realizadas -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-xs uppercase text-zinc-700 dark:text-zinc-300">
                            <tr>
                                <th class="p-4">Boleta/Factura</th>
                                <th class="p-4">Cliente</th>
                                <th class="p-4">Cajero</th>
                                <th class="p-4">Fecha</th>
                                <th class="p-4 text-right">Subtotal</th>
                                <th class="p-4 text-right">IGV (18%)</th>
                                <th class="p-4 text-right">Total Cobrado</th>
                                <th class="p-4 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            <tr v-if="ventas.length === 0">
                                <td colspan="8" class="p-8 text-center text-zinc-400">No se han registrado ventas en el sistema.</td>
                            </tr>
                            <tr v-for="v in ventas" :key="v.id_venta" class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                                <td class="p-4">
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ v.tipo_comprobante }}</span>
                                    <p class="text-xs text-zinc-400 mt-0.5">{{ v.num_comprobante }}</p>
                                </td>
                                <td class="p-4">
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ v.cliente?.nombre_razon_social }}</p>
                                    <p class="text-xs text-zinc-400 mt-0.5">{{ v.cliente?.tipo_documento }}: {{ v.cliente?.num_documento }}</p>
                                </td>
                                <td class="p-4 text-xs">
                                    {{ v.usuario?.nombres }}
                                </td>
                                <td class="p-4 text-xs">
                                    {{ formatDate(v.fecha_venta) }}
                                </td>
                                <td class="p-4 text-right">{{ formatCurrency(v.subtotal) }}</td>
                                <td class="p-4 text-right text-zinc-400">{{ formatCurrency(v.igv) }}</td>
                                <td class="p-4 text-right font-semibold text-zinc-900 dark:text-zinc-50">{{ formatCurrency(v.total) }}</td>
                                <td class="p-4 text-center">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-950/20 dark:text-emerald-400">
                                        {{ v.estado }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MODAL REGISTRO DE VENTA -->
            <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                <div class="w-full max-w-4xl rounded-xl border bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 p-6 shadow-xl animate-in fade-in zoom-in-95 duration-150 max-h-[90vh] flex flex-col">
                    
                    <h3 class="text-lg font-semibold text-zinc-950 dark:text-zinc-50 border-b pb-3 border-zinc-100 dark:border-zinc-800">
                        Registrar Nueva Venta (Punto de Venta POS)
                    </h3>

                    <!-- Alerta Caja Cerrada en Modal (por seguridad) -->
                    <div v-if="!cajaAbierta" class="mt-4 p-4 bg-red-50 text-red-800 rounded-lg text-sm flex items-center gap-2">
                        <AlertTriangle class="h-5 w-5 text-red-600" />
                        <span>No puedes procesar ventas porque la caja diaria está cerrada. Por favor abre caja primero.</span>
                    </div>

                    <form v-else @submit.prevent="submitVenta" class="mt-4 flex-1 overflow-y-auto space-y-4 pr-1">
                        
                        <!-- Campos Cabecera -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Cliente</label>
                                <select v-model="form.id_cliente" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-955 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option v-for="c in clientes" :key="c.id_cliente" :value="c.id_cliente.toString()">
                                        {{ c.nombre_razon_social }} ({{ c.tipo_documento }}: {{ c.num_documento }})
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Tipo Comprobante</label>
                                <select v-model="form.tipo_comprobante" class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option value="Boleta">Boleta de Venta</option>
                                    <option value="Factura">Factura</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">N° Comprobante</label>
                                <input v-model="form.num_comprobante" type="text" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Ej: B001-00045" />
                            </div>
                        </div>

                        <!-- Detalle de Venta -->
                        <div class="border-t pt-4 border-zinc-100 dark:border-zinc-850">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">Productos seleccionados</span>
                                <button type="button" @click="addLine" class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                                    <Plus class="h-3.5 w-3.5" />
                                    Agregar Producto
                                </button>
                            </div>

                            <div class="space-y-2">
                                <div v-for="(line, index) in form.detalles" :key="index" class="grid grid-cols-12 gap-2 items-center bg-zinc-50 dark:bg-zinc-800/40 p-2 rounded-lg border border-zinc-100 dark:border-zinc-800">
                                    
                                    <!-- Selector Producto -->
                                    <div class="col-span-5">
                                        <label class="block text-[10px] font-semibold text-zinc-400 uppercase">Producto</label>
                                        <select v-model="line.id_producto" @change="onProductChange(index)" required class="mt-1 block w-full rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-2 py-1.5 text-xs text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none">
                                            <option v-for="p in productos" :key="p.id_producto" :value="p.id_producto.toString()">
                                                {{ p.descripcion }} (Stock: {{ p.stock_actual }})
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Cantidad -->
                                    <div class="col-span-2">
                                        <label class="block text-[10px] font-semibold text-zinc-400 uppercase flex justify-between">
                                            Cantidad 
                                            <span class="text-[9px] text-zinc-400 font-normal">(Max: {{ getProductStock(line.id_producto) }})</span>
                                        </label>
                                        <input v-model="line.cantidad" type="number" min="1" required class="mt-1 block w-full rounded border px-2 py-1 text-xs text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none"
                                            :class="isStockInsufficient(line) ? 'border-red-500 bg-red-50 dark:bg-red-950/20 text-red-900' : 'border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-955'" />
                                    </div>

                                    <!-- Precio Venta -->
                                    <div class="col-span-2">
                                        <label class="block text-[10px] font-semibold text-zinc-400 uppercase">P. Unitario</label>
                                        <span class="block text-xs font-semibold text-zinc-900 dark:text-zinc-100 mt-2 bg-zinc-100 dark:bg-zinc-800 py-1 px-2 rounded">
                                            {{ formatCurrency(line.precio_unitario) }}
                                        </span>
                                    </div>

                                    <!-- Descuento -->
                                    <div class="col-span-1">
                                        <label class="block text-[10px] font-semibold text-zinc-400 uppercase">Desc.</label>
                                        <input v-model="line.descuento" type="number" step="0.01" min="0" class="mt-1 block w-full rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none" />
                                    </div>

                                    <!-- Subtotal -->
                                    <div class="col-span-1 text-center">
                                        <label class="block text-[10px] font-semibold text-zinc-400 uppercase">Subtotal</label>
                                        <span class="block text-xs font-semibold text-zinc-900 dark:text-zinc-100 mt-2">
                                            {{ formatCurrency(Math.max(0, (line.cantidad * line.precio_unitario) - (line.descuento || 0))) }}
                                        </span>
                                    </div>

                                    <!-- Eliminar -->
                                    <div class="col-span-1 text-center mt-4">
                                        <button type="button" @click="removeLine(index)" class="p-1 text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 rounded">
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de Totales / Resumen Asiento -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t pt-4 border-zinc-100 dark:border-zinc-850">
                            
                            <!-- Asiento Automático -->
                            <div class="p-4 rounded-lg bg-zinc-50 dark:bg-zinc-800/20 border border-zinc-150 dark:border-zinc-800/80 text-xs">
                                <h4 class="font-semibold text-zinc-800 dark:text-zinc-200 flex items-center gap-1.5 mb-2">
                                    <FileText class="h-4 w-4 text-indigo-600" />
                                    Integración Contable Automática (PCGE)
                                </h4>
                                <p class="text-zinc-500 mb-2">Se registrará automáticamente el siguiente asiento contable en el Libro Diario:</p>
                                <div class="font-mono text-[10px] space-y-1 bg-white dark:bg-zinc-950 p-2 rounded border">
                                    <div class="flex justify-between"><span>1212 Facturas por cobrar - Emitidas</span> <span class="text-emerald-600 font-semibold">DEBE: {{ formatCurrency(computedTotal) }}</span></div>
                                    <div class="flex justify-between pl-4"><span>40111 IGV - Cuenta propia</span> <span class="text-blue-600 font-semibold">HABER: {{ formatCurrency(computedIgv) }}</span></div>
                                    <div class="flex justify-between pl-4"><span>70111 Mercaderías - Venta local</span> <span class="text-blue-600 font-semibold">HABER: {{ formatCurrency(computedSubtotal) }}</span></div>
                                    <hr class="my-1 border-zinc-100 dark:border-zinc-800" />
                                    <div class="flex justify-between text-indigo-600 dark:text-indigo-400"><span>Caja Diaria Activa</span> <span>Ingreso Caja Chica: +{{ formatCurrency(computedTotal) }}</span></div>
                                </div>
                            </div>

                            <!-- Resumen Importes -->
                            <div class="flex flex-col justify-end space-y-2 text-sm pr-4">
                                <div class="flex justify-between">
                                    <span class="text-zinc-500">Subtotal (Neto)</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ formatCurrency(computedSubtotal) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-zinc-500">IGV (18%)</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ formatCurrency(computedIgv) }}</span>
                                </div>
                                <div class="flex justify-between border-t pt-2 border-zinc-100 dark:border-zinc-800">
                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">Total a Cobrar</span>
                                    <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ formatCurrency(computedTotal) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="flex justify-end gap-2 border-t pt-4 border-zinc-100 dark:border-zinc-800 mt-6">
                            <button type="button" @click="isModalOpen = false" class="px-4 py-2 border border-zinc-200 dark:border-zinc-850 rounded-lg text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-850/50 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="form.processing || hasStockError" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-sm transition-colors disabled:opacity-50">
                                <CreditCard class="h-4 w-4" />
                                Procesar Venta y Cobrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

    </div>
</template>
