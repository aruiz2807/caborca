<script setup>
import { inject } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button';
import { Label } from '@/Components/ui/label';
import { Input } from '@/Components/ui/input';
import { Switch } from '@/Components/ui/switch';

const props = defineProps({
    record: Object,
});

let openDialog = inject('openDialogState');

const form = useForm({
    parts_available: props.record.parts_available == 1,
    parts_arrival_date: props.record.parts_arrival_date || '',
});

const submit = () => {
    form.transform((data) => ({
        ...data,
        parts_available: data.parts_available ? 1 : 0,
    })).post(route('orders.update_parts', props.record.id), {
        onSuccess: () => {
            openDialog.value = false;
        },
    });
};
</script>

<template>
    <div class="grid gap-4 py-4">
        <div class="flex items-center space-x-2">
            <Switch id="parts-available" v-model="form.parts_available" />
            <Label for="parts-available">¿Las refacciones están disponibles?</Label>
        </div>

        <div v-if="!form.parts_available" class="grid gap-2 mt-4">
            <Label for="arrival-date">Fecha de llegada estimada</Label>
            <Input 
                id="arrival-date" 
                type="date" 
                v-model="form.parts_arrival_date" 
                :class="{ 'border-destructive': form.errors.parts_arrival_date }" 
            />
            <div v-if="form.errors.parts_arrival_date" class="text-sm text-destructive">
                {{ form.errors.parts_arrival_date }}
            </div>
        </div>
    </div>

    <div class="pt-6 grid grid-cols-1">
        <div class="flex justify-center items-center">
            <Button class="min-w-64" @click="submit" :disabled="form.processing">
                {{ $t('app.save') }}
            </Button>
        </div>
    </div>
</template>

