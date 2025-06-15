<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <main
        class="elative h-full max-h-screen transition-all duration-200 ease-in-out  rounded-xl overflow-y-auto">
        <section class="min-h-screen">
            <div class="relative flex items-center justify-center p-4 min-h-screen bg-gray-50">
                <div class="container z-10 max-w-7xl mx-auto">
                    <div class="flex flex-wrap justify-center -mx-3">
                        <slot />
                    </div>
                </div>
            </div>
        </section>
    </main>
</template>