<script setup>
import { Head } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
</script>
<template>
    <GuestLayout>
        <Head title="Skin Analysis"></Head>
        <div class="z-10 pt-8 pb-20 space-y-8 text-center">
            <h1 class="lg:text-[80px] text-[50px] lg:w-full w-4/5 mx-auto font-bold">Let’s do the magic</h1>
            <PrimaryButton href="/quiz">Analyze Your Skin</PrimaryButton>
            <p class="md:text-[1.3em] text-[1.2em] pt-5 font-normal">Accuracy level up to 80% - 98%</p>
            <p class="md:text-[1.3em] text-[1.5em]"><b>Say Goodbye to...</b></p>
            <div class="flex flex-wrap items-center justify-center gap-16">
                <div class="flex flex-col">
                    <img :src="'/storage/assets/image_1.png'" alt="">
                    <p>Bingung Pilih Skincare Yang Cocok</p>
                </div>
                <div class="flex-col hidden lg:flex">
                    <img :src="'/storage/assets/image_2.png'" alt="">
                    <p>Trial & Error Skincare</p>
                </div>
                <div class="flex-col hidden lg:flex">
                    <img :src="'/storage/assets/image_3.png'" alt="">
                    <p>Korban Mafia Skincare</p>
                </div>
            </div>
            <div class="flex items-center justify-center lg:hidden">
                <div class="flex flex-col">
                    <img :src="'/storage/assets/image_2.png'" alt="">
                    <p>Trial & Error Skincare</p>
                </div>
                <div class="flex flex-col">
                    <img :src="'/storage/assets/image_3.png'" alt="">
                    <p>Korban Mafia Skincare</p>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
