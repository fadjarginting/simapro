<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { defineProps, ref, onMounted, computed, onBeforeUnmount } from "vue";

defineOptions({
    layout: AuthenticatedLayout,
});

const { props } = usePage();
const roles = computed(() => props.roles);




</script>

<template>

    <Head>
        <title>Roles</title>
    </Head>

    <template name="header"> </template>

    <!-- Flash Message: Jika ada pesan sukses -->
    <div v-if="$page.props.flash.status" class="p-4 mb-4 text-sm text-green-700 bg-green-100 rounded">
        {{ $page.props.flash.status }}
    </div>

    <!-- Flash Message: Jika ada pesan error -->
    <div v-if="$page.props.flash.error" class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded">
        {{ $page.props.flash.error }}
    </div>

    <div class="container mx-auto px-4 py-12">
        <div class="mx-auto max-w-full sm:px-6 lg:px-8">
            <!-- table docs -->
            <div class="flex flex-wrap -mx-3">
                <div class="flex-none w-full max-w-full px-3">
                    <div
                        class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl sm:rounded-lg bg-clip-border">
                        <div
                            class="flex items-center justify-between p-6 pb-0 mb-0 border-b-0 border-b-solid border-b-transparent">
                            <!-- Judul Tabel -->
                            <h6 class="dark:text-white text-base font-bold">
                                Roles Management
                            </h6>
                            <!-- Button Add Role -->
                            <div class="flex items-center justify-end">
                                <Link :href="route('roles.create')"
                                    class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-black-500 transition duration-300 hover:bg-gradient-to-tl hover:from-emerald-500 hover:to-teal-400 hover:text-white">
                                <i class="fas fa-plus mr-2 text-xs leading-none"></i>
                                <span>Add Role</span>
                                <i class="ni ni-folder-17 ml-2 leading-none"></i>
                                </Link>
                            </div>
                        </div>

                        <div class="flex-auto px-0 pt-0 pb-2">
                            <div class="p-0 overflow-x-auto">
                                <table class="w-full table-auto">
                                    <thead class="bg-gray-50">
                                        <tr class="text-sm font-normal text-gray-600 border-t border-b text-left">
                                            <th class="px-4 py-3">Role Name</th>
                                            <th class="px-4 py-3">Users</th>
                                            <th class="px-4 py-3">
                                                Permissions
                                            </th>
                                            <th class="px-4 py-3">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm font-normal text-gray-700">
                                        <tr v-for="role in roles" :key="role.id"
                                            class="border-b border-gray-200 hover:bg-gray-100">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <span class="font-medium">{{ role.name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-wrap gap-2">
                                                    <span
                                                        class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                                        {{ role.users.length }} Users
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex flex-wrap gap-2">
                                                    <span
                                                        class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                                        {{ role.permissions.length }} Permissions
                                                    </span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 space-x-2">
                                                <!-- Edit Button -->
                                                <div class="flex-col space-x-2">
                                                    <Link :href="route('roles.edit', role.id)"
                                                        class="bg-transparent px-1.4 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-blue-500 transition duration-300 hover:bg-gradient-to-tl hover:from-blue-500 hover:to-blue-600 hover:text-white hover:shadow-md">
                                                    <i class="fas fa-edit"></i>
                                                    Edit
                                                    </Link>
                                                    <!-- Delete Button -->
                                                    <button
                                                        class="bg-transparent px-1.4 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-red-500 transition duration-300 hover:bg-gradient-to-tl hover:from-red-500 hover:to-red-600 hover:text-white hover:shadow-md">
                                                        <i class="fas fa-trash"></i>
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
