<script setup>
import SideBar from "@/Components/SideBar.vue";
import NavBar from "@/Components/NavBar.vue";


</script>

<template>
    <div class="absolute w-full bg-blue-500 dark:hidden min-h-75"></div>
    <SideBar />

    <!-- Page Content -->
    <main
        class="relative h-full max-h-screen transition-all duration-200 ease-in-out xl:ml-68 rounded-xl overflow-y-auto">
        <NavBar />
        <slot />
    </main>
</template>