<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import FooterVue from '@/Components/Footer.vue';
</script>

<template>
    <div class="relative min-h-screen bg-gray-100">
        <Navbar></Navbar>
        <main class="relative z-10 mx-auto max-w-7xl">
            <slot />
        </main>
        <div class="absolute top-0 left-0 z-0">
            <img :src="'/storage/backgrounds/left_top.png'" alt="">
        </div>
        <div class="absolute bottom-0 right-0 z-0">
            <img :src="'/storage/backgrounds/frame.png'" alt="">
        </div>
    </div>
    <FooterVue></FooterVue>
</template>
