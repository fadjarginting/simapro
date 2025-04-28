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
    breadcrumbs: Array
});

// Form state that syncs with URL parameters
const form = ref({
    search: props.filters.search || '',
    role: props.filters.role || null,
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

    <template name=header>
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Users Management
        </h2>
    </template>

    <div class="container mx-auto px-4 py-12">
        <div class="mx-auto max-w-full sm:px-6 lg:px-8">
            <div class="flex flex-wrap -mx-3">
                <div class="flex-none w-full max-w-full px-3">
                    <div
                        class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl sm:rounded-lg bg-clip-border">
                        <div
                            class="flex items-center justify-between p-6 pb-0 mb-0 border-b-0 border-b-solid border-b-transparent">
                            <h6 class="dark:text-white text-base font-bold">
                                Users Management
                            </h6>
                            <div class="flex items-center justify-end">
                                <Link :href="route('users.create')"
                                    class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-green-500 transition duration-300 hover:bg-gradient-to-tl hover:from-green-500 hover:to-teal-400 hover:text-white">
                                <i class="fas fa-plus mr-2 text-xs leading-none"></i>
                                <span>Add User</span>
                                <i class="ni ni-folder-17 ml-2 leading-none"></i>
                                </Link>
                            </div>
                        </div>

                        <!-- Filters and search -->
                        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <InputLabel for="search" value="Search" />
                                <div class="relative">
                                    <TextInput v-model="form.search" type="text" placeholder="Search by Name or Email"
                                        class="w-full" />
                                    <span v-if="form.search" @click="form.search = null"
                                        class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white text-red-300 hover:text-gray-700 focus:outline-none mr-2">
                                        <i class="fas fa-times"></i>
                                    </span>
                                </div>
                            </div>

                            <div>
                                <InputLabel for="role" value="Role" />
                                <div class="relative">
                                    <select v-model="form.role"
                                        class="focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding p-3 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none">
                                        <option :value="null">All Roles</option>
                                        <option v-for="role in roles" :key="role.id" :value="role.name">
                                            {{ role.name }}
                                        </option>
                                    </select>
                                    <i
                                        class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs pointer-events-none mr-1"></i>
                                    <span v-if="form.role" @click="form.role = null"
                                        class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white text-red-300 hover:text-gray-700 focus:outline-none mr-2">
                                        <i class="fas fa-times"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex-auto px-0 pt-0 pb-2">
                            <div class="p-0 overflow-x-auto">
                                <table class="w-full table-auto">
                                    <thead class="bg-gray-100">
                                        <tr class="text-sm font-normal text-gray-600 border-t border-b text-left">
                                            <th class="px-4 py-3 cursor-pointer" @click="toggleSort('name')">
                                                Name
                                                <span v-if="sortKey === 'name'" class="ml-1">
                                                    {{ sortOrder === 'asc' ? '▲' : '▼' }}
                                                </span>
                                            </th>
                                            <th class="px-4 py-3 cursor-pointer" @click="toggleSort('email')">
                                                Email
                                                <span v-if="sortKey === 'email'" class="ml-1">
                                                    {{ sortOrder === 'asc' ? '▲' : '▼' }}
                                                </span>
                                            </th>
                                            <th class="px-4 py-3">Role</th>

                                            <th class="px-4 py-3 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm font-normal text-gray-700">
                                        <tr v-for="user in users.data" :key="user.id"
                                            class="border-b border-gray-200 hover:bg-gray-100">
                                            <td class="px-4 py-3">{{ user.name }}</td>
                                            <td class="px-4 py-3">{{ user.email }}</td>
                                            <td class="px-4 py-3">
                                                {{user.roles.map(role => role.name).join(", ")}}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <Link :href="route('users.edit', user.id)"
                                                    class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-blue-500 transition duration-300 hover:bg-gradient-to-tl hover:from-blue-500 hover:to-blue-400 hover:text-white">
                                                <i class="fas fa-edit mr-2 text-xs leading-none"></i>
                                                <span>Edit</span>
                                                </Link>
                                                <button @click="deleteUser(user.id)"
                                                    class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-red-500 transition duration-300 hover:bg-gradient-to-tl hover:from-red-500 hover:to-red-400 hover:text-white">
                                                    <i class="fas fa-trash mr-2 text-xs leading-none"></i>
                                                    <span>Delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr v-if="users.data.length === 0">
                                            <td colspan="4" class="px-4 py-3 text-center text-gray-500">
                                                No users found
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <div class="flex justify-between items-center p-4 bg-gray-50">
                                <div class="flex items-center">
                                    <span class="mr-2 text-sm text-gray-700">Show</span>
                                    <select v-model="form.perPage"
                                        class="px-2 py-1 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="10">10</option>
                                        <option value="15">15</option>
                                        <option value="20">20</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                    </select>
                                    <span class="ml-2 text-sm text-gray-700">rows</span>
                                </div>
                                <!-- Container with horizontal scroll -->
                                <div class=" overflow-x-auto bg-gray-50 flex justify-between items-center p-4">
                                    <!-- Inner flex dengan lebar minimal sesuai konten -->
                                    <div class="min-w-max flex items-center space-x-1">
                                        <template v-for="(link, i) in users.links" :key="i">
                                            <template
                                                v-if="link.label !== '&laquo; Previous' && link.label !== 'Next &raquo;'">
                                                <button @click="changePage(link.url)" :disabled="!link.url"
                                                    class="px-2 py-1 text-xs rounded-md min-w-[28px] text-center"
                                                    :class="{
                                                        'bg-blue-500 text-white': link.active,
                                                        'bg-gray-50 text-gray-700 hover:bg-blue-500': !link.active && link.url,
                                                        'bg-gray-50 text-gray-400': !link.url && link.label === '...',
                                                    }">
                                                    <span v-html="link.label"></span>
                                                </button>
                                            </template>
                                        </template>
                                    </div>
                                </div>
                                <!-- total user -->
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-700">Showing
                                        {{ users.from }} to {{ users.to }} of {{ users.total }} User
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>