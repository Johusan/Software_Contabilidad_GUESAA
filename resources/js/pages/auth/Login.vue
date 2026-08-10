<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Info } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineOptions({
    layout: {
        title: 'Iniciar sesión en tu cuenta',
        description: 'Ingresa tu correo electrónico y contraseña para acceder al sistema contable',
    },
});

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <Head title="Iniciar Sesión" />

    <div
        v-if="status"
        class="mb-4 text-center text-sm font-medium text-green-600 animate-pulse"
    >
        {{ status }}
    </div>

    <Form
        v-bind="store.form()"
        :reset-on-success="['password']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-5">
            
            <!-- Campo de Correo Electrónico -->
            <div class="grid gap-2">
                <Label for="email" class="text-zinc-700 dark:text-zinc-350 font-semibold text-xs uppercase tracking-wider">
                    Correo Electrónico
                </Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email"
                    placeholder="ejemplo@guesaa.com"
                    class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2.5 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
                <InputError :message="errors.email" />
            </div>

            <!-- Campo de Contraseña -->
            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label for="password" class="text-zinc-700 dark:text-zinc-350 font-semibold text-xs uppercase tracking-wider">
                        Contraseña
                    </Label>
                    <TextLink
                        v-if="canResetPassword"
                        :href="request()"
                        class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline"
                        :tabindex="5"
                    >
                        ¿Olvidaste tu contraseña?
                    </TextLink>
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    :tabindex="2"
                    autocomplete="current-password"
                    placeholder="••••••••"
                    class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
                <InputError :message="errors.password" />
            </div>

            <!-- Recordarme -->
            <div class="flex items-center justify-between mt-1">
                <Label for="remember" class="flex items-center space-x-3 cursor-pointer">
                    <Checkbox id="remember" name="remember" :tabindex="3" class="rounded border-zinc-300 text-indigo-600 focus:ring-indigo-500" />
                    <span class="text-sm text-zinc-600 dark:text-zinc-400">Recordarme en este dispositivo</span>
                </Label>
            </div>

            <!-- Botón de Envío -->
            <Button
                type="submit"
                class="mt-4 w-full rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3 shadow-md shadow-indigo-100 dark:shadow-none transition-all duration-200 disabled:opacity-50 flex items-center justify-center gap-2 cursor-pointer"
                :tabindex="4"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" class="h-4 w-4 text-white" />
                <span>Ingresar al Sistema</span>
            </Button>
        </div>

        <!-- Aviso Informativo para Creación de Cuentas por Administrador -->
        <div class="mt-2 rounded-xl bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200 dark:border-zinc-800 p-3.5 text-xs text-zinc-600 dark:text-zinc-400 flex items-start gap-3">
            <Info class="h-4 w-4 text-indigo-600 dark:text-indigo-400 shrink-0 mt-0.5" />
            <div class="space-y-0.5">
                <p class="font-semibold text-zinc-800 dark:text-zinc-200">¿Necesitas acceso al sistema?</p>
                <p class="leading-relaxed">
                    Ponte en contacto con el <strong class="text-indigo-600 dark:text-indigo-400 font-semibold">Administrador</strong> de GUESAA PERÚ E.I.R.L. para la creación de tu usuario y asignación de rol.
                </p>
            </div>
        </div>
    </Form>
</template>
