<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';

import { 
    Users, 
    Plus, 
    Edit, 
    Power, 
    Check, 
    AlertCircle,
    UserCheck,
    Truck,
    Trash2
} from '@lucide/vue';

const props = defineProps<{
    clientes: any[];
    proveedores: any[];
    errors: any;
}>();

// Estado local para controlar pestañas
const activeTab = ref('clientes');

// Estados para modales de edición / creación
const isClienteModalOpen = ref(false);
const isProveedorModalOpen = ref(false);
const editingClienteId = ref<number | null>(null);
const editingProveedorId = ref<number | null>(null);

// Formulario de Clientes
const clienteForm = useForm({
    tipo_documento: 'DNI',
    num_documento: '',
    nombre_razon_social: '',
    direccion: '',
    telefono: '',
});

// Formulario de Proveedores
const proveedorForm = useForm({
    ruc: '',
    razon_social: '',
    direccion: '',
    telefono: '',
});

// Funciones para Clientes
const openNewClienteModal = () => {
    editingClienteId.value = null;
    clienteForm.reset();
    clienteForm.clearErrors();
    isClienteModalOpen.value = true;
};

const openEditClienteModal = (cliente: any) => {
    editingClienteId.value = cliente.id_cliente;
    clienteForm.tipo_documento = cliente.tipo_documento;
    clienteForm.num_documento = cliente.num_documento;
    clienteForm.nombre_razon_social = cliente.nombre_razon_social;
    clienteForm.direccion = cliente.direccion || '';
    clienteForm.telefono = cliente.telefono || '';
    clienteForm.clearErrors();
    isClienteModalOpen.value = true;
};

const submitCliente = () => {
    // Validaciones básicas antes de enviar
    const doc = clienteForm.num_documento;
    const tipo = clienteForm.tipo_documento;
    
    if (tipo === 'DNI' && (doc.length !== 8 || isNaN(Number(doc)))) {
        clienteForm.setError('num_documento', 'El DNI debe contener exactamente 8 números.');
        return;
    }
    if (tipo === 'RUC' && (doc.length !== 11 || isNaN(Number(doc)) || !(doc.startsWith('10') || doc.startsWith('20')))) {
        clienteForm.setError('num_documento', 'El RUC debe contener exactamente 11 números y empezar con 10 o 20.');
        return;
    }

    if (editingClienteId.value) {
        clienteForm.put(`/terceros/cliente/${editingClienteId.value}`, {
            onSuccess: () => {
                isClienteModalOpen.value = false;
                clienteForm.reset();
            }
        });
    } else {
        clienteForm.post('/terceros/cliente', {
            onSuccess: () => {
                isClienteModalOpen.value = false;
                clienteForm.reset();
            }
        });
    }
};

const toggleCliente = (id: number) => {
    clienteForm.post(`/terceros/cliente/${id}/toggle`);
};

// Funciones para Proveedores
const openNewProveedorModal = () => {
    editingProveedorId.value = null;
    proveedorForm.reset();
    proveedorForm.clearErrors();
    isProveedorModalOpen.value = true;
};

const openEditProveedorModal = (prov: any) => {
    editingProveedorId.value = prov.id_proveedor;
    proveedorForm.ruc = prov.ruc;
    proveedorForm.razon_social = prov.razon_social;
    proveedorForm.direccion = prov.direccion || '';
    proveedorForm.telefono = prov.telefono || '';
    proveedorForm.clearErrors();
    isProveedorModalOpen.value = true;
};

const submitProveedor = () => {
    const ruc = proveedorForm.ruc;
    if (ruc.length !== 11 || isNaN(Number(ruc)) || !(ruc.startsWith('10') || ruc.startsWith('20'))) {
        proveedorForm.setError('ruc', 'El RUC debe contener exactamente 11 números y empezar con 10 o 20.');
        return;
    }

    if (editingProveedorId.value) {
        proveedorForm.put(`/terceros/proveedor/${editingProveedorId.value}`, {
            onSuccess: () => {
                isProveedorModalOpen.value = false;
                proveedorForm.reset();
            }
        });
    } else {
        proveedorForm.post('/terceros/proveedor', {
            onSuccess: () => {
                isProveedorModalOpen.value = false;
                proveedorForm.reset();
            }
        });
    }
};

const toggleProveedor = (id: number) => {
    proveedorForm.post(`/terceros/proveedor/${id}/toggle`);
};

const confirmDeleteCliente = (c: any) => {
    if (confirm(`¿Estás seguro de eliminar al cliente "${c.nombre_razon_social}"? Esta acción no se puede deshacer.`)) {
        clienteForm.delete(`/terceros/cliente/${c.id_cliente}`);
    }
};

const confirmDeleteProveedor = (p: any) => {
    if (confirm(`¿Estás seguro de eliminar al proveedor "${p.razon_social}"? Esta acción no se puede deshacer.`)) {
        proveedorForm.delete(`/terceros/proveedor/${p.id_proveedor}`);
    }
};
defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Clientes y Proveedores',
                href: '/terceros',
            },
        ],
    },
});
</script>

