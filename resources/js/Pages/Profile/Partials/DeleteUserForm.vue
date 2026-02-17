<script setup>
import { useForm } from '@inertiajs/vue3';
import { Button } from '@/Components/ui/button'
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/Components/ui/card'
import ConfirmsPassword from '@/Components/ConfirmsPassword.vue';

const form = useForm();

const deleteUser = () => {
    form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>{{ $t("pages.profile.delete_account_title") }}</CardTitle>
            <CardDescription>{{ $t("pages.profile.delete_account_description") }}</CardDescription>
        </CardHeader>

        <CardContent>
            <div class="mt-3 w-full lg:w-1/2 text-sm text-gray-600 dark:text-gray-400">
                <p>
                    {{ $t("pages.profile.delete_account_instructions") }}
                </p>
            </div>

            <div class="w-full lg:w-1/2 flex justify-end mt-5">
                <ConfirmsPassword @confirmed="deleteUser">
                    <Button type="button" variant="destructive" class="w-32">
                        {{ $t("pages.profile.delete_account_title") }}
                    </Button>
                </ConfirmsPassword>
            </div>
        </CardContent>
    </Card>
</template>

<!--
<script>

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
};

const deleteUser = () => {
    form.delete(route('current-user.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};

</script>

    <ActionSection>
        <template #title>
            Delete Account
        </template>

        <template #description>
            Permanently delete your account.
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
                Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.
            </div>

            <div class="mt-5">
                <DangerButton @click="confirmUserDeletion">
                    Delete Account
                </DangerButton>
            </div>


            <DialogModal :show="confirmingUserDeletion" @close="closeModal">
                <template #title>
                    Delete Account
                </template>

                <template #content>
                    Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.

                    <div class="mt-4">
                        <TextInput
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-3/4"
                            placeholder="Password"
                            autocomplete="current-password"
                            @keyup.enter="deleteUser"
                        />

                        <InputError :message="form.errors.password" class="mt-2" />
                    </div>
                </template>

                <template #footer>
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>

                    <DangerButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                    >
                        Delete Account
                    </DangerButton>
                </template>
            </DialogModal>
        </template>
    </ActionSection>
-->
