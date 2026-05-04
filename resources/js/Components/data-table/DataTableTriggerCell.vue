<script setup>
import { ref, provide } from 'vue'
import { Button } from '@/Components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog'

const props = defineProps({
    record: Object,
    action: Object,
    size: {
        type: String,
        default: '825px'
    },
    label: String,
});

const openDialog = ref(false)
provide('openDialogState', openDialog)

const showDialog = () => {
    openDialog.value = true
};
</script>

<template>
    <button 
        @click="showDialog" 
        class="font-medium text-primary hover:underline underline-offset-4"
    >
        {{ label }}
    </button>

    <Dialog v-model:open="openDialog">
        <DialogContent :class="`sm:max-w-[${size}]`">
            <DialogHeader>
                <DialogTitle>{{ action.title }}</DialogTitle>
                <DialogDescription>{{ action.description }}</DialogDescription>
            </DialogHeader>

            <component :is="action.form" v-if="action.form" :record="record" />

            <div class="grid grid-cols-1">
                <div class="flex justify-center items-center">
                    <Button class="min-w-64" variant="secondary" @click="openDialog = false">
                        {{ $t("app.cancel") }}
                    </Button>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
