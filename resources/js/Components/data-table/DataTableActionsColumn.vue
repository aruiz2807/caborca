<script setup>
import { ref, shallowRef, provide, computed } from 'vue'
import { MoreHorizontal } from 'lucide-vue-next'
import { Button } from '@/Components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/Components/ui/dialog'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger
} from '@/Components/ui/dropdown-menu'

const props = defineProps({
    record: Object,
    actions: Array,
    size: String,
});

const visibleActions = computed(() =>
    props.actions.filter(action => !action.hidden)
)

let openDialog = ref(false)
let form = shallowRef(null)
let title = ref('')
let description = ref('')
provide('openDialogState', openDialog)

const showDialog = (formType, formTitle, formDescription) => {
    openDialog.value = true
    form.value = formType
    title.value = formTitle
    description = formDescription
};
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" class="w-8 h-8 p-0">
                <span class="sr-only">Open menu</span>
                <MoreHorizontal class="w-4 h-4" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuItem v-for="action in visibleActions" :key="record" @select="showDialog(action.form, action.title, action.description)">
                <component :is="action.icon" v-if="action.icon" />
                <span>{{ action.name }}</span>
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>

    <Dialog v-model:open="openDialog">
        <DialogContent :class="`sm:max-w-[${size}]`">
            <DialogHeader>
                <DialogTitle>
                    {{ title }}
                </DialogTitle>

                <DialogDescription>
                    {{ description }}
                </DialogDescription>
            </DialogHeader>

            <component :is="form" v-if="form" :record="record" />

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
