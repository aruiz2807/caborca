<script setup>
import { inject } from "vue"
import { useForm, usePage } from '@inertiajs/vue3';
import { Card, CardContent } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import InputError from '@/Components/InputError.vue'

const page = usePage();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
    role: '',
    type: 'A',
    bpro_user: '',
});

let openDialog = inject('openDialogState')

const submit = () => {
    form.post(route('users.store'), {
        onFinish: () => {
            form.reset('password', 'password_confirmation')
        },
        onSuccess: () => {
            openDialog.value = false
            form.reset('password', 'password_confirmation')
        }
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="grid gap-4">
        <div class="grid gap-2">
            <Label for="name">
                {{ $t("app.name") }}
            </Label>
            <Input v-model="form.name" id="name" type="text" placeholder="Nombre del usuario" required />
            <InputError class="mt-2" :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="email">
                {{ $t("app.email") }}
            </Label>
            <Input v-model="form.email" id="email" type="email" placeholder="usuario@email.com" required />
            <InputError class="mt-2" :message="form.errors.email" />
        </div>

        <div class="grid gap-2">
            <Label for="password">
                {{ $t("app.password") }}
            </Label>
            <Input v-model="form.password" id="password" type="password" required />
            <InputError class="mt-2" :message="form.errors.password" />
        </div>

        <div class="grid gap-2">
            <Label for="password_confirmation">
                {{ $t("app.password_confirm") }}
            </Label>
            <Input v-model="form.password_confirmation" id="password_confirmation" type="password" required />
            <InputError class="mt-2" :message="form.errors.password_confirmation" />
        </div>

        <div class="grid gap-2">
            <Label for="role">
                {{ $t("app.role") }}
            </Label>
            <Select v-model="form.role">
                <SelectTrigger>
                    <SelectValue placeholder="Selecciona un rol" />
                </SelectTrigger>
                <SelectContent>
                    <SelectGroup>
                        <SelectItem v-for="role in page.props.roles" :key="role.id" :value="role.name">
                            {{ role.name }}
                        </SelectItem>
                    </SelectGroup>
                </SelectContent>
            </Select>
            <InputError class="mt-2" :message="form.errors.role" />
        </div>

        <div class="w-full mt-2">
            <Label for="type">
                {{ $t("app.type") }}
            </Label>
            <Tabs v-model="form.type">
                <TabsList class="w-full mt-2">
                    <TabsTrigger class="w-full" value="A">{{ $t("app.advisor") }}</TabsTrigger>
                    <TabsTrigger class="w-full" value="G">{{ $t("app.government") }}</TabsTrigger>
                </TabsList>

                <TabsContent value="A">
                    <Card>
                        <CardContent>
                            <div class="grid gap-2 pt-4">
                                <Label for="bpro_user">
                                    {{ $t("app.bpro_user") }}
                                </Label>
                                <Input v-model="form.bpro_user" id="bpro_user" type="text" maxlength="5" placeholder="Usuario del sistema BPro" />
                                <InputError class="mt-2" :message="form.errors.bpro_user" />
                            </div>
                        </CardContent>
                    </Card>
                </TabsContent>

                <TabsContent value="G">
                    <Card>
                        <CardContent class="pt-6">
                            <p class="text-sm text-muted-foreground">Los usuarios gubernamentales no requieren un usuario de BPro.</p>
                        </CardContent>
                    </Card>
                </TabsContent>
            </Tabs>
            <InputError class="mt-2" :message="form.errors.type" />
        </div>

        <Button type="submit" class="mt-2" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            {{ $t("app.save") }}
        </Button>
    </form>
</template>
