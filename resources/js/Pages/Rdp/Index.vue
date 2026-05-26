<script setup>
import { Alert, AlertDescription, AlertTitle } from '@/Components/ui/alert'
import { Button } from '@/Components/ui/button'
import UserLayout from '@Layouts/UserLayout.vue'
import { ExternalLink, TriangleAlert } from 'lucide-vue-next'

defineProps({
    url: {
        type: String,
        default: '',
    },
})
</script>

<template>
    <UserLayout
        tabTitle="RDP"
        appName="Home"
        panelClass="flex-1 overflow-hidden rounded-xl border bg-background shadow-sm"
    >
        <div v-if="url" class="flex h-[calc(100svh-6rem)] flex-col">
            <div class="flex items-center justify-between border-b px-4 py-3">
                <div>
                    <p class="text-sm font-medium text-foreground">Sesion remota</p>
                    <p class="text-xs text-muted-foreground">
                        La pagina configurada se muestra dentro de esta ventana.
                    </p>
                </div>

                <Button asChild variant="outline" size="sm">
                    <a :href="url" target="_blank" rel="noopener noreferrer">
                        <ExternalLink class="h-4 w-4" />
                        Abrir aparte
                    </a>
                </Button>
            </div>

            <iframe
                :src="url"
                title="RDP"
                class="h-full w-full flex-1 border-0 bg-background"
                loading="lazy"
                referrerpolicy="strict-origin-when-cross-origin"
            />
        </div>

        <div v-else class="flex h-[calc(100svh-6rem)] items-center justify-center p-6">
            <Alert class="max-w-2xl">
                <TriangleAlert class="h-4 w-4" />
                <AlertTitle>RDP sin configurar</AlertTitle>
                <AlertDescription class="space-y-3">
                    <p>
                        Todavia no hay una URL registrada para esta vista.
                    </p>
                    <p>
                        Un super administrador debe capturarla en <span class="font-medium">Settings / RDP</span>.
                    </p>
                </AlertDescription>
            </Alert>
        </div>
    </UserLayout>
</template>
