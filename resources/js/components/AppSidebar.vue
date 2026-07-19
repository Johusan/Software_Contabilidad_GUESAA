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
    const items: NavItem[] = [
        {
            title: 'Dashboard',
            href: '/dashboard',
            icon: LayoutGrid,
        },
        {
            title: 'Clientes y Prov.',
            href: '/terceros',
            icon: Users,
        },
        {
            title: 'Inventario / Kardex',
            href: '/inventario',
            icon: Package,
        },
        {
            title: 'Registro Compras',
            href: '/compras',
            icon: ShoppingBag,
        },
        {
            title: 'Punto de Venta (POS)',
            href: '/ventas',
            icon: Receipt,
        },
        {
            title: 'Caja Chica',
            href: '/caja',
            icon: Wallet,
        },
        {
            title: 'Plan de Cuentas',
            href: '/plan-cuentas',
            icon: BookOpen,
        },
    ];

    if ((page.props.auth?.user as any)?.id_rol === 1) {
        items.push({
            title: 'Usuarios y Roles',
            href: '/usuarios',
            icon: Shield,
        });
    }

    return items;
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
