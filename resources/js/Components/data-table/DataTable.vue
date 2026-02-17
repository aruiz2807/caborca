<script setup>
import { ref } from 'vue'
import { valueUpdater } from '@/lib/utils'
import DataTablePaginator from './DataTablePaginator.vue'
import DataTableColumnToggle from './DataTableColumnToggle.vue'
import { Input } from '@/Components/ui/input'
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/Components/ui/table'
import {
    useVueTable,
    FlexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
} from '@tanstack/vue-table'

const props = defineProps({
    data: Array,
    columns: Array,
    filter: String,
    filterPlaceholder: String,
})

let data = ref(props.data)
const columns = props.columns
const sorting = ref([])
const columnFilters = ref([])
const columnVisibility = ref({})

const table = useVueTable({
    data: data.value,
    columns: columns,

    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),

    onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
    onColumnFiltersChange: updaterOrValue => valueUpdater(updaterOrValue, columnFilters),
    onColumnVisibilityChange: updaterOrValue => valueUpdater(updaterOrValue, columnVisibility),

    state: {
        get sorting() { return sorting.value },
        get columnFilters() { return columnFilters.value },
        get columnVisibility() { return columnVisibility.value },
    },
});

const updateTable = (updatedData) => {
    data.value = updatedData
};

defineExpose({
  updateTable
});
</script>

<template>
    <div class="flex items-center py-4">
        <Input class="max-w-sm" :placeholder="`Buscar por ${props.filterPlaceholder}...`"
            :model-value="table.getColumn(props.filter)?.getFilterValue()"
            @update:model-value=" table.getColumn(props.filter)?.setFilterValue($event)" />

        <DataTableColumnToggle :table="table" />
    </div>

    <div class="rounded-md border">
        <Table>
            <TableHeader>
                <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                    <TableHead v-for="header in headerGroup.headers" :key="header.id">
                        <FlexRender :render="header.column.columnDef.header" :props="header.getContext()" />
                    </TableHead>
                </TableRow>
            </TableHeader>
            <TableBody>
                <TableRow v-for="row in table.getRowModel().rows" :key="row.id">
                    <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                        <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                    </TableCell>
                </TableRow>
            </TableBody>
        </Table>
    </div>

    <DataTablePaginator :table="table" />
</template>
