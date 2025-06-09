<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    work: Object,
});
</script>

<template>
    <div class="sm:flex sm:items-center sm:justify-between mb-6 lg:mb-8">
        <div class="min-w-0 flex-1">
            <h2 class="text-xl font-bold leading-7 text-gray-900 sm:text-2xl lg:text-3xl sm:tracking-tight">
                Detail Pekerjaan
            </h2>
            <div class="mt-1 flex flex-col sm:mt-2 sm:flex-row sm:flex-wrap sm:space-x-6">
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <span>Nomor ERF: {{ work.erf_number || "-" }}</span>
                </div>
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <span>Plant: {{ work.plant.name }}</span>
                </div>
            </div>
        </div>
        <div class="mt-4 flex flex-wrap gap-2 sm:ml-4 sm:mt-0">
            <Link
                :href="route('works.index')"
                class="bg-gray-200 px-3 py-2 text-xs rounded inline-block whitespace-nowrap text-center font-bold leading-none text-gray-700 transition duration-300 hover:bg-gray-300"
            >
                <i class="fas fa-arrow-left mr-2 text-xs leading-none"></i>
                <span>Kembali</span>
            </Link>
        </div>
    </div>
</template>