<template>
    <Head title="Clientes y Proveedores - GUESAA SIC" />

        <div class="p-6 max-w-7xl mx-auto space-y-6">
            
            <!-- Encabezado de Página -->
            <div class="flex justify-between items-center border-b pb-5 border-zinc-200 dark:border-zinc-800">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-zinc-950 dark:text-zinc-50">Clientes y Proveedores</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        Mantenimiento del catálogo de terceros de GUESAA PERÚ E.I.R.L.
                    </p>
                </div>
                <button
                    @click="activeTab === 'clientes' ? openNewClienteModal() : openNewProveedorModal()"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors"
                >
                    <Plus class="h-4 w-4" />
                    Registrar {{ activeTab === 'clientes' ? 'Cliente' : 'Proveedor' }}
                </button>
            </div>

            <!-- Navegación por pestañas -->
            <div class="border-b border-zinc-200 dark:border-zinc-800">
                <nav class="-mb-px flex gap-6">
                    <button
                        @click="activeTab = 'clientes'"
                        class="pb-4 px-1 text-sm font-medium border-b-2 transition-colors flex items-center gap-2"
                        :class="activeTab === 'clientes' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:hover:text-zinc-300'"
                    >
                        <UserCheck class="h-4.5 w-4.5" />
                        Clientes ({{ clientes.length }})
                    </button>
                    <button
                        @click="activeTab = 'proveedores'"
                        class="pb-4 px-1 text-sm font-medium border-b-2 transition-colors flex items-center gap-2"
                        :class="activeTab === 'proveedores' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:hover:text-zinc-300'"
                    >
                        <Truck class="h-4.5 w-4.5" />
                        Proveedores ({{ proveedores.length }})
                    </button>
                </nav>
            </div>

            <!-- Mensaje de errores globales de Laravel -->
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

            <!-- Tabla de Clientes -->
            <div v-if="activeTab === 'clientes'" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-xs uppercase text-zinc-700 dark:text-zinc-300">
                            <tr>
                                <th class="p-4">Tipo Doc.</th>
                                <th class="p-4">Número Doc.</th>
                                <th class="p-4">Nombres / Razón Social</th>
                                <th class="p-4">Dirección</th>
                                <th class="p-4">Teléfono</th>
                                <th class="p-4 text-center">Estado</th>
                                <th class="p-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            <tr v-if="clientes.length === 0">
                                <td colspan="7" class="p-8 text-center text-zinc-400">No hay clientes registrados en el sistema.</td>
                            </tr>
                            <tr v-for="c in clientes" :key="c.id_cliente" class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                                <td class="p-4 font-medium text-zinc-900 dark:text-zinc-100">{{ c.tipo_documento }}</td>
                                <td class="p-4 font-mono text-zinc-900 dark:text-zinc-100">{{ c.num_documento }}</td>
                                <td class="p-4 font-medium text-zinc-900 dark:text-zinc-100">{{ c.nombre_razon_social }}</td>
                                <td class="p-4">{{ c.direccion || '-' }}</td>
                                <td class="p-4">{{ c.telefono || '-' }}</td>
                                <td class="p-4 text-center">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset"
                                        :class="c.estado ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-red-50 text-red-700 ring-red-600/20'">
                                        {{ c.estado ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="p-4 text-right flex justify-end gap-2">
                                    <button @click="openEditClienteModal(c)" class="p-1.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg text-zinc-600 dark:text-zinc-300 transition-colors" title="Editar">
                                        <Edit class="h-4 w-4" />
                                    </button>
                                    <button @click="toggleCliente(c.id_cliente)" class="p-1.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors" :class="c.estado ? 'text-amber-500 hover:text-amber-600' : 'text-emerald-500 hover:text-emerald-600'" :title="c.estado ? 'Desactivar' : 'Activar'">
                                        <Power class="h-4 w-4" />
                                    </button>
                                    <button @click="confirmDeleteCliente(c)" class="p-1.5 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg text-red-600 dark:text-red-400 transition-colors" title="Eliminar">
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabla de Proveedores -->
            <div v-else class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-xs uppercase text-zinc-700 dark:text-zinc-300">
                            <tr>
                                <th class="p-4">RUC</th>
                                <th class="p-4">Razón Social</th>
                                <th class="p-4">Dirección</th>
                                <th class="p-4">Teléfono</th>
                                <th class="p-4 text-center">Estado</th>
                                <th class="p-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            <tr v-if="proveedores.length === 0">
                                <td colspan="6" class="p-8 text-center text-zinc-400">No hay proveedores registrados en el sistema.</td>
                            </tr>
                            <tr v-for="p in proveedores" :key="p.id_proveedor" class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30">
                                <td class="p-4 font-mono font-medium text-zinc-900 dark:text-zinc-100">{{ p.ruc }}</td>
                                <td class="p-4 font-medium text-zinc-900 dark:text-zinc-100">{{ p.razon_social }}</td>
                                <td class="p-4">{{ p.direccion || '-' }}</td>
                                <td class="p-4">{{ p.telefono || '-' }}</td>
                                <td class="p-4 text-center">
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset"
                                        :class="p.estado ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20' : 'bg-red-50 text-red-700 ring-red-600/20'">
                                        {{ p.estado ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="p-4 text-right flex justify-end gap-2">
                                    <button @click="openEditProveedorModal(p)" class="p-1.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg text-zinc-600 dark:text-zinc-300 transition-colors" title="Editar">
                                        <Edit class="h-4 w-4" />
                                    </button>
                                    <button @click="toggleProveedor(p.id_proveedor)" class="p-1.5 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors" :class="p.estado ? 'text-amber-500 hover:text-amber-600' : 'text-emerald-500 hover:text-emerald-600'" :title="p.estado ? 'Desactivar' : 'Activar'">
                                        <Power class="h-4 w-4" />
                                    </button>
                                    <button @click="confirmDeleteProveedor(p)" class="p-1.5 hover:bg-red-50 dark:hover:bg-red-950/40 rounded-lg text-red-600 dark:text-red-400 transition-colors" title="Eliminar">
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MODAL CLIENTES -->
            <div v-if="isClienteModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                <div class="w-full max-w-lg rounded-xl border bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 p-6 shadow-xl animate-in fade-in zoom-in-95 duration-150">
                    <h3 class="text-lg font-semibold text-zinc-950 dark:text-zinc-50 border-b pb-3 border-zinc-100 dark:border-zinc-800">
                        {{ editingClienteId ? 'Editar Cliente' : 'Registrar Cliente' }}
                    </h3>
                    
                    <form @submit.prevent="submitCliente" class="mt-4 space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Tipo Documento</label>
                                <select v-model="clienteForm.tipo_documento" class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                    <option value="DNI">DNI</option>
                                    <option value="RUC">RUC</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Número Documento</label>
                                <input v-model="clienteForm.num_documento" type="text" maxlength="15" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Ej: 12345678" />
                                <p v-if="clienteForm.errors.num_documento" class="text-xs text-red-500 mt-1">{{ clienteForm.errors.num_documento }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Nombre / Razón Social</label>
                            <input v-model="clienteForm.nombre_razon_social" type="text" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Nombre completo o Razón social" />
                            <p v-if="clienteForm.errors.nombre_razon_social" class="text-xs text-red-500 mt-1">{{ clienteForm.errors.nombre_razon_social }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Dirección</label>
                            <input v-model="clienteForm.direccion" type="text" class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Dirección del domicilio fiscal (opcional)" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Teléfono</label>
                            <input v-model="clienteForm.telefono" type="text" class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Celular / Teléfono fijo (opcional)" />
                        </div>

                        <div class="flex justify-end gap-2 border-t pt-4 border-zinc-100 dark:border-zinc-800 mt-6">
                            <button type="button" @click="isClienteModalOpen = false" class="px-4 py-2 border border-zinc-200 dark:border-zinc-850 rounded-lg text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-850/50 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="clienteForm.processing" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-sm transition-colors disabled:opacity-50">
                                {{ editingClienteId ? 'Actualizar' : 'Guardar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL PROVEEDORES -->
            <div v-if="isProveedorModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                <div class="w-full max-w-lg rounded-xl border bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 p-6 shadow-xl animate-in fade-in zoom-in-95 duration-150">
                    <h3 class="text-lg font-semibold text-zinc-950 dark:text-zinc-50 border-b pb-3 border-zinc-100 dark:border-zinc-800">
                        {{ editingProveedorId ? 'Editar Proveedor' : 'Registrar Proveedor' }}
                    </h3>
                    
                    <form @submit.prevent="submitProveedor" class="mt-4 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Número de RUC</label>
                            <input v-model="proveedorForm.ruc" type="text" maxlength="11" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Ej: 20603510721" />
                            <p v-if="proveedorForm.errors.ruc" class="text-xs text-red-500 mt-1">{{ proveedorForm.errors.ruc }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Razón Social</label>
                            <input v-model="proveedorForm.razon_social" type="text" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Razón social de la distribuidora" />
                            <p v-if="proveedorForm.errors.razon_social" class="text-xs text-red-500 mt-1">{{ proveedorForm.errors.razon_social }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Dirección</label>
                            <input v-model="proveedorForm.direccion" type="text" class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Dirección comercial (opcional)" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Teléfono</label>
                            <input v-model="proveedorForm.telefono" type="text" class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Celular / Teléfono de contacto (opcional)" />
                        </div>

                        <div class="flex justify-end gap-2 border-t pt-4 border-zinc-100 dark:border-zinc-800 mt-6">
                            <button type="button" @click="isProveedorModalOpen = false" class="px-4 py-2 border border-zinc-200 dark:border-zinc-850 rounded-lg text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-850/50 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="proveedorForm.processing" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-sm transition-colors disabled:opacity-50">
                                {{ editingProveedorId ? 'Actualizar' : 'Guardar' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

    </div>
</template>
