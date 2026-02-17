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
    location_id: '',
    database: '',
});

let openDialog = inject('openDialogState')

const submit = () => {
    form.post(route('workshops.store'), {
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
            <Input v-model="form.name" id="name" type="text" placeholder="Nombre del taller" required />
            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="location_id">
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
            <Label for="database">
                {{ $t("app.database") }}
            </Label>
            <Input v-model="form.database" id="database" type="text" placeholder="Base de datos del DMS" required />
            <InputError class="mt-2" :message="form.errors.database" />
        </div>

        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            {{ $t("app.save") }}
        </Button>
    </form>
</template>
