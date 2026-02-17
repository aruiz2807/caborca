<script setup>
import { inject } from "vue"
import { useForm } from '@inertiajs/vue3';
import { Card, CardContent } from '@/Components/ui/card'
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { Label } from '@/Components/ui/label'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/Components/ui/tabs'
import InputError from '@/Components/InputError.vue'

const props = defineProps({
    record: Object,
});

let openDialog = inject('openDialogState')

const form = useForm({
    name: props.record.name,
    email: props.record.email,
    type: props.record.type,
    bpro_user: props.record.bpro_user,
});

const activeTab = form.type;

const submit = () => {
    form.put(route('users.update', { user: props.record.id }), {
        onSuccess: () => {
            openDialog.value = false
        }
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="grid gap-4">
        <div class="grid gap-2">
            <Label for="email">
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

        <div class="w-full">
            <Label for="type">
                {{ $t("app.type") }}
            </Label>
            <Tabs v-model="activeTab">
                <TabsList class="w-full">
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
                        <CardContent>

                        </CardContent>
                    </Card>
                </TabsContent>
            </Tabs>
        </div>

        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            {{ $t("app.save") }}
        </Button>
    </form>
</template>
