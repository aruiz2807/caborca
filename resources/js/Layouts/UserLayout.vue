<script setup>
import { getLastWordFromUrl } from '@/lib/utils'
import { useColorMode } from '@vueuse/core'
import { Sun, Moon } from 'lucide-vue-next'
import { Button } from '@/Components/ui/button'
import { Separator } from '@/Components/ui/separator'
import { SidebarProvider, SidebarTrigger, SidebarInset } from '@/Components/ui/sidebar'
import Menubar from './UserMenu.vue'
import {
    Breadcrumb,
    BreadcrumbItem,
    BreadcrumbLink,
    BreadcrumbList,
    BreadcrumbPage,
    BreadcrumbSeparator,
} from '@/Components/ui/breadcrumb'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger
} from '@/Components/ui/dropdown-menu'

defineProps({
    tabTitle: String,
    appName: String,
});

const mode = useColorMode()
</script>

<template>
    <Head :title="tabTitle" />
    <SidebarProvider>
        <Menubar />

        <SidebarInset>
            <header class="flex h-16 shrink-0 items-center gap-2 transition-[width,height] ease-linear group-has-[[data-collapsible=icon]]/sidebar-wrapper:h-12">
                <div class="flex items-center gap-2 px-4">
                    <SidebarTrigger class="ml-1" />

                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="ghost">
                                <Sun
                                    class="rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0" />
                                <Moon
                                    class="absolute rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100" />
                            </Button>
                        </DropdownMenuTrigger>

                        <DropdownMenuContent align="end">
                            <DropdownMenuItem @click="mode = 'light'">
                                <Sun /> {{ $t("app.light-mode") }}
                            </DropdownMenuItem>

                            <DropdownMenuItem @click="mode = 'dark'">
                                <Moon /> {{ $t("app.dark-mode") }}
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>

                    <Separator orientation="vertical" class="mr-2 h-4" />

                    <Breadcrumb>
                        <BreadcrumbList>
                            <BreadcrumbItem class="hidden md:block">
                                <BreadcrumbLink href="#">
                                    {{ appName }}
                                </BreadcrumbLink>
                            </BreadcrumbItem>
                            <BreadcrumbSeparator class="hidden md:block" />
                            <BreadcrumbItem>
                                <BreadcrumbPage>{{ getLastWordFromUrl($page.url) }}</BreadcrumbPage>
                            </BreadcrumbItem>
                        </BreadcrumbList>
                    </Breadcrumb>


                </div>
            </header>
            <div class="flex flex-1 flex-col gap-4 p-4 pt-0">
                <div class="min-h-[100vh] flex-1 rounded-xl bg-muted/50 md:min-h-min">
                    <slot />
                </div>
            </div>
        </SidebarInset>
    </SidebarProvider>
</template>
