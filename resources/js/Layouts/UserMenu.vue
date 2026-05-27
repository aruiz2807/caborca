<script setup>
import { computed } from 'vue'
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

const resolvedMenuItems = computed(() => {
    const dynamicOptions = {
        reportsMenuSections: Array.isArray(page.props.reportsMenuSections) ? page.props.reportsMenuSections : [],
    }

    return MenuItems.map((item) => {
        if (!item.dynamicKey) {
            return item
        }

        return {
            ...item,
            options: dynamicOptions[item.dynamicKey] ?? [],
        }
    })
})

const itemUrl = (item) => {
    if (item.routeName) {
        return route(item.routeName, item.routeParams ?? {})
    }

    return item.url ?? '#'
}

const isMenuItemActive = (item) => {
    if (item.options?.length && item.options.some((option) => isMenuItemActive(option))) {
        return true
    }

    if (item.routeName && item.routeParams && route().current(item.routeName, item.routeParams)) {
        return true
    }

    if (item.activePatterns?.length) {
        return item.activePatterns.some((pattern) => route().current(pattern))
    }

    const url = itemUrl(item)

    return url !== '#' && page.url.startsWith(url)
};

const hasAccess = (item) => {
    const userRoles = page.props.auth?.roles || [];
    const userPermissions = page.props.auth?.permissions || [];
    const userType = page.props.auth?.user?.type;
    const isSuperAdmin = userRoles.includes('Super-Admin');

    if (item.hiddenForExternal && userType === 'G') {
        return false;
    }

    if (item.requiresSuperAdmin && !isSuperAdmin) {
        return false;
    }

    if (item.requiresPermission && !userPermissions.includes(item.requiresPermission)) {
        return false;
    }

    return true;
};

const hasVisibleOptions = (item) => {
    if (item.dynamicKey && (!item.options || item.options.length === 0)) {
        return false
    }

    if (!item.options?.length) {
        return true
    }

    return item.options.some((option) => hasAccess(option) && hasVisibleOptions(option))
}
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
                    <template v-for="item in resolvedMenuItems" :key="item.title">
                        <Collapsible v-if="hasAccess(item) && hasVisibleOptions(item)" as-child :default-open="isMenuItemActive(item)" class="group/collapsible">
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
                                            <SidebarMenuSubItem v-if="hasAccess(subItem) && !subItem.options?.length">
                                                <SidebarMenuSubButton as-child :isActive="isMenuItemActive(subItem)">
                                                    <Link :href="itemUrl(subItem)">
                                                        <span>{{ subItem.title }}</span>
                                                    </Link>
                                                </SidebarMenuSubButton>
                                            </SidebarMenuSubItem>

                                            <SidebarMenuSubItem v-else-if="hasAccess(subItem) && hasVisibleOptions(subItem)">
                                                <Collapsible as-child :default-open="isMenuItemActive(subItem)" class="group/sub-collapsible">
                                                    <div class="w-full">
                                                        <CollapsibleTrigger as-child>
                                                            <SidebarMenuSubButton :isActive="isMenuItemActive(subItem)">
                                                                <span>{{ subItem.title }}</span>
                                                                <ChevronRight class="ml-auto transition-transform duration-200 group-data-[state=open]/sub-collapsible:rotate-90" />
                                                            </SidebarMenuSubButton>
                                                        </CollapsibleTrigger>

                                                        <CollapsibleContent>
                                                            <SidebarMenuSub class="ml-2">
                                                                <template v-for="reportItem in subItem.options" :key="`${subItem.title}-${reportItem.title}`">
                                                                    <SidebarMenuSubItem v-if="hasAccess(reportItem)">
                                                                        <SidebarMenuSubButton as-child size="sm" :isActive="isMenuItemActive(reportItem)">
                                                                            <Link :href="itemUrl(reportItem)">
                                                                                <span>{{ reportItem.title }}</span>
                                                                            </Link>
                                                                        </SidebarMenuSubButton>
                                                                    </SidebarMenuSubItem>
                                                                </template>
                                                            </SidebarMenuSub>
                                                        </CollapsibleContent>
                                                    </div>
                                                </Collapsible>
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
