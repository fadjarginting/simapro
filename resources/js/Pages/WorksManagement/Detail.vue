<script setup>
import { useForm, Head, router, Link } from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";


defineOptions({
    layout: AuthenticatedLayout,
});

const props = defineProps({
    work: Object,
});

// Utility functions
const formatDate = (date) => {
    if (!date) return null;
    return new Date(date).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const getPriorityClass = (priority) => {
    const classes = {
        LOW: "bg-green-50 text-green-700 ring-green-600/20",
        MEDIUM: "bg-yellow-50 text-yellow-800 ring-yellow-600/20",
        HIGH: "bg-orange-50 text-orange-700 ring-orange-600/20",
        URGENT: "bg-red-50 text-red-700 ring-red-600/20",
    };
    return classes[priority] || "bg-gray-50 text-gray-600 ring-gray-500/10";
};

const getStatusClass = (status) => {
    const classes = {
        "Not started": "bg-gray-50 text-gray-600 ring-gray-500/10",
        "In Progress": "bg-yellow-50 text-yellow-800 ring-yellow-600/20",
        Completed: "bg-green-50 text-green-700 ring-green-600/20",
        "On Hold": "bg-orange-50 text-orange-700 ring-orange-600/20",
    };
    return classes[status] || "bg-gray-50 text-gray-600 ring-gray-500/10";
};

const getPhaseClass = (phase) => {
    const classes = {
        "Not started": "bg-gray-50 text-gray-600 ring-gray-500/10",
        Initiating: "bg-blue-50 text-blue-700 ring-blue-600/20",
        Planning: "bg-indigo-50 text-indigo-700 ring-indigo-600/20",
        Executing: "bg-yellow-50 text-yellow-800 ring-yellow-600/20",
        Monitoring: "bg-purple-50 text-purple-700 ring-purple-600/20",
        Closing: "bg-orange-50 text-orange-700 ring-orange-600/20",
        Completed: "bg-green-50 text-green-700 ring-green-600/20",
    };
    return classes[phase] || "bg-gray-50 text-gray-600 ring-gray-500/10";
};

const getVerificationClass = (status) => {
    const classes = {
        "Belum Verifikasi": "bg-gray-50 text-gray-600 ring-gray-500/10",
        "Sedang Diverifikasi": "bg-yellow-50 text-yellow-800 ring-yellow-600/20",
        "Sudah Diverifikasi": "bg-green-50 text-green-700 ring-green-600/20",
        Ditolak: "bg-red-50 text-red-700 ring-red-600/20",
    };
    return classes[status] || "bg-gray-50 text-gray-600 ring-gray-500/10";
};

// Sample disciplines for progress bars (to be replaced with actual data)
const disciplines = [
    { name: 'Civil', progress: 75, color: 'bg-blue-600' },
    { name: 'Mechanical', progress: 60, color: 'bg-green-600' },
    { name: 'Electrical', progress: 85, color: 'bg-yellow-600' },
    { name: 'Instrumentation', progress: 45, color: 'bg-purple-600' },
    { name: 'Piping', progress: 90, color: 'bg-red-600' },
    { name: 'HSE', progress: 70, color: 'bg-orange-600' }
];
</script>

<template>
    <Head>
        <title>Detail Pekerjaan - {{ work.slug }}</title>
    </Head>
    <div class=" mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="sm:flex sm:items-center sm:justify-between mb-6 lg:mb-8">
            <div class="min-w-0 flex-1">
                <h2
                    class="text-xl font-bold leading-7 text-gray-900 sm:text-2xl lg:text-3xl sm:tracking-tight"
                >
                    Detail Pekerjaan
                </h2>
                <div
                    class="mt-1 flex flex-col sm:mt-2 sm:flex-row sm:flex-wrap sm:space-x-6"
                >
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <span>Nomor ERF: {{ work.erf_number || "-" }}</span>
                    </div>
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <span>Plant : {{ work.plant.name }}</span>
                    </div>
                </div>
            </div>
            <div class="mt-4 flex flex-wrap gap-2 sm:ml-4 sm:mt-0">
                <Link
                    :href="route('works.index')"
                    class="bg-gray-200 px-3 py-2 text-xs rounded inline-block whitespace-nowrap text-center font-bold leading-none text-gray-700 transition duration-300 hover:bg-gray-300"                >
                <i class="fas fa-arrow-left mr-2 text-xs leading-none"></i>
                <span>Kembali</span>
                </Link>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <Link
                    
                    class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors"
                >
                    <CalendarDaysIcon class="-ml-0.5 mr-2 h-5 w-5" />
                    Lihat Schedule EAT
                </Link>
                
                <Link
                    
                    class="inline-flex items-center justify-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 transition-colors"
                >
                    <ChartBarIcon class="-ml-0.5 mr-2 h-5 w-5" />
                    Update Progress
                </Link>
                
                <Link
                    :href="route('works.edit', work.id)"
                    class="inline-flex items-center justify-center rounded-md bg-yellow-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-yellow-500 transition-colors"
                >
                    <PencilSquareIcon class="-ml-0.5 mr-2 h-5 w-5" />
                    Update Data Pekerjaan
                </Link>
                
                <Link
                    class="inline-flex items-center justify-center rounded-md bg-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-500 transition-colors"
                >
                    <ClipboardDocumentListIcon class="-ml-0.5 mr-2 h-5 w-5" />
                    Laporan Progress
                </Link>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 gap-4 lg:gap-6 xl:grid-cols-4">
            <!-- Left Column - Work Info -->
            <div class="xl:col-span-3 space-y-4 lg:space-y-6">
            <!-- Basic Information -->
            <div class="bg-white shadow-sm border border-gray-200 sm:rounded-lg">
                <div class="px-4 py-4 sm:px-6 sm:py-5">
                <h3
                    class="text-base font-semibold leading-6 text-gray-900 mb-3 lg:mb-4"
                >
                    Informasi Dasar
                </h3>
                <dl
                    class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2 lg:gap-y-6"
                >
                    <div>
                    <dt class="text-sm font-medium text-gray-500">
                        Deskripsi Pekerjaan
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900 break-words">
                        {{ work.description }}
                    </dd>
                    </div>
                    <!-- lead engineer -->
                    <div>
                    <dt class="text-sm font-medium text-gray-500">
                        Lead Engineer
                    </dt>
                    <dd class="mt-1 flex items-center gap-2">
                        <span v-if="!work.lead_engineer || work.lead_engineer === ''" 
                        class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        Belum ditentukan
                        </span>
                        <span v-else class="text-sm text-gray-900">
                        {{ work.lead_engineer }}
                        </span>
                        
                        <button v-if="!work.lead_engineer || work.lead_engineer === ''"
                        type="button"
                        class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-colors"
                        title="Tetapkan Lead Engineer">
                        <i class="fas fa-plus text-xs"></i>
                        </button>
                    </dd>
                    </div>

                    <div>
                    <dt class="text-sm font-medium text-gray-500">
                        Unit Peminta
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ work.requester_unit }}
                    </dd>
                    </div>
                    <div>
                    <dt class="text-sm font-medium text-gray-500">
                        Prioritas
                    </dt>
                    <dd class="mt-1">
                        <span
                        :class="
                            getPriorityClass(work.work_priority)
                        "
                        class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
                        >
                        {{ work.work_priority }}
                        </span>
                    </dd>
                    </div>
                    <div>
                    <dt class="text-sm font-medium text-gray-500">
                        Jenis Pekerjaan
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ work.work_type }}
                    </dd>
                    </div>
                    <div>
                    <dt class="text-sm font-medium text-gray-500">
                        Kategori Permintaan
                    </dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ work.request_category }}
                    </dd>
                    </div>
                </dl>
                </div>
            </div>

            <!-- PIC Information -->
            <div class="bg-white shadow-sm border border-gray-200 sm:rounded-lg">
                <div class="px-4 py-4 sm:px-6 sm:py-5">
                <h3
                    class="text-base font-semibold leading-6 text-gray-900 mb-3 lg:mb-4"
                >
                    Person In Charge (PIC)
                </h3>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Project Manager -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        </div>
                        <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-blue-600 uppercase tracking-wide">Project Manager</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">John Doe</p>
                        <p class="text-xs text-gray-600">john.doe@company.com</p>
                        </div>
                    </div>
                    </div>

                    <!-- Engineering Lead -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        </div>
                        <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-green-600 uppercase tracking-wide">Engineering Lead</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">Jane Smith</p>
                        <p class="text-xs text-gray-600">jane.smith@company.com</p>
                        </div>
                    </div>
                    </div>

                    <!-- Site Supervisor -->
                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-4 border border-yellow-200">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-yellow-600 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        </div>
                        <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-yellow-600 uppercase tracking-wide">Site Supervisor</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">Mike Johnson</p>
                        <p class="text-xs text-gray-600">mike.johnson@company.com</p>
                        </div>
                    </div>
                    </div>

                    <!-- QC Inspector -->
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        </div>
                        <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-purple-600 uppercase tracking-wide">QC Inspector</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">Sarah Wilson</p>
                        <p class="text-xs text-gray-600">sarah.wilson@company.com</p>
                        </div>
                    </div>
                    </div>

                    <!-- HSE Officer -->
                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-4 border border-red-200">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        </div>
                        <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-red-600 uppercase tracking-wide">HSE Officer</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">David Brown</p>
                        <p class="text-xs text-gray-600">david.brown@company.com</p>
                        </div>
                    </div>
                    </div>

                    <!-- Procurement Lead -->
                    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg p-4 border border-indigo-200">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        </div>
                        <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-indigo-600 uppercase tracking-wide">Procurement Lead</p>
                        <p class="text-sm font-semibold text-gray-900 truncate">Lisa Anderson</p>
                        <p class="text-xs text-gray-600">lisa.anderson@company.com</p>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>

            <!-- Progress per Disiplin -->
            <div class="bg-white shadow-sm border border-gray-200 sm:rounded-lg">
                <div class="px-4 py-4 sm:px-6 sm:py-5">
                <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">
                    Progress per Disiplin
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="discipline in disciplines" :key="discipline.name" class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                    <!-- Circular Progress -->
                    <div class="relative w-12 h-12 flex-shrink-0">
                        <svg class="w-12 h-12 transform -rotate-90" viewBox="0 0 36 36">
                        <!-- Background circle -->
                        <path
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none"
                            stroke="#e5e7eb"
                            stroke-width="2"
                        />
                        <!-- Progress circle -->
                        <path
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none"
                            :stroke="discipline.color.replace('bg-', '#').replace('-600', '')"
                            stroke-width="2"
                            :stroke-dasharray="`${discipline.progress}, 100`"
                            stroke-linecap="round"
                        />
                        </svg>
                        <!-- Percentage text -->
                        <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xs font-medium text-gray-700">{{ discipline.progress }}%</span>
                        </div>
                    </div>
                    <!-- Discipline name -->
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 truncate">{{ discipline.name }}</span>
                    </div>
                    </div>
                </div>
                
                <!-- Overall Progress -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="flex items-center justify-center space-x-3">
                    <div class="relative w-16 h-16 flex-shrink-0">
                        <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                        <!-- Background circle -->
                        <path
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none"
                            stroke="#e5e7eb"
                            stroke-width="3"
                        />
                        <!-- Progress circle with gradient -->
                        <path
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                            fill="none"
                            stroke="#3b82f6"
                            stroke-width="3"
                            stroke-dasharray="71, 100"
                            stroke-linecap="round"
                        />
                        </svg>
                        <!-- Percentage text -->
                        <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-sm font-bold text-gray-900">71%</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-semibold text-gray-900">Progress Keseluruhan</span>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            </div>

            <!-- Right Column - Status Summary -->
            <div class="xl:col-span-1 space-y-4 lg:space-y-6">
            <!-- Status Summary -->
            <div class="bg-white shadow-sm border border-gray-200 sm:rounded-lg">
                <div class="px-4 py-4 sm:px-6 sm:py-5">
                <div class="flex items-center justify-between mb-3 lg:mb-4">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">
                    Status
                    </h3>
                    <button
                    type="button"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-2.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors"
                    >
                    <i class="fa fa-edit -ml-0.5 mr-1 h-4 w-4"></i>
                    Perbarui Status
                    </button>
                </div>
                <dl class="space-y-3 lg:space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">
                            Status Verifikasi
                        </dt>
                        <dd>
                            <span
                            :class="
                                getVerificationClass(
                                work.verification_status
                                )
                            "
                            class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset w-full justify-center"
                            >
                            {{ work.verification_status }}
                            </span>
                        </dd>
                    </div>
                    <div>
                    <dt class="text-sm font-medium text-gray-500 mb-1">
                        Status Proyek
                    </dt>
                    <dd>
                        <span
                        :class="
                            getStatusClass(work.project_status)
                        "
                        class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset w-full justify-center"
                        >
                        {{ work.project_status }}
                        </span>
                    </dd>
                    </div>
                    <div>
                    <dt class="text-sm font-medium text-gray-500 mb-1">
                        Fase Saat Ini
                    </dt>
                    <dd>
                        <span
                        :class="
                            getPhaseClass(work.current_phase)
                        "
                        class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset w-full justify-center"
                        >
                        {{ work.current_phase }}
                        </span>
                    </dd>
                    </div>
                    
                </dl>
                </div>
            </div>

            <!-- Timeline Information (Vertical) -->
            <div class="bg-white shadow-sm border border-gray-200 sm:rounded-lg">
                <div class="px-4 py-4 sm:px-6 sm:py-5">
                <div class="flex items-center justify-between mb-3 lg:mb-4">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">
                    Timeline
                    </h3>
                    <button
                    type="button"
                    class="inline-flex items-center rounded-md bg-blue-600 px-2.5 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-colors"
                    >
                    <i class="fa fa-calendar-plus -ml-0.5 mr-1 h-4 w-4"></i>
                    Update Timeline
                    </button>
                </div>
                
                <!-- Vertical Timeline -->
                <div class="relative">
                    <!-- Timeline Line -->
                    <div class="absolute left-4 top-6 bottom-6 w-0.5 bg-gray-200" aria-hidden="true"></div>
                    
                    <!-- Timeline Items -->
                    <div class="space-y-6">
                    <!-- Tanggal Masuk -->
                    <div class="relative flex items-start">
                        <div :class="work.entry_date ? 'bg-blue-600' : 'bg-gray-300'" class="relative z-10 h-3 w-3 rounded-full ring-4 ring-white"></div>
                        <div class="ml-4">
                        <div class="text-xs font-medium text-gray-900">Tanggal Masuk</div>
                        <div class="text-xs text-gray-500">{{ formatDate(work.entry_date) || "-" }}</div>
                        </div>
                    </div>

                    <!-- ERF Disetujui -->
                    <div class="relative flex items-start">
                        <div :class="work.erf_approved_date ? 'bg-green-600' : 'bg-gray-300'" class="relative z-10 h-3 w-3 rounded-full ring-4 ring-white"></div>
                        <div class="ml-4">
                        <div class="text-xs font-medium text-gray-900">ERF Disetujui</div>
                        <div class="text-xs text-gray-500">{{ formatDate(work.erf_approved_date) || "-" }}</div>
                        </div>
                    </div>

                    <!-- Tanggal Klarifikasi -->
                    <div class="relative flex items-start">
                        <div :class="work.clarification_date ? 'bg-yellow-600' : 'bg-gray-300'" class="relative z-10 h-3 w-3 rounded-full ring-4 ring-white"></div>
                        <div class="ml-4">
                        <div class="text-xs font-medium text-gray-900">Tanggal Klarifikasi</div>
                        <div class="text-xs text-gray-500">{{ formatDate(work.clarification_date) || "-" }}</div>
                        </div>
                    </div>

                    <!-- ERF Divalidasi -->
                    <div class="relative flex items-start">
                        <div :class="work.erf_validated_date ? 'bg-purple-600' : 'bg-gray-300'" class="relative z-10 h-3 w-3 rounded-full ring-4 ring-white"></div>
                        <div class="ml-4">
                        <div class="text-xs font-medium text-gray-900">ERF Divalidasi</div>
                        <div class="text-xs text-gray-500">{{ formatDate(work.erf_validated_date) || "-" }}</div>
                        </div>
                    </div>

                    <!-- Target Inisiasi -->
                    <div class="relative flex items-start">
                        <div :class="work.initiating_target_date ? 'bg-indigo-600' : 'bg-gray-300'" class="relative z-10 h-3 w-3 rounded-full ring-4 ring-white"></div>
                        <div class="ml-4">
                        <div class="text-xs font-medium text-gray-900">Target Inisiasi</div>
                        <div class="text-xs text-gray-500">{{ formatDate(work.initiating_target_date) || "-" }}</div>
                        </div>
                    </div>

                    <!-- Target Mulai -->
                    <div class="relative flex items-start">
                        <div :class="work.executing_start_date ? 'bg-orange-600' : 'bg-gray-300'" class="relative z-10 h-3 w-3 rounded-full ring-4 ring-white"></div>
                        <div class="ml-4">
                        <div class="text-xs font-medium text-gray-900">Target Mulai</div>
                        <div class="text-xs text-gray-500">{{ formatDate(work.executing_start_date) || "-" }}</div>
                        </div>
                    </div>

                    <!-- Target Selesai -->
                    <div class="relative flex items-start">
                        <div :class="work.executing_target_date ? 'bg-red-600' : 'bg-gray-300'" class="relative z-10 h-3 w-3 rounded-full ring-4 ring-white"></div>
                        <div class="ml-4">
                        <div class="text-xs font-medium text-gray-900">Target Selesai</div>
                        <div class="text-xs text-gray-500">{{ formatDate(work.executing_target_date) || "-" }}</div>
                        </div>
                    </div>

                    <!-- Selesai Aktual -->
                    <div class="relative flex items-start">
                        <div :class="work.executing_actual_date ? 'bg-emerald-600' : 'bg-gray-300'" class="relative z-10 h-3 w-3 rounded-full ring-4 ring-white"></div>
                        <div class="ml-4">
                        <div class="text-xs font-medium text-gray-900">Selesai Aktual</div>
                        <div class="text-xs text-gray-500">{{ formatDate(work.executing_actual_date) || "-" }}</div>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
            
            <!-- Metadata -->
            <div class="bg-white shadow-sm border border-gray-200 sm:rounded-lg">
                <div class="px-4 py-4 sm:px-6 sm:py-5">
                <h3
                    class="text-base font-semibold leading-6 text-gray-900 mb-3 lg:mb-4"
                >
                    Info Tambahan
                </h3>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">
                            Dibuat oleh
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ work.creator?.name || 'Tidak diketahui' }}
                        </dd>
                    </div>
                    <div>
                    <dt class="text-sm font-medium text-gray-500">
                        Dibuat
                    </dt>
                    <dd class="mt-1 text-xs text-gray-600">
                        {{ formatDate(work.created_at) }}
                    </dd>
                    </div>
                    <div>
                    <dt class="text-sm font-medium text-gray-500">
                        Diperbarui
                    </dt>
                    <dd class="mt-1 text-xs text-gray-600">
                        {{ formatDate(work.updated_at) }}
                    </dd>
                    </div>
                </dl>
                </div>
            </div>
            </div>
        </div>
    </div>
</template>
