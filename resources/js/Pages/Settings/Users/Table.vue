<script setup>
import { h } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { DataTable, DataTableActionsColumn, DataTableColumnSorting } from '@/Components/data-table'
import { Trash2, SquarePen} from 'lucide-vue-next';
import { useTrans } from '/resources/js/Composables/trans';
import UpdateForm from "./FormUpdate.vue"
import DeleteForm from "./FormDelete.vue"

const page = usePage()
const usersActions = [
    {
        name: useTrans('app.edit'),
        form: UpdateForm,
        icon: SquarePen,
        title: useTrans('pages.settings.users_update_title'),
        description: useTrans('pages.settings.users_update_description'),
    },
    {
        name: useTrans('app.delete'),
        form: DeleteForm,
        icon: Trash2,
        title: useTrans('pages.settings.users_delete_title'),
        description: useTrans('pages.settings.users_delete_description'),
    }
]
const usersColumns = [
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
        accessorKey: 'email',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.email')
            })
        ),
    },
    {
        accessorKey: 'type',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.type')
            })
        ),
    },
    {
        accessorKey: 'bpro_user',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.bpro_user')
            })
        ),
    },
    {
        id: 'actions',
        enableHiding: false,
        header: () => h('div', { class: 'font-bold text-right' }, useTrans('app.actions')),
        cell: ({ row }) => {
            const record = page.props.users[page.props.users.findIndex(user => user.id === row.original.id)]
            const actions = usersActions
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
    <DataTable :data="$page.props.users" :columns="usersColumns" filter="name" :filterPlaceholder="$t('app.name')" />
</template>
