<script setup>
import { h } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { DataTable, DataTableActionsColumn, DataTableColumnSorting } from '@/Components/data-table'
import { Trash2, SquarePen, KeyRound} from 'lucide-vue-next';
import { useTrans } from '/resources/js/Composables/trans';
import UpdateForm from "./FormUpdate.vue"
import DeleteForm from "./FormDelete.vue"

const page = usePage()
const locationsActions = [
    {
        name: useTrans('app.edit'),
        form: UpdateForm,
        icon: SquarePen,
        title: useTrans('pages.settings.locations_update_title'),
        description: useTrans('pages.settings.locations_update_description'),
    },
    {
        name: useTrans('app.delete'),
        form: DeleteForm,
        icon: Trash2,
        title: useTrans('pages.settings.locations_delete_title'),
        description: useTrans('pages.settings.locations_delete_description'),
    },
]
const locationsColumns = [
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
        id: 'actions',
        enableHiding: false,
        header: () => h('div', { class: 'font-bold text-right' }, useTrans('app.actions')),
        cell: ({ row }) => {
            const record = page.props.locations[page.props.locations.findIndex(location => location.id === row.original.id)]
            const actions = locationsActions
            const size = '425px'

            return h('div', { class: 'text-right relative' }, h(DataTableActionsColumn, {
                record,
                actions,
                size,
            }))
        },
    },
]
</script>

<template>
    <DataTable :data="$page.props.locations" :columns="locationsColumns" filter="name" :filterPlaceholder="$t('app.name')" />
</template>
