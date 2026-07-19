<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';

import { 
    BookOpen, 
    Plus, 
    Search, 
    AlertCircle, 
    Folder, 
    FileCode 
} from '@lucide/vue';

const props = defineProps<{
    cuentas: any[];
    errors: any;
}>();

const isModalOpen = ref(false);
const searchQuery = ref('');

const form = useForm({
    codigo_cuenta: '',
    denominacion: '',
    codigo_padre: '',
});

// Filtrar todos los posibles "padres" (cuentas de 2 o 3 dígitos) para poblar el selector
const cuentasPadre = computed(() => {
    return props.cuentas.filter(c => c.codigo_cuenta.length === 2 || c.codigo_cuenta.length === 3);
});

// Filtrar cuentas por búsqueda
const filteredCuentas = computed(() => {
    const query = searchQuery.value.toLowerCase().trim();
    if (!query) return props.cuentas;
    return props.cuentas.filter(c => 
        c.codigo_cuenta.includes(query) || 
        c.denominacion.toLowerCase().includes(query)
    );
});

const openNewSubcuentaModal = () => {
    form.reset();
    if (cuentasPadre.value.length > 0) {
        form.codigo_padre = cuentasPadre.value[0].codigo_cuenta;
        form.codigo_cuenta = cuentasPadre.value[0].codigo_cuenta;
    }
    form.clearErrors();
    isModalOpen.value = true;
};

const onParentChange = () => {
    // Autocompletar el inicio del código con el del padre
    form.codigo_cuenta = form.codigo_padre;
};

const submitSubcuenta = () => {
    const cod = form.codigo_cuenta;
    const pad = form.codigo_padre;

    if (!cod.startsWith(pad)) {
        form.setError('codigo_cuenta', `El código debe comenzar con el del padre: ${pad}`);
        return;
    }
    if (cod.length <= pad.length) {
        form.setError('codigo_cuenta', 'El código de la subcuenta debe ser más largo que el de la cuenta padre.');
        return;
    }

    form.post('/plan-cuentas/subcuenta', {
        onSuccess: () => {
            isModalOpen.value = false;
            form.reset();
        }
    });
};

const getIndentationClass = (code: string) => {
    const len = code.length;
    if (len === 2) return 'font-bold text-zinc-950 dark:text-zinc-50 pl-2';
    if (len === 3) return 'font-semibold text-zinc-800 dark:text-zinc-200 pl-8';
    return 'text-zinc-600 dark:text-zinc-400 pl-14';
};
defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Plan de Cuentas',
                href: '/plan-cuentas',
            },
        ],
    },
});
</script>

