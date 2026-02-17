<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
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

const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: 'PUT',
    name: props.user.name,
    email: props.user.email,
    photo: null,
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
        errorBag: 'updateProfileInformation',
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (!photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>{{ $t("pages.profile.info_title") }}</CardTitle>
            <CardDescription>{{ $t("pages.profile.info_description") }}</CardDescription>
        </CardHeader>

        <CardContent>
            <form @submit.prevent="updateProfileInformation" class="grid gap-4">

                <!-- Profile Photo -->
                <div v-if="$page.props.jetstream.managesProfilePhotos" class="grid gap-2">

                    <!-- Profile Photo File Input -->
                    <div class="grid gap-2">
                        <Label for="photo">
                            {{ $t("pages.profile.info_photo") }}
                        </Label>
                        <input id="photo" ref="photoInput" type="file" class="hidden" @change="updatePhotoPreview" />
                    </div>

                    <!-- Current Profile Photo -->
                    <div v-show="!photoPreview" class="mt-2">
                        <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full size-20 object-cover">
                    </div>

                    <!-- New Profile Photo Preview -->
                    <div v-show="photoPreview" class="mt-2">
                        <span class="block rounded-full size-20 bg-cover bg-no-repeat bg-center"
                            :style="'background-image: url(\'' + photoPreview + '\');'" />
                    </div>

                    <!-- New and Remove Buttons -->
                    <div class="flex justify-start gap-4">
                        <Button variant="outline" @click.prevent="selectNewPhoto">
                            {{ $t("pages.profile.info_photo_new") }}
                        </Button>

                        <Button v-if="user.profile_photo_path" variant="outline" @click.prevent="deletePhoto">
                            {{ $t("pages.profile.info_photo_remove") }}
                        </Button>
                    </div>

                    <InputError :message="form.errors.photo" class="mt-2" />
                </div>

                <div class="grid gap-4">

                    <!-- Name -->
                    <div class="w-full lg:w-1/2 grid gap-2">
                        <Label for="name">
                            {{ $t("app.name") }}
                        </Label>
                        <Input v-model="form.name" id="name" type="text" placeholder="Nombre del usuario" required />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <!-- Email -->
                    <div class="w-full lg:w-1/2 grid gap-2">
                        <Label for="email">
                            {{ $t("app.email") }}
                        </Label>
                        <Input v-model="form.email" id="email" type="email" placeholder="usuario@email.com" required />

                        <InputError class="mt-2" :message="form.errors.name" />

                        <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
                            <p class="text-sm mt-2 dark:text-white">
                                {{ $t("pages.profile.info_email_verification_status") }}

                                <Link :href="route('verification.send')" method="post" as="button"
                                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                                    @click.prevent="sendEmailVerification">
                                {{ $t("pages.profile.info_email_verification_send") }}
                                </Link>
                            </p>

                            <div v-show="verificationLinkSent"
                                class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                                {{ $t("pages.profile.info_email_verification_sent") }}
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="w-full lg:w-1/2 flex justify-end">
                        <Button type="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="w-32">
                            {{ $t("app.save") }}
                        </Button>
                    </div>
                </div>
            </form>
        </CardContent>
    </Card>
</template>
