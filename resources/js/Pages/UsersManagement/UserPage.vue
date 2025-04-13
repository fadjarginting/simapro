<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import FilterSelect from "@/Components/FilterSelect.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import { Head, Link, useForm, usePage, useRemember } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";

defineOptions({
    layout: AuthenticatedLayout,
});

const { props } = usePage();
const users = computed(() => props.users);
const roles = computed(() => props.roles);

const searchQuery = ref("");
const selectedRole = ref("");

const filteredUsers = computed(() => {
    return users.value.filter(user => {
        const matchesRole = selectedRole.value
            ? user.roles.some(role => role.name === selectedRole.value)
            : true;

        const matchesSearch = user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            user.email.toLowerCase().includes(searchQuery.value.toLowerCase());

        return matchesRole && matchesSearch;
    });
});

const currentPage = ref(1);
const itemsPerPage = ref(10);

const paginatedUsers = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage.value;
    const end = start + itemsPerPage.value;
    return filteredUsers.value.slice(start, end);
});

const deleteForm = useForm();

const deleteUsers = (id) => {
    if (confirm("Are you sure you want to delete this user?")) {
        deleteForm.delete(route('users.destroy', id), {
            onSuccess: () => {
                alert("User deleted successfully");
            },
        });
    }
};

const totalPages = computed(() => {
    return Math.ceil(filteredUsers.value.length / itemsPerPage.value);
});

watch([searchQuery, selectedRole, itemsPerPage], () => {
    currentPage.value = 1;
});

function prevPage() {
    if (currentPage.value > 1) currentPage.value--;
}

function nextPage() {
    if (currentPage.value < totalPages.value) currentPage.value++;
}
</script>

<template>
    <Head title="User Management" />

    <template name="header">
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
                                    <TextInput v-model="searchQuery" type="text" placeholder="Search by Name or Email"
                                        class="w-full" />
                                </div>
                            </div>

                            <div>
                                <InputLabel for="role" value="Role" />
                                <div class="relative">
                                    <select v-model="selectedRole"
                                        class="focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 ease block w-full appearance-none rounded-lg border border-solid border-gray-300 bg-white bg-clip-padding p-3 font-normal text-gray-700 outline-none transition-all placeholder:text-gray-500 focus:border-fuchsia-300 focus:outline-none">
                                        <option value="">All Roles</option>
                                        <option v-for="role in roles" :key="role.id" :value="role.name">
                                            {{ role.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex-auto px-0 pt-0 pb-2">
                            <div class="p-0 overflow-x-auto">
                                <table class="w-full table-auto">
                                    <thead class="bg-gray-100">
                                        <tr class="text-sm font-normal text-gray-600 border-t border-b text-left">
                                            <th class="px-4 py-3">Name</th>
                                            <th class="px-4 py-3">Email</th>
                                            <th class="px-4 py-3">Role</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm font-normal text-gray-700">
                                        <tr v-for="user in paginatedUsers" :key="user.id"
                                            class="border-b border-gray-200 hover:bg-gray-100">
                                            <td class="px-4 py-3">{{ user.name }}</td>
                                            <td class="px-4 py-3">{{ user.email }}</td>
                                            <td class="px-4 py-3">
                                                {{ user.roles.map(role => role.name).join(", ") }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <span v-if="user.status === 'active'"
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                                <span v-else
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                    Offline
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <Link :href="route('users.edit', user.id)"
                                                    class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-blue-500 transition duration-300 hover:bg-gradient-to-tl hover:from-blue-500 hover:to-blue-400 hover:text-white">
                                                    <i class="fas fa-edit mr-2 text-xs leading-none"></i>
                                                    <span>Edit</span>
                                                </Link>
                                                <button @click="deleteUsers(user.id)"
                                                    class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-red-500 transition duration-300 hover:bg-gradient-to-tl hover:from-red-500 hover:to-red-400 hover:text-white">
                                                    <i class="fas fa-trash mr-2 text-xs leading-none"></i>
                                                    <span>Delete</span>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="flex justify-between items-center p-4 bg-gray-50">
                                <div class="flex items-center">
                                    <span class="mr-2 text-sm text-gray-700">Show</span>
                                    <select v-model="itemsPerPage"
                                        class="px-2 py-1 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="10">10</option>
                                        <option value="15">15</option>
                                        <option value="20">20</option>
                                        <option value="25">25</option>
                                    </select>
                                    <span class="ml-2 text-sm text-gray-700">rows</span>
                                </div>
                                <div>
                                    <button @click="prevPage" :disabled="currentPage === 1"
                                        class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-blue-500 transition duration-300 hover:bg-gradient-to-tl hover:from-blue-500 hover:to-blue-400 hover:text-white disabled:opacity-50">
                                        Previous
                                    </button>
                                    <span class="text-sm text-gray-700">
                                        Page {{ currentPage }} of {{ totalPages }} | Showing {{
                                            paginatedUsers.length }} of {{ filteredUsers.length }} users
                                    </span>
                                    <button @click="nextPage" :disabled="currentPage === totalPages"
                                        class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-blue-500 transition duration-300 hover:bg-gradient-to-tl hover:from-blue-500 hover:to-blue-400 hover:text-white disabled:opacity-50">
                                        Next
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
