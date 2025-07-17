<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import Swal from 'sweetalert2';
import { ref, watch } from "vue";

defineOptions({
    layout: AuthenticatedLayout,
});

const props = defineProps({
    roles: Object,
    filters: Object,
    errors: Object,
    flash: Object,
    success: String,
});

const page = usePage();

// Form state that syncs with URL parameters
const form = ref({
    search: props.filters.search || '',
    perPage: props.filters.perPage || 10,
    page: props.roles.current_page || 1,
});

// Debounce search to prevent too many requests
let timeout;
const performSearch = () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route('roles.index'), {
            search: form.value.search,
            perPage: form.value.perPage,
            page: 1, // Always reset to first page on new search
        }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
};

// Watch for changes in search and perPage to trigger search
watch(() => form.value.search, performSearch);
watch(() => form.value.perPage, () => {
    router.get(route('roles.index'), {
        search: form.value.search,
        perPage: form.value.perPage,
        page: 1, // Reset to first page when changing items per page
    }, {
        preserveState: true,
        replace: true,
    });
});

// Handle page change
const changePage = (url) => {
    if (!url) return;

    router.visit(url, {
        preserveState: true,
        replace: true,
    });
};

// Handle delete role with sweetalert2 confirmation
const deleteRole = (roleId, roleName) => {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus role "${roleName}"? Tindakan ini tidak dapat dibatalkan.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#9CA3AF',
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-lg shadow-lg',
            title: 'text-lg font-semibold text-gray-800',
            htmlContainer: 'text-sm text-gray-600',
            confirmButton: 'px-4 py-2 rounded-md text-white bg-red-500 hover:bg-red-600',
            cancelButton: 'px-4 py-2 rounded-md text-gray-700 bg-gray-200 hover:bg-gray-300'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('roles.destroy', roleId), {
                onSuccess: () => {
                    if (page.props.flash.error) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: page.props.flash.error,
                            showConfirmButton: false,
                            timer: 4000,
                            timerProgressBar: true,
                        });
                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Role berhasil dihapus.',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                        });
                    }
                },
                onError: () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Gagal menghapus role.',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true,
                    });
                }
            });
        }
    });
};
</script>

<template>

    <Head title="Roles Management" />

    <div class="py-12">
        <div class=" mx-auto ">
            <div class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border rounded-2xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                                <i class="fas fa-user-shield text-white text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 tracking-tight">
                                    Manajemen Role
                                </h2>
                                <p class="text-sm text-gray-500">
                                    Kelola role, izin, dan pengguna.
                                </p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <Link :href="route('roles.create')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-sm font-semibold rounded-md hover:from-blue-700 hover:to-purple-700 shadow transition">
                                <i class="fas fa-plus text-xs"></i>
                                Tambah Role
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Content Body -->
                <div class="p-4 space-y-4">
                    <!-- Filters and search -->
                    <div class="bg-white border rounded-lg p-3 shadow-sm">
                        <InputLabel for="search" value="Cari Role" class="mb-1 text-sm font-medium" />
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <TextInput v-model="form.search" type="text" placeholder="Cari berdasarkan nama role..."
                                class="w-full pl-10" />
                            <button v-if="form.search" @click="form.search = ''"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-hidden border rounded-lg shadow-sm bg-white">
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead class="bg-gradient-to-r from-gray-50 to-blue-50 border-b">
                                    <tr class="text-sm font-semibold text-blue-900 text-left">
                                        <th class="px-4 py-3">Nama Role</th>
                                        <th class="px-4 py-3">Pengguna</th>
                                        <th class="px-4 py-3">Izin</th>
                                        <th class="px-4 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm text-gray-700 divide-y">
                                    <tr v-for="role in roles.data" :key="role.id"
                                        class="hover:bg-blue-50 transition">
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ role.name }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full shadow-sm bg-green-100 text-green-800">
                                                {{ role.users.length }} Pengguna
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full shadow-sm bg-blue-100 text-blue-800">
                                                {{ role.permissions.length }} Izin
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <Link :href="route('roles.edit', role.id)"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 shadow-sm transition">
                                                    <i class="fas fa-edit"></i>
                                                    <span>Edit</span>
                                                </Link>
                                                <button @click="deleteRole(role.id, role.name)"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-white bg-red-600 rounded-md hover:bg-red-700 shadow-sm transition">
                                                    <i class="fas fa-trash"></i>
                                                    <span>Hapus</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="roles.data.length === 0">
                                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <i class="fas fa-folder-open text-4xl text-gray-300 mb-2"></i>
                                                <span class="font-medium">Tidak ada role ditemukan</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-between items-center p-3 bg-white border rounded-lg shadow-sm">
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-700">Tampilkan</span>
                            <select v-model="form.perPage"
                                class="px-2 py-1 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <span class="text-sm text-gray-700">baris</span>
                        </div>
                        <div class="flex items-center space-x-1">
                            <template v-for="(link, i) in roles.links" :key="i">
                                <button @click="changePage(link.url)" :disabled="!link.url"
                                    class="px-3 py-1.5 text-xs font-medium rounded-md min-w-[32px] text-center transition shadow-sm"
                                    :class="{
                                        'bg-blue-600 text-white hover:bg-blue-700': link.active,
                                        'bg-white text-gray-700 border hover:bg-blue-50': !link.active && link.url,
                                        'bg-gray-100 text-gray-400 cursor-not-allowed': !link.url,
                                    }">
                                    <span v-html="link.label"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
