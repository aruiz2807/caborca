<script setup>
import { inject } from "vue"
import { useForm, router } from '@inertiajs/vue3';
import { cn } from '@/lib/utils'
import { Button } from '@/Components/ui/button'
import { CalendarIcon } from 'lucide-vue-next'
import { Calendar } from '@/Components/ui/calendar'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { DateFormatter, getLocalTimeZone, today } from '@internationalized/date'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Popover, PopoverContent, PopoverTrigger } from '@/Components/ui/popover'
import { Select, SelectContent, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Textarea } from '@/Components/ui/textarea'
import InputError from '@/Components/InputError.vue'

const form = useForm({
    purchase_order: '',
    economic_number: '',
    vehicle_vin: '',
    vehicle_plate: '',
    vehicle_dependency: '',
    vehicle_dependency_id: '',
    vehicle_taxid: '',
    vehicle_description: '',
    vehicle_model: '',
    service_type: '',
    service_location: '',
    service_date: null,
    service_description: '',
});

let openDialog = inject('openDialogState')
const defaultPlaceholder = today(getLocalTimeZone())
const df = new DateFormatter('es-MX', {
  dateStyle: 'short',
})

const fetchVehicleData = () => {
    console.log('Fetch Vehicle Data');

    router.visit(route('orders.vehicle_data', { economic_number: form.economic_number }), { // defined server-side route
    only: ['vehicleData'], // Request only the 'vehicleData' prop from the server
    preserveState: true, // Keep the current form state/scroll position
        onSuccess: (page) => {
            // The new 'vehicleData' will be merged into the current page props automatically
            console.log('Vehicle data loaded:', page.props.vehicleData);

            form.vehicle_dependency = page.props.vehicleData[0].client;
            form.vehicle_dependency_id = page.props.vehicleData[0].idClient;
            form.vehicle_taxid = page.props.vehicleData[0].taxid;
            form.vehicle_vin = page.props.vehicleData[0].vin;
            form.vehicle_plate = page.props.vehicleData[0].plate;
            form.vehicle_description = page.props.vehicleData[0].description;
            form.vehicle_model = page.props.vehicleData[0].modelo;
        },
    });
};

const submit = () => {
    const date = form.service_date

    if (date) {
        form.service_date = `${date.year}-${String(date.month).padStart(2, '0')}-${String(date.day).padStart(2, '0')}`
    }

    form.post(route('orders.store'), {
        onFinish: () => {

        },
        onSuccess: () => {
            openDialog.value = false
        }
    });
};
</script>

