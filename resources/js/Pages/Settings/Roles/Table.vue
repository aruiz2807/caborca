<script setup>
import { h } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { DataTable, DataTableActionsColumn, DataTableColumnSorting } from '@/Components/data-table'
import { Trash2, SquarePen, KeyRound} from 'lucide-vue-next';
import { useTrans } from '/resources/js/Composables/trans';
import UpdateForm from "./FormUpdate.vue"
import DeleteForm from "./FormDelete.vue"
import PermissionsForm from "./FormPermissions.vue"

const page = usePage()
const rolesActions = [
    {
        name: useTrans('app.edit'),
        form: UpdateForm,
        icon: SquarePen,
        title: useTrans('pages.settings.roles_update_title'),
        description: useTrans('pages.settings.roles_update_description'),
    },
    {
        name: useTrans('app.delete'),
        form: DeleteForm,
        icon: Trash2,
        title: useTrans('pages.settings.roles_delete_title'),
        description: useTrans('pages.settings.roles_delete_description'),
    },
    {
        name: useTrans('app.permissions'),
        form: PermissionsForm,
        icon: KeyRound,
        title: useTrans('pages.settings.roles_delete_title'),
        description: useTrans('pages.settings.roles_delete_description'),
    }
]
const rolesColumns = [
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
        accessorKey: 'description',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.description')
            })
        ),
    },
    {
        id: 'actions',
        enableHiding: false,
        header: () => h('div', { class: 'font-bold text-right' }, useTrans('app.actions')),
        cell: ({ row }) => {
            const record = page.props.roles[page.props.roles.findIndex(role => role.id === row.original.id)]
            const actions = rolesActions
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
    <DataTable :data="$page.props.roles" :columns="rolesColumns" filter="name" :filterPlaceholder="$t('app.name')" />
</template>
