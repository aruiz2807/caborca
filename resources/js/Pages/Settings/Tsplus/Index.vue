<script setup>
import { watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { Alert, AlertDescription, AlertTitle } from '@/Components/ui/alert'
import { Button } from '@/Components/ui/button'
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import InputError from '@/Components/InputError.vue'
import UserLayout from '@Layouts/UserLayout.vue'
import { Globe, MonitorPlay, Save } from 'lucide-vue-next'
import { Toaster, toast } from 'vue-sonner'
import 'vue-sonner/style.css'

const props = defineProps({
    setting: {
        type: Object,
        default: null,
    },
})

const page = usePage()

const form = useForm({
    url: props.setting?.url ?? '',
})

watch(() => page.props.flash?.message, (message) => {
    if (message === 'tsplus-stored') {
        toast.success('URL de TSPlus actualizada correctamente.', { duration: 5000 })
    }
}, { immediate: true })

const submit = () => {
    form.put(route('tsplus-settings.update'), {
        preserveScroll: true,
    })
}
</script>

<template>
    <UserLayout tabTitle="TSPlus" appName="Settings">
        <div class="max-w-6xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1.15fr)_minmax(280px,0.85fr)]">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Globe class="h-5 w-5" />
                            Configuracion de TSPlus
                        </CardTitle>
                        <CardDescription class="mt-2">
                            Define la direccion que se mostrara dentro de la opcion <span class="font-medium">Home / TSPlus</span>.
                        </CardDescription>
                    </CardHeader>

                    <CardContent>
                        <form class="grid gap-6" @submit.prevent="submit">
                            <div class="grid gap-2">
                                <Label for="url">URL embebida</Label>
                                <Input
                                    id="url"
                                    v-model="form.url"
                                    type="url"
                                    placeholder="https://tsplus.tu-dominio.com/"
                                />
                                <p class="text-xs text-muted-foreground">
                                    Usa una URL completa con <span class="font-medium">http://</span> o <span class="font-medium">https://</span>.
                                </p>
                                <InputError class="mt-1" :message="form.errors.url" />
                            </div>

                            <div class="flex justify-end">
                                <Button type="submit" :disabled="form.processing" class="gap-2">
                                    <Save class="h-4 w-4" />
                                    Guardar configuracion
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <div class="grid gap-6">
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <MonitorPlay class="h-5 w-5" />
                                Vista esperada
                            </CardTitle>
                            <CardDescription>
                                La pagina <span class="font-medium">TSPlus</span> abrira esta direccion dentro de un iframe ajustado al area visible del sistema.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="rounded-xl border border-dashed bg-muted/40 p-4 text-sm text-muted-foreground">
                                <p class="font-medium text-foreground">
                                    URL actual
                                </p>
                                <p class="mt-2 break-all">
                                    {{ form.url || 'Sin configurar' }}
                                </p>
                            </div>
                        </CardContent>
                    </Card>

                    <Alert>
                        <Globe class="h-4 w-4" />
                        <AlertTitle>Importante</AlertTitle>
                        <AlertDescription>
                            El sitio configurado debe permitir mostrarse dentro de iframes. Si el servidor remoto envia bloqueos como <span class="font-medium">X-Frame-Options</span> o <span class="font-medium">frame-ancestors</span>, el navegador no podra renderizarlo aqui.
                        </AlertDescription>
                    </Alert>
                </div>
            </div>
        </div>

        <Toaster rich-colors position="top-right" />
    </UserLayout>
</template>
