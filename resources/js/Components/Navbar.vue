<template>
    <nav class="relative z-10 flex items-center justify-between px-6 py-4 mx-auto max-w-7xl">
        <!-- Logo di tengah untuk mode mobile -->
        <div class="logo">
            <ApplicationLogo></ApplicationLogo>
        </div>

        <!-- Ikon Hamburger untuk mode mobile di pojok kanan -->
        <div class="block md:hidden">
            <button @click="toggleSidebar" class="focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Menu navigasi tersembunyi di mode mobile, ditampilkan di desktop -->
        <div class="items-center hidden space-x-6 text-center md:flex nav-menu">
            <NavLink href="">About</NavLink>
            <NavLink href="">Contact</NavLink>
            <NavLink href="">Privacy</NavLink>
        </div>

        <div class="hidden md:block w-14 placeholder"></div>
    </nav>

    <!-- Sidebar Mobile -->
    <div :class="[
        'fixed inset-y-0 right-0 z-50 w-64 bg-white shadow-xl transform transition-transform duration-300 ease-in-out',
        isSidebarOpen ? 'translate-x-0' : 'translate-x-full'
    ]">
        <div class="flex flex-col h-full">
            <div class="flex p-5">
                <ApplicationLogo></ApplicationLogo>
            </div>
            <!-- Tombol Tutup -->
            <button @click="toggleSidebar" class="absolute top-4 right-4 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <!-- Menu Sidebar -->
            <div class="flex flex-col flex-grow px-4 mt-10 space-y-4">
                <NavLink href="" class="block py-2">About</NavLink>
                <NavLink href="" class="block py-2">Contact</NavLink>
                <NavLink href="" class="block py-2">Privacy</NavLink>
            </div>

            <!-- Footer Sidebar dalam bentuk row di bagian bawah -->
            <div class="p-4 bg-gray-100">
                <div class="flex flex-col items-center justify-between gap-4">
                    <div class="flex items-center justify-center gap-5 socmed">
                        <img :src="'/storage/icon/blue_facebook.svg'" alt="Facebook" class="w-5 h-5">
                        <img :src="'/storage/icon/blue_twitter.svg'" alt="Twitter" class="w-5 h-5">
                        <img :src="'/storage/icon/blue_instagram.svg'" alt="Instagram" class="w-5 h-5">
                        <img :src="'/storage/icon/blue_youtube.svg'" alt="YouTube" class="w-5 h-5">
                    </div>
                    <p class="text-sm text-center text-[#0063F2]">Copyright© 2025 Loremipsum | <br> All Rights Reserved
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import NavLink from '@/Components/NavLink.vue'

const isSidebarOpen = ref(false)

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value
}
</script>
