<script setup>
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle, } from '@/Components/ui/card'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Separator } from '@/Components/ui/separator'
import InputError from '@/Components/InputError.vue'

defineProps({
    canResetPassword: {
        type: Boolean,
        default: true,
    },
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Card class="mx-auto w-full max-w-md rounded-2xl border border-slate-200/90 bg-white/90 shadow-xl backdrop-blur">
        <CardHeader class="mt-2 mb-2">
            <CardTitle class="text-3xl text-center text-slate-900">
                {{ $t("pages.auth.login_title") }}
            </CardTitle>
            <CardDescription class="text-base leading-relaxed text-slate-600">
                {{ $t("pages.auth.login_description") }}
            </CardDescription>
        </CardHeader>

        <CardContent>
            <form @submit.prevent="submit" class="grid gap-6">
                <div v-if="status" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
                    {{ status }}
                </div>

                <div class="grid gap-2">
                    <Label for="email">
                        {{ $t("app.email") }}
                    </Label>
                    <Input
                        v-model="form.email"
                        id="email"
                        type="email"
                        autocomplete="username"
                        autofocus
                        placeholder="usuario@email.com"
                        required
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="password">
                        {{ $t("app.password") }}
                    </Label>
                    <Input v-model="form.password" id="password" type="password" autocomplete="current-password" required />
                </div>

                <InputError class="mt-2" :message="form.errors.email" />

                <Button type="submit" class="h-12 text-base font-semibold" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ $t("pages.auth.login_submit") }}
                </Button>

                <Separator v-if="canResetPassword" />

                <div v-if="canResetPassword" class="text-center text-sm">
                    <Link :href="route('password.request')" class="underline underline-offset-4">
                        {{ $t("pages.auth.login_forgot_password") }}
                    </Link>
                </div>
            </form>
        </CardContent>
    </Card>
</template>
