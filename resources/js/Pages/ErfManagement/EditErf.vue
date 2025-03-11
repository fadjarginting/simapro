<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
defineOptions({
    layout: AuthenticatedLayout,
});
</script>

<template>
    <Head title="Edit ERF" />

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">Edit ERF</div>
            </div>
        </div>
    </div>
</template>
