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
import 'vue-sonner/style.css'
import { Toaster, toast } from 'vue-sonner'
import { useTrans } from '/resources/js/Composables/trans';
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'


defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'), {
        preserveScroll: true,
        onFinish: () => showMessage(),
    });
};

const showMessage = () => {
    toast.success(useTrans('pages.auth.password_recovery_sent'));
};
</script>

<template>
    <Card class="mx-auto max-w-sm">
        <CardHeader>
            <CardTitle class="text-xl">
                {{ $t('pages.auth.password_recovery') }}
            </CardTitle>

            <CardDescription>
                {{ $t('pages.auth.password_recovery_description') }}
            </CardDescription>
        </CardHeader>

        <CardContent>
            <form @submit="submit" class="grid gap-4">
                <div class="grid gap-2">
                    <Label for="email">
                        {{ $t('app.email') }}
                    </Label>
                    <Input v-model="form.email" id="email" type="email" placeholder="usuario@email.com" required />
                </div>

                <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ $t('pages.auth.password_recovery_submit') }}
                </Button>

                <Button variant="secondary">
                    <Link href="/">{{ $t("app.cancel") }}</Link>
                </Button>
            </form>

            <Toaster richColors />
        </CardContent>
    </Card>
</template>
