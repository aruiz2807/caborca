<script setup>
import { inject, watch } from "vue"
import { useForm, usePage } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import { Label } from '@/Components/ui/label'
import { Checkbox } from '@/Components/ui/checkbox'

const props = defineProps({
    record: Object,
});

const page = usePage();

// Initialize form with the workshop's current advisors
const form = useForm({
    advisors: props.record.advisors ? props.record.advisors.map(a => a.id) : []
});

// Watch for changes to the record
watch(() => props.record, (newRecord) => {
    if (newRecord && newRecord.advisors) {
        form.advisors = newRecord.advisors.map(a => a.id);
    } else {
        form.advisors = [];
    }
}, { deep: true, immediate: true });

let openDialog = inject('openDialogState')

const toggleAdvisor = (advisorId) => {
    const index = form.advisors.indexOf(advisorId);
    if (index === -1) {
        form.advisors.push(advisorId);
    } else {
        form.advisors.splice(index, 1);
    }
};

const submit = () => {
    form.put(route('workshops.update_advisors', props.record.id), {
        onSuccess: () => {
            openDialog.value = false
        }
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="grid gap-4">
        <div class="grid gap-2 mb-4">
            <h3 class="text-sm font-medium leading-none mb-2">Asesores del Taller</h3>
            
            <div class="max-h-[340px] overflow-y-auto p-2 border rounded-md space-y-3">
                <div class="grid grid-cols-1 gap-3">
                    <div v-for="advisor in $page.props.advisors" :key="advisor.id" class="flex items-center space-x-2">
                        <Checkbox 
                            :id="`advisor-${advisor.id}`" 
                            :model-value="form.advisors.includes(advisor.id)"
                            @update:modelValue="toggleAdvisor(advisor.id)"
                        />
                        <Label :for="`advisor-${advisor.id}`" class="text-sm font-normal cursor-pointer select-none">
                            {{ advisor.name }}
                        </Label>
                    </div>
                </div>
                
                <div v-if="!$page.props.advisors || !$page.props.advisors.length" class="text-sm text-muted-foreground p-2">
                    No hay asesores disponibles en el sistema.
                </div>
            </div>
        </div>

        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            {{ $t("app.save") }}
        </Button>
    </form>
</template>
