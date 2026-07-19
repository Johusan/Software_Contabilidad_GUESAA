<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import {
    index as confirmOptions,
    store as confirmStore,
} from '@/actions/Laravel/Passkeys/Http/Controllers/PasskeyConfirmationController';
import InputError from '@/components/InputError.vue';
import PasskeyVerify from '@/components/PasskeyVerify.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/password/confirm';

defineOptions({
    layout: {
        title: 'Confirmar Contraseña',
        description:
            'Esta es un área segura de la aplicación. Por favor, confirma tu contraseña antes de continuar.',
    },
});
</script>

<template>
    <Head title="Confirmar Contraseña" />

    <PasskeyVerify
        :routes="{
            options: confirmOptions(),
            submit: confirmStore(),
        }"
        label="Confirmar con Llave de Acceso"
        loading-label="Confirmando..."
        separator="O confirma con tu contraseña"
    />

    <Form
        v-bind="store.form()"
        reset-on-success
        v-slot="{ errors, processing }"
    >
        <div class="space-y-6">
            <div class="grid gap-2">
                <Label htmlFor="password" class="text-zinc-700 dark:text-zinc-350 font-semibold text-xs uppercase tracking-wider">
                    Contraseña
                </Label>
                <PasswordInput
                    id="password"
                    name="password"
                    class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    required
                    autocomplete="current-password"
                    autofocus
                    placeholder="••••••••"
                />

                <InputError :message="errors.password" />
            </div>

            <div class="flex items-center">
                <Button
                    class="w-full rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3 shadow-md shadow-indigo-100 dark:shadow-none transition-all duration-200 disabled:opacity-50 flex items-center justify-center gap-2 cursor-pointer"
                    :disabled="processing"
                    data-test="confirm-password-button"
                >
                    <Spinner v-if="processing" class="h-4 w-4 text-white" />
                    <span>Confirmar Contraseña</span>
                </Button>
            </div>
        </div>
    </Form>
</template>