<template>
    <form @submit.prevent="submit">
        <div class="grid grid-cols-2 gap-4">

            <div class="grid gap-2">
                <Label for="purchase_order">
                    {{ $t("app.purchase_order") }}
                </Label>
                <Input v-model="form.purchase_order" id="purchase_order" type="text" placeholder="Numero de la orden de servicio" required />
                <InputError class="mt-2" :message="form.errors.purchase_order" />
            </div>

            <div class="grid gap-2">
                <Label for="economic_number">
                    {{ $t("app.economic_number") }}
                </Label>
                <div class="flex w-full max-w-sm items-center space-x-2">
                    <Input v-model="form.economic_number" id="economic_number" type="text" placeholder="Numero economico de la unidad" required />
                    <Button @click="fetchVehicleData()">
                        Buscar
                    </Button>
                </div>
                <InputError class="mt-2" :message="form.errors.economic_number" />
            </div>

            <Card class="col-span-2">
                <CardHeader>
                    <CardTitle>{{ $t("pages.orders.vehicle_data") }}</CardTitle>
                </CardHeader>

                <CardContent>
                    <div class="grid grid-cols-2 gap-4">

                        <!-- Hidden input field -->
                        <Input v-model="form.vehicle_dependency_id" id="vehicle_dependency_id" type="hidden"/>

                        <div class="grid gap-2">
                            <Label for="vehicle_dependency">
                                {{ $t("app.dependency") }}
                            </Label>
                            <Input v-model="form.vehicle_dependency" id="vehicle_dependency" type="text" placeholder="Nombre de la dependencia" required />
                            <InputError class="mt-2" :message="form.errors.vehicle_dependency" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="vehicle_taxid">
                                {{ $t("app.taxid") }}
                            </Label>
                            <Input v-model="form.vehicle_taxid" id="vehicle_taxid" type="text" placeholder="RFC" required />
                            <InputError class="mt-2" :message="form.errors.vehicle_taxid" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="vehicle_description">
                                {{ $t("app.description") }}
                            </Label>
                            <Input v-model="form.vehicle_description" id="vehicle_description" type="text" placeholder="Descripción del vehiculo" required />
                            <InputError class="mt-2" :message="form.errors.vehicle_description" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="vehicle_model">
                                {{ $t("app.model") }}
                            </Label>
                            <Input v-model="form.vehicle_model" id="vehicle_model" type="text" placeholder="Año modelo del vehiculo" required />
                            <InputError class="mt-2" :message="form.errors.vehicle_model" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="vehicle_vin">
                                {{ $t("app.vin") }}
                            </Label>
                            <Input v-model="form.vehicle_vin" id="vehicle_vin" type="text" placeholder="Número de serie del vehiculo" required />
                            <InputError class="mt-2" :message="form.errors.vehicle_vin" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="vehicle_plate">
                                {{ $t("app.plate") }}
                            </Label>
                            <Input v-model="form.vehicle_plate" id="vehicle_plate" type="text" placeholder="Placas del vehiculo" required />
                            <InputError class="mt-2" :message="form.errors.vehicle_plate" />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card class="col-span-2">
                <CardHeader>
                    <CardTitle>{{ $t("pages.orders.service_data") }}</CardTitle>
                </CardHeader>

                <CardContent>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label for="service_type">
                                {{ $t("app.service") }}
                            </Label>

                            <Select v-model="form.service_type">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Selecciona un servicio" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="service in $page.props.services" :key="service.id" :value="service.id">
                                        {{ service.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <InputError class="mt-2" :message="form.errors.service_type" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="service_location">
                                {{ $t("app.location") }}
                            </Label>

                            <Select v-model="form.service_location">
                                <SelectTrigger class="w-full">
                                    <SelectValue placeholder="Selecciona una localidad" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="location in $page.props.locations" :key="location.id" :value="location.id">
                                        {{ location.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <InputError class="mt-2" :message="form.errors.service_location" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="service_date">
                                {{ $t("app.date") }}
                            </Label>

                            <Popover v-slot="{ close }">
                                <PopoverTrigger as-child>
                                    <Button variant="outline" :class="cn('justify-start text-left font-normal', !form.service_date && 'text-muted-foreground')">
                                        <CalendarIcon />
                                        {{ form.service_date ? df.format(form.service_date.toDate(getLocalTimeZone())) : "Fecha preferente" }}
                                    </Button>
                                </PopoverTrigger>

                                <PopoverContent class="w-auto p-0" align="start">
                                    <Calendar
                                        v-model="form.service_date"
                                        :default-placeholder="defaultPlaceholder"
                                        layout="month-and-year"
                                        initial-focus
                                        @update:model-value="close"
                                    />
                                </PopoverContent>
                            </Popover>

                            <InputError class="mt-2" :message="form.errors.service_date" />
                        </div>

                        <div class="grid gap-2"></div>

                        <div class="col-span-2 grid gap-2">
                            <Label for="service_description">
                                {{ $t("app.description") }}
                            </Label>

                            <Textarea v-model="form.service_description" id="service_description" placeholder="Descripción del servicio solicitado." />

                            <InputError class="mt-2" :message="form.errors.service_description" />
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="pt-6 grid grid-cols-1">
            <div class="flex justify-center items-center">
                <Button class="min-w-64" type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ $t("app.save") }}
                </Button>
            </div>
        </div>
    </form>
</template>
