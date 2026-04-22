<script setup>
import { ref } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { CircleCheckBig, Ban, BookUser, Truck, CalendarCheck } from 'lucide-vue-next'
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Label } from '@/Components/ui/label'
import { Stepper, StepperIndicator, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from '@/Components/ui/stepper'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/Components/ui/dialog'
import { Textarea } from '@/Components/ui/textarea'
import { useTrans } from '/resources/js/Composables/trans';

const props = defineProps({
    record: Object,
});

const page = usePage();
const showCancelDialog = ref(false);

const cancelForm = useForm({
    motive: '',
});

const confirmCancel = () => {
    cancelForm.post(route('orders.cancel_appointment', props.record.id), {
        onSuccess: () => {
            showCancelDialog.value = false;
            cancelForm.reset();
        },
    });
};

const formatDate = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('es-MX', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

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
                            {{ props.record.dependency?.name ?? 'N/D' }}
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
                            {{ record.service_type?.name ?? 'N/D' }}
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
                                    <div v-if="props.record.appointment" class="grid grid-cols-4 gap-2 pt-6">
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
                                                    {{ props.record.appointment_workshop?.name ?? 'N/D' }}
                                                </Label>
                                            </div>
                                        </div>

                                        <div class="flex flex-row-reverse">
                                            <div v-if="$page.props.auth.permissions.includes('cancel-appointment')">
                                                <Button @click="showCancelDialog = true">
                                                    {{ $t("app.cancel_appointment") }}
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="p-6 text-center text-muted-foreground">
                                        {{ $t('pages.orders.status_requested') }} - Sin cita agendada
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
                                                <TableHead>Fecha</TableHead>
                                                <TableHead>Evento</TableHead>
                                                <TableHead>Descripción</TableHead>
                                                <TableHead>Usuario</TableHead>
                                            </TableRow>
                                        </TableHeader>

                                        <TableBody v-if="props.record.events && props.record.events.length">
                                            <TableRow v-for="event in props.record.events" :key="event.id">
                                                <TableCell>{{ formatDate(event.created_at) }}</TableCell>
                                                <TableCell class="font-medium">{{ event.event_name }}</TableCell>
                                                <TableCell>{{ event.description ?? '-' }}</TableCell>
                                                <TableCell>{{ event.user?.name ?? 'Sistema' }}</TableCell>
                                            </TableRow>
                                        </TableBody>
                                        <TableBody v-else>
                                            <TableRow>
                                                <TableCell colspan="4" class="text-center text-muted-foreground">
                                                    No hay eventos registrados para esta orden.
                                                </TableCell>
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

    <Dialog v-model:open="showCancelDialog">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>{{ $t('app.cancel_appointment') }}</DialogTitle>
                <DialogDescription>
                    ¿Estás seguro de que deseas cancelar esta cita? Por favor, indica el motivo.
                </DialogDescription>
            </DialogHeader>
            <div class="grid gap-4 py-4">
                <div class="grid gap-2">
                    <Label for="motive">Motivo de cancelación</Label>
                    <Textarea
                        id="motive"
                        v-model="cancelForm.motive"
                        placeholder="Escriba aquí el motivo..."
                    />
                    <div v-if="cancelForm.errors.motive" class="text-sm text-destructive">
                        {{ cancelForm.errors.motive }}
                    </div>
                </div>
            </div>
            <DialogFooter>
                <Button variant="outline" @click="showCancelDialog = false">
                    {{ $t('app.cancel') }}
                </Button>
                <Button
                    :disabled="cancelForm.processing || !cancelForm.motive"
                    @click="confirmCancel"
                >
                    Confirmar Cancelación
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
