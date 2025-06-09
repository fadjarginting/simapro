<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { ref, watch, computed, nextTick } from "vue";
import Swal from "sweetalert2";

defineOptions({
    layout: AuthenticatedLayout,
});

// Get data from backend props
const { props } = usePage();

// Form state yang sync dengan URL parameters - sama seperti Users Management
const form = ref({
    search: props.filters?.search || '',
    plant_id: props.filters?.plant_id || '',
    work_priority: props.filters?.work_priority || '',
    work_type: props.filters?.work_type || '',
    request_category: props.filters?.request_category || '',
    verification_status: props.filters?.verification_status || '',
    project_status: props.filters?.project_status || '',
    current_phase: props.filters?.current_phase || '',
    start_date: props.filters?.start_date || '',
    end_date: props.filters?.end_date || '',
    target_start_date: props.filters?.target_start_date || '',
    target_end_date: props.filters?.target_end_date || '',
    sort_by: props.filters?.sort_by || 'erf_number',
    sort_order: props.filters?.sort_order || 'desc',
    per_page: props.filters?.per_page || 10,
    page: props.works?.current_page || 1
});

// Filter modal state
const showFilterModal = ref(false);
const showDateFilter = ref(false);

// Loading state untuk mencegah multiple requests
const isLoading = ref(false);

// Debounce search function - sama seperti Users Management
let timeout;
const performSearch = () => {
    if (isLoading.value) return;

    clearTimeout(timeout);
    timeout = setTimeout(() => {
        applyFilters();
    }, 300); // Kurangi delay untuk response yang lebih cepat
};

// Watch for changes in search to trigger debounced search - sama seperti Users Management
watch(() => form.value.search, performSearch);

// Watch for other filter changes
watch(() => form.value.plant_id, () => {
    if (!isLoading.value) applyFilters();
});

watch(() => form.value.work_priority, () => {
    if (!isLoading.value) applyFilters();
});

watch(() => form.value.work_type, () => {
    if (!isLoading.value) applyFilters();
});

watch(() => form.value.request_category, () => {
    if (!isLoading.value) applyFilters();
});

watch(() => form.value.verification_status, () => {
    if (!isLoading.value) applyFilters();
});

watch(() => form.value.project_status, () => {
    if (!isLoading.value) applyFilters();
});

watch(() => form.value.current_phase, () => {
    if (!isLoading.value) applyFilters();
});

// Watch for per_page changes
watch(() => form.value.per_page, () => {
    if (!isLoading.value) {
        form.value.page = 1; // Reset to first page
        applyFilters();
    }
});

// Apply filters function - diperbaiki untuk preserveState
function applyFilters() {
    if (isLoading.value) return;

    isLoading.value = true;

    const filterData = {};

    // Only include non-empty filters
    Object.keys(form.value).forEach(key => {
        if (form.value[key] !== '' && form.value[key] !== null && form.value[key] !== undefined) {
            filterData[key] = form.value[key];
        }
    });

    router.get(route('works.index'), filterData, {
        preserveState: true,   // Ini yang penting untuk menjaga state input
        preserveScroll: true,
        replace: true,
        only: ['works', 'statistics'], // Hanya update data yang diperlukan
        onStart: () => {
            isLoading.value = true;
        },
        onFinish: () => {
            isLoading.value = false;
        },
        onError: (errors) => {
            console.error('Filter error:', errors);
            isLoading.value = false;
        }
    });
}

// Handle page change - sama seperti Users Management
const changePage = (url) => {
    if (!url || isLoading.value) return;

    router.visit(url, {
        preserveState: true,
        replace: true
    });
};

// Toggle methods for filter buttons
function togglePriority(priority) {
    if (form.value.work_priority === priority) {
        form.value.work_priority = '';
    } else {
        form.value.work_priority = priority;
    }
}

function toggleWorkType(type) {
    if (form.value.work_type === type) {
        form.value.work_type = '';
    } else {
        form.value.work_type = type;
    }
}

function toggleProjectStatus(status) {
    if (form.value.project_status === status) {
        form.value.project_status = '';
    } else {
        form.value.project_status = status;
    }
}

function toggleCurrentPhase(phase) {
    if (form.value.current_phase === phase) {
        form.value.current_phase = '';
    } else {
        form.value.current_phase = phase;
    }
}

function toggleRequestCategory(category) {
    if (form.value.request_category === category) {
        form.value.request_category = '';
    } else {
        form.value.request_category = category;
    }
}

