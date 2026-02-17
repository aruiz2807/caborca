<script setup>
import { inject } from "vue"
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'

const props = defineProps({
    record: Object,
});

const form = useForm();
let openDialog = inject('openDialogState')

const submit = () => {
    form.delete(route('roles.destroy', { role: props.record.id }), {
        onSuccess: () => {
            openDialog.value = false
        }
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="grid gap-4">
        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            {{ $t("app.delete") }}
        </Button>
    </form>
</template>
