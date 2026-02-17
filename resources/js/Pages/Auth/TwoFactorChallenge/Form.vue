<script setup>
import { nextTick, ref } from 'vue';
import { useForm } from '@inertiajs/vue3'
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

const form = useForm({
    code: '',
    recovery_code: '',
});

const recovery = ref(false);
const codeInput = ref(null);
const recoveryCodeInput = ref(null);

const toggleRecovery = async () => {
    recovery.value ^= true;

    await nextTick();

    if (recovery.value) {
        recoveryCodeInput.value.focus();
        form.code = '';
    } else {
        codeInput.value.focus();
        form.recovery_code = '';
    }
};

const submit = () => {
    form.post(route('two-factor.login'));
};
</script>

<template>
    <Card class="mx-auto max-w-sm">
        <CardHeader>
            <CardTitle class="text-xl">
                {{ $t("pages.auth.two_factor_authentication") }}
            </CardTitle>

            <CardDescription>
                <template v-if="! recovery">
                    {{ $t("pages.auth.two_factor_description") }}
                </template>

                <template v-else>
                    {{ $t('pages.auth.two_factor_description_recovery') }}
                </template>
            </CardDescription>
        </CardHeader>

        <CardContent>
            <form @submit.prevent="submit">
                <div v-if="! recovery">
                    <Label for="code">
                        {{ $t("app.code") }}
                    </Label>
                    <Input v-model="form.code" ref="codeInput" id="code" type="text" required />
                    <InputError class="mt-2" :message="form.errors.code" />
                </div>

                <div v-else>
                    <Label for="recovery_code">
                        {{ $t("pages.auth.two_factor_recovery_code") }}
                    </Label>
                    <Input v-model="form.recovery_code" ref="recoveryCodeInput" id="recovery_code" type="text" required />
                    <InputError class="mt-2" :message="form.errors.recovery_code" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <Button variant="link" @click.prevent="toggleRecovery">
                        <template v-if="! recovery">
                            {{ $t("pages.auth.two_factor_use_recovery_code") }}
                        </template>

                        <template v-else>
                            {{ $t("pages.auth.two_factor_use_code") }}
                        </template>
                    </Button>

                    <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        {{ $t("pages.auth.login_submit") }}
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>
</template>