function toggleVerificationStatus(status) {
    if (form.value.verification_status === status) {
        form.value.verification_status = '';
    } else {
        form.value.verification_status = status;
    }
}

// Clear all filters - diperbaiki
function clearFilters() {
    if (isLoading.value) return;

    // Reset semua filter
    const defaultForm = {
        search: '',
        plant_id: '',
        work_priority: '',
        work_type: '',
        request_category: '',
        verification_status: '',
        project_status: '',
        current_phase: '',
        start_date: '',
        end_date: '',
        target_start_date: '',
        target_end_date: '',
        sort_by: 'erf_number',
        sort_order: 'desc',
        per_page: 10,
        page: 1
    };

    // Update form
    Object.keys(defaultForm).forEach(key => {
        form.value[key] = defaultForm[key];
    });

    // Apply filters immediately
    nextTick(() => {
        applyFilters();
    });
}

// Sort function - diperbaiki menggunakan form state
function sortBy(field) {
    if (isLoading.value) return;

    if (form.value.sort_by === field) {
        form.value.sort_order = form.value.sort_order === 'asc' ? 'desc' : 'asc';
    } else {
        form.value.sort_by = field;
        form.value.sort_order = 'asc';
    }

    form.value.page = 1; // Reset to first page when sorting

    // Apply immediately untuk sorting
    nextTick(() => {
        applyFilters();
    });
}

// Get sort icon
function getSortIcon(field) {
    if (form.value.sort_by !== field) return 'fas fa-sort text-gray-400';
    return form.value.sort_order === 'asc' ? 'fas fa-sort-up text-blue-500' : 'fas fa-sort-down text-blue-500';
}

// Function to update work
function updateWork(slug) {
    router.visit(route("works.edit", slug));
}

// Function to view work details
function viewWork(slug) {
    router.visit(route("works.show", slug));
}

// Function to delete work with SweetAlert2 confirmation
function deleteWork(slug) {
    Swal.fire({
        title: "Konfirmasi Hapus",
        text: "Apakah Anda yakin ingin menghapus pekerjaan ini? Tindakan ini tidak dapat dibatalkan.",
        icon: "warning",
        iconColor: '#F59E0B',
        showCancelButton: true,
        confirmButtonText: "Hapus",
        confirmButtonColor: '#DC2626',
        cancelButtonText: "Batal",
        cancelButtonColor: '#374151',
        width: '350px',
        backdrop: 'rgba(0, 0, 0, 0.3)',
        customClass: {
            popup: 'rounded-xl shadow-lg',
            confirmButton: 'px-4 py-2 rounded-lg font-medium bg-red-600 hover:bg-red-700',
            cancelButton: 'px-4 py-2 rounded-lg font-medium bg-gray-700 hover:bg-gray-800 text-white'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("works.destroy", slug), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire({
                        icon: "success",
                        title: "Terhapus!",
                        text: "Data pekerjaan berhasil dihapus.",
                        timer: 2000,
                        showConfirmButton: false,
                        customClass: {
                            popup: 'rounded-xl shadow-lg'
                        }
                    });
                },
                onError: (errors) => {
                    let errorMessage = "Terjadi kesalahan saat menghapus data pekerjaan!";
                    if (errors && errors.message) {
                        errorMessage = errors.message;
                    } else if (typeof errors === 'string') {
                        errorMessage = errors;
                    }
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: errorMessage,
                        customClass: {
                            popup: 'rounded-xl shadow-lg'
                        }
                    });
                },
            });
        }
    });
}

// Format date for display
function formatDate(dateStr) {
    if (!dateStr) return "Belum ditentukan";
    const date = new Date(dateStr);
    return date.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
}

// Apply filters from modal - diperbaiki
function applyFiltersFromModal() {
    showFilterModal.value = false;

    // Pastikan modal tertutup dulu, baru apply filter
    nextTick(() => {
        applyFilters();
    });
}

// Truncate text for display
function truncateText(text, length = 50) {
    if (!text) return 'Tidak ada deskripsi';
    return text.length > length ? text.substring(0, length) + '...' : text;
}

// Get priority badge class
function getPriorityClass(priority) {
    const classes = {
        'HIGH': 'bg-red-100 text-red-800',
        'MEDIUM': 'bg-yellow-100 text-yellow-800',
        'LOW': 'bg-green-100 text-green-800'
    };
    return classes[priority] || 'bg-gray-100 text-gray-800';
}

