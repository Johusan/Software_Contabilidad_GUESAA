<script setup lang="ts">
import { Form, Head, usePage } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import DeleteUser from '@/components/DeleteUser.vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Ajustes de Perfil',
                href: edit(),
            },
        ],
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <Head title="Ajustes de Perfil" />

    <h1 class="sr-only">Ajustes de Perfil</h1>

    <div class="flex flex-col space-y-6">
        <Heading
            variant="small"
            title="Perfil"
            description="Actualiza tu nombre y dirección de correo electrónico"
        />

        <Form
            v-bind="ProfileController.update.form()"
            class="space-y-6"
            v-slot="{ errors, processing }"
        >
            <div class="grid gap-2">
                <Label for="name">Nombre</Label>
                <Input
                    id="name"
                    class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2.5 text-sm"
                    name="name"
                    :default-value="user.name"
                    required
                    autocomplete="name"
                    placeholder="Nombre Completo"
                />
                <InputError class="mt-2" :message="errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Dirección de Correo</Label>
                <Input
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2.5 text-sm"
                    name="email"
                    :default-value="user.email"
                    required
                    autocomplete="username"
                    placeholder="correo@ejemplo.com"
                />
                <InputError class="mt-2" :message="errors.email" />
            </div>

            <div v-if="page.props.mustVerifyEmail && !user.email_verified_at">
                <p class="-mt-4 text-sm text-muted-foreground">
                    Tu dirección de correo electrónico no está verificada.
                    <Link
                        :href="send()"
                        as="button"
                        class="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                    >
                        Haz clic aquí para reenviar el correo de verificación.
                    </Link>
                </p>

                <div
                    v-if="page.props.status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600 animate-pulse"
                >
                    Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="processing" class="rounded-xl px-5 cursor-pointer" data-test="update-profile-button"
                    >Guardar</Button
                >
            </div>
        </Form>
    </div>

    <DeleteUser />
</template>
