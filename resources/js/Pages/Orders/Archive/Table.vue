<script setup>
import { h } from 'vue'
import { DataTable, DataTableActionsColumn, DataTableColumnSorting } from '@/Components/data-table'
import { useTrans } from '/resources/js/Composables/trans';

const props = defineProps({
    orders: {
        type: Array,
        required: true
    }
})

const ordersActions = [

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
        accessorKey: 'dependency.name',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.dependency')
            })
        ),
        cell: ({ row }) => row.original.dependency?.name ?? 'N/D',
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
        accessorKey: 'service_type.name',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.service')
            })
        ),
        cell: ({ row }) => row.original.service_type?.name ?? 'N/D',
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
            const record = props.orders.find(order => order.id === row.original.id)
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
    <DataTable :data="orders" :columns="ordersColumns" filter="purchase_order" :filterPlaceholder="$t('app.purchase_order')" />
</template>
