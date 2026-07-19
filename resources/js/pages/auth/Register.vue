<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';
import { store } from '@/routes/register';

defineProps<{
    passwordRules: string;
}>();

defineOptions({
    layout: {
        title: 'Crear una cuenta',
        description: 'Ingresa tus datos a continuación para registrarte en el sistema contable',
    },
});
</script>

<template>
    <Head title="Registrarse" />

    <Form
        v-bind="store.form()"
        :reset-on-success="['password', 'password_confirmation']"
        v-slot="{ errors, processing }"
        class="flex flex-col gap-6"
    >
        <div class="grid gap-5">
            
            <!-- Campo de Nombre -->
            <div class="grid gap-2">
                <Label for="name" class="text-zinc-700 dark:text-zinc-350 font-semibold text-xs uppercase tracking-wider">
                    Nombre Completo
                </Label>
                <Input
                    id="name"
                    type="text"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="name"
                    name="name"
                    placeholder="Tu nombre completo"
                    class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2.5 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
                <InputError :message="errors.name" />
            </div>

            <!-- Campo de Correo Electrónico -->
            <div class="grid gap-2">
                <Label for="email" class="text-zinc-700 dark:text-zinc-350 font-semibold text-xs uppercase tracking-wider">
                    Correo Electrónico
                </Label>
                <Input
                    id="email"
                    type="email"
                    required
                    :tabindex="2"
                    autocomplete="email"
                    name="email"
                    placeholder="correo@ejemplo.com"
                    class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2.5 text-sm text-zinc-900 dark:text-zinc-50 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />
                <InputError :message="errors.email" />
            </div>

            <!-- Campo de Contraseña -->
            <div class="grid gap-2">
                <Label for="password" class="text-zinc-700 dark:text-zinc-350 font-semibold text-xs uppercase tracking-wider">
                    Contraseña
                </Label>
                <PasswordInput
                    id="password"
                    required
                    :tabindex="3"
                    autocomplete="new-password"
                    name="password"
                    placeholder="••••••••"
                    class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password" />
            </div>

            <!-- Confirmar Contraseña -->
            <div class="grid gap-2">
                <Label for="password_confirmation" class="text-zinc-700 dark:text-zinc-350 font-semibold text-xs uppercase tracking-wider">
                    Confirmar Contraseña
                </Label>
                <PasswordInput
                    id="password_confirmation"
                    required
                    :tabindex="4"
                    autocomplete="new-password"
                    name="password_confirmation"
                    placeholder="••••••••"
                    class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2.5 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    :passwordrules="passwordRules"
                />
                <InputError :message="errors.password_confirmation" />
            </div>

            <!-- Botón de Envío -->
            <Button
                type="submit"
                class="mt-4 w-full rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3 shadow-md shadow-indigo-100 dark:shadow-none transition-all duration-200 disabled:opacity-50 flex items-center justify-center gap-2 cursor-pointer"
                tabindex="5"
                :disabled="processing"
                data-test="register-user-button"
            >
                <Spinner v-if="processing" class="h-4 w-4 text-white" />
                <span>Crear Cuenta</span>
            </Button>
        </div>

        <!-- Enlace a Login -->
        <div class="text-center text-sm text-zinc-500 dark:text-zinc-400 mt-2">
            ¿Ya tienes una cuenta?
            <TextLink
                :href="login()"
                class="text-indigo-600 dark:text-indigo-400 font-semibold hover:underline"
                :tabindex="6"
            >
                Iniciar Sesión
            </TextLink>
        </div>
    </Form>
</template>
