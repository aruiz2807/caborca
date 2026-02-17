<script setup>
import RegisterForm from '@/Pages/Auth/Register/Form.vue'
import logo from '../../../../assets/images/caborca-logo.png'
</script>

<template>
    <Head title="Register" />
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-muted p-6 md:p-10">
        <div class="flex w-full max-w-sm flex-col gap-6">
            <img class="w-full" :src="logo" />
            <RegisterForm />
        </div>
    </div>
</template>
