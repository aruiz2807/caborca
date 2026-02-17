<script setup>
import { inject } from "vue"
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { RadioGroup, RadioGroupItem } from '@/Components/ui/radio-group'
import InputError from '@/Components/InputError.vue'

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
    type: 'A',
});

let openDialog = inject('openDialogState')

const submit = () => {
    form.post(route('users.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation')
        },
        onSuccess: () => {
            openDialog.value = false
            form.reset('password', 'password_confirmation')
        }
    });
};
</script>

<template>
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

        <div class="grid gap-2">
            <Label for="type">
                {{ $t("app.type") }}
            </Label>
            <RadioGroup v-model="form.type" id="type">
                <div class="flex pt-2 items-center space-x-2">
                    <RadioGroupItem id="optionA" value="A" />
                    <Label for="optionA">{{ $t("app.advisor") }}</Label>
                </div>

                <div class="flex pb-2 items-center space-x-2">
                    <RadioGroupItem id="optionG" value="G" />
                    <Label for="optionG">{{ $t("app.government") }}</Label>
                </div>
            </RadioGroup>
        </div>

        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            {{ $t("app.save") }}
        </Button>
    </form>
</template>
