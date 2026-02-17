<script setup>
import { ArrowDownIcon, ArrowUpIcon, ChevronsUpDownIcon, EyeOffIcon } from 'lucide-vue-next'
import { cn } from '@/lib/utils'
import { Button } from '@/Components/ui/button'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu'

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    column: {
        type: Object,
        required: true,
        validator: (value) =>
            value &&
            typeof value.getIsSorted === 'function' &&
            typeof value.getCanSort === 'function',
    }
});
</script>

<template>
    <div v-if="column.getCanSort()" :class="cn('flex items-center space-x-2', $attrs.class ?? '')">
        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button variant="ghost" size="sm" class="-ml-3 h-8 data-[state=open]:bg-accent">
                    <div class="text-sm font-bold">{{ title }}</div>
                    <ArrowDownIcon v-if="column.getIsSorted() === 'desc'" class="w-4 h-4 ml-2" />
                    <ArrowUpIcon v-else-if="column.getIsSorted() === 'asc'" class="w-4 h-4 ml-2" />
                    <ChevronsUpDownIcon v-else class="w-4 h-4 ml-2" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="start">
                <DropdownMenuItem @click="column.toggleSorting(false)">
                    <ArrowUpIcon class="mr-2 h-3.5 w-3.5 text-muted-foreground/70" />
                    {{ $t('app.ascending') }}
                </DropdownMenuItem>
                <DropdownMenuItem @click="column.toggleSorting(true)">
                    <ArrowDownIcon class="mr-2 h-3.5 w-3.5 text-muted-foreground/70" />
                    {{ $t('app.descending') }}
                </DropdownMenuItem>
                <DropdownMenuSeparator />
                <DropdownMenuItem @click="column.toggleVisibility(false)">
                    <EyeOffIcon class="mr-2 h-3.5 w-3.5 text-muted-foreground/70" />
                    {{ $t('app.hide') }}
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    </div>

    <div v-else :class="$attrs.class">
        {{ title }}
    </div>
</template>
