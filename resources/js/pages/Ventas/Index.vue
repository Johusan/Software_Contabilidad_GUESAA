<script setup lang="ts">
import { ref, computed, watch } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

import { 
    Plus, 
    Trash2, 
    CreditCard, 
    AlertCircle, 
    Check, 
    FileText, 
    AlertTriangle,
    Wallet,
    Search,
    UserPlus,
    X,
    UserCheck,
    Barcode,
    Sparkles
} from '@lucide/vue';

const props = defineProps<{
    ventas: any[];
    clientes: any[];
    productos: any[];
    cajaAbierta: any | null;
    errors: any;
}>();

const isModalOpen = ref(false);

// Estados para Buscador / Picker de Cliente
const isClientPickerOpen = ref(false);
const clientSearchQuery = ref('');

// Estados para Registro Rápido de Nuevo Cliente
const isNewClientModalOpen = ref(false);
const newClientForm = useForm({
    tipo_documento: 'DNI',
    num_documento: '',
    nombre_razon_social: '',
    direccion: '',
    telefono: '',
});

// Estados para Buscador / Picker de Producto por línea
const isProductPickerOpen = ref(false);
const activeProductLineIndex = ref<number | null>(null);
const productSearchQuery = ref('');

const form = useForm({
    id_cliente: '',
    tipo_comprobante: 'Boleta',
    num_comprobante: '',
    detalles: [] as Array<{ id_producto: string; cantidad: number; precio_unitario: number; descuento: number }>,
});

// Lógica de Generación de Número Correlativo de Comprobante
const generateNextVoucherNumber = (tipo: string) => {
    const isFactura = (tipo === 'Factura');
    const prefix = isFactura ? 'F001-' : 'B001-';
    
    let maxNumber = 0;
    
    if (props.ventas && props.ventas.length > 0) {
        props.ventas.forEach(v => {
            if (v.tipo_comprobante === tipo && v.num_comprobante && v.num_comprobante.startsWith(prefix)) {
                const parts = v.num_comprobante.split('-');
                if (parts.length > 1) {
                    const num = parseInt(parts[1], 10);
                    if (!isNaN(num) && num > maxNumber) {
                        maxNumber = num;
                    }
                }
            }
        });
    }

    const nextNum = maxNumber + 1;
    return `${prefix}${String(nextNum).padStart(8, '0')}`;
};

const autoGenerateNumComprobante = () => {
    form.num_comprobante = generateNextVoucherNumber(form.tipo_comprobante);
};

// Actualizar correlativo automáticamente al cambiar entre Boleta y Factura
watch(() => form.tipo_comprobante, () => {
    if (isModalOpen.value) {
        autoGenerateNumComprobante();
    }
});

const openNewVentaModal = () => {
    form.reset();
    if (props.clientes.length > 0) {
        form.id_cliente = props.clientes[0].id_cliente.toString();
    }
    form.detalles = [];
    addLine();
    autoGenerateNumComprobante();
    form.clearErrors();
    isModalOpen.value = true;
};

