<script setup>
import { computed, inject, watch } from "vue"
import { useForm, usePage } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import { Label } from '@/Components/ui/label'
import { Checkbox } from '@/Components/ui/checkbox'

const props = defineProps({
    record: Object,
});

const page = usePage();

const permissionGroups = computed(() => {
    if (Array.isArray(page.props.permissionGroups) && page.props.permissionGroups.length > 0) {
        return page.props.permissionGroups;
    }

    if (Array.isArray(page.props.permissions) && page.props.permissions.length > 0) {
        return [{
            key: 'all',
            label: 'Permisos',
            permissions: page.props.permissions,
        }];
    }

    return [];
});

const compatibilityRules = computed(() => {
    if (!Array.isArray(page.props.permissionCompatibilityRules)) {
        return [];
    }

    return page.props.permissionCompatibilityRules;
});

// Initialize form with the role's current permissions
const form = useForm({
    permissions: props.record.permissions ? props.record.permissions.map(p => p.name) : []
});

const applyCompatibilityRules = () => {
    const selectedPermissions = new Set(form.permissions);

    for (const rule of compatibilityRules.value) {
        const ifAny = Array.isArray(rule?.if_any) ? rule.if_any : [];
        const grant = Array.isArray(rule?.grant) ? rule.grant : [];
        const hasTriggerPermission = ifAny.some(permissionName => selectedPermissions.has(permissionName));

        if (!hasTriggerPermission) {
            continue;
        }

        for (const permissionName of grant) {
            selectedPermissions.add(permissionName);
        }
    }

    form.permissions = Array.from(selectedPermissions);
};

// Watch for changes to the record (e.g., after an Inertia refresh or when opening a different role)
watch(() => props.record, (newRecord) => {
    if (newRecord && newRecord.permissions) {
        form.permissions = newRecord.permissions.map(p => p.name);
    } else {
        form.permissions = [];
    }

    applyCompatibilityRules();
}, { deep: true, immediate: true });

let openDialog = inject('openDialogState')

const togglePermission = (permissionName) => {
    const index = form.permissions.indexOf(permissionName);
    if (index === -1) {
        form.permissions.push(permissionName);
    } else {
        form.permissions.splice(index, 1);
    }

    applyCompatibilityRules();
};

const submit = () => {
    applyCompatibilityRules();

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
            
            <div class="max-h-[340px] overflow-y-auto p-2 border rounded-md space-y-5">
                <div v-for="group in permissionGroups" :key="group.key" class="space-y-3">
                    <div class="text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                        {{ group.label }}
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div v-for="permission in group.permissions" :key="permission.id" class="flex items-center space-x-2">
                            <Checkbox 
                                :id="`permission-${permission.id}`" 
                                :model-value="form.permissions.includes(permission.name)"
                                @update:modelValue="togglePermission(permission.name)"
                            />
                            <Label :for="`permission-${permission.id}`" class="text-sm font-normal">
                                {{ $t('permissions.' + permission.name) }}
                            </Label>
                        </div>
                    </div>
                </div>
                
                <div v-if="!permissionGroups.length" class="text-sm text-muted-foreground">
                    No hay permisos disponibles.
                </div>
            </div>
        </div>

        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            {{ $t("app.save") }}
        </Button>
    </form>
</template>
