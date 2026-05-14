<script setup>
import { computed, watch } from 'vue'
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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/Components/ui/select'
import { Separator } from '@/Components/ui/separator'
import { Switch } from '@/Components/ui/switch'
import InputError from '@/Components/InputError.vue'
import UserLayout from '@Layouts/UserLayout.vue'
import { Mail, Send, ShieldCheck, Server } from 'lucide-vue-next'
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
    provider: props.setting?.provider ?? 'smtp',
    host: props.setting?.host ?? '',
    port: props.setting?.port?.toString() ?? '587',
    encryption: props.setting?.encryption ?? 'tls',
    username: props.setting?.username ?? '',
    password: '',
    oauth_tenant_id: props.setting?.oauth_tenant_id ?? '',
    oauth_client_id: props.setting?.oauth_client_id ?? '',
    oauth_client_secret: '',
    oauth_mailbox: props.setting?.oauth_mailbox ?? props.setting?.from_email ?? '',
    from_name: props.setting?.from_name ?? 'Caborca',
    from_email: props.setting?.from_email ?? '',
    active: props.setting?.active ?? true,
})

const testForm = useForm({
    test_email: props.setting?.from_email ?? '',
})

const isOffice365 = computed(() => form.provider === 'office365_oauth2')
const isActive = computed(() => !!props.setting?.active)
const providerLabel = computed(() => (
    props.setting?.provider === 'office365_oauth2' ? 'Office 365 OAuth2' : 'SMTP tradicional'
))

watch(() => form.provider, (provider) => {
    if (provider !== 'office365_oauth2') {
        return
    }

    if (!form.host) {
        form.host = 'smtp.office365.com'
    }

    if (!form.port) {
        form.port = '587'
    }

    if (!form.encryption || form.encryption === 'none') {
        form.encryption = 'tls'
    }
}, { immediate: true })

watch(() => page.props.flash?.message, (message) => {
    if (message === 'smtp-stored') {
        toast.success('Configuracion SMTP actualizada correctamente.', { duration: 5000 })
    }

    if (message === 'smtp-test-sent') {
        toast.success('Correo de prueba enviado correctamente.', { duration: 5000 })
    }
}, { immediate: true })

watch(() => page.props.flash?.error, (error) => {
    if (error === 'smtp-not-configured') {
        toast.error('Configura y activa un servidor SMTP antes de enviar pruebas.', { duration: 5000 })
    }

    if (error === 'smtp-test-failed') {
        toast.error('No fue posible enviar el correo de prueba.', { duration: 5000 })
    }
}, { immediate: true })

const submit = () => {
    form.transform((data) => ({
        ...data,
        active: data.active ? 1 : 0,
    })).put(route('smtp.update'), {
        preserveScroll: true,
    })
}

const sendTest = () => {
    testForm.post(route('smtp.test'), {
        preserveScroll: true,
    })
}
</script>

