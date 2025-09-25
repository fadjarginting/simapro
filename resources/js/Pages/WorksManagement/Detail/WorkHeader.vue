<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    work: Object,
});
</script>

<template>
    <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100 rounded-2xl mb-2">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow"
                >
                    <i class="fas fa-briefcase text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 tracking-tight sm:text-2xl">
                        Detail Pekerjaan
                    </h2>
                    <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:space-x-4">
                        <div class="mt-1 flex items-center text-xs text-gray-500">
                            <i class="fas fa-hashtag mr-1.5 text-gray-400"></i>
                            <span>Nomor ERF: {{ work.erf_number || "-" }}</span>
                        </div>
                        <div class="mt-1 flex items-center text-xs text-gray-500">
                            <i class="fas fa-industry mr-1.5 text-gray-400"></i>
                            <span>Plant: {{ work.plant?.name || "-" }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>