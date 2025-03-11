<script setup>
import { ref } from "vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { Link } from "@inertiajs/vue3";
import SideBar from "@/Components/SideBar.vue";
import NavBar from "@/Components/NavBar.vue";
</script>

<template>
    <div class="absolute w-full bg-blue-500 dark:hidden min-h-75"></div>
    <SideBar />
    <!-- Page Content -->
    <main
        class="relative h-full max-h-screen transition-all duration-200 ease-in-out xl:ml-68 rounded-xl"
    >
        <NavBar />
        <header>
            <slot name="header"></slot>
        </header>
        <slot />
    </main>
</template>
