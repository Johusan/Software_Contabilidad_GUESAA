<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { update } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Restablecer Contraseña',
        description: 'Por favor, ingresa tu nueva contraseña a continuación',
    },
});

const props = defineProps<{
    token: string;
    email: string;
    passwordRules: string;
}>();

const inputEmail = ref(props.email);
</script>

<template>
    <Head title="Restablecer Contraseña" />

    <Form
        v-bind="update.form()"
        :transform="(data) => ({ ...data, token, email })"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
    >
        <div class="grid gap-5">
            
            <!-- Campo de Correo (Solo Lectura) -->
            <div class="grid gap-2">
                <Label for="email" class="text-zinc-700 dark:text-zinc-355 font-semibold text-xs uppercase tracking-wider">
                    Correo Electrónico
                </Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    autocomplete="email"
                    v-model="inputEmail"
                    class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 px-3 py-2.5 text-sm text-zinc-500 dark:text-zinc-400"
                    readonly
                />
                <InputError :message="errors.email" class="mt-2" />
            </div>

            <!-- Campo de Nueva Contraseña -->
            <div class="grid gap-2">
                <Label for="password" class="text-zinc-700 dark:text-zinc-355 font-semibold text-xs uppercase tracking-wider">
                    Nueva Contraseña
                </Label>
                <PasswordInput
                    id="password"
                    name="password"
                    autocomplete="new-password"
                    class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    autofocus
                    placeholder="••••••••"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password" />
            </div>

            <!-- Confirmar Contraseña -->
            <div class="grid gap-2">
                <Label for="password_confirmation" class="text-zinc-700 dark:text-zinc-355 font-semibold text-xs uppercase tracking-wider">
                    Confirmar Nueva Contraseña
                </Label>
                <PasswordInput
                    id="password_confirmation"
                    name="password_confirmation"
                    autocomplete="new-password"
                    class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    placeholder="••••••••"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <!-- Botón de Envío -->
            <Button
                type="submit"
                class="mt-4 w-full rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3 shadow-md shadow-indigo-100 dark:shadow-none transition-all duration-200 disabled:opacity-50 flex items-center justify-center gap-2 cursor-pointer"
                :disabled="processing"
                data-test="reset-password-button"
            >
                <Spinner v-if="processing" class="h-4 w-4 text-white" />
                <span>Restablecer Contraseña</span>
            </Button>
        </div>
    </Form>
</template>
