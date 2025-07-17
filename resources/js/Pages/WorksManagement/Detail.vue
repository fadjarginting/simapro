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
    if (!date) return "-";
    return new Date(date).toLocaleDateString("id-ID", {
        day: "numeric",
        month: "short",
        year: "numeric",
    });
};

const getPriorityClass = (priority) => {
    const classes = {
        LOW: "bg-green-100 text-green-800 border-green-200",
        MEDIUM: "bg-yellow-100 text-yellow-800 border-yellow-200",
        HIGH: "bg-orange-100 text-orange-800 border-orange-200",
        URGENT: "bg-red-100 text-red-800 border-red-200",
    };
    return classes[priority] || "bg-gray-100 text-gray-800 border-gray-200";
};

const getStatusClass = (status) => {
    const classes = {
        "Not started": "bg-gray-100 text-gray-800 border-gray-200",
        "In Progress": "bg-blue-100 text-blue-800 border-blue-200",
        Completed: "bg-green-100 text-green-800 border-green-200",
        "On Hold": "bg-orange-100 text-orange-800 border-orange-200",
    };
    return classes[status] || "bg-gray-100 text-gray-800 border-gray-200";
};

const getPhaseClass = (phase) => {
    const classes = {
        "Not started": "bg-gray-100 text-gray-800",
        Initiating: "bg-blue-100 text-blue-800",
        Planning: "bg-indigo-100 text-indigo-800",
        Executing: "bg-yellow-100 text-yellow-800",
        Monitoring: "bg-purple-100 text-purple-800",
        Closing: "bg-orange-100 text-orange-800",
        Completed: "bg-green-100 text-green-800",
    };
    return classes[phase] || "bg-gray-100 text-gray-800";
};

const getVerificationClass = (status) => {
    const classes = {
        "Belum Verifikasi": "bg-gray-100 text-gray-800 border-gray-200",
        "Sedang Diverifikasi": "bg-yellow-100 text-yellow-800 border-yellow-200",
        "Sudah Diverifikasi": "bg-green-100 text-green-800 border-green-200",
        Ditolak: "bg-red-100 text-red-800 border-red-200",
    };
    return classes[status] || "bg-gray-100 text-gray-800 border-gray-200";
};

// Sample disciplines for progress bars (to be replaced with actual data)
const disciplines = [
    { name: 'Civil', progress: 75, color: 'from-blue-400 to-blue-600' },
    { name: 'Mechanical', progress: 60, color: 'from-green-400 to-green-600' },
    { name: 'Electrical', progress: 85, color: 'from-yellow-400 to-yellow-600' },
    { name: 'Instrumentation', progress: 45, color: 'from-purple-400 to-purple-600' },
    { name: 'Piping', progress: 90, color: 'from-red-400 to-red-600' },
    { name: 'HSE', progress: 70, color: 'from-orange-400 to-orange-600' }
];

const overallProgress = computed(() => {
    if (!disciplines.length) return 0;
    const total = disciplines.reduce((sum, d) => sum + d.progress, 0);
    return Math.round(total / disciplines.length);
});
</script>

