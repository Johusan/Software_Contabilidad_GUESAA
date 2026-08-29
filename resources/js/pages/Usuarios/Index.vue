<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { 
    Users, 
    UserPlus, 
    Search, 
    Shield, 
    CheckCircle2, 
    XCircle, 
    Edit2, 
    AlertCircle,
    UserCheck,
    Lock
} from '@lucide/vue';
import { ref, computed } from 'vue';

interface Rol {
    id_rol: number;
    nombre_rol: string;
    descripcion: string;
}

interface Usuario {
    id_usuario: number;
    id_rol: number;
    nombres: string;
    apellidos: string;
    email: string;
    estado: boolean;
    rol?: Rol;
}

const props = defineProps<{
    usuarios: Usuario[];
    roles: Rol[];
    errors: any;
}>();

const isModalOpen = ref(false);
const editingUsuario = ref<Usuario | null>(null);
const searchQuery = ref('');

const form = useForm({
    nombres: '',
    apellidos: '',
    email: '',
    password: '',
    id_rol: 1,
});

const filteredUsuarios = computed(() => {
    const q = searchQuery.value.toLowerCase().trim();

    if (!q) {
return props.usuarios;
}

    return props.usuarios.filter(u => 
        u.nombres.toLowerCase().includes(q) ||
        u.apellidos.toLowerCase().includes(q) ||
        u.email.toLowerCase().includes(q) ||
        u.rol?.nombre_rol.toLowerCase().includes(q)
    );
});

const totalAdmins = computed(() => props.usuarios.filter(u => u.id_rol === 1).length);
const totalCajeros = computed(() => props.usuarios.filter(u => u.id_rol === 2).length);
const totalAlmaceneros = computed(() => props.usuarios.filter(u => u.id_rol === 3).length);

const openCreateModal = () => {
    editingUsuario.value = null;
    form.reset();
    form.clearErrors();
    form.id_rol = props.roles.length > 0 ? props.roles[0].id_rol : 1;
    isModalOpen.value = true;
};

const openEditModal = (usuario: Usuario) => {
    editingUsuario.value = usuario;
    form.nombres = usuario.nombres;
    form.apellidos = usuario.apellidos;
    form.email = usuario.email;
    form.password = '';
    form.id_rol = usuario.id_rol;
    form.clearErrors();
    isModalOpen.value = true;
};

const submitForm = () => {
    if (editingUsuario.value) {
        form.put(`/usuarios/${editingUsuario.value.id_usuario}`, {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            }
        });
    } else {
        form.post('/usuarios', {
            onSuccess: () => {
                isModalOpen.value = false;
                form.reset();
            }
        });
    }
};

const toggleEstado = (usuario: Usuario) => {
    const actionText = usuario.estado ? 'desactivar' : 'activar';

    if (confirm(`¿Está seguro de que desea ${actionText} a ${usuario.nombres} ${usuario.apellidos}?`)) {
        useForm({}).post(`/usuarios/${usuario.id_usuario}/toggle`);
    }
};

const getRoleBadgeClass = (idRol: number) => {
    switch (idRol) {
        case 1:
            return 'bg-purple-50 text-purple-700 ring-purple-600/20 dark:bg-purple-950/30 dark:text-purple-400';
        case 2:
            return 'bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-950/30 dark:text-blue-400';
        case 3:
            return 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-950/30 dark:text-amber-400';
        default:
            return 'bg-zinc-50 text-zinc-700 ring-zinc-600/20 dark:bg-zinc-800 dark:text-zinc-300';
    }
};

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Usuarios y Roles',
                href: '/usuarios',
            },
        ],
    },
});
</script>