const addLine = () => {
    if (props.productos.length > 0) {
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

// Cliente seleccionado etiqueta
const selectedClienteName = computed(() => {
    const found = props.clientes.find(c => c.id_cliente.toString() === form.id_cliente);
    if (!found) return 'Seleccionar Cliente...';
    return `${found.nombre_razon_social} (${found.tipo_documento}: ${found.num_documento})`;
});

// Filtro de Clientes en tiempo real
const filteredClientes = computed(() => {
    const q = clientSearchQuery.value.trim().toLowerCase();
    if (!q) return props.clientes;
    return props.clientes.filter(c => 
        c.nombre_razon_social.toLowerCase().includes(q) || 
        c.num_documento.toLowerCase().includes(q)
    );
});

const selectCliente = (cliente: any) => {
    form.id_cliente = cliente.id_cliente.toString();
    isClientPickerOpen.value = false;
    clientSearchQuery.value = '';
};

// Modal de Nuevo Cliente Rápido
const openNewClientModal = () => {
    newClientForm.reset();
    newClientForm.clearErrors();
    isNewClientModalOpen.value = true;
};

const submitNewClient = () => {
    const doc = newClientForm.num_documento.trim();
    const tipo = newClientForm.tipo_documento;

    if (tipo === 'DNI') {
        if (doc.length !== 8 || isNaN(Number(doc))) {
            newClientForm.setError('num_documento', 'El DNI debe contener exactamente 8 dígitos numéricos.');
            return;
        }
    } else if (tipo === 'RUC') {
        if (doc.length !== 11 || isNaN(Number(doc)) || !(doc.startsWith('10') || doc.startsWith('20'))) {
            newClientForm.setError('num_documento', 'El RUC debe contener exactamente 11 dígitos y comenzar con 10 o 20.');
            return;
        }
    }

    newClientForm.post('/terceros/cliente', {
        preserveScroll: true,
        onSuccess: () => {
            const newlyCreated = props.clientes.find(c => c.num_documento === doc);
            if (newlyCreated) {
                form.id_cliente = newlyCreated.id_cliente.toString();
            }
            isNewClientModalOpen.value = false;
            isClientPickerOpen.value = false;
            newClientForm.reset();
        }
    });
};

// Picker de Producto por Línea
const openProductPicker = (index: number) => {
    activeProductLineIndex.value = index;
    productSearchQuery.value = '';
    isProductPickerOpen.value = true;
};

const getSelectedProductDescription = (idStr: string) => {
    const prod = props.productos.find(p => p.id_producto.toString() === idStr);
    if (!prod) return 'Seleccionar Producto...';
    return `${prod.descripcion} (Stock: ${prod.stock_actual})`;
};

const filteredProductos = computed(() => {
    const q = productSearchQuery.value.trim().toLowerCase();
    if (!q) return props.productos;
    return props.productos.filter(p => 
        p.descripcion.toLowerCase().includes(q) || 
        (p.codigo_barras && p.codigo_barras.toLowerCase().includes(q)) ||
        (p.categoria?.nombre && p.categoria.nombre.toLowerCase().includes(q))
    );
});

// Lógica de precio por mayor
const getEffectiveUnitPrice = (idStr: string, cantidad: number) => {
    const prod = props.productos.find(p => p.id_producto.toString() === idStr);
    if (!prod) return 0;
    
    const cantMayor = prod.cant_mayorista ? Number(prod.cant_mayorista) : 6;
    const precioMayor = prod.precio_mayorista ? Number(prod.precio_mayorista) : 0;
    
    if (precioMayor > 0 && cantidad >= cantMayor) {
        return precioMayor;
    }
    return Number(prod.precio_venta);
};

const isWholesaleApplied = (idStr: string, cantidad: number) => {
    const prod = props.productos.find(p => p.id_producto.toString() === idStr);
    if (!prod) return false;
    const cantMayor = prod.cant_mayorista ? Number(prod.cant_mayorista) : 6;
    const precioMayor = prod.precio_mayorista ? Number(prod.precio_mayorista) : 0;
    return (precioMayor > 0 && cantidad >= cantMayor);
};

const onQuantityChange = (line: any) => {
    line.precio_unitario = getEffectiveUnitPrice(line.id_producto, line.cantidad);
};

const selectProduct = (prod: any) => {
    if (activeProductLineIndex.value !== null && form.detalles[activeProductLineIndex.value]) {
        const idx = activeProductLineIndex.value;
        form.detalles[idx].id_producto = prod.id_producto.toString();
        form.detalles[idx].cantidad = 1;
        form.detalles[idx].precio_unitario = getEffectiveUnitPrice(prod.id_producto.toString(), 1);
        form.detalles[idx].descuento = 0;
    }
    isProductPickerOpen.value = false;
    activeProductLineIndex.value = null;
    productSearchQuery.value = '';
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
                title: 'Registro de Ventas',
                href: '/ventas',
            },
        ],
    },
});
</script>

