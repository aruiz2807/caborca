<script setup>
import { computed, watch } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import { Alert, AlertDescription, AlertTitle } from '@/Components/ui/alert'
import { Button } from '@/Components/ui/button'
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card'
import { Checkbox } from '@/Components/ui/checkbox'
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
import InputError from '@/Components/InputError.vue'
import UserLayout from '@Layouts/UserLayout.vue'
import { CircleAlert, FolderTree, Save, Shield, Trash2 } from 'lucide-vue-next'
import { Toaster, toast } from 'vue-sonner'
import 'vue-sonner/style.css'

const props = defineProps({
    sections: {
        type: Array,
        default: () => [],
    },
    roles: {
        type: Array,
        default: () => [],
    },
    users: {
        type: Array,
        default: () => [],
    },
})

const page = usePage()

const sectionForm = useForm({
    id: null,
    name: '',
})

const reportForm = useForm({
    id: null,
    bi_section_id: '',
    name: '',
    embed_url: '',
    role_ids: [],
    user_ids: [],
})

const sectionOptions = computed(() => props.sections ?? [])

watch(() => page.props.flash?.message, (message) => {
    if (message === 'bi-section-stored') {
        toast.success('Seccion guardada correctamente.', { duration: 5000 })
    }

    if (message === 'bi-section-deleted') {
        toast.warning('Seccion eliminada con sus paneles BI.', { duration: 5000 })
    }

    if (message === 'bi-report-stored') {
        toast.success('Panel BI guardado correctamente.', { duration: 5000 })
    }

    if (message === 'bi-report-deleted') {
        toast.warning('Panel BI eliminado.', { duration: 5000 })
    }
}, { immediate: true })

const resetSectionForm = () => {
    sectionForm.reset()
    sectionForm.clearErrors()
    sectionForm.id = null
}

const editSection = (section) => {
    sectionForm.id = section.id
    sectionForm.name = section.name
}

const submitSection = () => {
    if (sectionForm.id) {
        sectionForm.put(route('bi-settings.sections.update', sectionForm.id), {
            preserveScroll: true,
            onSuccess: () => resetSectionForm(),
        })
        return
    }

    sectionForm.post(route('bi-settings.sections.store'), {
        preserveScroll: true,
        onSuccess: () => resetSectionForm(),
    })
}

const destroySection = (section) => {
    const confirmed = window.confirm(
        `Si eliminas la seccion "${section.name}" tambien se eliminaran todos sus paneles BI y permisos asociados. Esta accion no se puede deshacer.`
    )

    if (!confirmed) {
        return
    }

    router.delete(route('bi-settings.sections.destroy', section.id), {
        preserveScroll: true,
    })
}

const resetReportForm = () => {
    reportForm.reset()
    reportForm.clearErrors()
    reportForm.id = null
    reportForm.bi_section_id = ''
    reportForm.role_ids = []
    reportForm.user_ids = []
}

const editReport = (report) => {
    reportForm.id = report.id
    reportForm.bi_section_id = report.bi_section_id.toString()
    reportForm.name = report.name
    reportForm.embed_url = report.embed_url
    reportForm.role_ids = [...(report.role_ids ?? [])]
    reportForm.user_ids = [...(report.user_ids ?? [])]
}

const toggleSelection = (fieldName, id) => {
    const current = Array.isArray(reportForm[fieldName]) ? [...reportForm[fieldName]] : []
    const index = current.indexOf(id)

    if (index === -1) {
        current.push(id)
    } else {
        current.splice(index, 1)
    }

    reportForm[fieldName] = current
}

const submitReport = () => {
    const payload = {
        ...reportForm.data(),
        bi_section_id: reportForm.bi_section_id ? Number(reportForm.bi_section_id) : null,
    }

    if (reportForm.id) {
        reportForm.transform(() => payload).put(route('bi-settings.reports.update', reportForm.id), {
            preserveScroll: true,
            onSuccess: () => resetReportForm(),
        })
        return
    }

    reportForm.transform(() => payload).post(route('bi-settings.reports.store'), {
        preserveScroll: true,
        onSuccess: () => resetReportForm(),
    })
}

const destroyReport = (report) => {
    const confirmed = window.confirm(
        `Se eliminara el panel "${report.name}" y su permiso asociado. Esta accion no se puede deshacer.`
    )

    if (!confirmed) {
        return
    }

    router.delete(route('bi-settings.reports.destroy', report.id), {
        preserveScroll: true,
    })
}
</script>

