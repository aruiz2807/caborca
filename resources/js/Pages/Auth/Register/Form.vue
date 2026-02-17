<script setup>
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Separator } from '@/Components/ui/separator'
import InputError from '@/Components/InputError.vue'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Card class="mx-auto max-w-sm">
        <CardHeader>
            <CardTitle class="text-xl">
                {{ $t("pages.auth.register_title") }}
            </CardTitle>
            <CardDescription>
                {{ $t("pages.auth.register_description") }}
            </CardDescription>
        </CardHeader>

        <CardContent>
            <form @submit.prevent="submit" class="grid gap-4">
                <div class="grid gap-2">
                    <Label for="name">
                        {{ $t("app.name") }}
                    </Label>
                    <Input v-model="form.name" id="name" type="text" placeholder="Nombre del usuario" required />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                    <Label for="email">
                        {{ $t("app.email") }}
                    </Label>
                    <Input v-model="form.email" id="email" type="email" placeholder="usuario@email.com" required />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">
                        {{ $t("app.password") }}
                    </Label>
                    <Input v-model="form.password" id="password" type="password" required />
                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                    <Label for="password_confirmation">
                        {{ $t("app.password_confirm") }}
                    </Label>
                    <Input v-model="form.password_confirmation" id="password_confirmation" type="password" required />
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>

                <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ $t("pages.auth.register_submit") }}
                </Button>

                <Separator />

                <div class="text-center text-sm">
                    <Link :href="route('login')" class="underline underline-offset-4">
                        {{ $t("pages.auth.register_already_registered") }}
                    </Link>
                </div>
            </form>
        </CardContent>
    </Card>
</template>
