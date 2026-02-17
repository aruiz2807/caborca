<script setup>
import { ChevronsUpDown, Plus } from 'lucide-vue-next'
import { ref } from 'vue'
import { Companies } from "../../assets/stores/companies"
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuShortcut,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu'
import {
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from '@/Components/ui/sidebar'

const { isMobile } = useSidebar()
const activeCompany = ref(Companies[0])
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <SidebarMenuButton size="lg"
                        class="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground">
                        <div
                            class="flex aspect-square size-8 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                            <component :is="activeCompany.logo" class="size-4" />
                        </div>
                        <div class="grid flex-1 text-left text-sm leading-tight">
                            <span class="truncate font-semibold">
                                {{ activeCompany.name }}
                            </span>
                            <span class="truncate text-xs">{{ activeCompany.plan }}</span>
                        </div>
                        <ChevronsUpDown class="ml-auto" />
                    </SidebarMenuButton>
                </DropdownMenuTrigger>
                <DropdownMenuContent class="w-[--reka-dropdown-menu-trigger-width] min-w-56 rounded-lg" align="start"
                    :side="isMobile ? 'bottom' : 'right'" :side-offset="4">
                    <DropdownMenuLabel class="text-xs text-muted-foreground">
                        Companies
                    </DropdownMenuLabel>
                    <DropdownMenuItem v-for="(company, index) in Companies" :key="company.name" class="gap-2 p-2"
                        @click="activeCompany = company">
                        <div class="flex size-6 items-center justify-center rounded-sm border">
                            <component :is="company.logo" class="size-4 shrink-0" />
                        </div>
                        {{ company.name }}
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