<template>
    <Head title="Plan Contable PCGE - GUESAA SIC" />

        <div class="p-6 max-w-7xl mx-auto space-y-6">
            
            <!-- Encabezado -->
            <div class="flex justify-between items-center border-b pb-5 border-zinc-200 dark:border-zinc-800">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight text-zinc-950 dark:text-zinc-50">Plan Contable General Empresarial</h1>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        Catálogo de cuentas (PCGE) oficial precargado. Permite estructurar e integrar las transacciones comerciales.
                    </p>
                </div>
                <button
                    @click="openNewSubcuentaModal"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors"
                >
                    <Plus class="h-4 w-4" />
                    Registrar Subcuenta
                </button>
            </div>

            <!-- Buscador y Alertas -->
            <div class="flex items-center gap-4">
                <div class="relative flex-1 max-w-md">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-zinc-400">
                        <Search class="h-4 w-4" />
                    </span>
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Buscar por código o denominación..."
                        class="block w-full pl-10 pr-3 py-2 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-white dark:bg-zinc-900 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none"
                    />
                </div>
            </div>

            <!-- Errores -->
            <div v-if="Object.keys(errors).length > 0" class="rounded-lg bg-red-50 p-4 dark:bg-red-950/20 border border-red-200 dark:border-red-800/40">
                <div class="flex">
                    <AlertCircle class="h-5 w-5 text-red-600 dark:text-red-400" />
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Errores de validación</h3>
                        <ul class="mt-2 list-disc list-inside text-xs text-red-700 dark:text-red-400 space-y-1">
                            <li v-for="err in errors" :key="err">{{ err }}</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Árbol Jerárquico (Indented Table) -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                <div class="max-h-[60vh] overflow-y-auto">
                    <table class="w-full text-left text-sm text-zinc-500 dark:text-zinc-400">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-xs uppercase text-zinc-700 dark:text-zinc-300 sticky top-0 z-10">
                            <tr>
                                <th class="p-4 w-1/4">Código Cuenta</th>
                                <th class="p-4">Denominación</th>
                                <th class="p-4 text-center w-24">Elemento</th>
                                <th class="p-4 text-center w-24">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            <tr v-if="filteredCuentas.length === 0">
                                <td colspan="4" class="p-8 text-center text-zinc-400">No se encontraron cuentas que coincidan con la búsqueda.</td>
                            </tr>
                            <tr v-for="c in filteredCuentas" :key="c.codigo_cuenta" 
                                class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                
                                <td class="p-3 font-mono" :class="getIndentationClass(c.codigo_cuenta)">
                                    <div class="flex items-center gap-2">
                                        <Folder v-if="c.codigo_cuenta.length <= 3" class="h-4 w-4 text-amber-500 shrink-0" />
                                        <FileCode v-else class="h-3.5 w-3.5 text-indigo-500 shrink-0" />
                                        <span>{{ c.codigo_cuenta }}</span>
                                    </div>
                                </td>
                                
                                <td class="p-3" :class="c.codigo_cuenta.length <= 3 ? 'font-semibold text-zinc-900 dark:text-zinc-100' : 'text-zinc-700 dark:text-zinc-300'">
                                    {{ c.denominacion }}
                                </td>
                                
                                <td class="p-3 text-center font-bold text-zinc-400">
                                    {{ c.elemento }}
                                </td>
                                
                                <td class="p-3 text-center">
                                    <span class="inline-flex items-center rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-950/20 dark:text-emerald-400">
                                        Activo
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MODAL REGISTRAR SUB-CUENTA -->
            <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
                <div class="w-full max-w-md rounded-xl border bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 p-6 shadow-xl animate-in fade-in zoom-in-95 duration-150">
                    <h3 class="text-lg font-semibold text-zinc-950 dark:text-zinc-50 border-b pb-3 border-zinc-100 dark:border-zinc-800">
                        Registrar Nueva Subcuenta
                    </h3>
                    
                    <form @submit.prevent="submitSubcuenta" class="mt-4 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Cuenta Madre / Padre</label>
                            <select v-model="form.codigo_padre" @change="onParentChange" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-955 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <option v-for="p in cuentasPadre" :key="p.codigo_cuenta" :value="p.codigo_cuenta">
                                    {{ p.codigo_cuenta }} - {{ p.denominacion }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Código de la Nueva Subcuenta</label>
                            <input v-model="form.codigo_cuenta" type="text" maxlength="10" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Ej: 10412" />
                            <p v-if="form.errors.codigo_cuenta" class="text-xs text-red-500 mt-1">{{ form.errors.codigo_cuenta }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Denominación / Nombre de Cuenta</label>
                            <input v-model="form.denominacion" type="text" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" placeholder="Ej: Banco BBVA M/N" />
                            <p v-if="form.errors.denominacion" class="text-xs text-red-500 mt-1">{{ form.errors.denominacion }}</p>
                        </div>

                        <div class="flex justify-end gap-2 border-t pt-4 border-zinc-100 dark:border-zinc-800 mt-6">
                            <button type="button" @click="isModalOpen = false" class="px-4 py-2 border border-zinc-200 dark:border-zinc-855 rounded-lg text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-850/50 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="form.processing" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-sm transition-colors disabled:opacity-50">
                                Guardar Cuenta
                            </button>
                        </div>
                    </form>
                </div>
            </div>

    </div>
</template>
