<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';

import { 
    Plus, 
    Trash2, 
    ShoppingCart, 
    AlertCircle, 
    Check, 
    FileText,
    Search,
    UserPlus,
    X,
    Truck,
    Barcode
} from '@lucide/vue';

const props = defineProps<{
    compras: any[];
    proveedores: any[];
    productos: any[];
    errors: any;
}>();

const isModalOpen = ref(false);

// Estados para Buscador / Picker de Proveedor
const isProveedorPickerOpen = ref(false);
const proveedorSearchQuery = ref('');

// Estados para Registro Rápido de Nuevo Proveedor
const isNewProveedorModalOpen = ref(false);
const newProveedorForm = useForm({
    ruc: '',
    razon_social: '',
    direccion: '',
    telefono: '',
});

// Estados para Buscador / Picker de Producto por línea
const isProductPickerOpen = ref(false);
const activeProductLineIndex = ref<number | null>(null);
const productSearchQuery = ref('');

const form = useForm({
    id_proveedor: '',
    tipo_comprobante: 'Factura',
    num_comprobante: '',
    detalles: [] as Array<{ id_producto: string; cantidad: number; precio_unitario: number }>,
});

const openNewCompraModal = () => {
    form.reset();
    if (props.proveedores.length > 0) {
        form.id_proveedor = props.proveedores[0].id_proveedor.toString();
    }
    form.detalles = [];
    addLine();
    form.clearErrors();
    isModalOpen.value = true;
};

const addLine = () => {
    if (props.productos.length > 0) {
        form.detalles.push({
            id_producto: props.productos[0].id_producto.toString(),
            cantidad: 1,
            precio_unitario: Number(props.productos[0].precio_compra)
        });
    }
};

const removeLine = (index: number) => {
    form.detalles.splice(index, 1);
};

// Proveedor seleccionado etiqueta
const selectedProveedorName = computed(() => {
    const found = props.proveedores.find(p => p.id_proveedor.toString() === form.id_proveedor);
    if (!found) return 'Seleccionar Proveedor...';
    return `${found.razon_social} (RUC: ${found.ruc})`;
});

// Filtro de Proveedores en tiempo real
const filteredProveedores = computed(() => {
    const q = proveedorSearchQuery.value.trim().toLowerCase();
    if (!q) return props.proveedores;
    return props.proveedores.filter(p => 
        p.razon_social.toLowerCase().includes(q) || 
        p.ruc.toLowerCase().includes(q)
    );
});

const selectProveedor = (proveedor: any) => {
    form.id_proveedor = proveedor.id_proveedor.toString();
    isProveedorPickerOpen.value = false;
    proveedorSearchQuery.value = '';
};

// Modal de Nuevo Proveedor Rápido
const openNewProveedorModal = () => {
    newProveedorForm.reset();
    newProveedorForm.clearErrors();
    isNewProveedorModalOpen.value = true;
};

const submitNewProveedor = () => {
    const ruc = newProveedorForm.ruc.trim();

    if (ruc.length !== 11 || isNaN(Number(ruc)) || !(ruc.startsWith('10') || ruc.startsWith('20'))) {
        newProveedorForm.setError('ruc', 'El RUC debe contener exactamente 11 dígitos numéricos y comenzar con 10 o 20.');
        return;
    }

    newProveedorForm.post('/terceros/proveedor', {
        preserveScroll: true,
        onSuccess: () => {
            const newlyCreated = props.proveedores.find(p => p.ruc === ruc);
            if (newlyCreated) {
                form.id_proveedor = newlyCreated.id_proveedor.toString();
            }
            isNewProveedorModalOpen.value = false;
            isProveedorPickerOpen.value = false;
            newProveedorForm.reset();
        }
    });
};

// Picker de Producto por Línea de Compra
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

const selectProduct = (prod: any) => {
    if (activeProductLineIndex.value !== null && form.detalles[activeProductLineIndex.value]) {
        const idx = activeProductLineIndex.value;
        form.detalles[idx].id_producto = prod.id_producto.toString();
        form.detalles[idx].precio_unitario = Number(prod.precio_compra);
    }
    isProductPickerOpen.value = false;
    activeProductLineIndex.value = null;
    productSearchQuery.value = '';
};

// Totales calculados
const computedSubtotal = computed(() => {
    return form.detalles.reduce((acc, line) => acc + (line.cantidad * line.precio_unitario), 0);
});

const computedIgv = computed(() => {
    return computedSubtotal.value * 0.18;
});

const computedTotal = computed(() => {
    return computedSubtotal.value + computedIgv.value;
});

const submitCompra = () => {
    if (form.detalles.length === 0) {
        alert('Debe agregar al menos un producto.');
        return;
    }
    form.post('/compras', {
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
        day: 'numeric'
    });
};
defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Registro de Compras',
                href: '/compras',
            },
        ],
    },
});
</script>

