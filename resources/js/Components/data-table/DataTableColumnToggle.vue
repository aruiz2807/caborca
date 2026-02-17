<script setup>
import { Settings2Icon } from 'lucide-vue-next'
import { Button } from '@/Components/ui/button'
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu'

const props = defineProps({
    table: {
        type: Object,
        required: true,
        validator: (value) =>
            value &&
            typeof value.getAllColumns === 'function',
    }
});
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="outline" class="ml-auto">
                <Settings2Icon class="w-4 h-4 ml-2" />
                {{ $t('app.columns') }}
            </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent align="end">
            <DropdownMenuLabel>{{ $t('app.show') }}</DropdownMenuLabel>

            <DropdownMenuSeparator />

            <DropdownMenuCheckboxItem
                v-for="column in table.getAllColumns().filter((column) => column.getCanHide())"
                :key="column.id" class="capitalize"
                :modelValue="column.getIsVisible()"
                @update:modelValue="(value) => {column.toggleVisibility(!!value)}"
            >
                {{ $t('app.' + column.id) }}
            </DropdownMenuCheckboxItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