<template>
    <Head title="Gestión de Usuarios y Roles - GUESAA SIC" />

    <div class="p-4 sm:p-6 max-w-7xl mx-auto space-y-6 w-full min-w-0 max-w-full">
        
        <!-- Encabezado -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b pb-5 border-zinc-200 dark:border-zinc-800">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-zinc-950 dark:text-zinc-50 flex items-center gap-2">
                    <Shield class="h-8 w-8 text-indigo-600 dark:text-indigo-400" />
                    Gestión de Usuarios y Roles
                </h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                    Administra el personal del sistema, asigna privilegios de acceso y controla los estados de cuenta.
                </p>
            </div>
            <button
                @click="openCreateModal"
                class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors cursor-pointer"
            >
                <UserPlus class="h-4 w-4" />
                Registrar Nuevo Usuario
            </button>
        </div>

        <!-- Tarjetas de Resumen (KPIs) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4 rounded-xl shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-zinc-500 uppercase">Total Usuarios</span>
                    <Users class="h-5 w-5 text-indigo-500" />
                </div>
                <p class="text-2xl font-bold text-zinc-900 dark:text-zinc-50 mt-2">{{ props.usuarios.length }}</p>
            </div>
            
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4 rounded-xl shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-purple-600 dark:text-purple-400 uppercase">Administradores</span>
                    <Shield class="h-5 w-5 text-purple-500" />
                </div>
                <p class="text-2xl font-bold text-purple-700 dark:text-purple-300 mt-2">{{ totalAdmins }}</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4 rounded-xl shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-blue-600 dark:text-blue-400 uppercase">Cajeros</span>
                    <UserCheck class="h-5 w-5 text-blue-500" />
                </div>
                <p class="text-2xl font-bold text-blue-700 dark:text-blue-300 mt-2">{{ totalCajeros }}</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-4 rounded-xl shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase">Almaceneros</span>
                    <Users class="h-5 w-5 text-amber-500" />
                </div>
                <p class="text-2xl font-bold text-amber-700 dark:text-amber-300 mt-2">{{ totalAlmaceneros }}</p>
            </div>
        </div>

        <!-- Buscador -->
        <div class="flex items-center gap-4">
            <div class="relative flex-1 max-w-md">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-zinc-400">
                    <Search class="h-4 w-4" />
                </span>
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Buscar por nombre, correo o rol..."
                    class="block w-full pl-10 pr-3 py-2 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-white dark:bg-zinc-900 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none"
                />
            </div>
        </div>

        <!-- Alertas de Error -->
        <div v-if="Object.keys(errors).length > 0" class="rounded-lg bg-red-50 p-4 dark:bg-red-950/20 border border-red-200 dark:border-red-800/40">
            <div class="flex">
                <AlertCircle class="h-5 w-5 text-red-600 dark:text-red-400 shrink-0" />
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Mensajes del sistema</h3>
                    <ul class="mt-1 list-disc list-inside text-xs text-red-700 dark:text-red-400 space-y-1">
                        <li v-for="err in errors" :key="err">{{ err }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Tabla de Usuarios -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[650px] text-left text-sm text-zinc-500 dark:text-zinc-400">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-xs uppercase text-zinc-700 dark:text-zinc-300">
                        <tr>
                            <th class="p-4">ID</th>
                            <th class="p-4">Usuario / Nombres</th>
                            <th class="p-4">Correo Electrónico</th>
                            <th class="p-4 text-center">Rol Asignado</th>
                            <th class="p-4 text-center">Estado</th>
                            <th class="p-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                        <tr v-if="filteredUsuarios.length === 0">
                            <td colspan="6" class="p-8 text-center text-zinc-400">No se encontraron usuarios registrados.</td>
                        </tr>
                        <tr v-for="u in filteredUsuarios" :key="u.id_usuario" class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="p-4 font-mono text-xs">{{ u.id_usuario }}</td>
                            <td class="p-4">
                                <p class="font-semibold text-zinc-900 dark:text-zinc-50">{{ u.nombres }} {{ u.apellidos }}</p>
                            </td>
                            <td class="p-4 font-mono text-xs text-zinc-600 dark:text-zinc-300">
                                {{ u.email }}
                            </td>
                            <td class="p-4 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset" :class="getRoleBadgeClass(u.id_rol)">
                                    {{ u.rol?.nombre_rol || 'Sin Rol' }}
                                </span>
                            </td>
                            <td class="p-4 text-center">
                                <span v-if="u.estado" class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20 dark:bg-emerald-950/20 dark:text-emerald-400">
                                    <CheckCircle2 class="h-3 w-3" />
                                    Activo
                                </span>
                                <span v-else class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/20 dark:bg-red-950/20 dark:text-red-400">
                                    <XCircle class="h-3 w-3" />
                                    Inactivo
                                </span>
                            </td>
                            <td class="p-4 text-center space-x-2">
                                <button
                                    @click="openEditModal(u)"
                                    title="Editar Usuario"
                                    class="p-1.5 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 rounded-lg transition-colors cursor-pointer"
                                >
                                    <Edit2 class="h-4 w-4" />
                                </button>
                                <button
                                    @click="toggleEstado(u)"
                                    :title="u.estado ? 'Desactivar Usuario' : 'Activar Usuario'"
                                    class="p-1.5 rounded-lg transition-colors cursor-pointer"
                                    :class="u.estado ? 'text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30' : 'text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/30'"
                                >
                                    <XCircle v-if="u.estado" class="h-4 w-4" />
                                    <CheckCircle2 v-else class="h-4 w-4" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL REGISTRO / EDICIÓN -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4">
            <div class="w-full max-w-lg rounded-xl border bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 p-6 shadow-xl animate-in fade-in zoom-in-95 duration-150">
                <h3 class="text-lg font-semibold text-zinc-950 dark:text-zinc-50 border-b pb-3 border-zinc-100 dark:border-zinc-800 flex items-center gap-2">
                    <Shield class="h-5 w-5 text-indigo-600" />
                    {{ editingUsuario ? 'Editar Usuario y Rol' : 'Registrar Nuevo Usuario' }}
                </h3>
                
                <form @submit.prevent="submitForm" class="mt-4 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Nombres</label>
                            <input v-model="form.nombres" type="text" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none" placeholder="Ej: Carlos Manuel" />
                            <p v-if="form.errors.nombres" class="text-xs text-red-500 mt-1">{{ form.errors.nombres }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Apellidos</label>
                            <input v-model="form.apellidos" type="text" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none" placeholder="Ej: Quispe Vargas" />
                            <p v-if="form.errors.apellidos" class="text-xs text-red-500 mt-1">{{ form.errors.apellidos }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Correo Electrónico</label>
                        <input v-model="form.email" type="email" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none" placeholder="usuario@guesaa.com" />
                        <p v-if="form.errors.email" class="text-xs text-red-500 mt-1">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">Rol de Acceso al Sistema</label>
                        <select v-model="form.id_rol" required class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none">
                            <option v-for="r in roles" :key="r.id_rol" :value="r.id_rol">
                                {{ r.nombre_rol }} - {{ r.descripcion }}
                            </option>
                        </select>
                        <p v-if="form.errors.id_rol" class="text-xs text-red-500 mt-1">{{ form.errors.id_rol }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider">
                            Contraseña {{ editingUsuario ? '(Dejar en blanco para mantener la actual)' : '' }}
                        </label>
                        <input v-model="form.password" type="password" :required="!editingUsuario" minlength="6" class="mt-1 block w-full rounded-lg border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none" placeholder="••••••••" />
                        <p v-if="form.errors.password" class="text-xs text-red-500 mt-1">{{ form.errors.password }}</p>
                    </div>

                    <div class="flex justify-end gap-2 border-t pt-4 border-zinc-100 dark:border-zinc-800 mt-6">
                        <button type="button" @click="isModalOpen = false" class="px-4 py-2 border border-zinc-200 dark:border-zinc-800 rounded-lg text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm shadow-sm transition-colors disabled:opacity-50 cursor-pointer">
                            {{ editingUsuario ? 'Guardar Cambios' : 'Registrar Usuario' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</template>
