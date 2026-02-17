<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
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

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('user-password.update'), {
        errorBag: 'updatePassword',
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }

            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>{{ $t("pages.profile.password_update_title") }}</CardTitle>
            <CardDescription>{{ $t("pages.profile.password_update_description") }}</CardDescription>
        </CardHeader>

        <CardContent>
            <form @submit.prevent="updatePassword" class="grid gap-4">
                <!-- Current Password -->
                <div class="w-full lg:w-1/2 grid gap-2">
                    <Label for="current_password">
                        {{ $t("pages.profile.password_update_current_password") }}
                    </Label>
                    <Input ref="currentPasswordInput" v-model="form.current_password" id="current_password"
                        type="password" required />
                    <InputError :message="form.errors.current_password" class="mt-2" />
                </div>

                <!-- New Password -->
                <div class="w-full lg:w-1/2 grid gap-2">
                    <Label for="password">
                        {{ $t("pages.profile.password_update_new_password") }}
                    </Label>
                    <Input ref="passwordInput" v-model="form.password" id="password" type="password" required />
                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <!-- Confirm New Password -->
                <div class="w-full lg:w-1/2 grid gap-2">
                    <Label for="password_confirmation">
                        {{ $t("pages.profile.password_update_new_password_confirmation") }}
                    </Label>
                    <Input v-model="form.password_confirmation" id="password_confirmation" type="password" required />
                    <InputError :message="form.errors.password_confirmation" class="mt-2" />
                </div>

                <!-- Submit Button -->
                <div class="w-full lg:w-1/2 flex justify-end">
                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        class="w-32">
                        {{ $t("app.save") }}
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>
</template>
