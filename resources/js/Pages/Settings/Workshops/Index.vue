<script setup>
import { ref, provide } from "vue"
import { router } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card'
import 'vue-sonner/style.css'
import { Toaster, toast } from 'vue-sonner'
import { useTrans } from '/resources/js/Composables/trans';
import UserLayout from "@Layouts/UserLayout.vue"
import WorkshopsTable from "./Table.vue"
import WorkshopsCreateDialog from "./DialogCreate.vue"

const openDialog = ref(false)
provide('openDialogState', openDialog);

const showMessage = (message, type = 'success') => {
    if(type === 'success') {
        if(message === 'stored') {
            toast.success(useTrans('pages.settings.workshops_toast_success_stored'), { duration: 5000 });
        }
        else if(message === 'deleted') {
            toast.warning(useTrans('pages.settings.workshops_toast_success_deleted'), { duration: 5000 });
        }
    } else if (type === 'error') {
        if(message === 'has-orders') {
            toast.error(useTrans('app.delete_error'), { duration: 5000 });
        }
    }
};
</script>

<template>
    <UserLayout tabTitle="Talleres" appName="Settings">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between ...">
                        <div>
                            <CardTitle>{{ $t("pages.settings.workshops_title") }}</CardTitle>
                            <CardDescription class="mt-2">{{ $t("pages.settings.workshops_description") }}</CardDescription>
                        </div>

                        <div v-if="$page.props.auth.permissions.includes('create-workshop')">
                            <Button @click="openDialog = true" >
                                {{ $t("pages.settings.workshops_button_create") }}
                            </Button>
                        </div>

                        <WorkshopsCreateDialog />
                    </div>
                </CardHeader>

                <CardContent>
                    <WorkshopsTable />
                    <Toaster richColors />
                </CardContent>
            </Card>
        </div>
    </UserLayout>

    <div v-if="$page.props.flash.message">
        {{ showMessage($page.props.flash.message, 'success') }}
    </div>
    <div v-if="$page.props.flash.error">
        {{ showMessage($page.props.flash.error, 'error') }}
    </div>
</template>
