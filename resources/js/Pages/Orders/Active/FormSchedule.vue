<script setup>
import { inject, computed, ref  } from "vue"
import { useForm, router } from '@inertiajs/vue3';
import { cn } from '@/lib/utils'
import { Alert, AlertDescription, AlertTitle } from '@/Components/ui/alert'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { DateFormatter, getLocalTimeZone, today } from '@internationalized/date'
import { Button } from '@/Components/ui/button'
import { CalendarIcon } from 'lucide-vue-next'
import { Calendar } from '@/Components/ui/calendar'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Popover, PopoverContent, PopoverTrigger } from '@/Components/ui/popover'
import { Select, SelectContent, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/Components/ui/select'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
    record: Object,
});

const noResults = ref(false)
let openDialog = inject('openDialogState')
const defaultPlaceholder = today(getLocalTimeZone())
const df = new DateFormatter('es-MX', {
    dateStyle: 'short',
})
const isBlocked = computed(() => props.record.appointment !== null)

const form = useForm({
    workshop: null,
    date: '',
    time: '',
});

const getSlots = () => {
    if (!form.workshop || !form.date) {
        return
    }

    // Call backend
    router.visit(route('orders.available_slots', { workshop: form.workshop, date: form.date.toString() }), {
    only: ['slots'], // Request only the 'vehicleData' prop from the server
    preserveState: true, // Keep the current form state/scroll position
        onSuccess: (page) => {
            // The new 'slots' will be merged into the current page props automatically
            const slots = page.props.slots

            if (!slots || slots.length === 0) {
                noResults.value = true
                return
            }

            noResults.value = false
        },
    });
}

const submit = () => {
    const date = form.date.toString()

    if (date) {
        form.date = date
    }

    form.post(route('orders.schedule', { order_id: props.record.id }), {
        onSuccess: () => {
            openDialog.value = false
        }
    });
};
</script>

<template>

    <Alert v-if="isBlocked" variant="destructive" class="mb-4">
        <AlertTitle>{{ $t("pages.orders.appointment_disabled") }}</AlertTitle>
        <AlertDescription>
            {{ $t("pages.orders.appointment_disabled_description") }}
        </AlertDescription>
    </Alert>

    <form @submit.prevent="submit" class="grid gap-4">
        <div class="grid grid-cols-3 gap-6">
            <div class="grid gap-2">
                <Label>
                    {{ $t("app.dependency") }}
                </Label>
                <Label class="font-bold">
                    {{ record.dependency?.name ?? 'N/D' }}
                </Label>
            </div>

            <div class="grid gap-2">
                <Label>
                    {{ $t("app.purchase_order") }}
                </Label>
                <Label class="font-bold">
                    {{ record.purchase_order }}
                </Label>
            </div>

            <div class="grid gap-2">
                <Label>
                    {{ $t("app.economic_number") }}
                </Label>
                <Label class="font-bold">
                    {{ record.economic_number }}
                </Label>
            </div>

            <Card class="col-span-3">
                <CardHeader>
                    <CardTitle>{{ $t("pages.orders.vehicle_data") }}</CardTitle>
                </CardHeader>

                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label>
                                {{ $t("app.description") }}
                            </Label>
                            <Label class="font-bold">
                                {{ record.vehicle_description }}
                            </Label>
                        </div>

                        <div class="grid gap-2">
                            <Label>
                                {{ $t("app.model") }}
                            </Label>
                            <Label class="font-bold">
                                {{ record.vehicle_model }}
                            </Label>
                        </div>

                        <div class="grid gap-2">
                            <Label>
                                {{ $t("app.vin") }}
                            </Label>
                            <Label class="font-bold">
                                {{ record.vehicle_vin }}
                            </Label>
                        </div>

                        <div class="grid gap-2">
                            <Label>
                                {{ $t("app.plate") }}
                            </Label>
                            <Label class="font-bold">
                                {{ record.vehicle_plate }}
                            </Label>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card class="col-span-3">
                <CardHeader>
                    <CardTitle>{{ $t("pages.orders.service_data") }}</CardTitle>
                </CardHeader>

                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label>
                                {{ $t("app.service") }}
                            </Label>
                            <Label class="font-bold">
                                {{ record.service_type?.name ?? 'N/D' }}
                            </Label>
                        </div>

                        <div class="grid gap-2">
                            <Label>
                                {{ $t("app.location") }}
                            </Label>
                            <Label class="font-bold">
                                {{ record.service_location?.name ?? 'N/D' }}
                            </Label>
                        </div>
                        <div class="grid gap-2">
                            <Label>
                                {{ $t("app.description") }}
                            </Label>
                            <Label class="font-bold">
                                {{ record.service_description }}
                            </Label>
                        </div>

                        <div class="grid gap-2">
                            <Label>
                                {{ $t("app.date") }}
                            </Label>
                            <Label class="font-bold">
                                {{ record.service_requested_date }}
                            </Label>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card class="col-span-3">
                <CardHeader>
                    <CardTitle>{{ $t("pages.orders.schedule_data") }}</CardTitle>
                </CardHeader>

                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2 col-span-2">
                            <Label for="workshop">
                                {{ $t("app.workshop") }}
                            </Label>

                            <Select v-model="form.workshop" @update:modelValue="getSlots">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Selecciona un taller" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="workshop in $page.props.workshops" :key="workshop.id" :value="workshop.id">
                                        {{ workshop.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <InputError class="mt-2" :message="form.errors.workshop" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="date">
                                {{ $t("app.date") }}
                            </Label>

                            <Popover v-slot="{ close }">
                                <PopoverTrigger as-child>
                                    <Button variant="outline" :class="cn('justify-start text-left font-normal', !form.date && 'text-muted-foreground')">
                                        <CalendarIcon />
                                        {{ form.date }}
                                    </Button>
                                </PopoverTrigger>

                                <PopoverContent class="w-auto p-0" align="start">
                                    <Calendar
                                        v-model="form.date"
                                        :default-placeholder="defaultPlaceholder"
                                        layout="month-and-year"
                                        initial-focus
                                        @update:model-value="getSlots"
                                    />
                                </PopoverContent>
                            </Popover>

                            <InputError class="mt-2" :message="form.errors.date" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="time">
                                {{ $t("app.time") }}
                            </Label>

                            <Select v-model="form.time">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Selecciona un horario" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="slot in $page.props.slots" :key="slot.CIT_HORCITA" :value="slot.CIT_HORCITA">
                                        {{ slot.CIT_HORCITA }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <InputError class="mt-2" :message="form.errors.time" />
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="grid grid-cols-1">
            <div class="flex justify-center items-center">
                <Button class="min-w-64" type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing || isBlocked">
                    {{ $t("app.schedule") }}
                </Button>
            </div>
        </div>
    </form>
</template>
