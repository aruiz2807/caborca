<script setup>
import { inject } from "vue"
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Select, SelectContent, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/Components/ui/select'
import InputError from '@/Components/InputError.vue'

const form = useForm({
    name: '',
    customer_number: '',
    location_id: '',
    user_id: '',
});

let openDialog = inject('openDialogState')

const submit = () => {
    form.post(route('dependencies.store'), {
        onFinish: () => {

        },
        onSuccess: () => {
            openDialog.value = false
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
            <Input v-model="form.name" id="name" type="text" placeholder="Nombre de la dependencia" required />
            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="customer_number">
                {{ $t("app.customer_number") }}
            </Label>
            <Input v-model="form.customer_number" id="customer_number" type="text" placeholder="Número de cliente en el DMS" required />
            <InputError class="mt-2" :message="form.errors.customer_number" />
        </div>

        <div class="grid gap-2">
            <Label for="location">
                {{ $t("app.location") }}
            </Label>

            <Select v-model="form.location_id">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Selecciona la localidad" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="location in $page.props.locations" :key="location.id" :value="location.id">
                        {{ location.name }}
                    </SelectItem>
                </SelectContent>
            </Select>

            <InputError class="mt-2" :message="form.errors.location_id" />
        </div>

        <div class="grid gap-2">
            <Label for="user">
                {{ $t("app.assigned") }}
            </Label>

            <Select v-model="form.user_id">
                <SelectTrigger class="w-full">
                    <SelectValue placeholder="Selecciona el responsable" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="user in $page.props.users" :key="user.id" :value="user.id">
                        {{ user.name }}
                    </SelectItem>
                </SelectContent>
            </Select>

            <InputError class="mt-2" :message="form.errors.user_id" />
        </div>

        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            {{ $t("app.save") }}
        </Button>
    </form>
</template>
