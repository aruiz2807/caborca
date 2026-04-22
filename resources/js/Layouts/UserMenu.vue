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

const itemUrl = (item) => {
    if (item.routeName) {
        return route(item.routeName)
    }

    return item.url ?? '#'
}

const isMenuItemActive = (item) => {
    if (item.activePatterns?.length) {
        return item.activePatterns.some((pattern) => route().current(pattern))
    }

    const url = itemUrl(item)

    return url !== '#' && page.url.startsWith(url)
};

const hasAccess = (item) => {
    const userRoles = page.props.auth?.roles || [];
    const userType = page.props.auth?.user?.type;
    const isSuperAdmin = userRoles.includes('Super-Admin');
    
    // External users (type 'G') cannot see Settings
    if (item.title === 'Settings' && userType === 'G') {
        return false;
    }

    if (item.title === 'Usuarios' || item.title === 'Roles') {
        return isSuperAdmin;
    }
    
    return true;
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
                    <template v-for="item in MenuItems" :key="item.title">
                        <Collapsible v-if="hasAccess(item)" as-child :default-open="isMenuItemActive(item)" class="group/collapsible">
                            <SidebarMenuItem>
                                <CollapsibleTrigger as-child>
                                    <SidebarMenuButton :tooltip="item.title">
                                        <component :is="item.icon" v-if="item.icon" />
                                        <span>{{ item.title }}</span>
                                        <span v-if="item.title === 'Mensajes' && page.props.auth.unreadMessagesCount > 0" class="ml-2 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-medium text-white">
                                            {{ page.props.auth.unreadMessagesCount }}
                                        </span>
                                        <ChevronRight class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                                    </SidebarMenuButton>
                                </CollapsibleTrigger>

                                <CollapsibleContent>
                                    <SidebarMenuSub>
                                        <template v-for="subItem in item.options" :key="subItem.title">
                                            <SidebarMenuSubItem v-if="hasAccess(subItem)">
                                                <SidebarMenuSubButton as-child :isActive="isMenuItemActive(subItem)">
                                                    <Link :href="itemUrl(subItem)">
                                                        <span>{{ subItem.title }}</span>
                                                    </Link>
                                                </SidebarMenuSubButton>
                                            </SidebarMenuSubItem>
                                        </template>
                                    </SidebarMenuSub>
                                </CollapsibleContent>
                            </SidebarMenuItem>
                        </Collapsible>
                    </template>
                </SidebarMenu>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <UserMenuFooter />
        </SidebarFooter>
    </Sidebar>
</template>