// Get status badge class
function getStatusClass(status) {
    const classes = {
        'NOT_STARTED': 'bg-gray-100 text-gray-800',
        'IN_PROGRESS': 'bg-blue-100 text-blue-800',
        'COMPLETED': 'bg-green-100 text-green-800',
        'ON_HOLD': 'bg-yellow-100 text-yellow-800',
        'CANCELLED': 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

// Get works pagination data - diperbaiki dengan reactive computed
const worksPagination = computed(() => {
    return usePage().props.works || { data: [], links: [] };
});

// Get available plants for filter dropdown
const availablePlants = computed(() => {
    return usePage().props.plants || [];
});

// Get filter options
const filterOptions = computed(() => ({
    plants: usePage().props.plants || [],
    workPriorities: usePage().props.workPriorities || [],
    workTypes: usePage().props.workTypes || [],
    requestCategories: usePage().props.requestCategories || [],
    verificationStatuses: usePage().props.verificationStatuses || [],
    projectStatuses: usePage().props.projectStatuses || [],
    currentPhases: usePage().props.currentPhases || []
}));

// Get statistics
const statistics = computed(() => {
    return usePage().props.statistics || { total: 0, filtered: 0, showing: 0 };
});

// Check if any filters are active
const hasActiveFilters = computed(() => {
    return Object.keys(form.value).some(key => {
        if (key === 'sort_by' || key === 'sort_order' || key === 'per_page' || key === 'page') return false;
        return form.value[key] !== '' && form.value[key] !== null && form.value[key] !== undefined;
    });
});
</script>

<template>

    <Head title="Manajemen Pekerjaan" />
    <div class="py-6">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">
                            Manajemen Pekerjaan
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Kelola semua data pekerjaan dan proyek
                        </p>
                    </div>
                    <Link :href="route('works.create')"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah Pekerjaan
                    </Link>
                </div>

                <!-- Search and Filter Section -->
                <div class="p-6 bg-gray-50 border-b border-gray-200">
                    <div
                        class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0 lg:space-x-4">
                        <!-- Search Bar -->
                        <div class="flex-1 max-w-md">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <!-- Gunakan form.search langsung tanpa localSearch -->
                                <input v-model="form.search" type="text"
                                    placeholder="Cari berdasarkan ERF, deskripsi, unit, atau plant..."
                                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                        <!-- Filter Controls -->
                        <div class="flex items-center space-x-2">
                            <!-- Per Page Selector -->
                            <select v-model="form.per_page"
                                class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="10">10 per halaman</option>
                                <option value="25">25 per halaman</option>
                                <option value="50">50 per halaman</option>
                                <option value="100">100 per halaman</option>
                            </select>

                            <!-- Filter Button -->
                            <button @click="showFilterModal = true"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-filter mr-2"></i>
                                Filter
                                <span v-if="hasActiveFilters"
                                    class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    Aktif
                                </span>
                            </button>

                            <!-- Clear Filters Button -->
                            <button v-if="hasActiveFilters" @click="clearFilters"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-times mr-2"></i>
                                Hapus Filter
                            </button>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="mt-4 flex items-center justify-between text-sm text-gray-600">
                        <div class="flex items-center space-x-6">
                            <span>Total: {{ statistics.total }} pekerjaan</span>
                            <span v-if="hasActiveFilters">Difilter: {{ statistics.filtered }} pekerjaan</span>
                            <span>Menampilkan: {{ statistics.showing }} pekerjaan</span>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                    @click="sortBy('erf_number')">
                                    <div class="flex items-center space-x-1">
                                        <span>Nomor ERF</span>
                                        <i :class="getSortIcon('erf_number')"></i>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                    @click="sortBy('description')">
                                    <div class="flex items-center space-x-1">
                                        <span>Deskripsi Pekerjaan</span>
                                        <i :class="getSortIcon('description')"></i>
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Plant
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                    @click="sortBy('entry_date')">
                                    <div class="flex items-center space-x-1">
                                        <span>Tanggal Entry</span>
                                        <i :class="getSortIcon('entry_date')"></i>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                    @click="sortBy('work_priority')">
                                    <div class="flex items-center space-x-1">
                                        <span>Prioritas</span>
                                        <i :class="getSortIcon('work_priority')"></i>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                    @click="sortBy('project_status')">
                                    <div class="flex items-center space-x-1">
                                        <span>Status</span>
                                        <i :class="getSortIcon('project_status')"></i>
                                    </div>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                    @click="sortBy('current_phase')">
                                    <div class="flex items-center space-x-1">
                                        <span>Fase</span>
                                        <i :class="getSortIcon('current_phase')"></i>
                                    </div>
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="!worksPagination.data || worksPagination.data.length === 0">
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                        <p class="text-lg font-medium text-gray-900">Tidak ada data pekerjaan</p>
                                        <p class="text-gray-500">Data pekerjaan yang Anda cari tidak ditemukan atau
                                            belum ada.</p>
                                    </div>
                                </td>
                            </tr>
                            <tr v-else v-for="work in worksPagination.data" :key="work.id"
                                class="hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ work.erf_number || 'Belum ada' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <div class="max-w-xs">
                                        <p class="font-medium">{{ truncateText(work.description, 60) }}</p>
                                        <p class="text-gray-500 text-xs mt-1">
                                            {{ work.work_type || 'Tidak diketahui' }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ work.plant?.name || 'Tidak ada' }}
                                    <div v-if="work.requester_unit" class="text-xs text-gray-500 mt-1">
                                        {{ work.requester_unit }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatDate(work.entry_date) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getPriorityClass(work.work_priority)"
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                        {{ work.work_priority }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span :class="getStatusClass(work.project_status)"
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                        {{ work.project_status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                        {{ work.current_phase }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button @click="viewWork(work.slug)"
                                            class="text-indigo-600 hover:text-indigo-900 focus:outline-none">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button @click="updateWork(work.slug)"
                                            class="text-blue-600 hover:text-blue-900 focus:outline-none">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button @click="deleteWork(work.slug)"
                                            class="text-red-600 hover:text-red-900 focus:outline-none">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="worksPagination.data && worksPagination.data.length > 0"
                    class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <button v-if="worksPagination.prev_page_url" @click="changePage(worksPagination.prev_page_url)"
                            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </button>
                        <button v-if="worksPagination.next_page_url" @click="changePage(worksPagination.next_page_url)"
                            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </button>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Menampilkan
                                <span class="font-medium">{{ worksPagination.from }}</span>
                                sampai
                                <span class="font-medium">{{ worksPagination.to }}</span>
                                dari
                                <span class="font-medium">{{ worksPagination.total }}</span>
                                hasil
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                aria-label="Pagination">
                                <!-- Previous Page Link -->
                                <button v-if="worksPagination.prev_page_url"
                                    @click="changePage(worksPagination.prev_page_url)"
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <i class="fas fa-chevron-left h-5 w-5"></i>
                                </button>
                                <span v-else
                                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300 cursor-not-allowed">
                                    <span class="sr-only">Previous</span>
                                    <i class="fas fa-chevron-left h-5 w-5"></i>
                                </span>

                                <!-- Page Numbers -->
                                <template v-for="(link, index) in worksPagination.links" :key="index">
                                    <button
                                        v-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                                        @click="changePage(link.url)"
                                        :class="link.active ?
                                            'z-10 bg-indigo-50 border-indigo-500 text-indigo-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium' :
                                            'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium'"
                                        v-html="link.label">
                                    </button>
                                    <span
                                        v-else-if="!link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                        ...
                                    </span>
                                </template>

                                <!-- Next Page Link -->
                                <button v-if="worksPagination.next_page_url"
                                    @click="changePage(worksPagination.next_page_url)"
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <i class="fas fa-chevron-right h-5 w-5"></i>
                                </button>
                                <span v-else
                                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300 cursor-not-allowed">
                                    <span class="sr-only">Next</span>
                                    <i class="fas fa-chevron-right h-5 w-5"></i>
                                </span>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <!-- Filter Modal with updated design matching the image -->
    <div v-if="showFilterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-0 border w-11/12 md:w-[600px] shadow-lg rounded-lg bg-white">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Filter Berdasarkan</h3>
                <button @click="showFilterModal = false" class="text-gray-400 hover:text-gray-600 text-xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Filter Content -->
            <div class="px-6 py-4 space-y-6">
                <!-- Plant Filter -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Pilih Plant</h4>
                    <div class="space-y-2 max-h-32 overflow-y-auto">
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="radio" :value="''" v-model="form.plant_id"
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">Semua Plant</span>
                        </label>
                        <label v-for="plant in filterOptions.plants" :key="plant.id"
                            class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-2 rounded">
                            <input type="radio" :value="plant.id" v-model="form.plant_id"
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">{{ plant.name }}</span>
                        </label>
                    </div>
                </div>

                <!-- Work Priority Filter -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Prioritas Pekerjaan</h4>
                    <div class="flex flex-wrap gap-2">
                        <button @click="togglePriority('')" :class="form.work_priority === '' ?
                            'bg-blue-100 text-blue-800 border-blue-300' :
                            'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200'"
                            class="px-3 py-1.5 text-xs font-medium rounded-full border transition-colors">
                            Semua Prioritas
                        </button>
                        <button v-for="priority in filterOptions.workPriorities" :key="priority.value || priority"
                            @click="togglePriority(priority.value || priority)" :class="form.work_priority === (priority.value || priority) ?
                                'bg-blue-100 text-blue-800 border-blue-300' :
                                'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200'"
                            class="px-3 py-1.5 text-xs font-medium rounded-full border transition-colors">
                            {{ priority.label || priority.value || priority }}
                        </button>
                    </div>
                </div>

                <!-- Work Type Filter -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Tipe Pekerjaan</h4>
                    <div class="flex flex-wrap gap-2">
                        <button @click="toggleWorkType('')" :class="form.work_type === '' ?
                            'bg-blue-100 text-blue-800 border-blue-300' :
                            'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200'"
                            class="px-3 py-1.5 text-xs font-medium rounded-full border transition-colors">
                            Semua Tipe
                        </button>
                        <button v-for="type in filterOptions.workTypes" :key="type.value || type"
                            @click="toggleWorkType(type.value || type)" :class="form.work_type === (type.value || type) ?
                                'bg-blue-100 text-blue-800 border-blue-300' :
                                'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200'"
                            class="px-3 py-1.5 text-xs font-medium rounded-full border transition-colors">
                            {{ type.label || type.value || type }}
                        </button>
                    </div>
                </div>

                <!-- Project Status Filter -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Status Proyek</h4>
                    <div class="flex flex-wrap gap-2">
                        <button @click="toggleProjectStatus('')" :class="form.project_status === '' ?
                            'bg-blue-100 text-blue-800 border-blue-300' :
                            'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200'"
                            class="px-3 py-1.5 text-xs font-medium rounded-full border transition-colors">
                            Semua Status
                        </button>
                        <button v-for="status in filterOptions.projectStatuses" :key="status.value || status"
                            @click="toggleProjectStatus(status.value || status)" :class="form.project_status === (status.value || status) ?
                                'bg-blue-100 text-blue-800 border-blue-300' :
                                'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200'"
                            class="px-3 py-1.5 text-xs font-medium rounded-full border transition-colors">
                            {{ status.label || status.value || status }}
                        </button>
                    </div>
                </div>

                <!-- Current Phase Filter -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Fase Saat Ini</h4>
                    <div class="flex flex-wrap gap-2">
                        <button @click="toggleCurrentPhase('')" :class="form.current_phase === '' ?
                            'bg-blue-100 text-blue-800 border-blue-300' :
                            'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200'"
                            class="px-3 py-1.5 text-xs font-medium rounded-full border transition-colors">
                            Semua Fase
                        </button>
                        <button v-for="phase in filterOptions.currentPhases" :key="phase.value || phase"
                            @click="toggleCurrentPhase(phase.value || phase)" :class="form.current_phase === (phase.value || phase) ?
                                'bg-blue-100 text-blue-800 border-blue-300' :
                                'bg-gray-100 text-gray-700 border-gray-300 hover:bg-gray-200'"
                            class="px-3 py-1.5 text-xs font-medium rounded-full border transition-colors">
                            {{ phase.label || phase.value || phase }}
                        </button>
                    </div>
                </div>

                <!-- Date Range Filter -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Rentang Tanggal</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Mulai</label>
                            <input v-model="form.start_date" type="date"
                                class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Selesai</label>
                            <input v-model="form.end_date" type="date"
                                class="w-full text-sm border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Note -->
                <div class="text-xs text-gray-500 italic">
                    *Anda dapat memilih beberapa opsi filter
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-lg">
                <button @click="clearFilters" class="text-sm font-medium text-gray-600 hover:text-gray-800">
                    Reset Filter
                </button>
                <button @click="applyFiltersFromModal"
                    class="px-6 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                    Terapkan Sekarang
                </button>
            </div>
        </div>
    </div>
</template>
