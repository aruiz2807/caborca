<script setup>
import { inject } from "vue"
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import { Label } from '@/Components/ui/label'

const props = defineProps({
    record: Object,
});

const form = useForm({});

let openDialog = inject('openDialogState')

const submit = () => {
    form.delete(route('locations.destroy', { id: props.record.id }), {
        onSuccess: () => {
            openDialog.value = false
        }
    });
};
</script>

<template>
    <div class="grid gap-4">
        <p class="text-sm text-muted-foreground">
            ¿Estás seguro de que deseas eliminar la localidad <strong>{{ record.name }}</strong>? Esta acción no se puede deshacer.
        </p>
        
        <Button @click="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            {{ $t("app.confirm") }}
        </Button>
    </div>
</template>
