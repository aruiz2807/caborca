<script setup>
import { usePage } from '@inertiajs/vue3'
import { ChevronRight } from 'lucide-vue-next'
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/Components/ui/collapsible'
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/Components/ui/sidebar'
import { MenuItems } from "../../assets/stores/menuItems.js"
import UserMenuHeader from './UserMenuHeader.vue'
import UserMenuFooter from './UserMenuFooter.vue'

const page = usePage()

const isMenuItemActive = (itemUrl) => {
    return page.url.startsWith(itemUrl)
};

</script>

<template>
    <Sidebar>
        <SidebarHeader>
            <UserMenuHeader />
        </SidebarHeader>

        <SidebarContent>
            <SidebarGroup>
                <SidebarGroupLabel>Platform</SidebarGroupLabel>
                <SidebarMenu>
                    <Collapsible v-for="item in MenuItems" :key="item.title" as-child :default-open="isMenuItemActive(item.url)" class="group/collapsible">
                        <SidebarMenuItem>
                            <CollapsibleTrigger as-child>
                                <SidebarMenuButton :tooltip="item.title">
                                    <component :is="item.icon" v-if="item.icon" />
                                    <span>{{ item.title }}</span>
                                    <ChevronRight class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                                </SidebarMenuButton>
                            </CollapsibleTrigger>

                            <CollapsibleContent>
                                <SidebarMenuSub>
                                    <SidebarMenuSubItem v-for="subItem in item.options" :key="subItem.title" >
                                        <SidebarMenuSubButton as-child :isActive="isMenuItemActive(subItem.url)">
                                            <Link :href="subItem.url">
                                                <span>{{ subItem.title }}</span>
                                            </Link>
                                        </SidebarMenuSubButton>
                                    </SidebarMenuSubItem>
                                </SidebarMenuSub>
                            </CollapsibleContent>
                        </SidebarMenuItem>
                    </Collapsible>
                </SidebarMenu>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <UserMenuFooter />
        </SidebarFooter>
    </Sidebar>
</template>
