<script setup>
import { h } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { DataTable, DataTableActionsColumn, DataTableColumnSorting } from '@/Components/data-table'
import { Trash2, Eye, EyeOff } from 'lucide-vue-next';
import { useTrans } from '/resources/js/Composables/trans';
import { Button } from '@/Components/ui/button'

const page = usePage()

const markAsRead = (id) => {
    router.put(route('messages.update', id))
}

const deleteMessage = (id) => {
    if (confirm(useTrans('app.confirm_delete'))) {
        router.delete(route('messages.destroy', id))
    }
}

const messagesColumns = [
    {
        accessorKey: 'title',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.title')
            })
        ),
    },
    {
        accessorKey: 'message',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.message')
            })
        ),
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
            return h('span', {
                class: status === 'New' ? 'font-bold text-blue-600' : 'text-gray-500'
            }, useTrans(`app.${status.toLowerCase()}`))
        }
    },
    {
        accessorKey: 'created_at',
        header: ({ column }) => (
            h(DataTableColumnSorting, {
                column: column,
                title: useTrans('app.date')
            })
        ),
        cell: ({ row }) => new Date(row.original.created_at).toLocaleString()
    },
    {
        id: 'actions',
        enableHiding: false,
        header: () => h('div', { class: 'font-bold text-right' }, useTrans('app.actions')),
        cell: ({ row }) => {
            const message = row.original

            return h('div', { class: 'text-right flex justify-end gap-2' }, [
                message.status === 'New' ? h(Button, {
                    variant: 'outline',
                    size: 'icon',
                    onClick: () => markAsRead(message.id)
                }, () => h(Eye, { class: 'w-4 h-4' })) : h(Button, {
                    variant: 'ghost',
                    size: 'icon',
                    disabled: true
                }, () => h(EyeOff, { class: 'w-4 h-4 text-gray-300' })),
                h(Button, {
                    variant: 'destructive',
                    size: 'icon',
                    onClick: () => deleteMessage(message.id)
                }, () => h(Trash2, { class: 'w-4 h-4' }))
            ])
        },
    },
]
</script>

<template>
    <DataTable :data="$page.props.messages" :columns="messagesColumns" filter="title" :filterPlaceholder="$t('app.title')" />
</template>
