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
        <DialogContent class="sm:max-w-[825px]">
            <DialogHeader>
                <DialogTitle>
                    {{ $t("pages.orders.create_title") }}
                </DialogTitle>

                <DialogDescription>
                    {{ $t("pages.orders.create_description") }}
                </DialogDescription>
            </DialogHeader>

            <CreateForm />

            <div class="grid grid-cols-1">
                <div class="flex justify-center items-center">
                    <Button class="min-w-64" variant="secondary" @click="openDialog=false" >
                        {{ $t("app.cancel") }}
                    </Button>
                </div>
            </div>

        </DialogContent>
    </Dialog>
</template>
