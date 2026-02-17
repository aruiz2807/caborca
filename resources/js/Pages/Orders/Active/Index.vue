<script setup>
import { ref, provide } from "vue"
import { router } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import 'vue-sonner/style.css'
import { Toaster, toast } from 'vue-sonner'
import { useTrans } from '/resources/js/Composables/trans';
import UserLayout from "@Layouts/UserLayout.vue"
import OrdersTable from "./Table.vue"
import OrdersCreateDialog from "./DialogCreate.vue"

const openDialog = ref(false)
provide('openDialogState', openDialog);

const showMessage = (message) => {
    setTimeout(() => {
        router.visit(route('orders.active'))
    }, 1250)

    if(message === 'stored') {
        toast.success(useTrans('pages.orders.toast_success_stored'), { duration: 1500 });
    }
    else if(message === 'deleted') {
        toast.warning(useTrans('pages.orders.toast_success_deleted'), { duration: 1500 });
    }
};
</script>

<template>
    <UserLayout tabTitle="Activas" appName="Ordenes">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>{{ $t("pages.orders.title") }}</CardTitle>
                            <CardDescription class="mt-2">{{ $t("pages.orders.description") }}</CardDescription>
                        </div>

                        <Button @click="openDialog = true" >
                            {{ $t("pages.orders.button_create") }}
                        </Button>

                        <OrdersCreateDialog />
                    </div>
                </CardHeader>

                <CardContent>
                    <OrdersTable />
                    <Toaster richColors />
                </CardContent>
            </Card>
        </div>
    </UserLayout>

    <div v-if="$page.props.flash.message">
        {{ showMessage($page.props.flash.message) }}
    </div>
</template>
