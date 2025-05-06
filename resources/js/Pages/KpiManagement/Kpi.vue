<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";

defineOptions({
    layout: AuthenticatedLayout,
});

// Data for progress items
const progressItems = ref([
    {
        title: "Proytebasian FEED - DED Design & Engineering",
        period: "01 January 25 - 05 May 25",
        percentage: 18.75,
        target: 85.00
    },
    {
        title: "Proytebasian Kajian Tabula Design & Engineering",
        period: "01 January 25 - 05 May 25",
        percentage: 0,
        target: 85.00
    },
    {
        title: "Proytebasian Technical Assist",
        period: "01 January 25 - 05 May 25",
        percentage: 33.33,
        target: 85.00
    }
]);
</script>

<template>
    <Head title="Monitoring Progress" />

    <template name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Morning Report
        </h2>
    </template>

    <div class="container mx-auto px-4 py-12">
        <div class="mx-auto max-w-full sm:px-6 lg:px-8">
            <!-- Progress Section -->
            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="flex-none w-full max-w-full px-3">
                    <div class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl sm:rounded-lg bg-clip-border">
                        <!-- Main Title -->
                        <div class="p-6 pb-2 mb-0 border-b-0 border-b-solid border-b-transparent">
                            <h1 class="text-2xl font-bold text-gray-900 uppercase">
                                SUNWASHES PERMANENT WITH ELDERESTER
                            </h1>
                        </div>
                        
                        <!-- Subtitle -->
                        <div class="p-6 pt-0 pb-2 mb-0 border-b-0 border-b-solid border-b-transparent">
                            <h2 class="text-lg font-semibold text-gray-700 uppercase">
                                Key Monitoring Unit Site Environmental Services | Tarun 2025
                            </h2>
                        </div>
                        
                        <!-- Progress Items -->
                        <div class="p-6">
                            <div class="space-y-8">
                                <div v-for="(item, index) in progressItems" :key="index" class="border-b border-gray-200 pb-6 last:border-b-0">
                                    <div class="flex justify-between items-start">
                                        <div class="w-2/3">
                                            <p class="font-bold text-gray-800">% {{ item.title }}</p>
                                            <p class="text-sm text-gray-600">Period {{ item.period }}</p>
                                        </div>
                                        <div class="w-1/3 flex flex-col items-end">
                                            <p class="font-bold text-gray-800">Target {{ item.target.toFixed(2) }}%</p>
                                            <div class="relative w-24 h-12 mt-2">
                                                <!-- Background semi-circle -->
                                                <svg class="w-full h-full" viewBox="0 0 100 50">
                                                    <path 
                                                        d="M 10,50 A 40,40 0 0 1 90,50" 
                                                        fill="none" 
                                                        stroke="#e5e7eb" 
                                                        stroke-width="10"
                                                    />
                                                    <!-- Progress semi-circle -->
                                                    <path 
                                                        class="transition-all duration-500"
                                                        :class="{
                                                            'stroke-red-500': item.percentage === 0,
                                                            'stroke-yellow-500': item.percentage > 0 && item.percentage < 30,
                                                            'stroke-green-500': item.percentage >= 30
                                                        }"
                                                        :d="`M 10,50 A 40,40 0 0 1 ${10 + (80 * (Math.min(item.percentage, 100)/100))},${50 - (40 * Math.sin(Math.PI * Math.min(item.percentage, 100)/100))}`" 
                                                        fill="none" 
                                                        stroke="currentColor"
                                                        stroke-width="10"
                                                        stroke-linecap="round"
                                                    />
                                                </svg>
                                                <!-- Percentage text -->
                                                <div class="absolute inset-0 flex items-center justify-center">
                                                    <span 
                                                        class="text-lg font-bold"
                                                        :class="{
                                                            'text-red-500': item.percentage === 0,
                                                            'text-yellow-500': item.percentage > 0 && item.percentage < 30,
                                                            'text-green-500': item.percentage >= 30
                                                        }"
                                                    >
                                                        {{ item.percentage.toFixed(2) }}%
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>