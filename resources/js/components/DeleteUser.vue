<script setup lang="ts">
import { Form } from '@inertiajs/vue3';
import { useTemplateRef } from 'vue';
import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';

const passwordInput = useTemplateRef('passwordInput');
</script>

<template>
    <div class="space-y-6">
        <Heading
            variant="small"
            title="Eliminar Cuenta"
            description="Eliminar tu cuenta y todos sus recursos de forma permanente"
        />
        <div
            class="space-y-4 rounded-xl border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10"
        >
            <div class="relative space-y-0.5 text-red-650 dark:text-red-100">
                <p class="font-bold">Advertencia</p>
                <p class="text-sm">
                    Por favor, procede con precaución. Esta acción es definitiva y no se puede deshacer.
                </p>
            </div>
            <Dialog>
                <DialogTrigger as-child>
                    <Button variant="destructive" class="rounded-xl px-5 cursor-pointer" data-test="delete-user-button"
                        >Eliminar Cuenta</Button
                    >
                </DialogTrigger>
                <DialogContent class="rounded-2xl max-w-md p-6">
                    <Form
                        v-bind="ProfileController.destroy.form()"
                        reset-on-success
                        @error="() => passwordInput?.focus()"
                        :options="{
                            preserveScroll: true,
                        }"
                        class="space-y-6"
                        v-slot="{ errors, processing, reset, clearErrors }"
                    >
                        <DialogHeader class="space-y-3">
                            <DialogTitle
                                >¿Estás seguro de que deseas eliminar tu cuenta?</DialogTitle
                            >
                            <DialogDescription class="text-xs text-zinc-500 leading-relaxed">
                                Una vez que tu cuenta sea eliminada, todos sus recursos y datos asociados en el sistema contable se borrarán de forma permanente. Por favor, introduce tu contraseña para confirmar la baja definitiva.
                            </DialogDescription>
                        </DialogHeader>

                        <div class="grid gap-2">
                            <Label for="password" class="sr-only"
                                >Contraseña</Label
                            >
                            <PasswordInput
                                id="password"
                                name="password"
                                ref="passwordInput"
                                placeholder="Ingresa tu contraseña"
                                class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 px-3 py-2.5 text-sm"
                            />
                            <InputError :message="errors.password" />
                        </div>

                        <DialogFooter class="gap-2 sm:justify-end">
                            <DialogClose as-child>
                                <Button
                                    variant="secondary"
                                    class="rounded-xl px-4 cursor-pointer"
                                    @click="
                                        () => {
                                            clearErrors();
                                            reset();
                                        }
                                    "
                                >
                                    Cancelar
                                </Button>
                            </DialogClose>

                            <Button
                                type="submit"
                                variant="destructive"
                                class="rounded-xl px-5 cursor-pointer"
                                :disabled="processing"
                                data-test="confirm-delete-user-button"
                            >
                                Confirmar y Eliminar
                            </Button>
                        </DialogFooter>
                    </Form>
                </DialogContent>
            </Dialog>
        </div>
    </div>
</template>
