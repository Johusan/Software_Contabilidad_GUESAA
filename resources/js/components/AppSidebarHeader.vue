<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Sun, Moon, Sidebar, Monitor, ChevronDown, LogOut, Settings, Smartphone } from '@lucide/vue';
import { ref, computed } from 'vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import RemoteConnectionModal from '@/components/RemoteConnectionModal.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { useAppearance } from '@/composables/useAppearance';
import { useInitials } from '@/composables/useInitials';
import { logout } from '@/routes';
import { edit as editProfile } from '@/routes/profile';
import type { BreadcrumbItem } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItem[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

const page = usePage();
const user = computed(() => (page.props.auth as any)?.user);
const isRemoteModalOpen = ref(false);

const { getInitials } = useInitials();
const { appearance, updateAppearance } = useAppearance();

const currentAppearanceLabel = computed(() => {
    if (appearance.value === 'light') {
return 'Claro';
}

    if (appearance.value === 'dark') {
return 'Oscuro';
}

    if (appearance.value === 'semi-dark') {
return 'Semidark';
}

    return 'Sistema';
});

const currentAppearanceIcon = computed(() => {
    if (appearance.value === 'light') {
return Sun;
}

    if (appearance.value === 'dark') {
return Moon;
}

    if (appearance.value === 'semi-dark') {
return Sidebar;
}

    return Monitor;
});
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md sticky top-0 z-10"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>

        <!-- Acciones del Header en el lado derecho -->
        <div class="flex items-center gap-3">
            
            <!-- Botón Conexión Remota (Limpio, sin emoticones) -->
            <button
                @click="isRemoteModalOpen = true"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-indigo-200 dark:border-indigo-900/60 bg-indigo-50/60 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900/40 cursor-pointer text-xs font-semibold transition-colors shrink-0"
                title="Vincular dispositivo móvil"
            >
                <Smartphone class="h-4 w-4 text-indigo-600 dark:text-indigo-400" />
                <span class="hidden sm:inline">Conexión Remota</span>
            </button>

            <!-- Selector de Temas -->
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <button class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-gray-200 dark:border-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer text-sm font-semibold transition-colors">
                        <component :is="currentAppearanceIcon" class="h-4.5 w-4.5 text-indigo-500" />
                        <span class="hidden md:block">{{ currentAppearanceLabel }}</span>
                        <ChevronDown class="h-3.5 w-3.5 text-gray-400" />
                    </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent class="w-48 rounded-xl p-1 z-50">
                    <DropdownMenuItem @click="updateAppearance('light')" class="flex items-center gap-2.5 rounded-lg px-3 py-2 cursor-pointer text-sm">
                        <Sun class="h-4 w-4 text-amber-500" />
                        <div class="flex-1">
                            <p class="font-medium text-gray-750 dark:text-gray-200">Claro</p>
                        </div>
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="updateAppearance('semi-dark')" class="flex items-center gap-2.5 rounded-lg px-3 py-2 cursor-pointer text-sm">
                        <Sidebar class="h-4 w-4 text-indigo-500" />
                        <div class="flex-1">
                            <p class="font-medium text-gray-750 dark:text-gray-200">Semidark</p>
                        </div>
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="updateAppearance('dark')" class="flex items-center gap-2.5 rounded-lg px-3 py-2 cursor-pointer text-sm">
                        <Moon class="h-4 w-4 text-gray-400" />
                        <div class="flex-1">
                            <p class="font-medium text-gray-750 dark:text-gray-200">Oscuro</p>
                        </div>
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="updateAppearance('system')" class="flex items-center gap-2.5 rounded-lg px-3 py-2 cursor-pointer text-sm">
                        <Monitor class="h-4 w-4 text-gray-450" />
                        <div class="flex-1">
                            <p class="font-medium text-gray-750 dark:text-gray-200">Sistema</p>
                        </div>
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>

            <!-- Menú de Usuario -->
            <DropdownMenu v-if="user">
                <DropdownMenuTrigger as-child>
                    <button class="flex items-center gap-2 p-1 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition-colors">
                        <div class="relative">
                            <Avatar class="h-8 w-8 overflow-hidden rounded-lg border border-indigo-100 dark:border-indigo-900">
                                <AvatarFallback class="rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400 font-semibold text-xs">
                                    {{ getInitials(user.nombres) }}
                                </AvatarFallback>
                            </Avatar>
                            <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white dark:border-gray-900 rounded-full"></div>
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-200 leading-none">{{ user.nombres }}</p>
                            <p class="text-[10px] text-indigo-600 dark:text-indigo-400 font-medium mt-0.5">{{ user.rol?.nombre_rol || (user.id_rol === 1 ? 'Administrador' : user.id_rol === 2 ? 'Cajero' : 'Almacenero') }}</p>
                        </div>
                        <ChevronDown class="h-3.5 w-3.5 text-gray-400 hidden md:block" />
                    </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent class="w-56 rounded-xl p-1 z-50">
                    <div class="px-3.5 py-2.5 border-b border-gray-100 dark:border-gray-800 mb-1 flex items-center gap-2.5">
                        <Avatar class="h-9 w-9">
                            <AvatarFallback class="bg-indigo-50 text-indigo-600 dark:bg-indigo-950/40 dark:text-indigo-400 font-semibold text-sm">
                                {{ getInitials(user.nombres) }}
                            </AvatarFallback>
                        </Avatar>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-gray-900 dark:text-white leading-tight truncate">{{ user.nombres }} {{ user.apellidos }}</p>
                            <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate w-36 mt-0.5">{{ user.email }}</p>
                            <p class="text-[10px] font-semibold text-indigo-600 dark:text-indigo-400 mt-0.5">{{ user.rol?.nombre_rol || (user.id_rol === 1 ? 'Administrador' : user.id_rol === 2 ? 'Cajero' : 'Almacenero') }}</p>
                        </div>
                    </div>
                    
                    <DropdownMenuItem :as-child="true" class="rounded-lg px-3 py-2 cursor-pointer text-xs">
                        <Link :href="editProfile()" class="flex items-center gap-2 w-full">
                            <Settings class="h-4 w-4 text-gray-400" />
                            <span>Configurar Cuenta</span>
                        </Link>
                    </DropdownMenuItem>

                    <DropdownMenuSeparator />

                    <DropdownMenuItem :as-child="true" class="rounded-lg px-3 py-2 cursor-pointer text-xs text-red-650 dark:text-red-400">
                        <Link :href="logout()" method="post" as="button" class="flex items-center gap-2 w-full text-left">
                            <LogOut class="h-4 w-4 text-red-500" />
                            <span>Cerrar Sesión</span>
                        </Link>
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>

        </div>
    </header>

    <!-- Modal de Conexión Remota / Código QR -->
    <RemoteConnectionModal v-model:open="isRemoteModalOpen" />
</template>
