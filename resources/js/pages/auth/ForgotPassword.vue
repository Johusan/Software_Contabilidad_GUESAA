<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { email } from '@/routes/password';

defineOptions({
    layout: {
        title: '¿Olvidaste tu contraseña?',
        description: 'Ingresa tu correo electrónico para recibir un enlace de restablecimiento de contraseña',
    },
});

defineProps<{
    status?: string;
}>();
</script>

<template>
    <Head title="Restablecer Contraseña" />

    <div
        v-if="status"
        class="mb-4 text-center text-sm font-medium text-green-600 animate-pulse"
    >
        {{ status }}
    </div>

    <div class="space-y-6">
        <Form v-bind="email.form()" v-slot="{ errors, processing }">
            <div class="grid gap-2">
                <Label for="email" class="text-zinc-700 dark:text-zinc-350 font-semibold text-xs uppercase tracking-wider">
                    Correo Electrónico
                </Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    autocomplete="off"
                    autofocus
                    placeholder="ejemplo@guesaa.com"
                    class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2.5 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="my-6 flex items-center justify-start">
                <Button
                    class="w-full rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3 shadow-md shadow-indigo-100 dark:shadow-none transition-all duration-200 disabled:opacity-50 flex items-center justify-center gap-2 cursor-pointer"
                    :disabled="processing"
                    data-test="email-password-reset-link-button"
                >
                    <Spinner v-if="processing" class="h-4 w-4 text-white" />
                    <span>Enviar enlace de restablecimiento</span>
                </Button>
            </div>
        </Form>

        <div class="space-x-1 text-center text-sm text-zinc-500 dark:text-zinc-400">
            <span>O, regresar al</span>
            <TextLink :href="login()" class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline">
                iniciar sesión
            </TextLink>
        </div>
    </div>
</template>
