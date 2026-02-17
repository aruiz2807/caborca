<script setup>
import LoginForm from '@/Pages/Auth/Login/Form.vue'
import logo from '../../../../assets/images/caborca-logo.png'
</script>

<template>
    <Head title="Login" />
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-muted p-6 md:p-10">
        <div class="flex w-full max-w-sm flex-col gap-10">
            <img class="w-full" :src="logo" />
            <LoginForm />
        </div>
    </div>
</template>
