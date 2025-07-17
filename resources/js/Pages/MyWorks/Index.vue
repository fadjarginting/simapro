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

// Form state that syncs with URL parameters
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

// Loading state to prevent multiple requests
const isLoading = ref(false);

// Debounce search function
let timeout;
const performSearch = () => {
    if (isLoading.value) return;

    clearTimeout(timeout);
    timeout = setTimeout(() => {
        applyFilters();
    }, 300);
};

// Watch for changes in search to trigger debounced search
watch(() => form.value.search, performSearch);

// Watch for other filter changes
watch(() => [
    form.value.plant_id,
    form.value.work_priority,
    form.value.work_type,
    form.value.request_category,
    form.value.verification_status,
    form.value.project_status,
    form.value.current_phase
], () => {
    if (!isLoading.value) applyFilters();
});


// Watch for per_page changes
watch(() => form.value.per_page, () => {
    if (!isLoading.value) {
        form.value.page = 1; // Reset to first page
        applyFilters();
    }
});

// Apply filters function
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

    router.get(route('my-works.index'), filterData, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        only: ['works', 'statistics'],
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

// Handle page change
const changePage = (url) => {
    if (!url || isLoading.value) return;

    router.visit(url, {
        preserveState: true,
        replace: true
    });
};

// Toggle methods for filter buttons
function togglePriority(priority) {
    form.value.work_priority = form.value.work_priority === priority ? '' : priority;
}

function toggleWorkType(type) {
    form.value.work_type = form.value.work_type === type ? '' : type;
}

function toggleProjectStatus(status) {
    form.value.project_status = form.value.project_status === status ? '' : status;
}

function toggleCurrentPhase(phase) {
    form.value.current_phase = form.value.current_phase === phase ? '' : phase;
}

function toggleRequestCategory(category) {
    form.value.request_category = form.value.request_category === category ? '' : category;
}

function toggleVerificationStatus(status) {
    form.value.verification_status = form.value.verification_status === status ? '' : status;
}

// Clear all filters
function clearFilters() {
    if (isLoading.value) return;

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

    Object.keys(defaultForm).forEach(key => {
        form.value[key] = defaultForm[key];
    });

    nextTick(() => {
        applyFilters();
    });
}

// Sort function
function sortBy(field) {
    if (isLoading.value) return;

    if (form.value.sort_by === field) {
        form.value.sort_order = form.value.sort_order === 'asc' ? 'desc' : 'asc';
    } else {
        form.value.sort_by = field;
        form.value.sort_order = 'asc';
    }

    form.value.page = 1;

    nextTick(() => {
        applyFilters();
    });
}

// Get sort icon
function getSortIcon(field) {
    if (form.value.sort_by !== field) return 'fas fa-sort text-gray-400';
    return form.value.sort_order === 'asc' ? 'fas fa-sort-up text-blue-500' : 'fas fa-sort-down text-blue-500';
}

// Function to view work details
function viewWork(slug) {
    router.visit(route("works.show", slug));
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

// Apply filters from modal
function applyFiltersFromModal() {
    showFilterModal.value = false;
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
        'CANCEL': 'bg-red-100 text-red-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
}

// Get works pagination data
const worksPagination = computed(() => {
    return usePage().props.works || { data: [], links: [] };
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
        if (['sort_by', 'sort_order', 'per_page', 'page'].includes(key)) return false;
        return form.value[key] !== '' && form.value[key] !== null && form.value[key] !== undefined;
    });
});
</script>

