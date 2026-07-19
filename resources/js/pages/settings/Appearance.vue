<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import Heading from '@/components/Heading.vue';
import { edit } from '@/routes/appearance';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Ajustes de Apariencia',
                href: edit(),
            },
        ],
    },
});
</script>

<template>
    <Head title="Ajustes de Apariencia" />

    <h1 class="sr-only">Ajustes de Apariencia</h1>

    <div class="space-y-6">
        <Heading
            variant="small"
            title="Ajustes de Apariencia"
            description="Actualiza la configuración de apariencia de tu cuenta contable"
        />
        <AppearanceTabs />
    </div>
</template>
