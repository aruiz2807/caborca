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

// Initialize form with the role's current permissions
const form = useForm({
    permissions: props.record.permissions ? props.record.permissions.map(p => p.name) : []
});

// Watch for changes to the record (e.g., after an Inertia refresh or when opening a different role)
watch(() => props.record, (newRecord) => {
    if (newRecord && newRecord.permissions) {
        form.permissions = newRecord.permissions.map(p => p.name);
    } else {
        form.permissions = [];
    }
}, { deep: true, immediate: true });

let openDialog = inject('openDialogState')

const togglePermission = (permissionName) => {
    const index = form.permissions.indexOf(permissionName);
    if (index === -1) {
        form.permissions.push(permissionName);
    } else {
        form.permissions.splice(index, 1);
    }
};

const submit = () => {
    form.put(route('roles.update_permissions', props.record.id), {
        onSuccess: () => {
            openDialog.value = false
        }
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="grid gap-4">
        <div class="grid gap-2 mb-4">
            <h3 class="text-sm font-medium leading-none mb-2">{{ $t('app.permissions') }}</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[300px] overflow-y-auto p-2 border rounded-md">
                <div v-for="permission in page.props.permissions" :key="permission.id" class="flex items-center space-x-2">
                    <Checkbox 
                        :id="`permission-${permission.id}`" 
                        :model-value="form.permissions.includes(permission.name)"
                        @update:modelValue="togglePermission(permission.name)"
                    />
                    <Label :for="`permission-${permission.id}`" class="text-sm font-normal">
                        {{ $t('permissions.' + permission.name) }}
                    </Label>
                </div>
                
                <div v-if="!page.props.permissions?.length" class="text-sm text-muted-foreground col-span-2">
                    No hay permisos disponibles.
                </div>
            </div>
        </div>

        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            {{ $t("app.save") }}
        </Button>
    </form>
</template>