<template>

    <Head title="Pekerjaan Saya" />
    <div class="py-4">
        <div class="mx-auto sm:px-4 lg:px-6">
            <div
                class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border rounded-xl shadow-md overflow-hidden">
                <!-- Header -->
                <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow-md">
                                <i class="fas fa-user-check text-white text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 tracking-tight">
                                    Pekerjaan Saya
                                </h2>
                                <p class="text-xs text-gray-600">
                                    Lihat dan kelola semua pekerjaan yang ditugaskan kepada Anda
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter Section -->
                <div class="p-4 border-b border-gray-200">
                    <div
                        class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-3 lg:space-y-0 lg:space-x-3">
                        <!-- Search Bar -->
                        <div class="flex-1 max-w-md">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400 text-sm"></i>
                                </div>
                                <input v-model="form.search" type="text"
                                    placeholder="Cari berdasarkan ERF, deskripsi, unit..."
                                    class="block w-full pl-9 pr-3 py-1.5 border border-gray-300 rounded-md text-sm leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                            </div>
                        </div>

                        <!-- Filter Controls -->
                        <div class="flex items-center space-x-2 flex-wrap gap-2">
                            <!-- Per Page Selector -->
                            <select v-model="form.per_page"
                                class="border border-gray-300 rounded-md px-2 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm bg-white">
                                <option value="10">10/hal</option>
                                <option value="25">25/hal</option>
                                <option value="50">50/hal</option>
                                <option value="100">100/hal</option>
                            </select>

                            <!-- Filter Button -->
                            <button @click="showFilterModal = true"
                                class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <i class="fas fa-filter mr-1.5 text-gray-500"></i>
                                Filter
                                <span v-if="hasActiveFilters"
                                    class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 shadow-sm">
                                    Aktif
                                </span>
                            </button>

                            <!-- Clear Filters Button -->
                            <button v-if="hasActiveFilters" @click="clearFilters"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent rounded-md shadow-sm text-xs font-medium text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-times mr-1.5"></i>
                                Hapus Filter
                            </button>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="mt-3 flex items-center justify-between text-xs text-gray-600 flex-wrap gap-2">
                        <div class="flex items-center space-x-3 flex-wrap">
                            <span class="flex items-center gap-1"><i
                                    class="fas fa-database text-gray-400"></i><span>Total Pekerjaan Anda: <strong>{{ statistics.total
                                        }}</strong></span></span>
                            <span v-if="hasActiveFilters" class="flex items-center gap-1"><i
                                    class="fas fa-filter text-blue-500"></i><span>Difilter: <strong>{{
                                        statistics.filtered }}</strong></span></span>
                            <span class="flex items-center gap-1"><i
                                    class="fas fa-eye text-gray-400"></i><span>Menampilkan: <strong>{{
                                        statistics.showing }}</strong></span></span>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-blue-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider cursor-pointer hover:bg-blue-100"
                                    @click="sortBy('erf_number')">
                                    <div class="flex items-center space-x-1">
                                        <span>Nomor ERF</span>
                                        <i :class="getSortIcon('erf_number')"></i>
                                    </div>
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider cursor-pointer hover:bg-blue-100"
                                    @click="sortBy('description')">
                                    <div class="flex items-center space-x-1">
                                        <span>Deskripsi Pekerjaan</span>
                                        <i :class="getSortIcon('description')"></i>
                                    </div>
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    Plant
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider cursor-pointer hover:bg-blue-100"
                                    @click="sortBy('entry_date')">
                                    <div class="flex items-center space-x-1">
                                        <span>Tanggal Entry</span>
                                        <i :class="getSortIcon('entry_date')"></i>
                                    </div>
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider cursor-pointer hover:bg-blue-100"
                                    @click="sortBy('work_priority')">
                                    <div class="flex items-center space-x-1">
                                        <span>Prioritas</span>
                                        <i :class="getSortIcon('work_priority')"></i>
                                    </div>
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider cursor-pointer hover:bg-blue-100"
                                    @click="sortBy('project_status')">
                                    <div class="flex items-center space-x-1">
                                        <span>Status</span>
                                        <i :class="getSortIcon('project_status')"></i>
                                    </div>
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider cursor-pointer hover:bg-blue-100"
                                    @click="sortBy('current_phase')">
                                    <div class="flex items-center space-x-1">
                                        <span>Fase</span>
                                        <i :class="getSortIcon('current_phase')"></i>
                                    </div>
                                </th>
                                <th
                                    class="px-4 py-2 text-left text-xs font-semibold text-blue-900 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-if="!worksPagination.data || worksPagination.data.length === 0">
                                <td colspan="8" class="px-6 py-10 text-center">
                                    <div
                                        class="w-14 h-14 bg-gradient-to-br from-gray-100 to-blue-100 rounded-full flex items-center justify-center mx-auto mb-3 shadow-md">
                                        <i class="fas fa-inbox text-blue-400 text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-800">Anda Belum Memiliki Pekerjaan</h3>
                                    <p class="text-xs text-gray-500">Tidak ada pekerjaan yang ditugaskan kepada Anda, atau filter tidak cocok.</p>
                                </td>
                            </tr>
                            <tr v-else v-for="work in worksPagination.data" :key="work.id"
                                class="hover:bg-blue-50 transition-colors duration-200">
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ work.erf_number || 'Belum ada' }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900">
                                    <div class="max-w-xs">
                                        <p class="font-semibold text-sm">{{ truncateText(work.description, 60) }}</p>
                                        <p class="text-gray-500 text-xs mt-0.5">
                                            {{ work.work_type || 'Tidak diketahui' }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                    {{ work.plant?.name || 'Tidak ada' }}
                                    <div v-if="work.requester_unit" class="text-xs text-gray-500 mt-0.5">
                                        {{ work.requester_unit }}
                                    </div>
                                </td>

                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatDate(work.entry_date) }}
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <span :class="getPriorityClass(work.work_priority)"
                                        class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full shadow-sm">
                                        {{ work.work_priority }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <span :class="getStatusClass(work.project_status)"
                                        class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full shadow-sm">
                                        {{ work.project_status }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full bg-purple-100 text-purple-800 shadow-sm">
                                        {{ work.current_phase }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-1">
                                        <button @click="viewWork(work.slug)" title="Lihat Detail"
                                            class="w-7 h-7 flex items-center justify-center text-indigo-600 hover:bg-indigo-100 rounded-full transition">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                        <!-- Aksi edit dan hapus mungkin tidak tersedia untuk user biasa -->
                                        <!-- <button @click="updateWork(work.slug)" title="Edit"
                                            class="w-7 h-7 flex items-center justify-center text-blue-600 hover:bg-blue-100 rounded-full transition">
                                            <i class="fas fa-edit text-sm"></i>
                                        </button>
                                        <button @click="deleteWork(work.slug)" title="Hapus"
                                            class="w-7 h-7 flex items-center justify-center text-red-600 hover:bg-red-100 rounded-full transition">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button> -->
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="worksPagination.data && worksPagination.data.length > 0"
                    class="bg-white px-4 py-2 flex items-center justify-between border-t border-gray-200 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <button v-if="worksPagination.prev_page_url" @click="changePage(worksPagination.prev_page_url)"
                            class="relative inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </button>
                        <button v-if="worksPagination.next_page_url" @click="changePage(worksPagination.next_page_url)"
                            class="ml-3 relative inline-flex items-center px-3 py-1.5 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </button>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs text-gray-700">
                                Menampilkan
                                <span class="font-medium">{{ worksPagination.from }}</span>
                                -
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
                                    class="relative inline-flex items-center px-2 py-1.5 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <i class="fas fa-chevron-left h-4 w-4"></i>
                                </button>
                                <span v-else
                                    class="relative inline-flex items-center px-2 py-1.5 rounded-l-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300 cursor-not-allowed">
                                    <span class="sr-only">Previous</span>
                                    <i class="fas fa-chevron-left h-4 w-4"></i>
                                </span>

                                <!-- Page Numbers -->
                                <template v-for="(link, index) in worksPagination.links" :key="index">
                                    <button
                                        v-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                                        @click="changePage(link.url)"
                                        :class="link.active ?
                                            'z-10 bg-blue-50 border-blue-500 text-blue-600 relative inline-flex items-center px-3 py-1.5 border text-xs font-medium' :
                                            'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-3 py-1.5 border text-xs font-medium'"
                                        v-html="link.label">
                                    </button>
                                    <span
                                        v-else-if="!link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                                        class="relative inline-flex items-center px-3 py-1.5 border border-gray-300 bg-white text-xs font-medium text-gray-700">
                                        ...
                                    </span>
                                </template>

                                <!-- Next Page Link -->
                                <button v-if="worksPagination.next_page_url"
                                    @click="changePage(worksPagination.next_page_url)"
                                    class="relative inline-flex items-center px-2 py-1.5 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <i class="fas fa-chevron-right h-4 w-4"></i>
                                </button>
                                <span v-else
                                    class="relative inline-flex items-center px-2 py-1.5 rounded-r-md border border-gray-300 bg-gray-100 text-sm font-medium text-gray-300 cursor-not-allowed">
                                    <span class="sr-only">Next</span>
                                    <i class="fas fa-chevron-right h-4 w-4"></i>
                                </span>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div v-if="showFilterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-0 border w-11/12 md:w-[600px] shadow-lg rounded-xl bg-white">
            <!-- Modal Header -->
            <div
                class="flex items-center justify-between px-4 py-3 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-purple-50 rounded-t-xl">
                <h3 class="text-base font-semibold text-gray-900">Filter Berdasarkan</h3>
                <button @click="showFilterModal = false" class="text-gray-400 hover:text-gray-600 text-lg">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Filter Content -->
            <div class="px-4 py-3 space-y-4 max-h-[70vh] overflow-y-auto">
                <!-- Plant Filter -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Pilih Plant</h4>
                    <div class="space-y-1 max-h-32 overflow-y-auto border rounded-lg p-1">
                        <label class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1.5 rounded-md">
                            <input type="radio" :value="''" v-model="form.plant_id"
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">Semua Plant</span>
                        </label>
                        <label v-for="plant in filterOptions.plants" :key="plant.id"
                            class="flex items-center space-x-2 cursor-pointer hover:bg-gray-50 p-1.5 rounded-md">
                            <input type="radio" :value="plant.id" v-model="form.plant_id"
                                class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                            <span class="text-sm text-gray-700">{{ plant.name }}</span>
                        </label>
                    </div>
                </div>

                <!-- Work Priority Filter -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Prioritas Pekerjaan</h4>
                    <div class="flex flex-wrap gap-2">
                        <button @click="togglePriority('')" :class="form.work_priority === '' ?
                            'bg-blue-600 text-white border-blue-600' :
                            'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                            class="px-2.5 py-1 text-xs font-medium rounded-full border transition-colors shadow-sm">
                            Semua Prioritas
                        </button>
                        <button v-for="priority in filterOptions.workPriorities" :key="priority.value || priority"
                            @click="togglePriority(priority.value || priority)" :class="form.work_priority === (priority.value || priority) ?
                                'bg-blue-600 text-white border-blue-600' :
                                'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                            class="px-2.5 py-1 text-xs font-medium rounded-full border transition-colors shadow-sm">
                            {{ priority.label || priority.value || priority }}
                        </button>
                    </div>
                </div>

                <!-- Work Type Filter -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Tipe Pekerjaan</h4>
                    <div class="flex flex-wrap gap-2">
                        <button @click="toggleWorkType('')" :class="form.work_type === '' ?
                            'bg-blue-600 text-white border-blue-600' :
                            'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                            class="px-2.5 py-1 text-xs font-medium rounded-full border transition-colors shadow-sm">
                            Semua Tipe
                        </button>
                        <button v-for="type in filterOptions.workTypes" :key="type.value || type"
                            @click="toggleWorkType(type.value || type)" :class="form.work_type === (type.value || type) ?
                                'bg-blue-600 text-white border-blue-600' :
                                'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                            class="px-2.5 py-1 text-xs font-medium rounded-full border transition-colors shadow-sm">
                            {{ type.label || type.value || type }}
                        </button>
                    </div>
                </div>

                <!-- Project Status Filter -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Status Proyek</h4>
                    <div class="flex flex-wrap gap-2">
                        <button @click="toggleProjectStatus('')" :class="form.project_status === '' ?
                            'bg-blue-600 text-white border-blue-600' :
                            'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                            class="px-2.5 py-1 text-xs font-medium rounded-full border transition-colors shadow-sm">
                            Semua Status
                        </button>
                        <button v-for="status in filterOptions.projectStatuses" :key="status.value || status"
                            @click="toggleProjectStatus(status.value || status)" :class="form.project_status === (status.value || status) ?
                                'bg-blue-600 text-white border-blue-600' :
                                'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                            class="px-2.5 py-1 text-xs font-medium rounded-full border transition-colors shadow-sm">
                            {{ status.label || status.value || status }}
                        </button>
                    </div>
                </div>

                <!-- Current Phase Filter -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Fase Saat Ini</h4>
                    <div class="flex flex-wrap gap-2">
                        <button @click="toggleCurrentPhase('')" :class="form.current_phase === '' ?
                            'bg-blue-600 text-white border-blue-600' :
                            'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                            class="px-2.5 py-1 text-xs font-medium rounded-full border transition-colors shadow-sm">
                            Semua Fase
                        </button>
                        <button v-for="phase in filterOptions.currentPhases" :key="phase.value || phase"
                            @click="toggleCurrentPhase(phase.value || phase)" :class="form.current_phase === (phase.value || phase) ?
                                'bg-blue-600 text-white border-blue-600' :
                                'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'"
                            class="px-2.5 py-1 text-xs font-medium rounded-full border transition-colors shadow-sm">
                            {{ phase.label || phase.value || phase }}
                        </button>
                    </div>
                </div>

                <!-- Date Range Filter -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Rentang Tanggal Entry</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Mulai</label>
                            <input v-model="form.start_date" type="date"
                                class="w-full text-sm border border-gray-300 rounded-md px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Tanggal Selesai</label>
                            <input v-model="form.end_date" type="date"
                                class="w-full text-sm border border-gray-300 rounded-md px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between px-4 py-3 border-t border-gray-200 bg-gray-50 rounded-b-xl">
                <button @click="clearFilters"
                    class="text-xs font-medium text-gray-600 hover:text-red-600 transition-colors">
                    Reset Filter
                </button>
                <button @click="applyFiltersFromModal"
                    class="px-4 py-1.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-md hover:from-blue-700 hover:to-purple-700 shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    Terapkan Filter
                </button>
            </div>
        </div>
    </div>
</template>
