<script setup>
import { Head, Link } from '@inertiajs/vue3';
import background from '../../assets/images/background.png'
import illustration from '../../assets/images/engine-oil.png'
import logo from '../../assets/images/caborca-logo.png'
import brands from '../../assets/images/brands.png'
import { Button } from '@/Components/ui/button'

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>

    <Head title="Welcome" />

    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <img id="background" class="fixed inset-0 w-screen h-screen object-cover" :src="background" />

        <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#202020] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-1 items-center gap-2">
                    <a href="#" class="flex items-center gap-2 self-center font-medium text-black">
                        <div class="flex h-21 w-21 items-center justify-center rounded-md pt-6">
                            <img class="z-10 w-full" :src="logo" />
                        </div>
                    </a>
                </header>

                <main class="mt-6 pt-20">
                    <!-- Hero Section Start -->
                    <div class="container mx-auto px-5 overflow-x-hidden lg:overflow-x-visible">
                        <section class="grid grid-cols-none lg:grid-cols-2 pb-16 pt-8 items-center">
                            <div class="lg:w-5/6 order-2 lg:order-none">
                                <h1 class="text-4xl xl:text-5xl font-bold font-theme-heading text-center lg:text-left">
                                    Citas de servicio para dependencias gubernamentales
                                </h1>
                                <p
                                    class="lg:text-xl mt-10 font-theme-content text-theme-grayish-blue text-center lg:text-left">
                                    Plataforma todo en uno para la creación de solicitudes y seguimiento de citas de servicio para las dependencias de gobierno
                                </p>
                                <div class="flex justify-center lg:justify-start mt-10">
                                    <Link :href="route('login')"
                                        class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                                        <Button class="py-5 px-10 text-lg font-bold">Ingresar</Button>
                                    </Link>
                                </div>
                            </div>
                            <div class="relative order-1 lg:order-none mb-20 lg:mt-0 lg:mb-0 lg:-mr-10">
                                <img class="z-10 w-full" :src="illustration" />
                                <div
                                    class="-z-10 bg-theme-primary h-52 w-full sm:h-80 sm:w-full rounded-l-full absolute -right-28 md:-right-48 -bottom-8">
                                </div>
                            </div>
                        </section>
                    </div>
                    <!-- Hero Section End -->
                </main>

                <footer class="py-6 pt-16 text-center text-sm text-black dark:text-white/70">
                    <div class="flex h-21 w-21 items-center justify-center rounded-md">
                        <img class="z-10 w-full" :src="brands" />
                    </div>
                </footer>
            </div>
        </div>
    </div>
</template>