<template>
    <Head>
        <title>Detail Pekerjaan - {{ work.slug }}</title>
    </Head>
    <div class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border rounded-2xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                        <i class="fas fa-briefcase text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight">
                            Detail Pekerjaan
                        </h2>
                        <div class="flex items-center gap-3 mt-1 flex-wrap">
                            <span class="text-xs text-gray-500">Nomor ERF: <strong>{{ work.erf_number || "-" }}</strong></span>
                            <span class="text-xs text-gray-500">Plant: <strong>{{ work.plant.name }}</strong></span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Link :href="route('works.index')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white text-gray-700 text-sm font-semibold rounded-md hover:bg-gray-100 shadow-sm border transition">
                        <i class="fas fa-arrow-left text-xs"></i>
                        Kembali
                    </Link>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="p-4">
            <!-- Action Buttons -->
            <div class="mb-4 grid grid-cols-2 sm:grid-cols-4 gap-2">
                <Link href="#" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs font-semibold rounded-md hover:from-blue-600 hover:to-blue-700 shadow transition">
                    <i class="fas fa-calendar-days"></i><span>Schedule EAT</span>
                </Link>
                <Link href="#" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white text-xs font-semibold rounded-md hover:from-green-600 hover:to-green-700 shadow transition">
                    <i class="fas fa-chart-bar"></i><span>Update Progress</span>
                </Link>
                <Link :href="route('works.edit', work.id)" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white text-xs font-semibold rounded-md hover:from-yellow-600 hover:to-yellow-700 shadow transition">
                    <i class="fas fa-pencil-alt"></i><span>Update Data</span>
                </Link>
                <Link href="#" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-gradient-to-r from-purple-500 to-purple-600 text-white text-xs font-semibold rounded-md hover:from-purple-600 hover:to-purple-700 shadow transition">
                    <i class="fas fa-clipboard-list"></i><span>Laporan</span>
                </Link>
            </div>

            <!-- Grid Layout -->
            <div class="grid grid-cols-1 gap-4 xl:grid-cols-4">
                <!-- Left Column -->
                <div class="xl:col-span-3 space-y-4">
                    <!-- Basic Information -->
                    <div class="border rounded-lg shadow-sm bg-white overflow-hidden">
                        <div class="bg-gray-50 border-b px-4 py-2">
                            <h3 class="text-sm font-bold text-gray-800">Informasi Dasar</h3>
                        </div>
                        <dl class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-3">
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-medium text-gray-500">Deskripsi Pekerjaan</dt>
                                <dd class="mt-1 text-sm text-gray-800 break-words">{{ work.description }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500">Lead Engineer</dt>
                                <dd class="mt-1 flex items-center gap-2">
                                    <span v-if="!work.lead_engineer" class="inline-flex items-center rounded-md bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-200">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>Belum ditentukan
                                    </span>
                                    <span v-else class="text-sm text-gray-900">{{ work.lead_engineer }}</span>
                                    <button v-if="!work.lead_engineer" type="button" class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-colors" title="Tetapkan Lead Engineer">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500">Unit Peminta</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ work.requester_unit }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500">Prioritas</dt>
                                <dd class="mt-1">
                                    <span :class="getPriorityClass(work.work_priority)" class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold border shadow-sm">{{ work.work_priority }}</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500">Jenis Pekerjaan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ work.work_type }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-xs font-medium text-gray-500">Kategori Permintaan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ work.request_category }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- PIC Information -->
                    <div class="border rounded-lg shadow-sm bg-white overflow-hidden">
                        <div class="bg-gray-50 border-b px-4 py-2">
                            <h3 class="text-sm font-bold text-gray-800">Person In Charge (PIC)</h3>
                        </div>
                        <div class="p-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-3 border border-blue-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0"><i class="fas fa-user-tie text-white"></i></div>
                                    <div>
                                        <p class="text-xs font-medium text-blue-700 uppercase tracking-wide">Project Manager</p>
                                        <p class="text-sm font-semibold text-gray-900 truncate">John Doe</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-3 border border-green-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center flex-shrink-0"><i class="fas fa-user-gear text-white"></i></div>
                                    <div>
                                        <p class="text-xs font-medium text-green-700 uppercase tracking-wide">Engineering Lead</p>
                                        <p class="text-sm font-semibold text-gray-900 truncate">Jane Smith</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-3 border border-yellow-200">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center flex-shrink-0"><i class="fas fa-helmet-safety text-white"></i></div>
                                    <div>
                                        <p class="text-xs font-medium text-yellow-700 uppercase tracking-wide">Site Supervisor</p>
                                        <p class="text-sm font-semibold text-gray-900 truncate">Mike Johnson</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress per Disiplin -->
                    <div class="border rounded-lg shadow-sm bg-white overflow-hidden">
                        <div class="bg-gray-50 border-b px-4 py-2 flex justify-between items-center">
                            <h3 class="text-sm font-bold text-gray-800">Progress per Disiplin</h3>
                            <span class="text-xs font-bold text-gray-700">Overall: {{ overallProgress }}%</span>
                        </div>
                        <div class="p-4">
                            <div class="w-full bg-gray-200 rounded-full h-2.5 shadow-inner mb-4">
                                <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2.5 rounded-full" :style="{ width: overallProgress + '%' }"></div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                <div v-for="(discipline, idx) in disciplines" :key="discipline.name" class="border rounded-lg p-2 hover:shadow-md transition bg-white">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                                            <span class="inline-block w-2 h-2 rounded-full" :class="`bg-gradient-to-r ${discipline.color}`"></span>
                                            {{ discipline.name }}
                                        </span>
                                        <span class="text-sm font-bold text-gray-900">{{ discipline.progress }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1.5 shadow-inner">
                                        <div class="h-1.5 rounded-full" :class="`bg-gradient-to-r ${discipline.color}`" :style="{ width: discipline.progress + '%' }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="xl:col-span-1 space-y-4">
                    <!-- Status Summary -->
                    <div class="border rounded-lg shadow-sm bg-white overflow-hidden">
                        <div class="bg-gray-50 border-b px-4 py-2 flex justify-between items-center">
                            <h3 class="text-sm font-bold text-gray-800">Status</h3>
                            <button type="button" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-white bg-indigo-600 rounded-md hover:bg-indigo-700 shadow-sm transition">
                                <i class="fa fa-edit"></i> Perbarui
                            </button>
                        </div>
                        <dl class="p-4 space-y-3">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 mb-1">Verifikasi</dt>
                                <dd><span :class="getVerificationClass(work.verification_status)" class="w-full justify-center inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold border shadow-sm">{{ work.verification_status }}</span></dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 mb-1">Proyek</dt>
                                <dd><span :class="getStatusClass(work.project_status)" class="w-full justify-center inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold border shadow-sm">{{ work.project_status }}</span></dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 mb-1">Fase</dt>
                                <dd><span :class="getPhaseClass(work.current_phase)" class="w-full justify-center inline-flex items-center rounded-md px-2 py-1 text-xs font-semibold shadow-sm">{{ work.current_phase }}</span></dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Timeline -->
                    <div class="border rounded-lg shadow-sm bg-white overflow-hidden">
                        <div class="bg-gray-50 border-b px-4 py-2 flex justify-between items-center">
                            <h3 class="text-sm font-bold text-gray-800">Timeline</h3>
                            <button type="button" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 shadow-sm transition">
                                <i class="fa fa-calendar-plus"></i> Update
                            </button>
                        </div>
                        <div class="p-4">
                            <div class="relative">
                                <div class="absolute left-2 top-2 bottom-2 w-0.5 bg-gray-200"></div>
                                <div class="space-y-4">
                                    <div class="relative flex items-start"><div :class="work.entry_date ? 'bg-blue-500' : 'bg-gray-300'" class="relative z-10 h-4 w-4 rounded-full ring-4 ring-white flex items-center justify-center"><i class="fas fa-arrow-down text-white" style="font-size: 8px;"></i></div><div class="ml-3 -mt-1"><div class="text-xs font-semibold text-gray-800">Tgl Masuk</div><div class="text-xs text-gray-500">{{ formatDate(work.entry_date) }}</div></div></div>
                                    <div class="relative flex items-start"><div :class="work.erf_approved_date ? 'bg-green-500' : 'bg-gray-300'" class="relative z-10 h-4 w-4 rounded-full ring-4 ring-white flex items-center justify-center"><i class="fas fa-check text-white" style="font-size: 8px;"></i></div><div class="ml-3 -mt-1"><div class="text-xs font-semibold text-gray-800">ERF Disetujui</div><div class="text-xs text-gray-500">{{ formatDate(work.erf_approved_date) }}</div></div></div>
                                    <div class="relative flex items-start"><div :class="work.clarification_date ? 'bg-yellow-500' : 'bg-gray-300'" class="relative z-10 h-4 w-4 rounded-full ring-4 ring-white flex items-center justify-center"><i class="fas fa-comments text-white" style="font-size: 8px;"></i></div><div class="ml-3 -mt-1"><div class="text-xs font-semibold text-gray-800">Klarifikasi</div><div class="text-xs text-gray-500">{{ formatDate(work.clarification_date) }}</div></div></div>
                                    <div class="relative flex items-start"><div :class="work.erf_validated_date ? 'bg-purple-500' : 'bg-gray-300'" class="relative z-10 h-4 w-4 rounded-full ring-4 ring-white flex items-center justify-center"><i class="fas fa-check-double text-white" style="font-size: 8px;"></i></div><div class="ml-3 -mt-1"><div class="text-xs font-semibold text-gray-800">ERF Divalidasi</div><div class="text-xs text-gray-500">{{ formatDate(work.erf_validated_date) }}</div></div></div>
                                    <div class="relative flex items-start"><div :class="work.executing_target_date ? 'bg-red-500' : 'bg-gray-300'" class="relative z-10 h-4 w-4 rounded-full ring-4 ring-white flex items-center justify-center"><i class="fas fa-flag-checkered text-white" style="font-size: 8px;"></i></div><div class="ml-3 -mt-1"><div class="text-xs font-semibold text-gray-800">Target Selesai</div><div class="text-xs text-gray-500">{{ formatDate(work.executing_target_date) }}</div></div></div>
                                    <div class="relative flex items-start"><div :class="work.executing_actual_date ? 'bg-emerald-500' : 'bg-gray-300'" class="relative z-10 h-4 w-4 rounded-full ring-4 ring-white flex items-center justify-center"><i class="fas fa-trophy text-white" style="font-size: 8px;"></i></div><div class="ml-3 -mt-1"><div class="text-xs font-semibold text-gray-800">Selesai Aktual</div><div class="text-xs text-gray-500">{{ formatDate(work.executing_actual_date) }}</div></div></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metadata -->
                    <div class="border rounded-lg shadow-sm bg-white overflow-hidden">
                        <div class="bg-gray-50 border-b px-4 py-2">
                            <h3 class="text-sm font-bold text-gray-800">Info Tambahan</h3>
                        </div>
                        <dl class="p-4 space-y-2">
                            <div>
                                <dt class="text-xs font-medium text-gray-500">Dibuat oleh</dt>
                                <dd class="mt-0.5 text-sm text-gray-900">{{ work.creator?.name || 'Tidak diketahui' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500">Dibuat pada</dt>
                                <dd class="mt-0.5 text-xs text-gray-600">{{ formatDate(work.created_at) }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500">Diperbarui pada</dt>
                                <dd class="mt-0.5 text-xs text-gray-600">{{ formatDate(work.updated_at) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
