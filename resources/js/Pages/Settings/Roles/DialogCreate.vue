<script setup>
import { inject } from "vue"
import { Button } from '@/Components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog'
import CreateForm from "./FormCreate.vue"

let openDialog = inject('openDialogState');
</script>

<template>
    <Dialog :open="openDialog" v-on:update:open="openDialog = false">
        <DialogContent class="sm:max-w-[425px]">
            <DialogHeader>
                <DialogTitle>
                    {{ $t("pages.settings.roles_create_title") }}
                </DialogTitle>

                <DialogDescription>
                    {{ $t("pages.settings.roles_create_description") }}
                </DialogDescription>
            </DialogHeader>

            <CreateForm />

            <Button variant="secondary" @click="openDialog=false" >
                {{ $t("app.cancel") }}
            </Button>
        </DialogContent>
    </Dialog>
</template>
