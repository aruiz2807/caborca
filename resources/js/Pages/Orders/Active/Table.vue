<script setup>
import { h } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { DataTable, DataTableActionsColumn, DataTableColumnSorting } from '@/Components/data-table'
import { Trash2, SquarePen, CalendarDays} from 'lucide-vue-next';
import { useTrans } from '/resources/js/Composables/trans';
import OrderForm from "./FormOrder.vue"
import DeleteForm from "./FormDelete.vue"
import ScheduleForm from "./FormSchedule.vue"

const page = usePage()
const ordersActions = [
    {
        name: useTrans('app.open'),
        form: OrderForm,
        icon: SquarePen,
        title: useTrans('pages.orders.record_title'),
        description: useTrans('pages.orders.record_description'),
    },
    {
        name: useTrans('app.delete'),
        form: DeleteForm,
        icon: Trash2,
        title: useTrans('pages.settings.locations_delete_title'),
        description: useTrans('pages.settings.locations_delete_description'),
    },
    {
        name: useTrans('app.schedule'),
        form: ScheduleForm,
        icon: CalendarDays,
        title: useTrans('app.schedule'),
        description: useTrans('pages.orders.schedule_description'),
        hidden: false,
    },
]
const ordersColumns = [
    {
        accessorKey: 'purchase_order',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.purchase_order')
            })
        ),
    },
    {
        accessorKey: 'economic_number',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.economic_number')
            })
        ),
    },
    {
        accessorKey: 'vehicle_dependency',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.dependency')
            })
        ),
    },
    {
        accessorKey: 'vehicle_description',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.vehicle_description')
            })
        ),
    },
    {
        accessorKey: 'service_type',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.service')
            })
        ),
    },
    {
        accessorKey: 'appointment',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.appointment')
            })
        ),
    },
    {
        accessorKey: 'service_order',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.service_order')
            })
        ),
    },
    {
        id: 'actions',
        enableHiding: false,
        header: () => h('div', { class: 'font-bold text-right' }, useTrans('app.actions')),
        cell: ({ row }) => {
            const record = page.props.orders[page.props.orders.findIndex(orders => orders.id === row.original.id)]
            const actions = ordersActions
            const size = '825px'

            return h('div', { class: 'text-right relative' }, h(DataTableActionsColumn, {
                record,
                actions,
                size
            }))
        },
    },
]
</script>

<template>
    <DataTable :data="$page.props.orders" :columns="ordersColumns" filter="purchase_order" :filterPlaceholder="$t('app.purchase_order')" />
</template>
