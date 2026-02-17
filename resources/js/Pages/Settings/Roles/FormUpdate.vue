<script setup>
import { inject } from "vue"
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
    record: Object,
});

let openDialog = inject('openDialogState')

const form = useForm({
    name: props.record.name,
    description: props.record.description,
});

const submit = () => {
    form.put(route('roles.update', { role: props.record.id }), {
        onSuccess: () => {
            openDialog.value = false
        }
    });
};
</script>

<template>

    <form @submit.prevent="submit" class="grid gap-4">
        <div class="grid gap-2">
            <Label for="email">
                {{ $t("app.name") }}
            </Label>
            <Input v-model="form.name" id="name" type="text" placeholder="John Doe" required />
            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="description">
                {{ $t("app.description") }}
            </Label>
            <Input v-model="form.description" id="description" type="text" placeholder="Descripción del rol" />
            <InputError class="mt-2" :message="form.errors.description" />
        </div>

        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            {{ $t("app.save") }}
        </Button>
    </form>

</template>
