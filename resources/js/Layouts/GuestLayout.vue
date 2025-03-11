<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <main class="min-h-screen mt-0 transition-all duration-200 ease-in-out bg-blue-100">
        <section>
            <div
                class="relative flex items-center p-0 overflow-hidden bg-center bg-cover"
            >
                <div class="container z-10">
                    <div class="flex flex-wrap mt-0 -mx-3">
                        <slot />
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>
