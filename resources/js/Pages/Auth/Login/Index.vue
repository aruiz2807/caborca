<script setup>
import LoginForm from '@/Pages/Auth/Login/Form.vue'
import logo from '../../../../assets/images/caborca-logo.png'
import brands from '../../../../assets/images/brands.png'
</script>

<template>
    <Head title="Login" />
    <div class="relative min-h-svh overflow-hidden bg-slate-100">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(99,102,241,0.14),_transparent_58%)]" />
        <div class="mx-auto flex min-h-svh w-full max-w-6xl items-center px-6 py-10 md:px-10">
            <div class="grid w-full items-center gap-12 lg:grid-cols-[1.1fr_0.9fr]">
                <section class="order-2 lg:order-1">
                    <img class="w-full max-w-[440px]" :src="logo" alt="Caborca Automotriz" />
                    <h1 class="mt-8 text-balance text-4xl font-semibold tracking-tight text-slate-900 md:text-5xl">
                        Portal interno de operaciones
                    </h1>
                    <p class="mt-4 max-w-xl text-lg leading-relaxed text-slate-600">
                        Accede para gestionar citas, órdenes, mensajería y nuevos módulos administrativos en un solo lugar.
                    </p>
                    <div class="mt-12 hidden w-full max-w-xl rounded-2xl border border-slate-700/15 bg-slate-900 px-6 py-4 shadow-lg lg:block">
                        <img
                            class="w-full opacity-95 brightness-110 contrast-110"
                            :src="brands"
                            alt="Marcas Caborca Automotriz"
                        />
                    </div>
                </section>

                <section class="order-1 lg:order-2">
                    <LoginForm />
                </section>
            </div>
        </div>
    </div>
</template>