<template>
    <UserLayout tabTitle="SMTP" appName="Settings">
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <div class="grid gap-6 lg:grid-cols-[minmax(0,1.1fr)_minmax(320px,0.9fr)]">
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Server class="h-5 w-5" />
                            Correo saliente
                        </CardTitle>
                        <CardDescription class="mt-2">
                            Configura el servidor con el que el sistema enviara credenciales, recuperacion de contrasena y notificaciones.
                        </CardDescription>
                    </CardHeader>

                    <CardContent>
                        <form @submit.prevent="submit" class="grid gap-6">
                            <div class="grid gap-2">
                                <Label for="provider">Proveedor</Label>
                                <Select v-model="form.provider">
                                    <SelectTrigger id="provider" class="w-full">
                                        <SelectValue placeholder="Selecciona el proveedor" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="smtp">
                                            SMTP tradicional
                                        </SelectItem>
                                        <SelectItem value="office365_oauth2">
                                            Office 365 OAuth2
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError class="mt-1" :message="form.errors.provider" />
                            </div>

                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="grid gap-2">
                                    <Label for="host">Servidor</Label>
                                    <Input id="host" v-model="form.host" type="text" placeholder="smtp.example.com" />
                                    <InputError class="mt-1" :message="form.errors.host" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="port">Puerto</Label>
                                    <Input id="port" v-model="form.port" type="number" min="1" max="65535" />
                                    <InputError class="mt-1" :message="form.errors.port" />
                                </div>
                            </div>

                            <div class="grid gap-2">
                                <Label for="encryption">Cifrado</Label>
                                <Select v-model="form.encryption">
                                    <SelectTrigger id="encryption" class="w-full">
                                        <SelectValue placeholder="Selecciona el cifrado" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="tls">TLS</SelectItem>
                                        <SelectItem value="ssl">SSL</SelectItem>
                                        <SelectItem value="none">Sin cifrado</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError class="mt-1" :message="form.errors.encryption" />
                            </div>

                            <template v-if="!isOffice365">
                                <div class="grid gap-4 md:grid-cols-2">
                                    <div class="grid gap-2">
                                        <Label for="username">Usuario</Label>
                                        <Input id="username" v-model="form.username" type="text" placeholder="usuario@dominio.com" />
                                        <InputError class="mt-1" :message="form.errors.username" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="password">Contrasena</Label>
                                        <Input id="password" v-model="form.password" type="password" autocomplete="new-password" placeholder="••••••••" />
                                        <p class="text-xs text-muted-foreground">Dejala vacia si no deseas actualizarla.</p>
                                        <InputError class="mt-1" :message="form.errors.password" />
                                    </div>
                                </div>
                            </template>

                            <template v-else>
                                <Alert>
                                    <ShieldCheck class="h-4 w-4" />
                                    <AlertTitle>Office 365 usa autenticacion OAuth2</AlertTitle>
                                    <AlertDescription>
                                        Aqui no se guarda la contrasena del buzon. Debes capturar Tenant ID, Client ID, Client Secret y el buzon autorizado.
                                    </AlertDescription>
                                </Alert>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <div class="grid gap-2">
                                        <Label for="oauth_tenant_id">Tenant ID</Label>
                                        <Input id="oauth_tenant_id" v-model="form.oauth_tenant_id" type="text" />
                                        <InputError class="mt-1" :message="form.errors.oauth_tenant_id" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="oauth_client_id">Client ID</Label>
                                        <Input id="oauth_client_id" v-model="form.oauth_client_id" type="text" />
                                        <InputError class="mt-1" :message="form.errors.oauth_client_id" />
                                    </div>
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <div class="grid gap-2">
                                        <Label for="oauth_client_secret">Client Secret</Label>
                                        <Input id="oauth_client_secret" v-model="form.oauth_client_secret" type="password" autocomplete="new-password" placeholder="••••••••" />
                                        <p class="text-xs text-muted-foreground">Dejalo vacio si no deseas actualizarlo.</p>
                                        <InputError class="mt-1" :message="form.errors.oauth_client_secret" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label for="oauth_mailbox">Buzon autenticador</Label>
                                        <Input id="oauth_mailbox" v-model="form.oauth_mailbox" type="email" placeholder="notificaciones@empresa.com" />
                                        <InputError class="mt-1" :message="form.errors.oauth_mailbox" />
                                    </div>
                                </div>
                            </template>

                            <Separator />

                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="grid gap-2">
                                    <Label for="from_name">Nombre remitente</Label>
                                    <Input id="from_name" v-model="form.from_name" type="text" placeholder="Caborca Automotriz" />
                                    <InputError class="mt-1" :message="form.errors.from_name" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="from_email">Correo remitente</Label>
                                    <Input id="from_email" v-model="form.from_email" type="email" placeholder="notificaciones@empresa.com" />
                                    <InputError class="mt-1" :message="form.errors.from_email" />
                                </div>
                            </div>

                            <div class="flex items-center justify-between rounded-lg border p-4">
                                <div>
                                    <p class="text-sm font-medium">Habilitar servidor para envios</p>
                                    <p class="text-xs text-muted-foreground">Si esta desactivado, el sistema no lo tomara como mailer activo.</p>
                                </div>

                                <Switch id="active" v-model="form.active" />
                            </div>

                            <div class="flex justify-end">
                                <Button type="submit" :disabled="form.processing">
                                    Guardar cambios
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>

                <div class="grid gap-6">
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Mail class="h-5 w-5" />
                                Estado actual
                            </CardTitle>
                            <CardDescription class="mt-2">
                                Resumen del proveedor configurado y recomendaciones de uso.
                            </CardDescription>
                        </CardHeader>

                        <CardContent class="grid gap-4">
                            <div class="flex flex-wrap gap-2">
                                <span :class="isActive ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'" class="rounded-full px-3 py-1 text-xs font-medium">
                                    {{ isActive ? 'Activo' : 'Deshabilitado' }}
                                </span>
                                <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700">
                                    {{ providerLabel }}
                                </span>
                            </div>

                            <div class="grid gap-3 text-sm">
                                <div class="rounded-lg border p-4">
                                    <p class="font-medium">Secretos cifrados</p>
                                    <p class="mt-1 text-muted-foreground">
                                        La contrasena SMTP y el Client Secret de Office 365 se almacenan cifrados en base de datos.
                                    </p>
                                </div>

                                <div class="rounded-lg border p-4">
                                    <p class="font-medium">Remitente consistente</p>
                                    <p class="mt-1 text-muted-foreground">
                                        El correo remitente debe coincidir con un buzon valido o con permisos de envio del proveedor configurado.
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Send class="h-5 w-5" />
                                Correo de prueba
                            </CardTitle>
                            <CardDescription class="mt-2">
                                Envia una prueba con la configuracion activa para validar conectividad y autenticacion.
                            </CardDescription>
                        </CardHeader>

                        <CardContent>
                            <form @submit.prevent="sendTest" class="grid gap-4">
                                <div class="grid gap-2">
                                    <Label for="test_email">Destino de prueba</Label>
                                    <Input id="test_email" v-model="testForm.test_email" type="email" placeholder="destino@dominio.com" />
                                    <InputError class="mt-1" :message="testForm.errors.test_email" />
                                </div>

                                <Button type="submit" variant="outline" :disabled="testForm.processing">
                                    Enviar prueba
                                </Button>
                            </form>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <Toaster richColors />
        </div>
    </UserLayout>
</template>
