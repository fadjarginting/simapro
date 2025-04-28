<script setup>
import SideBar from "@/Components/SideBar.vue";
import NavBar from "@/Components/NavBar.vue";
import { onMounted, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";

const mainElement = ref(null);

onMounted(() => {
    // Inisialisasi listener untuk event navigasi Inertia
    router.on('navigate', () => {
        if (mainElement.value) {
            mainElement.value.scrollTop = 0;
        }
    });
});
</script>

<template>
    <div class="absolute w-full bg-blue-500 dark:hidden min-h-75"></div>
    
    <SideBar />
    <!-- Page Content -->
    <main ref="mainElement"
    class="relative h-full max-h-screen transition-all duration-200 ease-in-out xl:ml-68 rounded-xl overflow-y-auto">
    <NavBar />
    <slot />
    </main>
</template>