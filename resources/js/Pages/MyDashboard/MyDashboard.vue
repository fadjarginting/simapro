<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

// Import komponen yang diperlukan
import Card from "@/Components/Card.vue";
import PieChart from "@/Components/PieChart.vue";
import BarChart from "@/Components/BarChart.vue";

// Definisikan layout default untuk halaman ini
defineOptions({
    layout: AuthenticatedLayout,
});

// Ambil props yang dikirim dari MyDashboardController
const props = defineProps({
    // Common
    userRole: String,
    message: String,

    // Props untuk role selain 'pic'
    workloadSummary: Object,
    priorityTasks: Array,
    workBreakdown: Object,

    // Props untuk role 'pic'
    activitySummary: Object,
    priorityActivities: Array,
    activityBreakdown: Object,
});

// Ambil data pengguna yang sedang login
const user = computed(() => usePage().props.auth.user.data);
const isPicRole = computed(() => props.userRole === 'pic');
const isEmptyRole = computed(() => props.userRole === 'empty');

// --- Konfigurasi dan Helper untuk Chart ---

// Warna untuk chart
const CHART_COLORS = {
    onTime: {
        'Tepat Waktu': '#48BB78', // green
        'Terlambat': '#F56565', // red
        'Lebih Cepat': '#44BFD6', // blue
    },
    progress: {
        'Selesai': '#48BB78', // green
        'Dalam Progress': '#44BFD6', // blue
        'Belum Mulai': '#F56565', // red
    },
    schedule: {
        'Sesuai Jadwal': '#48BB78', // green
        'Terlambat': '#F56565', // red
    },
    general: ["#6A39F7", "#44BFD6", "#93CAED", "#CABFEB", "#F9A825", "#FDD835", "#FB8C00"],
};

// Opsi untuk Pie/Donut Chart
const pieChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'right',
        },
        tooltip: {
            callbacks: {
                label: function (context) {
                    const label = context.label || '';
                    const value = context.raw || 0;
                    const total = context.chart.getDatasetMeta(0).total;
                    const percentage = total > 0 ? ((value / total) * 100).toFixed(0) + '%' : '0%';
                    return `${label}: ${value} (${percentage})`;
                }
            }
        }
    },
};

// Opsi untuk Bar Chart
const barChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false, // Sembunyikan legenda untuk bar chart sederhana
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                precision: 0,
            },
        },
    },
};

// Helper untuk memformat data objek menjadi format yang bisa dibaca Chart.js
const formatChartDataObject = (dataObject, colors, label = 'Total') => {
    if (!dataObject || Object.keys(dataObject).length === 0) {
        return { labels: ['Tidak ada data'], datasets: [{ data: [1], backgroundColor: ['#E0E0E0'] }] };
    }

    const labels = Object.keys(dataObject);
    const data = Object.values(dataObject);
    const backgroundColors = Array.isArray(colors) ? colors : labels.map(l => colors[l] || '#CCCCCC');

    return {
        labels,
        datasets: [{
            label,
            data,
            backgroundColor: backgroundColors,
        }],
    };
};

// --- Computed Properties untuk Data Chart ---

// Data untuk chart "Ketepatan Waktu Saya" (Non-PIC)
const onTimeDeliveryData = computed(() => {
    return formatChartDataObject(props.performanceSnapshot?.on_time_delivery, CHART_COLORS.onTime);
});

// Data untuk chart "Komposisi Pekerjaan per Jenis" (Non-PIC)
const breakdownByWorkTypeData = computed(() => {
    return formatChartDataObject(props.workBreakdown?.by_work_type, CHART_COLORS.general, 'Jumlah Pekerjaan');
});

// Data untuk chart "Komposisi Pekerjaan per Status" (Non-PIC)
const breakdownByStatusData = computed(() => {
    return formatChartDataObject(props.workBreakdown?.by_project_status, CHART_COLORS.general, 'Jumlah Pekerjaan');
});

// Data untuk chart "Ringkasan Progress Aktivitas" (PIC)
const progressSummaryData = computed(() => {
    return formatChartDataObject(props.activityPerformance?.progress_summary, CHART_COLORS.progress);
});

// Data untuk chart "Status Jadwal Aktivitas" (PIC)
const scheduleStatusData = computed(() => {
    if (!props.activityPerformance) return formatChartDataObject({});
    const data = {
        'Sesuai Jadwal': props.activityPerformance.on_schedule,
        'Terlambat': props.activityPerformance.behind_schedule,
    };
    return formatChartDataObject(data, CHART_COLORS.schedule);
});

// Data untuk chart "Rincian per Disiplin" (PIC)
const breakdownByWorkTypeDataActivity = computed(() => {
    return formatChartDataObject(props.activityBreakdown?.by_work_type, CHART_COLORS.general, '');
}); 

