<script setup>
import { inject } from "vue"
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
    record: Object,
});

const form = useForm({
    name: props.record.name,
    status: props.record.status,
});

let openDialog = inject('openDialogState')

const submit = () => {
    form.put(route('locations.update', { id: props.record.id }), {
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
            <Input v-model="form.name" id="name" type="text" placeholder="Nombre de la localidad" required />
            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="status">
                {{ $t("app.status") }}
            </Label>
            <Select v-model="form.status">
                <SelectTrigger>
                    <SelectValue :placeholder="$t('app.status')" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="Active">
                        {{ $t("app.active") }}
                    </SelectItem>
                    <SelectItem value="Inactive">
                        {{ $t("app.inactive") }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <InputError class="mt-2" :message="form.errors.status" />
        </div>

        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            {{ $t("app.update") }}
        </Button>
    </form>
</template>
