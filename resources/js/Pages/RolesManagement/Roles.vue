<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { defineProps, ref, onMounted, onBeforeUnmount } from "vue";

defineOptions({
    layout: AuthenticatedLayout,
});
</script>

<template>
    <Head>
        <title>Download Request List</title>
    </Head>

    <template name="header"> </template>

    <div class="py-12">
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
                                Roles Management
                            </h6>
                        </div>

                        <!-- Button Add Docs -->
                        <div class="flex items-center justify-end">
                            <Link
                                :href="route('erfs.create')"
                                class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-black-500 transition duration-300 hover:bg-gradient-to-tl hover:from-emerald-500 hover:to-teal-400 hover:text-white"
                            >
                                <i
                                    class="fas fa-plus mr-2 text-xs leading-none"
                                ></i>
                                <span>add role</span>
                                <i
                                    class="ni ni-folder-17 ml-2 leading-none"
                                ></i>
                            </Link>
                        </div>

                        <div class="flex-auto px-0 pt-0 pb-2">
                            <div class="p-0 overflow-x-auto">
                                <table class="w-full table-auto">
                                    <thead class="bg-gray-50">
                                        <tr
                                            class="text-sm font-normal text-gray-600 border-t border-b text-left"
                                        >
                                            <th class="px-4 py-3">Nama Role</th>
                                            <th class="px-4 py-3">
                                                Jumlah User
                                            </th>
                                            <th class="px-4 py-3">
                                                Permissions
                                            </th>
                                            <th class="px-4 py-3">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="text-sm font-normal text-gray-700"
                                    >
                                        <tr
                                            class="border-b border-gray-200 hover:bg-gray-100"
                                        >
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <span class="font-medium"
                                                        >Administrator</span
                                                    >
                                                    <span
                                                        class="ml-2 text-xs text-gray-500"
                                                        >Super User</span
                                                    >
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">5 Users</td>
                                            <td class="px-6 py-4">
                                                <div
                                                    class="flex flex-wrap gap-2"
                                                >
                                                    <span
                                                        class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full"
                                                        >*</span
                                                    >
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 space-x-2">
                                                <!-- Edit Button -->
                                                <div class="flex-col space-x-2">
                                                    <button
                                                        class="bg-transparent px-1.4 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-blue-500 transition duration-300 hover:bg-gradient-to-tl hover:from-blue-500 hover:to-blue-600 hover:text-white hover:shadow-md"
                                                    >
                                                        <i
                                                            class="fas fa-edit"
                                                        ></i>
                                                        Edit
                                                    </button>
                                                    <!-- Delete Button -->
                                                    <button
                                                        class="bg-transparent px-1.4 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-red-500 transition duration-300 hover:bg-gradient-to-tl hover:from-red-500 hover:to-red-600 hover:text-white hover:shadow-md"
                                                    >
                                                        <i
                                                            class="fas fa-trash"
                                                        ></i>
                                                        Delete
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
