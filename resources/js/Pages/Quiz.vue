<script setup>
import { Head } from '@inertiajs/vue3';
import MultipleForms from '@/Components/MultipleForms.vue';
import Navbar from '@/Components/Navbar.vue';
</script>


<template>

    <Head title="Quiz" />
    <div class="min-h-screen">
        <Navbar></Navbar>
        <div class="pt-20">
            <MultipleForms></MultipleForms>
        </div>
    </div>
</template>
