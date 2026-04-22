<script setup>
import { h } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { DataTable, DataTableActionsColumn, DataTableColumnSorting } from '@/Components/data-table'
import { Trash2, SquarePen, CircleCheck, CircleX } from 'lucide-vue-next';
import { useTrans } from '/resources/js/Composables/trans';
import UpdateForm from "./FormUpdate.vue"
import DeleteForm from "./FormDelete.vue"

const page = usePage()
const dependenciesActions = [
    {
        name: useTrans('app.edit'),
        form: UpdateForm,
        icon: SquarePen,
        title: useTrans('pages.settings.dependencies_update_title'),
        description: useTrans('pages.settings.dependencies_update_description'),
    },
    {
        name: useTrans('app.delete'),
        form: DeleteForm,
        icon: Trash2,
        title: useTrans('pages.settings.dependencies_delete_title'),
        description: useTrans('pages.settings.dependencies_delete_description'),
        hidden: !page.props.auth.permissions.includes('delete-dependency'),
    },
]
const dependenciesColumns = [
    {
        accessorKey: 'id',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.id')
            })
        ),
    },
    {
        accessorKey: 'name',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.name')
            })
        ),
    },
    {
        accessorKey: 'customer_number',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.customer_number')
            })
        ),
    },
    {
        accessorKey: 'location.name',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.location')
            })
        ),
        cell: ({ row }) => row.original.location?.name ?? 'N/D',
    },
    {
        accessorKey: 'user.name',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.assigned')
            })
        ),
        cell: ({ row }) => row.original.user?.name ?? 'N/D',
    },
    {
        accessorKey: 'advisor.name',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.advisor')
            })
        ),
        cell: ({ row }) => row.original.advisor?.name ?? 'N/D',
    },
    {
        accessorKey: 'status',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.status')
            })
        ),
        cell: ({ row }) => {
            const status = row.original.status
            const isActive = status === 'Active'
            
            return h('div', { class: 'flex items-center gap-2' }, [
                h(isActive ? CircleCheck : CircleX, {
                    class: isActive ? 'text-green-500 w-4 h-4' : 'text-red-500 w-4 h-4'
                }),
                h('span', useTrans(isActive ? 'app.active' : 'app.inactive'))
            ])
        }
    },
    {
        id: 'actions',
        enableHiding: false,
        header: () => h('div', { class: 'font-bold text-right' }, useTrans('app.actions')),
        cell: ({ row }) => {
            const record = page.props.dependencies[page.props.dependencies.findIndex(dependency => dependency.id === row.original.id)]
            const actions = dependenciesActions
            const size = '425px'

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
    <DataTable :data="$page.props.dependencies" :columns="dependenciesColumns" filter="name" :filterPlaceholder="$t('app.name')" />
</template>
