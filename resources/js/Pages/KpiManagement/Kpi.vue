<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
defineOptions({
    layout: AuthenticatedLayout,
});
</script>

<template>
    <div class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border rounded-2xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                    <i class="fas fa-chart-pie text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 tracking-tight">
                        Key Performance Indicator (KPI)
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Monitor and track key performance indicators for this work.
                    </p>
                </div>
            </div>
        </div>

        <!-- Content Body -->
        <div class="p-4">
            <div class="py-16 text-center">
                <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-blue-100 rounded-full flex items-center justify-center mx-auto mb-5 shadow-lg">
                    <i class="fas fa-tools text-blue-400 text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">
                    Fitur Segera Hadir
                </h3>
                <p class="text-base text-gray-500 mb-6 max-w-md mx-auto">
                    Halaman ini sedang dalam tahap pengembangan. Kami bekerja keras untuk menyelesaikannya dan akan segera tersedia untuk Anda.
                </p>
                <div class="inline-flex items-center gap-2 px-4 py-2 text-sm text-gray-500 bg-gray-100 rounded-lg shadow">
                    <i class="fas fa-hourglass-half"></i>
                    <span>Under Construction</span>
                </div>
            </div>
        </div>
    </div>
</template>