<script setup lang="ts">
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';

import { 
    Plus, 
    Edit, 
    Power, 
    AlertTriangle, 
    Package, 
    Tags, 
    AlertCircle 
} from '@lucide/vue';

const props = defineProps<{
    productos: any[];
    categorias: any[];
    errors: any;
}>();

const activeTab = ref('productos');

// Estados de modales
const isProductoModalOpen = ref(false);
const isCategoriaModalOpen = ref(false);
const editingProductoId = ref<number | null>(null);
const editingCategoriaId = ref<number | null>(null);

// Formulario de Productos
const productoForm = useForm({
    id_categoria: '',
    codigo_barras: '',
    descripcion: '',
    stock_actual: 0,
    stock_minimo: 5,
    precio_compra: 0.00,
    precio_venta: 0.00,
});

// Formulario de Categorías
const categoriaForm = useForm({
    nombre: '',
    descripcion: '',
});

// Funciones para Productos
const openNewProductoModal = () => {
    editingProductoId.value = null;
    productoForm.reset();
    if (props.categorias.length > 0) {
        productoForm.id_categoria = props.categorias[0].id_categoria.toString();
    }
    productoForm.clearErrors();
    isProductoModalOpen.value = true;
};

const openEditProductoModal = (prod: any) => {
    editingProductoId.value = prod.id_producto;
    productoForm.id_categoria = prod.id_categoria.toString();
    productoForm.codigo_barras = prod.codigo_barras || '';
    productoForm.descripcion = prod.descripcion;
    productoForm.stock_actual = prod.stock_actual;
    productoForm.stock_minimo = prod.stock_minimo;
    productoForm.precio_compra = Number(prod.precio_compra);
    productoForm.precio_venta = Number(prod.precio_venta);
    productoForm.clearErrors();
    isProductoModalOpen.value = true;
};

const submitProducto = () => {
    if (editingProductoId.value) {
        productoForm.put(`/inventario/producto/${editingProductoId.value}`, {
            onSuccess: () => {
                isProductoModalOpen.value = false;
                productoForm.reset();
            }
        });
    } else {
        productoForm.post('/inventario/producto', {
            onSuccess: () => {
                isProductoModalOpen.value = false;
                productoForm.reset();
            }
        });
    }
};

const toggleProducto = (id: number) => {
    productoForm.post(`/inventario/producto/${id}/toggle`);
};

// Funciones para Categorías
const openNewCategoriaModal = () => {
    editingCategoriaId.value = null;
    categoriaForm.reset();
    categoriaForm.clearErrors();
    isCategoriaModalOpen.value = true;
};

const openEditCategoriaModal = (cat: any) => {
    editingCategoriaId.value = cat.id_categoria;
    categoriaForm.nombre = cat.nombre;
    categoriaForm.descripcion = cat.descripcion || '';
    categoriaForm.clearErrors();
    isCategoriaModalOpen.value = true;
};

const submitCategoria = () => {
    if (editingCategoriaId.value) {
        categoriaForm.put(`/inventario/categoria/${editingCategoriaId.value}`, {
            onSuccess: () => {
                isCategoriaModalOpen.value = false;
                categoriaForm.reset();
            }
        });
    } else {
        categoriaForm.post('/inventario/categoria', {
            onSuccess: () => {
                isCategoriaModalOpen.value = false;
                categoriaForm.reset();
            }
        });
    }
};

const formatCurrency = (val: number) => {
    return new Intl.NumberFormat('es-PE', { style: 'currency', currency: 'PEN' }).format(val);
};
defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Inventario de Productos',
                href: '/inventario',
            },
        ],
    },
});
</script>

