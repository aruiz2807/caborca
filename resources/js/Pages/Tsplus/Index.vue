<script setup>
import { Alert, AlertDescription, AlertTitle } from '@/Components/ui/alert'
import UserLayout from '@Layouts/UserLayout.vue'
import { TriangleAlert } from 'lucide-vue-next'

defineProps({
    url: {
        type: String,
        default: '',
    },
})
</script>

<template>
    <UserLayout
        tabTitle="TSPlus"
        appName="Home"
        panelClass="flex-1 overflow-hidden rounded-xl border bg-background shadow-sm"
    >
        <div v-if="url" class="flex h-[calc(100svh-6rem)] flex-col">
            <iframe
                :src="url"
                title="TSPlus"
                class="h-full w-full flex-1 border-0 bg-background"
                loading="lazy"
                referrerpolicy="strict-origin-when-cross-origin"
            />
        </div>

        <div v-else class="flex h-[calc(100svh-6rem)] items-center justify-center p-6">
            <Alert class="max-w-2xl">
                <TriangleAlert class="h-4 w-4" />
                <AlertTitle>TSPlus sin configurar</AlertTitle>
                <AlertDescription class="space-y-3">
                    <p>
                        Todavia no hay una URL registrada para esta vista.
                    </p>
                    <p>
                        Un super administrador debe capturarla en <span class="font-medium">Settings / TSPlus</span>.
                    </p>
                </AlertDescription>
            </Alert>
        </div>
    </UserLayout>
</template>