// Data untuk chart "Rincian per Status Progress" (PIC)
const breakdownByProgressStatusData = computed(() => {
    return formatChartDataObject(props.activityBreakdown?.by_progress_status, CHART_COLORS.general, 'Jumlah Aktivitas');
});


// Helper untuk format tanggal
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Intl.DateTimeFormat('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    }).format(new Date(dateString));
};
</script>

<template>

    <Head title="My Dashboard" />

    <div class="py-6">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border rounded-2xl shadow-lg overflow-hidden">
                <!-- Header Dasbor Personal -->
                <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100">
                    <div class="flex flex-wrap justify-between items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                                <i class="fas fa-user text-white text-lg"></i>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold text-gray-900 tracking-tight">
                                    Dasbor Saya
                                </h1>
                                <p class="text-sm text-gray-600">
                                    Ringkasan pekerjaan dan performa untuk {{ user.name }}.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================================================== -->
                <!-- Tampilan untuk Role PIC -->
                <!-- ================================================== -->
                <div v-if="isPicRole" class="p-6 space-y-6">
                    <!-- 1. Ringkasan Aktivitas Saya -->
                    <section>
                        <h2 class="text-base font-bold text-gray-900 mb-4">Ringkasan Aktivitas Saya</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div class="flex flex-col">
                                        <span class="text-gray-500 text-sm font-medium">Aktivitas Aktif</span>
                                        <span class="text-3xl font-bold text-blue-600">{{ activitySummary.active_activities }}</span>
                                    </div>
                                    <div class="p-3 rounded-lg bg-blue-100 text-blue-600">
                                        <i class="fas fa-tasks h-6 w-6"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div class="flex flex-col">
                                        <span class="text-gray-500 text-sm font-medium">Mendekati Deadline</span>
                                        <span class="text-3xl font-bold text-yellow-500">{{ activitySummary.nearing_deadline }}</span>
                                    </div>
                                    <div class="p-3 rounded-lg bg-yellow-100 text-yellow-600">
                                        <i class="fas fa-hourglass-half h-6 w-6"></i>
                                    </div>
                                </div>
                                <div class="mt-3 text-xs text-gray-500">
                                    Dalam 7 hari ke depan.
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div class="flex flex-col">
                                        <span class="text-gray-500 text-sm font-medium">Aktivitas Terlambat</span>
                                        <span class="text-3xl font-bold text-red-600">{{ activitySummary.overdue_activities }}</span>
                                    </div>
                                    <div class="p-3 rounded-lg bg-red-100 text-red-600">
                                        <i class="fas fa-exclamation-triangle h-6 w-6"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- 2. Daftar Aktivitas Prioritas -->
                    <section>
                        <Card>
                            <h2 class="text-base font-bold text-gray-900 mb-4">Daftar Aktivitas Prioritas</h2>
                            <div class="overflow-y-auto max-h-[350px]">
                                <table class="min-w-full bg-white text-sm">
                                    <thead class="sticky top-0 bg-gray-50">
                                        <tr>
                                            <th class="p-3 text-center font-semibold text-gray-600">Aktivitas</th>
                                            <th class="p-3 text-center font-semibold text-gray-600">Disiplin</th>
                                            <th class="p-3 text-center font-semibold text-gray-600">Target Selesai</th>
                                            <th class="p-3 text-center font-semibold text-gray-600">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="activity in priorityActivities" :key="activity.id" class="hover:bg-gray-50">
                                            <td class="p-3 text-left text-gray-700 truncate max-w-md">{{ activity.activity_name }}</td>
                                            <td class="p-3 text-left text-gray-700">{{ activity.discipline_name }}</td>
                                            <td class="p-3 text-left text-gray-700">{{ formatDate(activity.end_date) }}</td>
                                            <td class="p-3 text-left font-bold" :class="activity.urgency_type === 'overdue' ? 'text-red-600' : 'text-yellow-600'">
                                                {{ activity.urgency_status }}
                                            </td>
                                        </tr>
                                        <tr v-if="priorityActivities.length === 0">
                                            <td colspan="4" class="p-4 text-center text-gray-500">
                                                Tidak ada aktivitas mendesak saat ini. Kerja bagus!
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </Card>
                    </section>

                    <!-- 4. Rincian Aktivitas Saya -->
                    <section>
                        <h2 class="text-base font-bold text-gray-900 mb-4">Rincian Aktivitas Saya</h2>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <Card>
                                <h3 class="font-bold mb-2 text-gray-900">Berdasarkan Tipe Pekerjaan</h3>
                                <div class="min-h-[300px]">
                                    <BarChart :chart-data="breakdownByWorkTypeDataActivity" :options="barChartOptions" />
                                </div>
                            </Card>
                            <Card>
                                <h3 class="font-bold mb-2 text-gray-900">Berdasarkan Status Progress</h3>
                                <div class="min-h-[300px]">
                                    <BarChart :chart-data="breakdownByProgressStatusData" :options="barChartOptions" />
                                </div>
                            </Card>
                        </div>
                    </section>
                </div>

                <!-- ================================================== -->
                <!-- Tampilan untuk Role 'empty' -->
                <!-- ================================================== -->
                <div v-else-if="isEmptyRole" class="p-6">
                    <div class="flex flex-col items-center justify-center min-h-[400px] text-center">
                        <div class="p-5 rounded-full bg-blue-100 text-blue-500 mb-4">
                            <i class="fas fa-info-circle fa-2x"></i>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-800">Informasi</h2>
                        <p class="text-gray-600 mt-2 max-w-md">{{ message }}</p>
                    </div>
                </div>

                <!-- ================================================== -->
                <!-- Tampilan untuk Role Selain PIC (Tampilan Lama) -->
                <!-- ================================================== -->
                <div v-else class="p-6 space-y-6">
                    <!-- 1. Ringkasan Beban Kerja Saya -->
                    <section>
                        <h2 class="text-base font-bold text-gray-900 mb-4">Ringkasan Beban Kerja Saya</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div class="flex flex-col">
                                        <span class="text-gray-500 text-sm font-medium">Pekerjaan Aktif</span>
                                        <span class="text-3xl font-bold text-blue-600">{{ workloadSummary.active_jobs
                                            }}</span>
                                    </div>
                                    <div class="p-3 rounded-lg bg-blue-100 text-blue-600">
                                        <i class="fas fa-tasks h-6 w-6"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div class="flex flex-col">
                                        <span class="text-gray-500 text-sm font-medium">Mendekati Deadline</span>
                                        <span class="text-3xl font-bold text-yellow-500">{{
                                            workloadSummary.nearing_deadline }}</span>
                                    </div>
                                    <div class="p-3 rounded-lg bg-yellow-100 text-yellow-600">
                                        <i class="fas fa-hourglass-half h-6 w-6"></i>
                                    </div>
                                </div>
                                <div class="mt-3 text-xs text-gray-500">
                                    Dalam 7 hari ke depan.
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex justify-between items-start">
                                    <div class="flex flex-col">
                                        <span class="text-gray-500 text-sm font-medium">Pekerjaan Terlambat</span>
                                        <span class="text-3xl font-bold text-red-600">{{ workloadSummary.overdue_jobs
                                            }}</span>
                                    </div>
                                    <div class="p-3 rounded-lg bg-red-100 text-red-600">
                                        <i class="fas fa-exclamation-triangle h-6 w-6"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- 2. Daftar Tugas & Prioritas -->
                    <section>
                        <Card>
                            <h2 class="text-base font-bold text-gray-900 mb-4">Daftar Tugas & Prioritas</h2>
                            <div class="overflow-y-auto max-h-[350px]">
                                <table class="min-w-full bg-white text-sm">
                                    <thead class="sticky top-0 bg-gray-50">
                                        <tr>
                                            <th class="p-3 text-left font-semibold text-gray-600">ERF Number</th>
                                            <th class="p-3 text-left font-semibold text-gray-600">Deskripsi</th>
                                            <th class="p-3 text-left font-semibold text-gray-600">Target Selesai</th>
                                            <th class="p-3 text-left font-semibold text-gray-600">Status Mendesak</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="(task, index) in priorityTasks" :key="index"
                                            class="hover:bg-gray-50">
                                            <td class="p-3 text-gray-700 font-medium">{{ task.erf_number || 'N/A' }}
                                            </td>
                                            <td class="p-3 text-gray-700 truncate max-w-md">{{ task.description }}</td>
                                            <td class="p-3 text-gray-700">{{ formatDate(task.executing_target_date) }}
                                            </td>
                                            <td class="p-3 font-bold"
                                                :class="task.urgency_status.includes('Terlambat') ? 'text-red-600' : 'text-yellow-600'">
                                                {{ task.urgency_status }}
                                            </td>
                                        </tr>
                                        <tr v-if="priorityTasks.length === 0">
                                            <td colspan="4" class="p-4 text-center text-gray-500">
                                                Tidak ada pekerjaan mendesak saat ini. Kerja bagus!
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </Card>
                    </section>

                   

                    <!-- 4. Rincian Pekerjaan Saya -->
                    <section>
                        <h2 class="text-base font-bold text-gray-900 mb-4">Rincian Pekerjaan Saya</h2>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <Card>
                                <h3 class="font-bold mb-2 text-gray-900">Berdasarkan Jenis Pekerjaan</h3>
                                <div class="min-h-[300px]">
                                    <BarChart :chart-data="breakdownByWorkTypeData" :options="barChartOptions" />
                                </div>
                            </Card>
                            <Card>
                                <h3 class="font-bold mb-2 text-gray-900">Berdasarkan Status Pekerjaan</h3>
                                <div class="min-h-[300px]">
                                    <BarChart :chart-data="breakdownByStatusData" :options="barChartOptions" />
                                </div>
                            </Card>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</template>