<template>
    <Head title="Inventario y Kardex - GUESAA SIC" />

        <div class="p-6 max-w-7xl mx-auto space-y-6">
            
            <!-- Encabezado -->
            <div class="flex justify-between items-center border-b pb-5 border-zinc-200 dark:border-zinc-800">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-zinc-950 dark:text-zinc-50">Inventario y Kardex</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        Catálogo de productos, control de stock mínimo y categorías.
                    </p>
                </div>
                <button
                    @click="activeTab === 'productos' ? openNewProductoModal() : openNewCategoriaModal()"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors"
                >
                    <Plus class="h-4 w-4" />
                    Registrar {{ activeTab === 'productos' ? 'Producto' : 'Categoría' }}
                </button>
            </div>

            <!-- Pestañas -->
            <div class="border-b border-zinc-200 dark:border-zinc-800">
                <nav class="-mb-px flex gap-6">
                    <button
                        @click="activeTab = 'productos'"
                        class="pb-4 px-1 text-sm font-medium border-b-2 transition-colors flex items-center gap-2"
                        :class="activeTab === 'productos' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:hover:text-zinc-300'"
                    >
                        <Package class="h-4.5 w-4.5" />
                        Productos ({{ productos.length }})
                    </button>
                    <button
                        @click="activeTab = 'categorias'"
                        class="pb-4 px-1 text-sm font-medium border-b-2 transition-colors flex items-center gap-2"
                        :class="activeTab === 'categorias' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:hover:text-zinc-300'"
                    >
                        <Tags class="h-4.5 w-4.5" />
                        Categorías ({{ categorias.length }})
                    </button>
                </nav>
            </div>

            <!-- Alertas globales de Laravel -->
            <div v-if="Object.keys(errors).length > 0" class="rounded-lg bg-red-50 p-4 dark:bg-red-950/20 border border-red-200 dark:border-red-800/40">
                <div class="flex">
                    <AlertCircle class="h-5 w-5 text-red-600 dark:text-red-400" />
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Hubo errores de validación</h3>
                        <ul class="mt-2 list-disc list-inside text-xs text-red-700 dark:text-red-400 space-y-1">
                            <li v-for="err in errors" :key="err">{{ err }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Contenido de Pestaña: Productos -->
            <div v-if="activeTab === 'productos'" class="space-y-4">
                
                <!-- Tabla de Productos -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                            <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-xs uppercase text-zinc-700 dark:text-zinc-300">
                                <tr>
                                    <th class="p-4">Código Barras</th>
                                    <th class="p-4">Descripción</th>
                                    <th class="p-4">Categoría</th>
                                    <th class="p-4">Stock Actual</th>
                                    <th class="p-4">Stock Mínimo</th>
                                    <th class="p-4">P. Compra</th>
                                    <th class="p-4">P. Venta</th>
                                    <th class="p-4 text-center">Estado</th>
                                    <th class="p-4 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                <tr v-if="productos.length === 0">
                                    <td colspan="9" class="p-8 text-center text-zinc-400">No hay productos registrados.</td>
                                </tr>
                                <tr v-for="p in productos" :key="p.id_producto" 
                                    class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors"
                                    :class="p.stock_actual <= p.stock_minimo ? 'bg-amber-50/20 dark:bg-amber-950/5' : ''">
                                    
                                    <td class="p-4 font-mono">{{ p.codigo_barras || 'S/C' }}</td>
                                    <td class="p-4 font-medium text-zinc-900 dark:text-zinc-100">
                                        <div class="flex items-center gap-2">
                                            <span>{{ p.descripcion }}</span>
                                            <span v-if="p.stock_actual <= p.stock_minimo" class="inline-flex items-center rounded-md bg-amber-50 px-1.5 py-0.5 text-xs font-medium text-amber-800 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-950/20 dark:text-amber-400">
                                                <AlertTriangle class="h-3 w-3 mr-0.5" />
                                                Bajo Stock
                                            </span>
                                        </div>
                                    </td>
                                    <td class="p-4">{{ p.categoria?.nombre }}</td>
                                    <td class="p-4 font-semibold" :class="p.stock_actual <= p.stock_minimo ? 'text-amber-600 dark:text-amber-400' : 'text-zinc-950 dark:text-zinc-50'">
                                        {{ p.stock_actual }}
                                    </td>
                                    <td class="p-4 text-zinc-400">{{ p.stock_minimo }}</td>
                                    <td class="p-4">{{ formatCurrency(Number(p.precio_compra)) }}</td>
                                    <td class="p-4 font-medium text-zinc-900 dark:text-zinc-100">{{ formatCurrency(Number(p.precio_venta)) }}</td>
                                    <td class="p-4 text-center">
                                        <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset"
                                            :class="p.estado ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-red-50 text-red-700 ring-red-600/20'">
                                            {{ p.estado ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right flex justify-end gap-2">
                                        <button @click="openEditProductoModal(p)" class="p-1.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg text-zinc-600 dark:text-zinc-300" title="Editar">
                                            <Edit class="h-4 w-4" />
                                        </button>
                                        <button @click="toggleProducto(p.id_producto)" class="p-1.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg" :class="p.estado ? 'text-red-500' : 'text-emerald-500'" :title="p.estado ? 'Desactivar' : 'Activar'">
                                            <Power class="h-4 w-4" />
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Contenido de Pestaña: Categorías -->
            <div v-else class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-xs uppercase text-zinc-700 dark:text-zinc-300">
                            <tr>
                                <th class="p-4">ID</th>
                                <th class="p-4">Nombre de Categoría</th>
                                <th class="p-4">Descripción</th>
                                <th class="p-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            <tr v-if="categorias.length === 0">
                                <td colspan="4" class="p-8 text-center text-zinc-400">No hay categorías registradas en el sistema.</td>
                            </tr>
                            <tr v-for="c in categorias" :key="c.id_categoria" class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                                <td class="p-4 font-mono">{{ c.id_categoria }}</td>
                                <td class="p-4 font-medium text-zinc-900 dark:text-zinc-100">{{ c.nombre }}</td>
                                <td class="p-4">{{ c.descripcion || '-' }}</td>
                                <td class="p-4 text-right flex justify-end gap-2">
                                    <button @click="openEditCategoriaModal(c)" class="p-1.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg text-zinc-600 dark:text-zinc-300" title="Editar">
                                        <Edit class="h-4 w-4" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MODAL PRODUCTO -->
            <div v-if="isProductoModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                <div class="w-full max-w-lg rounded-xl border bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 p-6 shadow-xl animate-in fade-in zoom-in-95 duration-150">
                    <h3 class="text-lg font-semibold text-zinc-950 dark:text-zinc-50 border-b pb-3 border-zinc-100 dark:border-zinc-800">
                        {{ editingProductoId ? 'Editar Producto' : 'Registrar Producto' }}
                    </h3>
                    
                    <form @submit.prevent="submitProducto" class="mt-4 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Categoría</label>
                                <select v-model="productoForm.id_categoria" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-955 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option v-for="c in categorias" :key="c.id_categoria" :value="c.id_categoria.toString()">
                                        {{ c.nombre }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Código de Barras</label>
                                <input v-model="productoForm.codigo_barras" type="text" class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Código de barras" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Descripción del Producto</label>
                            <input v-model="productoForm.descripcion" type="text" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Nombre comercial, presentación, etc." />
                        </div>

                        <div class="grid grid-cols-2 gap-4" v-if="!editingProductoId">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Stock Inicial</label>
                                <input v-model="productoForm.stock_actual" type="number" min="0" class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Stock Mínimo</label>
                                <input v-model="productoForm.stock_minimo" type="number" min="0" class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                            </div>
                        </div>

                        <!-- Si estamos editando, de todas formas permitimos ajustar el stock -->
                        <div class="grid grid-cols-2 gap-4" v-else>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Stock Actual</label>
                                <input v-model="productoForm.stock_actual" type="number" min="0" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Stock Mínimo</label>
                                <input v-model="productoForm.stock_minimo" type="number" min="0" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Precio Compra (S/.)</label>
                                <input v-model="productoForm.precio_compra" type="number" step="0.01" min="0" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Precio Venta (S/.)</label>
                                <input v-model="productoForm.precio_venta" type="number" step="0.01" min="0" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 border-t pt-4 border-zinc-100 dark:border-zinc-800 mt-6">
                            <button type="button" @click="isProductoModalOpen = false" class="px-4 py-2 border border-zinc-200 dark:border-zinc-850 rounded-lg text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-850/50 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="productoForm.processing" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-sm transition-colors disabled:opacity-50">
                                {{ editingProductoId ? 'Actualizar' : 'Guardar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL CATEGORÍA -->
            <div v-if="isCategoriaModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                <div class="w-full max-w-lg rounded-xl border bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 p-6 shadow-xl animate-in fade-in zoom-in-95 duration-150">
                    <h3 class="text-lg font-semibold text-zinc-950 dark:text-zinc-50 border-b pb-3 border-zinc-100 dark:border-zinc-800">
                        {{ editingCategoriaId ? 'Editar Categoría' : 'Registrar Categoría' }}
                    </h3>
                    
                    <form @submit.prevent="submitCategoria" class="mt-4 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Nombre de Categoría</label>
                            <input v-model="categoriaForm.nombre" type="text" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Ej: Abarrotes, Perfumería, Fármacos" />
                            <p v-if="categoriaForm.errors.nombre" class="text-xs text-red-500 mt-1">{{ categoriaForm.errors.nombre }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Descripción</label>
                            <input v-model="categoriaForm.descripcion" type="text" class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Breve detalle (opcional)" />
                        </div>

                        <div class="flex justify-end gap-2 border-t pt-4 border-zinc-100 dark:border-zinc-800 mt-6">
                            <button type="button" @click="isCategoriaModalOpen = false" class="px-4 py-2 border border-zinc-200 dark:border-zinc-850 rounded-lg text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-850/50 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="categoriaForm.processing" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-sm transition-colors disabled:opacity-50">
                                {{ editingCategoriaId ? 'Actualizar' : 'Guardar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

    </div>
</template>
