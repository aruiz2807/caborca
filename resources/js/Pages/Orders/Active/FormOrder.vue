<script setup>
import { CircleCheckBig, Ban, BookUser, Truck, CalendarCheck } from 'lucide-vue-next'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Label } from '@/Components/ui/label'
import { Stepper, StepperIndicator, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from '@/Components/ui/stepper'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'
import { useTrans } from '/resources/js/Composables/trans';

const props = defineProps({
    record: Object,
});

const events = [
    {
        id: 1,
        date: '2026-01-17',
        description: 'Solicitud creada',
        user: 'Admin',
    },
    {
        id: 2,
        date: '2026-01-17',
        description: 'Cita agendada para el 20 de enero',
        user: 'Asesor',
    },
    {
        id: 3,
        date: '2026-01-18',
        description: 'Cita cancelada por refacciones',
        user: 'Asesor',
    },
    {
        id: 4,
        date: '2026-01-19',
        description: 'Credit agendada para el 25 de enero',
        user: 'Asesor',
    },
];

const steps = [
    {
        step: 1,
        title: useTrans('pages.orders.status_requested'),
        icon: BookUser,
    },
    {
        step: 2,
        title: useTrans('pages.orders.status_scheduled'),
        icon: CalendarCheck,
    },
    {
        step: 3,
        title: useTrans('pages.orders.status_entered'),
        icon: Truck,
    },
    {
        step: 4,
        title: useTrans('pages.orders.status_finished'),
        icon: CircleCheckBig,
    },
    {
        step: 5,
        title: useTrans('pages.orders.status_no_show'),
        icon: Ban,
    },
];
</script>

<template>
    <div>
        <Card>
            <CardHeader>
                <CardTitle>{{ $t("app.order") }} {{ props.record.purchase_order }}</CardTitle>
            </CardHeader>

            <CardContent>
                <Stepper v-model="props.record.status" class="grid grid-flow-col auto-cols-fr w-full">
                    <StepperItem
                        v-for="item in steps"
                        :key="item.step"
                        :step="item.step"
                        class="relative flex w-full flex-col items-center justify-center"
                    >
                        <StepperTrigger disabled="true">
                            <StepperIndicator v-slot="{ step }" class="bg-muted">
                                <template v-if="item.icon">
                                    <component :is="item.icon" class="w-4 h-4" />
                                </template>
                                <span v-else>{{ step }}</span>
                            </StepperIndicator>
                        </StepperTrigger>

                        <StepperSeparator
                            v-if="item.step !== steps[steps.length - 1]?.step"
                            class="absolute left-[calc(50%+20px)] right-[calc(-50%+10px)] top-5 block h-0.5 shrink-0 rounded-full bg-muted group-data-[state=completed]:bg-primary"
                        />

                        <div class="flex flex-col items-center">
                            <StepperTitle>{{ item.title }}</StepperTitle>
                        </div>
                    </StepperItem>
                </Stepper>
            </CardContent>
        </Card>
    </div>

    <div>
        <Card>
            <CardHeader>
                <CardTitle>{{ $t("app.detail") }}</CardTitle>
            </CardHeader>

            <CardContent>
                <div class="grid grid-cols-2 gap-4">
                    <div class="grid gap-2">
                        <Label>
                            {{ $t("app.dependency") }}
                        </Label>
                        <Label class="font-bold">
                            {{ props.record.vehicle_dependency }}
                        </Label>
                    </div>

                    <div class="grid gap-2">
                        <Label>
                            {{ $t("app.economic_number") }}
                        </Label>
                        <Label class="font-bold">
                            {{ props.record.economic_number }}
                        </Label>
                    </div>

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
                            {{ $t("app.plate") }}
                        </Label>
                        <Label class="font-bold">
                            {{ record.vehicle_plate }}
                        </Label>
                    </div>

                    <div class="grid gap-2">
                        <Label>
                            {{ $t("app.service_type") }}
                        </Label>
                        <Label class="font-bold">
                            {{ record.service_type }}
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
                </div>

                <div class="w-full pt-6">
                    <Tabs default-value="appointment">
                        <TabsList class="w-full">
                            <TabsTrigger class="w-full" value="appointment">{{ $t("app.appointment") }}</TabsTrigger>
                            <TabsTrigger class="w-full" value="order">{{ $t("app.order") }}</TabsTrigger>
                            <TabsTrigger class="w-full" value="tracking">{{ $t("app.tracking") }}</TabsTrigger>
                        </TabsList>

                        <TabsContent value="appointment">
                            <Card>
                                <CardContent>
                                    <div class="grid grid-cols-4 gap-2 pt-6">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="grid gap-2">
                                                <Label>
                                                    {{ $t("app.appointment") }}
                                                </Label>
                                                <Label class="font-bold">
                                                    {{ props.record.appointment }}
                                                </Label>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="grid gap-2">
                                                <Label>
                                                    {{ $t("app.date") }}
                                                </Label>
                                                <Label class="font-bold">
                                                    {{ props.record.appointment_date }}
                                                </Label>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="grid gap-2">
                                                <Label>
                                                    {{ $t("app.workshop") }}
                                                </Label>
                                                <Label class="font-bold">
                                                    {{ props.record.appointment_workshop }}
                                                </Label>
                                            </div>
                                        </div>

                                        <div class="flex flex-row-reverse">
                                            <Button>
                                                {{ $t("app.cancel") + " Cita" }}
                                            </Button>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        </TabsContent>

                        <TabsContent value="order">
                            <Card>
                                <CardContent>
                                    <div class="grid grid-cols-3 gap-2 pt-6">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="grid gap-2">
                                                <Label>
                                                    {{ $t("app.service_order") }}
                                                </Label>
                                                <Label class="font-bold">
                                                    {{ props.record.service_order }}
                                                </Label>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="grid gap-2">
                                                <Label>
                                                    {{ $t("app.date") }}
                                                </Label>
                                                <Label class="font-bold">
                                                    {{ props.record.service_order_date }}
                                                </Label>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="grid gap-2">
                                                <Label>
                                                    {{ $t("app.status") }}
                                                </Label>
                                                <Label class="font-bold">
                                                    {{ props.record.service_order_status }}
                                                </Label>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="grid gap-2">
                                                <Label>
                                                    {{ $t("app.cone") }}
                                                </Label>
                                                <Label class="font-bold">
                                                    {{ props.record.service_order_cone }}
                                                </Label>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="grid gap-2">
                                                <Label>
                                                    {{ $t("app.mileage") }}
                                                </Label>
                                                <Label class="font-bold">
                                                    {{ props.record.service_order_mileage }}
                                                </Label>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div class="grid gap-2">
                                                <Label>
                                                    {{ $t("app.advisor") }}
                                                </Label>
                                                <Label class="font-bold">
                                                    {{ props.record.service_order_user }}
                                                </Label>
                                            </div>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>
                        </TabsContent>

                        <TabsContent value="tracking">
                            <Card>
                                <CardContent>
                                    <Table>
                                        <TableHeader>
                                            <TableRow>
                                                <TableHead>ID</TableHead>
                                                <TableHead>Fecha</TableHead>
                                                <TableHead>Evento</TableHead>
                                                <TableHead>Usuario</TableHead>
                                            </TableRow>
                                        </TableHeader>

                                        <TableBody>
                                            <TableRow v-for="event in events" :key="event.id">
                                                <TableCell class="font-medium">{{ event.id }}</TableCell>
                                                <TableCell>{{ event.date }}</TableCell>
                                                <TableCell>{{ event.description }}</TableCell>
                                                <TableCell>{{ event.user }}</TableCell>
                                            </TableRow>
                                        </TableBody>
                                    </Table>
                                </CardContent>
                            </Card>
                        </TabsContent>
                    </Tabs>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
