<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { defineProps, ref, onMounted, onBeforeUnmount } from "vue";

defineOptions({
    layout: AuthenticatedLayout,
});
</script>

<template>
    <Head title="Work Audit Trail" />
    
    <div class="container mx-auto px-4 py-12">
        <div class="mx-auto max-w-full sm:px-6 lg:px-8">
            <!-- table docs -->
            <div class="flex flex-wrap -mx-3">
                <div class="flex-none w-full max-w-full px-3">
                    <div
                        class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl sm:rounded-lg bg-clip-border"
                    >
                        <div
                            class="flex items-center justify-between p-6 pb-0 mb-0 border-b-0 border-b-solid border-b-transparent"
                        >
                            <!-- Judul Tabel -->
                            <h6 class="text-2xl font-bold text-gray-900">
                                Work Audit Trail
                            </h6>
                        </div>

                        <!-- Tabel -->
                        
                        <div class="flex-auto px-0 pt-0 pb-2">
                            <div class="p-0 overflow-x-auto">
                                <!-- Log Table -->
                                <div
                                    class="bg-white rounded-lg shadow overflow-hidden"
                                >
                                    <table class="w-full table-auto">
                                        <thead class="bg-gray-50">
                                            <tr
                                                class="text-sm font-normal text-gray-600 border-t border-b text-left"
                                            >
                                                <th class="px-4 py-3">
                                                    Action Date
                                                </th>
                                                <th class="px-4 py-3">Job Title</th>
                                                <th class="px-4 py-3">
                                                    Operation
                                                </th>
                                                <th class="px-4 py-3">By Whom</th>
                                                <th class="px-4 py-3">
                                                    To Whom User
                                                </th>
                                                <th class="px-4 py-3">
                                                    IP Address
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody
                                            class="text-sm font-normal text-gray-700"
                                        >
                                            <!-- Sample Data -->
                                            <tr
                                                class="border-b border-gray-200 hover:bg-gray-100"
                                            >
                                                <td class="px-6 py-4">
                                                    <div class="text-sm">
                                                        2024-03-15
                                                    </div>
                                                    <div
                                                        class="text-xs text-gray-500"
                                                    >
                                                        14:32:05
                                                    </div>
                                                </td>
                                                
                                                <td class="px-6 py-4">
                                                    <div
                                                        class="text-sm font-medium"
                                                    >
                                                        Laporan-Tahunan-2023.pdf
                                                    </div>
                                                    <div
                                                        class="text-xs text-gray-500"
                                                    >
                                                        Size: 2.4 MB
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span
                                                        class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full"
                                                        >Upload</span
                                                    >
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div
                                                        class="flex items-center"
                                                    >
                                                        <div
                                                            class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center"
                                                        >
                                                            <span
                                                                class="text-xs font-medium"
                                                                >JD</span
                                                            >
                                                        </div>
                                                        <div class="ml-3">
                                                            <div
                                                                class="text-sm"
                                                            >
                                                                John Doe
                                                            </div>
                                                            <div
                                                                class="text-xs text-gray-500"
                                                            >
                                                                Admin
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    -
                                                </td>
                                                <td class="px-6 py-4 text-sm">
                                                    192.168.1.1
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Pagination -->
                                    <div
                                        class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200"
                                    >
                                        <div
                                            class="flex-1 flex justify-between sm:hidden"
                                        >
                                            <a
                                                href="#"
                                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                            >
                                                Previous
                                            </a>
                                            <a
                                                href="#"
                                                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                            >
                                                Next
                                            </a>
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
