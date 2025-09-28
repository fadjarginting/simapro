<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

defineOptions({
    layout: AuthenticatedLayout,
});

const props = defineProps({
    users: Object,
    filters: Object,
    roles: Array,
    disciplines: Array,
    errors: Object,
    breadcrumbs: Array
});

// Form state that syncs with URL parameters
const form = ref({
    search: props.filters.search || '',
    role: props.filters.role || null,
    discipline_id: props.filters.discipline_id || null,
    page: props.users.current_page || 1,
    perPage: props.filters.perPage || 10
});

// Debounce search to prevent too many requests
let timeout;
const performSearch = () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route('users.index'), {
            search: form.value.search,
            role: form.value.role,
            perPage: form.value.perPage,
            sort: sortKey.value,
            direction: sortOrder.value,
            page: 1 // Always reset to first page on new search
        }, {
            preserveState: true,
            replace: true
        });
    }, 300);
};

// Watch for changes in search and role to trigger search
watch(() => form.value.search, performSearch);
watch(() => form.value.role, performSearch);
watch(() => form.value.perPage, () => {
    router.get(route('users.index'), {
        search: form.value.search,
        role: form.value.role,
        perPage: form.value.perPage,
        sort: sortKey.value,
        direction: sortOrder.value,
        page: 1 // Reset to first page when changing items per page
    }, {
        preserveState: true,
        replace: true
    });
});

// Handle page change
const changePage = (url) => {
    if (!url) return;

    router.visit(url, {
        preserveState: true,
        replace: true
    });
};

// Sort functionality
const sortKey = ref(props.filters.sort || "name");
const sortOrder = ref(props.filters.direction || "asc");

const toggleSort = (key) => {
    if (sortKey.value === key) {
        sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
    } else {
        sortKey.value = key;
        sortOrder.value = "asc";
    }

    router.get(route('users.index'), {
        search: form.value.search,
        role: form.value.role,
        perPage: form.value.perPage,
        sort: sortKey.value,
        direction: sortOrder.value,
        page: 1 // Reset to first page when sorting
    }, {
        preserveState: true,
        replace: true
    });
};

// Delete user with sweet alert confirmation
import Swal from 'sweetalert2';

const deleteUser = (userId) => {
    Swal.fire({
        title: 'Confirm Deletion',
        text: "Are you sure you want to delete this user? This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#9CA3AF',
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'rounded-lg shadow-lg',
            title: 'text-lg font-semibold text-gray-800',
            htmlContainer: 'text-sm text-gray-600',
            confirmButton: 'px-4 py-2 rounded-md text-white bg-red-500 hover:bg-red-600',
            cancelButton: 'px-4 py-2 rounded-md text-gray-700 bg-gray-200 hover:bg-gray-300'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('users.destroy', userId), {
                preserveState: true,
                replace: true
            });
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'The user has been successfully deleted.',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'rounded-lg shadow-lg',
                    title: 'text-sm font-semibold text-gray-800',
                }
            });
        }
    });
};


</script>

<template>

    <Head title="User Management" />

    <div class="py-6">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border rounded-2xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100">
                    <div class="flex flex-wrap justify-between items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                                <i class="fas fa-users text-white text-lg"></i>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold text-gray-900 tracking-tight">
                                    Users Management
                                </h1>
                                <p class="text-sm text-gray-600">
                                    Manage all registered users in the system.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <Link :href="route('users.create')"
                                class="inline-flex items-center gap-2 bg-blue-500 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:bg-blue-600 transition duration-300 text-sm">
                            <i class="fas fa-plus"></i>
                            <span>Add User</span>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="p-6 space-y-6">
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <!-- Filters and search -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <InputLabel for="search" value="Search"
                                    class="text-sm font-medium text-gray-700 mb-1" />
                                <div class="relative">
                                    <TextInput v-model="form.search" type="text" placeholder="Search by Name or Email"
                                        class="w-full text-sm" />
                                    <span v-if="form.search" @click="form.search = ''"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer">
                                        <i class="fas fa-times"></i>
                                    </span>
                                </div>
                            </div>

                            <div>
                                <InputLabel for="role" value="Role" class="text-sm font-medium text-gray-700 mb-1" />
                                <div class="relative">
                                    <select v-model="form.role"
                                        class="focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding p-3 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none">
                                        <option :value="null">All Roles</option>
                                        <option v-for="role in roles" :key="role.id" :value="role.name">
                                            {{ role.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="p-3 text-left font-semibold text-gray-600 cursor-pointer"
                                            @click="toggleSort('name')">
                                            Name
                                            <span v-if="sortKey === 'name'" class="ml-1">
                                                {{ sortOrder === 'asc' ? '▲' : '▼' }}
                                            </span>
                                        </th>
                                        <th class="p-3 text-left font-semibold text-gray-600 cursor-pointer"
                                            @click="toggleSort('email')">
                                            Email
                                            <span v-if="sortKey === 'email'" class="ml-1">
                                                {{ sortOrder === 'asc' ? '▲' : '▼' }}
                                            </span>
                                        </th>
                                        <th class="p-3 text-left font-semibold text-gray-600 cursor-pointer"
                                            @click="toggleSort('discipline')">
                                            Discipline
                                            <span v-if="sortKey === 'discipline'" class="ml-1">
                                                {{ sortOrder === 'asc' ? '▲' : '▼' }}
                                            </span>
                                        </th>
                                        <th class="p-3 text-left font-semibold text-gray-600">Role</th>
                                        <th class="p-3 text-center font-semibold text-gray-600">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                                        <td class="p-3">{{ user.name }}</td>
                                        <td class="p-3">{{ user.email }}</td>
                                        <td class="p-3">
                                            {{ user.discipline ? user.discipline.name : 'N/A' }}
                                        </td>
                                        <td class="p-3">
                                            {{user.roles.map(role => role.name).join(", ")}}
                                        </td>
                                        <td class="p-3 text-center whitespace-nowrap">
                                            <Link :href="route('users.edit', user.id)"
                                                class="text-blue-600 hover:text-blue-800 mr-3" title="Edit">
                                            <i class="fas fa-edit"></i>
                                            </Link>
                                            <button @click="deleteUser(user.id)" class="text-red-600 hover:text-red-800"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr v-if="users.data.length === 0">
                                        <td colspan="5" class="p-3 text-center text-gray-500">
                                            No users found.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="flex flex-wrap justify-between items-center p-4 bg-white border-t mt-4 gap-4">
                            <div class="flex items-center">
                                <span class="mr-2 text-sm text-gray-700">Show</span>
                                <select v-model="form.perPage"
                                    class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                    <option value="20">20</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                                <span class="ml-2 text-sm text-gray-700">rows</span>
                            </div>

                            <div class="flex items-center space-x-1">
                                <template v-for="(link, i) in users.links" :key="i">
                                    <button @click="changePage(link.url)" :disabled="!link.url"
                                        class="px-3 py-1 text-sm rounded-md min-w-[32px] text-center transition" :class="{
                                            'bg-blue-500 text-white': link.active,
                                            'bg-white text-gray-700 hover:bg-gray-100 border': !link.active && link.url,
                                            'bg-white text-gray-400 cursor-default border': !link.url,
                                        }" v-html="link.label">
                                    </button>
                                </template>
                            </div>

                            <div class="text-sm text-gray-700">
                                Showing {{ users.from }} to {{ users.to }} of {{ users.total }} results
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
