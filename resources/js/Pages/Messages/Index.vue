<script setup>
import { router } from '@inertiajs/vue3'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import 'vue-sonner/style.css'
import { Toaster, toast } from 'vue-sonner'
import { useTrans } from '/resources/js/Composables/trans';
import UserLayout from "@Layouts/UserLayout.vue"
import MessagesTable from "./Table.vue"

const showMessage = (message) => {
    setTimeout(() => {
        router.visit(route('messages.index'))
    }, 1250)

    if(message === 'deleted') {
        toast.warning(useTrans('pages.messages.toast_success_deleted'), { duration: 1500 });
    }
};
</script>

<template>
    <UserLayout :tabTitle="$t('pages.messages.title')" appName="Mensajes">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div>
                            <CardTitle>{{ $t("pages.messages.title") }}</CardTitle>
                            <CardDescription class="mt-2">{{ $t("pages.messages.description") }}</CardDescription>
                        </div>
                    </div>
                </CardHeader>

                <CardContent>
                    <MessagesTable />
                    <Toaster richColors />
                </CardContent>
            </Card>
        </div>
    </UserLayout>

    <div v-if="$page.props.flash.message">
        {{ showMessage($page.props.flash.message) }}
    </div>
</template>
