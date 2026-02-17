<script setup>
import { ref, reactive, nextTick } from 'vue';
import { Button } from '@/Components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import InputError from './InputError.vue';

const emit = defineEmits(['confirmed']);

const confirmingPassword = ref(false);
const passwordInput = ref(null);
const form = reactive({
    password: '',
    error: '',
    processing: false,
});

const startConfirmingPassword = () => {
    axios.get(route('password.confirmation')).then(response => {
        if (response.data.confirmed) {
            emit('confirmed');
        } else {
            confirmingPassword.value = true;
        }
    });
};

const confirmPassword = () => {
    form.processing = true;

    axios.post(route('password.confirm'), {
        password: form.password,
    }).then(() => {
        form.processing = false;

        closeModal();
        nextTick().then(() => emit('confirmed'));

    }).catch(error => {
        form.processing = false;
        form.error = error.response.data.errors.password[0];
    });
};

const closeModal = () => {
    confirmingPassword.value = false;
    form.password = '';
    form.error = '';
};
</script>

<template>
    <span @click="startConfirmingPassword">
        <slot />
    </span>

    <Dialog :open="confirmingPassword" v-on:update:open="closeModal">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>
                    {{ $t('app.password_confirm') }}
                </DialogTitle>

                <DialogDescription>
                    {{ $t('pages.profile.two_factor_password_confirmation') }}
                </DialogDescription>
            </DialogHeader>

            <div class="grid gap-2">
                <Label for="password">
                    {{ $t("app.password") }}
                </Label>

                <Input
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    @keyup.enter="confirmPassword"
                    required
                />

                <InputError :message="form.error" class="mt-2" />

                <Button
                    class="mt-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="confirmPassword"
                >
                    {{ $t("app.confirm") }}
                </Button>

                <Button variant="outline" @click="closeModal" >
                    {{ $t("app.cancel") }}
                </Button>
            </div>
        </DialogContent>
    </Dialog>
</template>