<template>
    <Head title="Registro de Compras - GUESAA SIC" />

        <div class="p-6 max-w-7xl mx-auto space-y-6">
            
            <!-- Encabezado -->
            <div class="flex justify-between items-center border-b pb-5 border-zinc-200 dark:border-zinc-800">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-zinc-950 dark:text-zinc-50">Registro de Compras</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        Ingresa facturas de proveedores, incrementa el stock de medicamentos e insumos, y genera los asientos de forma automática.
                    </p>
                </div>
                <button
                    @click="openNewCompraModal"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors"
                >
                    <Plus class="h-4 w-4" />
                    Nueva Compra
                </button>
            </div>

            <!-- Alertas globales de Laravel -->
            <div v-if="Object.keys(errors).length > 0" class="rounded-lg bg-red-50 p-4 dark:bg-red-950/20 border border-red-200 dark:border-red-800/40">
                <div class="flex">
                    <AlertCircle class="h-5 w-5 text-red-600 dark:text-red-400" />
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Hubo errores al procesar la compra</h3>
                        <ul class="mt-2 list-disc list-inside text-xs text-red-700 dark:text-red-400 space-y-1">
                            <li v-for="err in errors" :key="err">{{ err }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tabla de Compras Realizadas -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-xs uppercase text-zinc-700 dark:text-zinc-300">
                            <tr>
                                <th class="p-4">ID</th>
                                <th class="p-4">Proveedor</th>
                                <th class="p-4">Comprobante</th>
                                <th class="p-4">Fecha</th>
                                <th class="p-4 text-right">Subtotal</th>
                                <th class="p-4 text-right">IGV (18%)</th>
                                <th class="p-4 text-right">Total</th>
                                <th class="p-4 text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            <tr v-if="compras.length === 0">
                                <td colspan="8" class="p-8 text-center text-zinc-400">No se han registrado compras. Presione "Nueva Compra" para registrar una.</td>
                            </tr>
                            <tr v-for="c in compras" :key="c.id_compra" class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                                <td class="p-4 font-mono">{{ c.id_compra }}</td>
                                <td class="p-4">
                                    <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ c.proveedor?.razon_social }}</p>
                                    <p class="text-xs text-zinc-400 mt-0.5">RUC: {{ c.proveedor?.ruc }}</p>
                                </td>
                                <td class="p-4">
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ c.tipo_comprobante }}</span>
                                    <p class="text-xs text-zinc-400 mt-0.5">{{ c.num_comprobante }}</p>
                                </td>
                                <td class="p-4 text-xs">
                                    {{ formatDate(c.fecha_compra) }}
                                </td>
                                <td class="p-4 text-right">{{ formatCurrency(c.subtotal) }}</td>
                                <td class="p-4 text-right text-zinc-400">{{ formatCurrency(c.igv) }}</td>
                                <td class="p-4 text-right font-semibold text-zinc-900 dark:text-zinc-50">{{ formatCurrency(c.total) }}</td>
                                <td class="p-4 text-center">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-950/20 dark:text-emerald-400">
                                        {{ c.estado }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MODAL REGISTRO DE COMPRA -->
            <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                <div class="w-full max-w-4xl rounded-xl border bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 p-6 shadow-xl animate-in fade-in zoom-in-95 duration-150 max-h-[90vh] flex flex-col">
                    
                    <h3 class="text-lg font-semibold text-zinc-950 dark:text-zinc-50 border-b pb-3 border-zinc-100 dark:border-zinc-800">
                        Nuevo Registro de Compra (Ingreso de Mercadería)
                    </h3>

                    <form @submit.prevent="submitCompra" class="mt-4 flex-1 overflow-y-auto space-y-4 pr-1">
                        
                        <!-- Campos Cabecera -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Proveedor</label>
                                    <button type="button" @click="openNewProveedorModal" class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold hover:underline flex items-center gap-1">
                                        <UserPlus class="h-3 w-3" />
                                        <span>+ Nuevo Proveedor</span>
                                    </button>
                                </div>
                                <button type="button" @click="isProveedorPickerOpen = true" class="mt-1 w-full text-left rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none flex items-center justify-between shadow-sm hover:bg-zinc-50 dark:hover:bg-zinc-850 transition-colors">
                                    <span class="truncate font-medium">{{ selectedProveedorName }}</span>
                                    <Search class="h-4 w-4 text-zinc-400 shrink-0 ml-1" />
                                </button>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Tipo Comprobante</label>
                                <select v-model="form.tipo_comprobante" class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option value="Factura">Factura</option>
                                    <option value="Boleta">Boleta de Venta</option>
                                    <option value="Guia">Guía de Remisión</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">N° Comprobante</label>
                                <input v-model="form.num_comprobante" type="text" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Ej: F001-12345" />
                            </div>
                        </div>

                        <!-- Detalle de Productos -->
                        <div class="border-t pt-4 border-zinc-100 dark:border-zinc-850">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">Ítems de la Factura</span>
                                <button type="button" @click="addLine" class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                                    <Plus class="h-3.5 w-3.5" />
                                    Agregar Producto
                                </button>
                            </div>

                            <div class="space-y-2">
                                <div v-for="(line, index) in form.detalles" :key="index" class="grid grid-cols-12 gap-2 items-center bg-zinc-50 dark:bg-zinc-800/40 p-2 rounded-lg border border-zinc-100 dark:border-zinc-800">
                                    
                                    <!-- Selector de Producto -->
                                    <div class="col-span-6">
                                        <label class="block text-[10px] font-semibold text-zinc-400 uppercase">Producto</label>
                                        <button type="button" @click="openProductPicker(index)" class="mt-1 w-full text-left rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-2 py-1.5 text-xs text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none flex items-center justify-between truncate shadow-sm hover:bg-zinc-50 dark:hover:bg-zinc-850 transition-colors">
                                            <span class="truncate">{{ getSelectedProductDescription(line.id_producto) }}</span>
                                            <Search class="h-3.5 w-3.5 text-zinc-400 shrink-0 ml-1" />
                                        </button>
                                    </div>

                                    <!-- Cantidad -->
                                    <div class="col-span-2">
                                        <label class="block text-[10px] font-semibold text-zinc-400 uppercase">Cantidad</label>
                                        <input v-model="line.cantidad" type="number" min="1" required class="mt-1 block w-full rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none" />
                                    </div>

                                    <!-- Precio Unitario -->
                                    <div class="col-span-2">
                                        <label class="block text-[10px] font-semibold text-zinc-400 uppercase">P. Unitario</label>
                                        <input v-model="line.precio_unitario" type="number" step="0.01" min="0" required class="mt-1 block w-full rounded border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-950 px-2 py-1 text-xs text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none" />
                                    </div>

                                    <!-- Subtotal Fila -->
                                    <div class="col-span-1 text-center">
                                        <label class="block text-[10px] font-semibold text-zinc-400 uppercase">Subtotal</label>
                                        <span class="block text-xs font-semibold text-zinc-900 dark:text-zinc-100 mt-2">
                                            {{ formatCurrency(line.cantidad * line.precio_unitario) }}
                                        </span>
                                    </div>

                                    <!-- Eliminar Fila -->
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
                                    <span class="text-zinc-500">Subtotal</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ formatCurrency(computedSubtotal) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-zinc-500">IGV (18%)</span>
                                    <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ formatCurrency(computedIgv) }}</span>
                                </div>
                                <div class="flex justify-between border-t pt-2 border-zinc-100 dark:border-zinc-800">
                                    <span class="font-semibold text-zinc-900 dark:text-zinc-100">Total Factura</span>
                                    <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ formatCurrency(computedTotal) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="flex justify-end gap-2 border-t pt-4 border-zinc-100 dark:border-zinc-800 mt-6">
                            <button type="button" @click="isModalOpen = false" class="px-4 py-2 border border-zinc-200 dark:border-zinc-850 rounded-lg text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-850/50 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="form.processing" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-sm transition-colors disabled:opacity-50">
                                <ShoppingCart class="h-4 w-4" />
                                Procesar Compra y Asiento
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL BUSCADOR DE PROVEEDORES -->
            <div v-if="isProveedorPickerOpen" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                <div class="w-full max-w-lg rounded-xl border bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 p-5 shadow-2xl animate-in fade-in zoom-in-95 duration-150 flex flex-col max-h-[80vh]">
                    
                    <div class="flex items-center justify-between border-b pb-3 border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center gap-2">
                            <Search class="h-4 w-4 text-indigo-600 dark:text-indigo-400" />
                            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">Buscar y Seleccionar Proveedor</h3>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" @click="openNewProveedorModal" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-500 rounded-md transition-colors cursor-pointer">
                                <UserPlus class="h-3 w-3" />
                                <span>+ Nuevo Proveedor</span>
                            </button>
                            <button type="button" @click="isProveedorPickerOpen = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 p-1">
                                <X class="h-4 w-4" />
                            </button>
                        </div>
                    </div>

                    <!-- Buscador -->
                    <div class="my-3 relative">
                        <input 
                            v-model="proveedorSearchQuery" 
                            type="text" 
                            autofocus
                            placeholder="Buscar por RUC o Razón Social..." 
                            class="w-full pl-9 pr-3 py-2 text-xs rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950 text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none" 
                        />
                        <Search class="h-4 w-4 text-zinc-400 absolute left-3 top-2.5" />
                    </div>

                    <!-- Lista de Resultados -->
                    <div class="flex-1 overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800 pr-1">
                        <div v-if="filteredProveedores.length === 0" class="p-6 text-center text-xs text-zinc-400">
                            No se encontraron proveedores coincidentes.
                        </div>
                        <button 
                            v-for="p in filteredProveedores" 
                            :key="p.id_proveedor" 
                            type="button" 
                            @click="selectProveedor(p)" 
                            class="w-full text-left p-3 hover:bg-indigo-50/50 dark:hover:bg-indigo-950/20 transition-colors flex items-center justify-between group rounded-lg cursor-pointer"
                            :class="form.id_proveedor === p.id_proveedor.toString() ? 'bg-indigo-50/80 dark:bg-indigo-950/40 border-l-2 border-indigo-600' : ''"
                        >
                            <div>
                                <p class="text-xs font-semibold text-zinc-900 dark:text-zinc-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                    {{ p.razon_social }}
                                </p>
                                <p class="text-[11px] text-zinc-400 mt-0.5">
                                    <span class="font-mono">RUC: {{ p.ruc }}</span>
                                    <span v-if="p.telefono"> • Tel: {{ p.telefono }}</span>
                                </p>
                            </div>
                            <span v-if="form.id_proveedor === p.id_proveedor.toString()" class="text-indigo-600 dark:text-indigo-400">
                                <Check class="h-4 w-4" />
                            </span>
                        </button>
                    </div>

                </div>
            </div>

            <!-- MODAL REGISTRO RÁPIDO DE NUEVO PROVEEDOR -->
            <div v-if="isNewProveedorModalOpen" class="fixed inset-0 z-[70] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
                <div class="w-full max-w-md rounded-xl border bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 p-6 shadow-2xl animate-in fade-in zoom-in-95 duration-150">
                    
                    <div class="flex items-center justify-between border-b pb-3 border-zinc-100 dark:border-zinc-800">
                        <div class="flex items-center gap-2">
                            <Truck class="h-4 w-4 text-indigo-600 dark:text-indigo-400" />
                            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">Registrar Nuevo Proveedor</h3>
                        </div>
                        <button type="button" @click="isNewProveedorModalOpen = false" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                            <X class="h-4 w-4" />
                        </button>
                    </div>

                    <form @submit.prevent="submitNewProveedor" class="mt-4 space-y-3">
                        <div>
                            <label class="block text-[10px] font-semibold text-zinc-500 uppercase">N° RUC</label>
                            <input v-model="newProveedorForm.ruc" type="text" required class="mt-1 block w-full rounded border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-2.5 py-1.5 text-xs text-zinc-900 dark:text-zinc-50" placeholder="11 dígitos (empezando con 10 o 20)" />
                            <span v-if="newProveedorForm.errors.ruc" class="text-[10px] text-red-500 block mt-0.5">{{ newProveedorForm.errors.ruc }}</span>
                        </div>

                        <div>
                            <label class="block text-[10px] font-semibold text-zinc-500 uppercase">Razón Social</label>
                            <input v-model="newProveedorForm.razon_social" type="text" required class="mt-1 block w-full rounded border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-2.5 py-1.5 text-xs text-zinc-900 dark:text-zinc-50" placeholder="Ej: Laboratorios Droguería S.A.C." />
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] font-semibold text-zinc-500 uppercase">Dirección (opcional)</label>
                                <input v-model="newProveedorForm.direccion" type="text" class="mt-1 block w-full rounded border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-2 py-1.5 text-xs text-zinc-900 dark:text-zinc-50" placeholder="Lima / Chiclayo" />
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-zinc-500 uppercase">Teléfono (opcional)</label>
                                <input v-model="newProveedorForm.telefono" type="text" class="mt-1 block w-full rounded border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-2 py-1.5 text-xs text-zinc-900 dark:text-zinc-50" placeholder="01-4567890" />
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 border-t pt-3 border-zinc-100 dark:border-zinc-800 mt-4">
                            <button type="button" @click="isNewProveedorModalOpen = false" class="px-3 py-1.5 border border-zinc-200 dark:border-zinc-800 rounded-lg text-xs text-zinc-700 dark:text-zinc-300">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="newProveedorForm.processing" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs rounded-lg transition-colors cursor-pointer">
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
                            <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 text-sm">Buscar Producto para Compra</h3>
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
                                        P. Compra: {{ formatCurrency(Number(p.precio_compra)) }}
                                    </span>
                                </p>
                            </div>
                            
                            <div class="text-right">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400">
                                    Stock Actual: {{ p.stock_actual }}
                                </span>
                            </div>
                        </button>
                    </div>

                </div>
            </div>

    </div>
</template>