<template>
    <UserLayout tabTitle="BI" appName="Settings">
        <div class="max-w-7xl mx-auto overflow-x-hidden py-10 sm:px-6 lg:px-8">
            <div class="grid gap-6 xl:grid-cols-[minmax(0,0.95fr)_minmax(0,1.05fr)]">
                <Card class="min-w-0">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <FolderTree class="h-5 w-5" />
                            Secciones BI
                        </CardTitle>
                        <CardDescription class="mt-2">
                            Administra las secciones que agrupan reportes en el menu <span class="font-medium">Reportes</span>.
                        </CardDescription>
                    </CardHeader>

                    <CardContent class="space-y-4">
                        <form class="grid gap-3" @submit.prevent="submitSection">
                            <div class="grid gap-2">
                                <Label for="section-name">Nombre de seccion</Label>
                                <Input id="section-name" v-model="sectionForm.name" type="text" placeholder="Postventa" />
                                <InputError :message="sectionForm.errors.name" />
                            </div>

                            <div class="flex items-center justify-end gap-2">
                                <Button type="button" variant="secondary" @click="resetSectionForm">
                                    Limpiar
                                </Button>
                                <Button type="submit" :disabled="sectionForm.processing" class="gap-2">
                                    <Save class="h-4 w-4" />
                                    {{ sectionForm.id ? 'Actualizar seccion' : 'Crear seccion' }}
                                </Button>
                            </div>
                        </form>

                        <Separator />

                        <div class="max-h-[420px] space-y-3 overflow-y-auto pr-1">
                            <div v-if="!sections.length" class="rounded-lg border border-dashed p-4 text-sm text-muted-foreground">
                                Todavia no hay secciones BI registradas.
                            </div>

                            <div v-for="section in sections" :key="section.id" class="rounded-lg border p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate font-medium text-foreground">{{ section.name }}</p>
                                        <p class="truncate text-xs text-muted-foreground">Slug: {{ section.slug }}</p>
                                        <p class="truncate text-xs text-muted-foreground">Permiso: {{ section.permission_name }}</p>
                                        <p class="mt-2 text-xs text-muted-foreground">
                                            {{ section.reports.length }} panel(es) registrado(s)
                                        </p>
                                    </div>

                                    <div class="flex shrink-0 items-center gap-2">
                                        <Button type="button" size="sm" variant="secondary" @click="editSection(section)">
                                            Editar
                                        </Button>
                                        <Button type="button" size="sm" variant="destructive" @click="destroySection(section)">
                                            <Trash2 class="mr-1 h-4 w-4" />
                                            Eliminar
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div class="grid min-w-0 gap-6">
                    <Card class="min-w-0">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Shield class="h-5 w-5" />
                                Paneles BI y accesos
                            </CardTitle>
                            <CardDescription class="mt-2">
                                Registra la URL del panel BI y define que roles/usuarios podran verlo.
                            </CardDescription>
                        </CardHeader>

                        <CardContent>
                            <form class="grid gap-4" @submit.prevent="submitReport">
                                <div class="grid gap-2">
                                    <Label for="report-section">Seccion</Label>
                                    <Select v-model="reportForm.bi_section_id">
                                        <SelectTrigger id="report-section" class="w-full">
                                            <SelectValue placeholder="Selecciona una seccion" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem
                                                v-for="section in sectionOptions"
                                                :key="section.id"
                                                :value="section.id.toString()"
                                            >
                                                {{ section.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                    <InputError :message="reportForm.errors.bi_section_id" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="report-name">Nombre del BI</Label>
                                    <Input id="report-name" v-model="reportForm.name" type="text" placeholder="Ordenes" />
                                    <InputError :message="reportForm.errors.name" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="report-url">URL del panel BI</Label>
                                    <Input
                                        id="report-url"
                                        v-model="reportForm.embed_url"
                                        type="url"
                                        placeholder="https://app.powerbi.com/view?r=..."
                                    />
                                    <InputError :message="reportForm.errors.embed_url" />
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <div class="space-y-2">
                                        <p class="text-sm font-medium">Roles asignados</p>
                                        <div class="max-h-40 space-y-2 overflow-y-auto rounded-md border p-3">
                                            <div v-if="!roles.length" class="text-sm text-muted-foreground">
                                                No hay roles disponibles.
                                            </div>
                                            <div v-for="role in roles" :key="role.id" class="flex items-center gap-2">
                                                <Checkbox
                                                    :id="`role-${role.id}`"
                                                    :model-value="reportForm.role_ids.includes(role.id)"
                                                    @update:modelValue="toggleSelection('role_ids', role.id)"
                                                />
                                                <Label :for="`role-${role.id}`" class="font-normal">
                                                    {{ role.name }}
                                                </Label>
                                            </div>
                                        </div>
                                        <InputError :message="reportForm.errors.role_ids" />
                                    </div>

                                    <div class="space-y-2">
                                        <p class="text-sm font-medium">Usuarios asignados</p>
                                        <div class="max-h-40 space-y-2 overflow-y-auto rounded-md border p-3">
                                            <div v-if="!users.length" class="text-sm text-muted-foreground">
                                                No hay usuarios disponibles.
                                            </div>
                                            <div v-for="user in users" :key="user.id" class="flex items-start gap-2">
                                                <Checkbox
                                                    :id="`user-${user.id}`"
                                                    :model-value="reportForm.user_ids.includes(user.id)"
                                                    @update:modelValue="toggleSelection('user_ids', user.id)"
                                                />
                                                <Label :for="`user-${user.id}`" class="font-normal leading-tight">
                                                    <span class="block">{{ user.name }}</span>
                                                    <span class="break-all text-xs text-muted-foreground">{{ user.email }}</span>
                                                </Label>
                                            </div>
                                        </div>
                                        <InputError :message="reportForm.errors.user_ids" />
                                    </div>
                                </div>

                                <div class="flex items-center justify-end gap-2">
                                    <Button type="button" variant="secondary" @click="resetReportForm">
                                        Limpiar
                                    </Button>
                                    <Button type="submit" :disabled="reportForm.processing" class="gap-2">
                                        <Save class="h-4 w-4" />
                                        {{ reportForm.id ? 'Actualizar BI' : 'Crear BI' }}
                                    </Button>
                                </div>
                            </form>
                        </CardContent>
                    </Card>

                    <Card class="min-w-0">
                        <CardHeader>
                            <CardTitle>Paneles registrados</CardTitle>
                            <CardDescription>
                                Cada panel genera un permiso unico y se muestra en el menu <span class="font-medium">Reportes</span> segun asignaciones.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div v-if="!sections.length" class="rounded-lg border border-dashed p-4 text-sm text-muted-foreground">
                                Crea primero una seccion para comenzar.
                            </div>

                            <div v-for="section in sections" :key="`reports-${section.id}`" class="mb-4 min-w-0 rounded-lg border p-4">
                                <div class="mb-3">
                                    <p class="font-medium text-foreground">{{ section.name }}</p>
                                    <p class="break-all text-xs text-muted-foreground">Permiso: {{ section.permission_name }}</p>
                                </div>

                                <div v-if="!section.reports.length" class="text-sm text-muted-foreground">
                                    Esta seccion aun no tiene paneles.
                                </div>

                                <div v-for="report in section.reports" :key="report.id" class="mb-3 min-w-0 rounded-md border bg-muted/20 p-3 last:mb-0">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <p class="font-medium text-foreground">{{ report.name }}</p>
                                            <p class="break-all text-xs text-muted-foreground">{{ report.embed_url }}</p>
                                            <p class="mt-1 break-all text-xs text-muted-foreground">Permiso: {{ report.permission_name }}</p>
                                            <p class="text-xs text-muted-foreground">
                                                Roles: {{ report.role_ids.length }} | Usuarios: {{ report.user_ids.length }}
                                            </p>
                                        </div>

                                        <div class="flex shrink-0 items-center gap-2">
                                            <Button type="button" size="sm" variant="secondary" @click="editReport(report)">
                                                Editar
                                            </Button>
                                            <Button type="button" size="sm" variant="destructive" @click="destroyReport(report)">
                                                <Trash2 class="mr-1 h-4 w-4" />
                                                Eliminar
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <Alert>
                        <CircleAlert class="h-4 w-4" />
                        <AlertTitle>Borrado en cascada</AlertTitle>
                        <AlertDescription>
                            Si eliminas una seccion, tambien se eliminaran todos sus paneles BI y los permisos asociados.
                        </AlertDescription>
                    </Alert>
                </div>
            </div>
        </div>

        <Toaster rich-colors position="top-right" />
    </UserLayout>
</template>
