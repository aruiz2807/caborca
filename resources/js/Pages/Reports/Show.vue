<script setup>
import { Alert, AlertDescription, AlertTitle } from '@/Components/ui/alert'
import UserLayout from '@Layouts/UserLayout.vue'
import { TriangleAlert } from 'lucide-vue-next'

defineProps({
    report: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <UserLayout
        :tabTitle="report.name"
        appName="Reportes"
        panelClass="flex-1 overflow-hidden rounded-xl border bg-background shadow-sm"
    >
        <div v-if="report.embed_url" class="flex h-[calc(100svh-6rem)] flex-col">
            <iframe
                :src="report.embed_url"
                :title="`BI ${report.name}`"
                class="h-full w-full flex-1 border-0 bg-background"
                loading="lazy"
                referrerpolicy="strict-origin-when-cross-origin"
            />
        </div>

        <div v-else class="flex h-[calc(100svh-6rem)] items-center justify-center p-6">
            <Alert class="max-w-2xl">
                <TriangleAlert class="h-4 w-4" />
                <AlertTitle>Panel BI sin URL</AlertTitle>
                <AlertDescription class="space-y-3">
                    <p>
                        El panel seleccionado no tiene una URL configurada.
                    </p>
                    <p>
                        Solicita a un administrador revisar <span class="font-medium">Settings / BI</span>.
                    </p>
                </AlertDescription>
            </Alert>
        </div>
    </UserLayout>
</template>

