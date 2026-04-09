<script setup>
import { ref, provide } from "vue"
import { useForm, router } from '@inertiajs/vue3';
import { cn } from '@/lib/utils'
import { CalendarIcon } from 'lucide-vue-next'
import { Button } from '@/Components/ui/button'
import { Calendar } from '@/Components/ui/calendar'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import { DateFormatter, getLocalTimeZone, today, parseDate } from '@internationalized/date'
import { Label } from '@/Components/ui/label'
import { Popover, PopoverContent, PopoverTrigger } from '@/Components/ui/popover'
import { Select, SelectContent, SelectItem, SelectLabel, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Separator } from '@/Components/ui/separator'
import { useTrans } from '/resources/js/Composables/trans';
import UserLayout from "@Layouts/UserLayout.vue"
import OrdersTable from "./Table.vue"

const props = defineProps({
    orders: Array,
    dependencies: Array,
    filters: Object,
});

const openDialog = ref(false)
provide('openDialogState', openDialog);

const form = useForm({
    status: props.filters?.status ?? '1',
    vehicle_dependency: props.filters?.vehicle_dependency ?? '1',
    order_date_from: props.filters?.order_date_from ? parseDate(props.filters.order_date_from) : null,
    order_date_to: props.filters?.order_date_to ? parseDate(props.filters.order_date_to) : null,
});

const defaultPlaceholder = today(getLocalTimeZone())
const df = new DateFormatter('es-MX', {
  dateStyle: 'short',
})

const status = [
    {
        id: 1,
        title: useTrans('pages.orders.status_requested'),
    },
    {
        id: 2,
        title: useTrans('pages.orders.status_scheduled'),
    },
    {
        id: 3,
        title: useTrans('pages.orders.status_entered'),
    },
    {
        id: 4,
        title: useTrans('pages.orders.status_finished'),
    },
    {
        id: 5,
        title: useTrans('pages.orders.status_no_show'),
    },
];

const fetchOrdersData = () => {
    console.log('Fetch Orders Data');

    const queryParams = {
        status: form.status,
        vehicle_dependency: form.vehicle_dependency,
        order_date_from: form.order_date_from ? form.order_date_from.toString() : null,
        order_date_to: form.order_date_to ? form.order_date_to.toString() : null,
    };

    router.get(route('orders.archive'), queryParams, {
        preserveState: true,
        replace: true,
        only: ['orders', 'filters'],
        onSuccess: (page) => {
            console.log('Orders data loaded:', page.props.orders);
        },
    });
};
</script>

<template>
    <UserLayout tabTitle="Hostorico" appName="Ordenes">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <Card>
                <CardHeader>

                    <div>
                        <CardTitle>{{ $t("pages.orders.archive_title") }}</CardTitle>
                        <CardDescription class="mt-2">{{ $t("pages.orders.archive_description") }}</CardDescription>
                    </div>

                    <div class="flex items-center justify-between pt-6 pb-6">
                        <div class="grid gap-2">
                            <Label for="status">
                                {{ $t("app.status") }}
                            </Label>

                            <Select v-model="form.status">
                                <SelectTrigger class="w-[180px]">
                                    <SelectValue placeholder="Selecciona un estatus" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="option in status" :key="option.id" :value="option.id.toString()">
                                        {{ option.title }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="grid gap-2">
                            <Label for="vehicle_dependency">
                                {{ $t("app.dependency") }}
                            </Label>

                            <Select v-model="form.vehicle_dependency">
                                <SelectTrigger class="w-[200px]">
                                    <SelectValue placeholder="Selecciona una dependencia" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="dependency in dependencies" :key="dependency.id" :value="dependency.id.toString()">
                                        {{ dependency.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="grid gap-2">
                            <Label for="order_date_from">
                                {{ $t("app.from") }}
                            </Label>

                            <Popover v-slot="{ close }">
                                <PopoverTrigger as-child>
                                    <Button variant="outline" :class="cn('justify-start text-left font-normal', !form.order_date_from && 'text-muted-foreground')">
                                        <CalendarIcon class="mr-2 h-4 w-4" />
                                        {{ form.order_date_from ? df.format(form.order_date_from.toDate(getLocalTimeZone())) : "Fecha inicial" }}
                                    </Button>
                                </PopoverTrigger>

                                <PopoverContent class="w-auto p-0" align="start">
                                    <Calendar
                                        v-model="form.order_date_from"
                                        :default-placeholder="defaultPlaceholder"
                                        layout="month-and-year"
                                        initial-focus
                                        @update:model-value="close"
                                    />
                                </PopoverContent>
                            </Popover>
                        </div>

                        <div class="grid gap-2">
                            <Label for="order_date_to">
                                {{ $t("app.to") }}
                            </Label>

                            <Popover v-slot="{ close }">
                                <PopoverTrigger as-child>
                                    <Button variant="outline" :class="cn('justify-start text-left font-normal', !form.order_date_to && 'text-muted-foreground')">
                                        <CalendarIcon class="mr-2 h-4 w-4" />
                                        {{ form.order_date_to ? df.format(form.order_date_to.toDate(getLocalTimeZone())) : "Fecha final" }}
                                    </Button>
                                </PopoverTrigger>

                                <PopoverContent class="w-auto p-0" align="start">
                                    <Calendar
                                        v-model="form.order_date_to"
                                        :default-placeholder="defaultPlaceholder"
                                        layout="month-and-year"
                                        initial-focus
                                        @update:model-value="close"
                                    />
                                </PopoverContent>
                            </Popover>
                        </div>

                        <Button @click="fetchOrdersData()">
                            {{ $t("pages.orders.button_query") }}
                        </Button>
                    </div>
                </CardHeader>

                <CardContent>
                    <Separator />
                    <OrdersTable :orders="orders" />
                </CardContent>
            </Card>
        </div>
    </UserLayout>

    <div v-if="$page.props.flash.message">
        {{ showMessage($page.props.flash.message) }}
    </div>
</template>
