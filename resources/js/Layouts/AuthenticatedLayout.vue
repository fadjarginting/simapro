<script setup>
import SideBar from "@/Components/SideBar.vue";
import NavBar from "@/Components/NavBar.vue";
import { onMounted, ref } from "vue";
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
    <div class="absolute w-full min-h-75 bg-gradient-to-br from-blue-500 to-purple-500 dark:hidden"></div>

    <SideBar />
    <!-- Page Content -->
    <main ref="mainElement"
        class="relative h-full max-h-screen transition-all duration-200 ease-in-out xl:ml-68 rounded-2xl overflow-y-auto shadow-lg">
        <div class="py-4 px-6">
            <NavBar />
            <slot />
        </div>
    </main>
</template>