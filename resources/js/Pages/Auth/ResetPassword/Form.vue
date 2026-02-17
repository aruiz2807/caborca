<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
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
import InputError from '@/Components/InputError.vue'

const props = defineProps({
    email: String,
    token: String,
});

const page = usePage();
const form = useForm({
    token: page.props.token,
    email: page.props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Card class="mx-auto max-w-sm">
        <CardHeader>
            <CardTitle class="text-xl">
                {{ $t("pages.auth.password_reset") }}
            </CardTitle>
            <CardDescription>
                {{ $t("pages.auth.password_reset_description") }}
            </CardDescription>
        </CardHeader>

        <CardContent>
            <form @submit.prevent="submit" class="grid gap-4">
                <div class="grid gap-2">
                    <Label for="email">
                        {{ $t("app.email") }}
                    </Label>
                    <Input v-model="form.email" id="email" type="email" required />
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
                    {{ $t("pages.auth.password_reset") }}
                </Button>
            </form>
        </CardContent>
    </Card>
</template>
