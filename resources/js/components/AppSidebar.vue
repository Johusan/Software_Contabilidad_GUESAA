<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, LayoutGrid, Users, Package, ShoppingBag, Receipt, Wallet, Shield } from '@lucide/vue';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';

import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

const page = usePage();

const mainNavItems = computed<NavItem[]>(() => {
    const user = (page.props.auth?.user as any);
    const roleId = user?.id_rol ?? 1;

    const allItems: { item: NavItem; roles: number[] }[] = [
        {
            item: { title: 'Dashboard', href: '/dashboard', icon: LayoutGrid },
            roles: [1, 2, 3]
        },
        {
            item: { title: 'Clientes y Prov.', href: '/terceros', icon: Users },
            roles: [1, 2, 3]
        },
        {
            item: { title: 'Inventario', href: '/inventario', icon: Package },
            roles: [1, 3]
        },
        {
            item: { title: 'Registro Compras', href: '/compras', icon: ShoppingBag },
            roles: [1, 3]
        },
        {
            item: { title: 'Registro Ventas', href: '/ventas', icon: Receipt },
            roles: [1, 2]
        },
        {
            item: { title: 'Caja Chica', href: '/caja', icon: Wallet },
            roles: [1, 2]
        },
        {
            item: { title: 'Plan de Cuentas', href: '/plan-cuentas', icon: BookOpen },
            roles: [1]
        },
        {
            item: { title: 'Usuarios y Roles', href: '/usuarios', icon: Shield },
            roles: [1]
        },
    ];

    return allItems
        .filter(entry => entry.roles.includes(roleId))
        .map(entry => entry.item);
});

const footerNavItems: NavItem[] = [];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
