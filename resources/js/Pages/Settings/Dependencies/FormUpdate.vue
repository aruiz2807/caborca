<script setup>
import { inject } from "vue"
import { useForm, usePage } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
    record: Object,
});

const page = usePage()

const form = useForm({
    name: props.record.name,
    customer_number: props.record.customer_number,
    location_id: props.record.location_id.toString(), // convert to string to match SelectItem value if it's numeric
    user_id: props.record.user_id.toString(),
    status: props.record.status,
});

let openDialog = inject('openDialogState')

const submit = () => {
    form.put(route('dependencies.update', { id: props.record.id }), {
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
            <Input v-model="form.customer_number" id="customer_number" type="text" placeholder="No. Cliente" required />
            <InputError class="mt-2" :message="form.errors.customer_number" />
        </div>

        <div class="grid gap-2">
            <Label for="location_id">
                {{ $t("app.location") }}
            </Label>
            <Select v-model="form.location_id">
                <SelectTrigger>
                    <SelectValue :placeholder="$t('app.location')" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="location in page.props.locations" :key="location.id" :value="location.id.toString()">
                        {{ location.name }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <InputError class="mt-2" :message="form.errors.location_id" />
        </div>

        <div class="grid gap-2">
            <Label for="user_id">
                {{ $t("app.assigned") }}
            </Label>
            <Select v-model="form.user_id">
                <SelectTrigger>
                    <SelectValue :placeholder="$t('app.assigned')" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="user in page.props.users" :key="user.id" :value="user.id.toString()">
                        {{ user.name }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <InputError class="mt-2" :message="form.errors.user_id" />
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
