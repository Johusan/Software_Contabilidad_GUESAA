<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { logout } from '@/routes';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        title: 'Verificación de Correo',
        description:
            'Por favor, verifica tu dirección de correo haciendo clic en el enlace que te acabamos de enviar por email.',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Verificación de Correo" />

    <div
        v-if="status === 'verification-link-sent'"
        class="mb-4 text-center text-sm font-medium text-green-600 animate-pulse"
    >
        Se ha enviado un nuevo enlace de verificación a la dirección de correo proporcionada durante el registro.
    </div>

    <Form
        v-bind="send.form()"
        class="space-y-6 text-center"
        v-slot="{ processing }"
    >
        <Button :disabled="processing" variant="secondary" class="w-full rounded-xl py-3 border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-850/50 cursor-pointer">
            <Spinner v-if="processing" class="h-4 w-4" />
            <span>Reenviar correo de verificación</span>
        </Button>

        <TextLink :href="logout()" as="button" class="mx-auto block text-sm text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-300">
            Cerrar Sesión
        </TextLink>
    </Form>
</template>