<template>
    <Head title="Registro de Ventas - GUESAA SIC" />

        <div class="p-6 max-w-7xl mx-auto space-y-6">
            
            <!-- Encabezado -->
            <div class="flex justify-between items-center border-b pb-5 border-zinc-200 dark:border-zinc-800">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-zinc-950 dark:text-zinc-50">Registro de Ventas</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        Cobra al contado, verifica stocks de forma reactiva y genera reportes comerciales.
                    </p>
                </div>
                <button
                    @click="openNewVentaModal"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors"
                >
                    <Plus class="h-4 w-4" />
                    Registrar Venta
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
                        Registrar Nueva Venta
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
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Cliente</label>
                                    <button type="button" @click="openNewClientModal" class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold hover:underline flex items-center gap-1">
                                        <UserPlus class="h-3 w-3" />
                                        <span>+ Nuevo Cliente</span>
                                    </button>
                                </div>
                                <button type="button" @click="isClientPickerOpen = true" class="mt-1 w-full text-left rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none flex items-center justify-between shadow-sm hover:bg-zinc-50 dark:hover:bg-zinc-850 transition-colors">
                                    <span class="truncate font-medium">{{ selectedClienteName }}</span>
                                    <Search class="h-4 w-4 text-zinc-400 shrink-0 ml-1" />
                                </button>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Tipo Comprobante</label>
                                <select v-model="form.tipo_comprobante" class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option value="Boleta">Boleta de Venta</option>
                                    <option value="Factura">Factura</option>
                                </select>
                            </div>
                            <div>
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">N° Comprobante</label>
                                    <button type="button" @click="autoGenerateNumComprobante" class="text-[11px] text-indigo-600 dark:text-indigo-400 font-semibold hover:underline flex items-center gap-1 transition-colors">
                                        <Sparkles class="h-3 w-3 text-amber-500" />
                                        <span>⚡ Auto Generar</span>
                                    </button>
                                </div>
                                <input v-model="form.num_comprobante" type="text" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 font-mono font-medium" placeholder="Ej: B001-00000001" />
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
                                        <button type="button" @click="openProductPicker(index)" class="mt-1 w-full text-left rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-2 py-1.5 text-xs text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none flex items-center justify-between truncate shadow-sm hover:bg-zinc-50 dark:hover:bg-zinc-850 transition-colors">
                                            <span class="truncate">{{ getSelectedProductDescription(line.id_producto) }}</span>
                                            <Search class="h-3.5 w-3.5 text-zinc-400 shrink-0 ml-1" />
                                        </button>
                                    </div>

                                    <!-- Cantidad -->
                                    <div class="col-span-2">
                                        <label class="block text-[10px] font-semibold text-zinc-400 uppercase flex justify-between">
                                            Cantidad 
                                            <span class="text-[9px] text-zinc-400 font-normal">(Max: {{ getProductStock(line.id_producto) }})</span>
                                        </label>
                                        <input v-model="line.cantidad" @input="onQuantityChange(line)" @change="onQuantityChange(line)" type="number" min="1" required class="mt-1 block w-full rounded border px-2 py-1 text-xs text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none"
                                            :class="isStockInsufficient(line) ? 'border-red-500 bg-red-50 dark:bg-red-950/20 text-red-900' : 'border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-955'" />
                                    </div>

                                    <!-- Precio Venta -->
                                    <div class="col-span-2">
                                        <div class="flex items-center justify-between">
                                            <label class="block text-[10px] font-semibold text-zinc-400 uppercase">P. Unitario</label>
                                            <span v-if="isWholesaleApplied(line.id_producto, line.cantidad)" class="text-[9px] font-bold text-emerald-600 dark:text-emerald-400">
                                                ¡Mayorista!
                                            </span>
                                        </div>
                                        <span class="block text-xs font-semibold mt-1 py-1 px-2 rounded"
                                            :class="isWholesaleApplied(line.id_producto, line.cantidad) ? 'bg-emerald-100 text-emerald-900 dark:bg-emerald-950/50 dark:text-emerald-300 font-bold border border-emerald-300 dark:border-emerald-800' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100'">
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

                        <!-- Sección de Totales -->
                        <div class="flex justify-end border-t pt-4 border-zinc-100 dark:border-zinc-850">
                            <!-- Resumen Importes -->
                            <div class="w-full md:w-72 space-y-2 text-sm pr-2">
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

            <!-- MODAL BUSCADOR DE CLIENTES -->
            <div v-if="isClientPickerOpen" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                <div class="w-full max-w-lg rounded-xl border bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 p-5 shadow-2xl animate-in fade-in zoom-in-95 duration-150 flex flex-col max-h-[80vh]">
                    
                    <div class="flex items-center justify-between border-b pb-3 border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center gap-2">
                            <Search class="h-4 w-4 text-indigo-600 dark:text-indigo-400" />
                            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">Buscar y Seleccionar Cliente</h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="openNewClientModal" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-md transition-colors cursor-pointer">
                                <UserPlus class="h-3 w-3" />
                                <span>+ Nuevo Cliente</span>
                            </button>
                            <button type="button" @click="isClientPickerOpen = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 p-1">
                                <X class="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Buscador -->
                    <div class="my-3 relative">
                        <input 
                            v-model="clientSearchQuery" 
                            type="text" 
                            autofocus
                            placeholder="Buscar por DNI, RUC o Nombre..." 
                            class="w-full pl-9 pr-3 py-2 text-xs rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none" 
                        />
                        <Search class="h-4 w-4 text-zinc-400 absolute left-3 top-2.5" />
                    </div>

                    <!-- Lista de Resultados -->
                    <div class="flex-1 overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800 pr-1">
                        <div v-if="filteredClientes.length === 0" class="p-6 text-center text-xs text-zinc-400">
                            No se encontraron clientes coincidentes.
                        </div>
                        <button 
                            v-for="c in filteredClientes" 
                            :key="c.id_cliente" 
                            type="button" 
                            @click="selectCliente(c)" 
                            class="w-full text-left p-3 hover:bg-indigo-50/50 dark:hover:bg-indigo-950/20 transition-colors flex items-center justify-between group rounded-lg cursor-pointer"
                            :class="form.id_cliente === c.id_cliente.toString() ? 'bg-indigo-50/80 dark:bg-indigo-950/40 border-l-2 border-indigo-600' : ''"
                        >
                            <div>
                                <p class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    {{ c.nombre_razon_social }}
                                </p>
                                <p class="text-[11px] text-zinc-400 mt-0.5">
                                    <span class="font-mono">{{ c.tipo_documento }}: {{ c.num_documento }}</span>
                                    <span v-if="c.telefono"> • Tel: {{ c.telefono }}</span>
                                </p>
                            </div>
                            <span v-if="form.id_cliente === c.id_cliente.toString()" class="text-indigo-600 dark:text-indigo-400">
                                <Check class="h-4 w-4" />
                            </span>
                        </button>
                    </div>

                </div>
            </div>

            <!-- MODAL REGISTRO RÁPIDO DE NUEVO CLIENTE -->
            <div v-if="isNewClientModalOpen" class="fixed inset-0 z-[70] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                <div class="w-full max-w-md rounded-xl border bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 p-6 shadow-2xl animate-in fade-in zoom-in-95 duration-150">
                    
                    <div class="flex items-center justify-between border-b pb-3 border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center gap-2">
                            <UserPlus class="h-4 w-4 text-indigo-600 dark:text-indigo-400" />
                            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">Registrar Nuevo Cliente</h3>
                        </div>
                        <button type="button" @click="isNewClientModalOpen = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                            <X class="h-4 w-4" />
                        </button>
                    </div>

                    <form @submit.prevent="submitNewClient" class="mt-4 space-y-3">
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-[10px] font-semibold text-zinc-500 uppercase">Documento</label>
                                <select v-model="newClientForm.tipo_documento" class="mt-1 block w-full rounded border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-2 py-1.5 text-xs text-zinc-900 dark:text-zinc-50">
                                    <option value="DNI">DNI</option>
                                    <option value="RUC">RUC</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-[10px] font-semibold text-zinc-500 uppercase">N° Documento</label>
                                <input v-model="newClientForm.num_documento" type="text" required class="mt-1 block w-full rounded border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-2 py-1.5 text-xs text-zinc-900 dark:text-zinc-50" placeholder="8 u 11 dígitos" />
                                <span v-if="newClientForm.errors.num_documento" class="text-[10px] text-red-500 block mt-0.5">{{ newClientForm.errors.num_documento }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-semibold text-zinc-500 uppercase">Nombre o Razón Social</label>
                            <input v-model="newClientForm.nombre_razon_social" type="text" required class="mt-1 block w-full rounded border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-2.5 py-1.5 text-xs text-zinc-900 dark:text-zinc-50" placeholder="Ej: Juan Pérez / Empresa S.A.C." />
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-semibold text-zinc-500 uppercase">Dirección (opcional)</label>
                                <input v-model="newClientForm.direccion" type="text" class="mt-1 block w-full rounded border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-2 py-1.5 text-xs text-zinc-900 dark:text-zinc-50" placeholder="Chiclayo" />
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-zinc-500 uppercase">Teléfono (opcional)</label>
                                <input v-model="newClientForm.telefono" type="text" class="mt-1 block w-full rounded border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-2 py-1.5 text-xs text-zinc-900 dark:text-zinc-50" placeholder="987654321" />
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 border-t pt-3 border-zinc-100 dark:border-zinc-800 mt-4">
                            <button type="button" @click="isNewClientModalOpen = false" class="px-3 py-1.5 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs text-zinc-700 dark:text-zinc-300">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="newClientForm.processing" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-lg transition-colors cursor-pointer">
                                Guardar y Seleccionar
                            </button>
                        </div>
                    </form>

                </div>
            </div>

            <!-- MODAL BUSCADOR DE PRODUCTOS -->
            <div v-if="isProductPickerOpen" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                <div class="w-full max-w-xl rounded-xl border bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 p-5 shadow-2xl animate-in fade-in zoom-in-95 duration-150 flex flex-col max-h-[80vh]">
                    
                    <div class="flex items-center justify-between border-b pb-3 border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center gap-2">
                            <Search class="h-4 w-4 text-indigo-600 dark:text-indigo-400" />
                            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">Buscar Producto para Venta</h3>
                        </div>
                        <button type="button" @click="isProductPickerOpen = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 p-1">
                            <X class="h-4 w-4" />
                        </button>
                    </div>

                    <!-- Buscador por código o nombre -->
                    <div class="my-3 relative">
                        <input 
                            v-model="productSearchQuery" 
                            type="text" 
                            autofocus
                            placeholder="Buscar por Descripción, Código de Barras o Categoría..." 
                            class="w-full pl-9 pr-3 py-2 text-xs rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none" 
                        />
                        <Search class="h-4 w-4 text-zinc-400 absolute left-3 top-2.5" />
                    </div>

                    <!-- Lista de Productos -->
                    <div class="flex-1 overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800 pr-1">
                        <div v-if="filteredProductos.length === 0" class="p-6 text-center text-xs text-zinc-400">
                            No se encontraron productos coincidentes.
                        </div>
                        <button 
                            v-for="p in filteredProductos" 
                            :key="p.id_producto" 
                            type="button" 
                            @click="selectProduct(p)" 
                            class="w-full text-left p-3 hover:bg-indigo-50/50 dark:hover:bg-indigo-950/20 transition-colors flex items-center justify-between group rounded-lg cursor-pointer"
                        >
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                        {{ p.descripcion }}
                                    </span>
                                    <span v-if="p.categoria" class="text-[10px] px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                                        {{ p.categoria.nombre }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-zinc-400 flex items-center gap-2">
                                    <span v-if="p.codigo_barras" class="font-mono flex items-center gap-1">
                                        <Barcode class="h-3 w-3 inline" /> {{ p.codigo_barras }}
                                    </span>
                                    <span class="font-semibold text-zinc-700 dark:text-zinc-300">
                                        Precio: {{ formatCurrency(Number(p.precio_venta)) }}
                                    </span>
                                </p>
                            </div>
                            
                            <div class="text-right">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold"
                                    :class="p.stock_actual > p.stock_minimo ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400' : (p.stock_actual > 0 ? 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400' : 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400')">
                                    Stock: {{ p.stock_actual }}
                                </span>
                            </div>
                        </button>
                    </div>

                </div>
            </div>

    </div>
</template>
