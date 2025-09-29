<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, usePage, Link, router } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import GaugeChart from "@/Components/GaugeChart.vue"; // Import komponen baru
import html2pdf from 'html2pdf.js';

// Import Components
import Card from "@/Components/Card.vue";
import PieChart from "@/Components/PieChart.vue";
import BarChart from "@/Components/BarChart.vue";

// Define layout
defineOptions({
    layout: AuthenticatedLayout,
});

// Define props from controller
const props = defineProps({
    stats: Object,
    weeklySummary: Object,
    filters: Object, // Menggantikan availableYears dan selectedYear
    allStatuses: Array,
    phaseDetails: Object,
    completionTime: Object,
    onTimeCompletion: Object,
    engineerWorkload: Object,
    kpiMonitoring: Object,
});

// Get authenticated user from Inertia
const user = computed(() => usePage().props.auth.user.data);

// Filter state
const localFilters = ref({
    type: props.filters.type,
    year: props.filters.year,
    month: props.filters.month,
});

// Watch for changes in filters and reload the page
watch(localFilters, (newFilters) => {
    router.get(route('dashboard'), {
        filter_type: newFilters.type,
        year: newFilters.year,
        month: newFilters.month,
    }, {
        preserveState: true,
        replace: true,
    });
}, { deep: true });

// Computed property for month names
const monthNames = computed(() => {
    return Array.from({ length: 12 }, (e, i) => {
        return { value: i + 1, name: new Date(null, i, 1).toLocaleDateString('id-ID', { month: 'long' }) };
    });
});

// Computed property for dynamic header
const dynamicHeaderText = computed(() => {
    if (localFilters.value.type === 'monthly') {
        const monthName = monthNames.value.find(m => m.value === localFilters.value.month)?.name;
        return `Ringkasan untuk ${monthName} ${localFilters.value.year}.`;
    }
    if (localFilters.value.type === 'yearly') {
        return `Ringkasan untuk tahun ${localFilters.value.year}.`;
    }
    return 'Ringkasan semua pekerjaan.';
});

// Helper to convert status from "Title Case" to "snake_case" for CSS classes
const toSnakeCase = (str) => {
    if (!str) return '';
    return str.toLowerCase().replace(/\s+/g, '_');
};

// Colors for charts
const CHART_COLORS = {
    // Mengubah pie dari array menjadi objek untuk pemetaan yang konsisten
    workTypeData: {
        'FEED/DED': '#3B82F6', // Blue
        'Kajian Engineering': '#F59E0B', // Amber
        'Technical Assist': '#10B981', // Emerald
    },
    status: {
        'Not Started': '#A0AEC0', // gray
        'In Progress': '#44BFD6', // blue
        'Finish': '#48BB78', // green
        'On Hold': '#F59E0B', // amber
        'Cancel': '#F56565', // red
    },
    phase: {
        'FEED/DED': 'rgba(59, 130, 246, 0.7)',
        'Kajian Engineering': 'rgba(239, 68, 68, 0.7)',
        'Technical Assist': 'rgba(34, 197, 94, 0.7)',
    },
    // New colors for on-time completion chart
    onTime: {
        'Tepat Waktu': '#48BB78', // green
        'Terlambat': '#F56565', // red
        'Lebih Cepat': '#44BFD6', // blue
    }
};

// Options for grouped bar charts (bars side-by-side)
const barChartOptions = {
    scales: {
        x: {
            stacked: false,
            ticks: {
                callback: function (value) {
                    const label = this.getLabelForValue(value);
                    if (typeof label === 'string') {
                        return label.split(' ');
                    }
                    return label;
                }
            }
        },
        y: {
            stacked: false,
            beginAtZero: true,
            ticks: {
                precision: 0
            }
        }
    }
};

// Options for Pie Chart to customize labels
const pieChartOptions = {
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
    }
};

// Helper to format data for pie charts, sorted and with percentage in label
const formatPieChartData = (dataObject, colorMapping) => {
    if (!dataObject || Object.keys(dataObject).length === 0) return { labels: [], datasets: [] };

    const total = Object.values(dataObject).reduce((sum, value) => sum + value, 0);
    if (total === 0) {
        return { labels: ['No data'], datasets: [{ data: [1], backgroundColor: ['#E0E0E0'] }] };
    }

    const sortedEntries = Object.entries(dataObject).sort(([, a], [, b]) => b - a);

    const labels = sortedEntries.map(([key, value]) => {
        const percentage = ((value / total) * 100).toFixed(0);
        return `${key}, ${value}, ${percentage}%`;
    });
    const data = sortedEntries.map(([, value]) => value);
    const backgroundColors = sortedEntries.map(([key]) => colorMapping[key] || '#CCCCCC');

    return {
        labels: labels,
        datasets: [{
            data: data,
            backgroundColor: backgroundColors,
        },],
    };
};

// Helper to format data for bar charts, sorted by total value from largest to smallest
const formatBarChartData = (dataObject) => {
    const labels = Object.keys(dataObject);
    if (labels.length === 0) {
        return { labels: [], datasets: [] };
    }

    const sortedLabels = labels.sort((a, b) => {
        const totalA = Object.values(dataObject[a]).reduce((sum, count) => sum + count, 0);
        const totalB = Object.values(dataObject[b]).reduce((sum, count) => sum + count, 0);
        return totalB - totalA;
    });

    const datasets = props.allStatuses.map(status => ({
        label: status,
        data: sortedLabels.map(label => dataObject[label][status] || 0),
        backgroundColor: CHART_COLORS.status[status] || '#CCCCCC',
    }));

    return {
        labels: sortedLabels,
        datasets: datasets,
    };
};

// --- Logika Baru untuk Chart Fase ---

// Helper to format data for the phase bar charts
const formatPhaseChartData = (phaseData, workType) => {
    if (!phaseData || phaseData.total === 0) {
        return { labels: [], datasets: [] };
    }
    const labels = Object.keys(phaseData.phases);
    const data = Object.values(phaseData.phases);

    return {
        labels: labels,
        datasets: [{
            data: data,
            backgroundColor: CHART_COLORS.phase[workType] || '#A0AEC0',
        }]
    };
};

// Computed properties for each phase chart's data
const phaseChartData = computed(() => {
    const formattedData = {};
    if (props.phaseDetails) {
        for (const workType in props.phaseDetails) {
            formattedData[workType] = formatPhaseChartData(props.phaseDetails[workType], workType);
        }
    }
    return formattedData;
});

// Function to generate options for phase charts, including custom data labels
const getPhaseChartOptions = (workType) => {
    const chartData = props.phaseDetails[workType];
    if (!chartData) return {};

    return {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { beginAtZero: true, display: false },
            x: {
                grid: { display: false },
                ticks: {
                    // FUNGSI INI DIPERBARUI UNTUK MEMPERBAIKI LABEL MIRING
                    callback: function (value) {
                        const label = this.getLabelForValue(value);
                        if (typeof label === 'string') {
                            // Memecah label menjadi beberapa baris jika mengandung spasi
                            return label.split(' ');
                        }
                        return label;
                    }
                }
            }
        },
        plugins: {
            legend: { display: false },
            // Custom plugin to display value and percentage on top of bars
            datalabels: {
                anchor: 'end',
                align: 'top',
                color: '#000',
                font: { weight: 'bold', size: 10 },
                formatter: (value) => {
                    if (value === 0) return null;
                    const total = chartData.total;
                    const percentage = total > 0 ? ((value / total) * 100).toFixed(0) + '%' : '0%';
                    return `${value}; ${percentage}`;
                },
            }
        }
    };
};
// --- Akhir Logika Baru ---

// --- New Insight Visualizations Logic ---

// 6. Data for Bar Chart: Average Completion Time by Work Type
const avgCompletionTimeByTypeData = computed(() => {
    if (!props.completionTime || !props.completionTime.byWorkType) return { labels: [], datasets: [] };
    const data = props.completionTime.byWorkType;
    const labels = Object.keys(data);
    const values = Object.values(data);
    return {
        labels,
        datasets: [{
            label: 'Rata-rata Hari Penyelesaian',
            data: values,
            backgroundColor: 'rgba(75, 192, 192, 0.7)',
        }]
    };
});

// 7. Data for Pie Chart: On-Time Completion Status
const onTimeCompletionPieData = computed(() => {
    if (!props.onTimeCompletion || !props.onTimeCompletion.summary) return { labels: [], datasets: [] };
    return formatPieChartData(props.onTimeCompletion.summary, CHART_COLORS.onTime);
});

// 8. Data for Bar Chart: Engineer Workload
const engineerWorkloadData = computed(() => {
    if (!props.engineerWorkload) return { labels: [], datasets: [] };
    const labels = Object.keys(props.engineerWorkload);
    const data = Object.values(props.engineerWorkload);
    return {
        labels,
        datasets: [{
            label: 'Pekerjaan Aktif',
            data,
            backgroundColor: 'rgba(255, 159, 64, 0.7)',
        }]
    };
});

// Options for horizontal bar chart
const horizontalBarOptions = {
    indexAxis: 'y',
    responsive: true,
    maintainAspectRatio: false,
    scales: {
        x: {
            beginAtZero: true,
            ticks: { precision: 0 }
        }
    },
    plugins: {
        legend: { display: false },
        datalabels: {
            anchor: 'end',
            align: 'right',
            color: '#000',
            font: { weight: 'bold' },
            formatter: (value) => value > 0 ? value : null,
        }
    }
};


// --- End of New Insight Logic ---


// 1. Data untuk Pie Chart: Total Pekerjaan Berdasarkan Status
const workStatusData = computed(() => formatPieChartData(props.stats.byStatus, CHART_COLORS.status));

// 2. Data untuk Pie Chart: Pekerjaan Berdasarkan Jenis
const workTypeData = computed(() => formatPieChartData(props.stats.byType, CHART_COLORS.workTypeData));

// 3. Data untuk Bar Chart: Detail Pekerjaan per Plant
const workByPlantData = computed(() => formatBarChartData(props.stats.byPlant));

// 4. Data untuk Bar Chart: Pekerjaan Berdasarkan Jenis dan Statusnya
const workByTypeAndStatusData = computed(() => formatBarChartData(props.stats.byTypeAndStatus));

// 5. Data untuk Bar Chart: Total Pekerjaan Berdasarkan Lead Engineer dan Statusnya
const workByLeadEngineerData = computed(() => formatBarChartData(props.stats.byLeadEngineer));

// Computed property to calculate totals for the weekly summary table
const weeklySummaryTotals = computed(() => {
    const totals = {
        masuk: 0,
        selesai: 0,
        initiating: 0,
        executing: 0,
        inProgressTotal: 0,
    };

    if (props.weeklySummary.summary) {
        for (const type in props.weeklySummary.summary) {
            const data = props.weeklySummary.summary[type];
            totals.masuk += data.masuk;
            totals.selesai += data.selesai;
            totals.initiating += data.initiating;
            totals.executing += data.executing;
        }
    }
    totals.inProgressTotal = totals.initiating + totals.executing;
    return totals;
});

// Format date for display
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Intl.DateTimeFormat('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    }).format(new Date(dateString));
};

// Get status color for badge
const getStatusClass = (status) => {
    const statusClasses = {
        'Not Started': 'bg-gray-100 text-gray-800',
        'In Progress': 'bg-blue-100 text-blue-800',
        'Finish': 'bg-green-100 text-green-800',
        'On Hold': 'bg-yellow-100 text-yellow-800',
        'Cancelled': 'bg-red-100 text-red-800',
        'default': 'bg-gray-100 text-gray-800',
    };
    return statusClasses[status] || statusClasses['default'];
};

// Function to generate PDF
const generatePDF = () => {
    const element = document.getElementById('dashboard-content');
    if (!element) return;
    html2pdf()
        .set({
            margin: 0.5,
            filename: `dashboard-${new Date().toISOString().slice(0,10)}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
        })
        .from(element)
        .save();
};
</script>

<template>

    <Head title="Dasbor" />

    <div class="py-4">
        <div class="mx-auto sm:px-4 lg:px-6">
            <div
                class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border rounded-2xl shadow-lg overflow-hidden">
                <!-- Header dan Filter Tahun -->
                <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4">
                        <div class="flex items-center gap-3 mb-2 md:mb-0">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                                <i class="fas fa-chart-pie text-white text-lg"></i>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold text-gray-900 tracking-tight">
                                    Selamat Datang, {{ user.name }}!
                                </h1>
                                <p class="text-sm text-gray-600">
                                    {{ dynamicHeaderText }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 md:gap-4 items-center">
                            <!-- Filter Jenis -->
                            <div class="flex items-center gap-2">
                                <label for="filter-type" class="text-sm font-medium text-gray-700">Filter:</label>
                                <select id="filter-type" v-model="localFilters.type"
                                    class="block w-full pl-3 pr-8 py-1.5 border border-gray-300 rounded-md text-sm bg-white shadow-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="all">Semua</option>
                                    <option value="yearly">Tahunan</option>
                                    <option value="monthly">Bulanan</option>
                                </select>
                            </div>
                            <!-- Filter Tahun -->
                            <div v-if="localFilters.type !== 'all'" class="flex items-center gap-2">
                                <label for="year-filter" class="text-sm font-medium text-gray-700">Tahun:</label>
                                <select id="year-filter" v-model="localFilters.year"
                                    class="block w-full pl-3 pr-8 py-1.5 border border-gray-300 rounded-md text-sm bg-white shadow-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option v-for="year in filters.available_years" :key="year" :value="year">
                                        {{ year }}
                                    </option>
                                </select>
                            </div>
                            <!-- Filter Bulan -->
                            <div v-if="localFilters.type === 'monthly'" class="flex items-center gap-2">
                                <label for="month-filter" class="text-sm font-medium text-gray-700">Bulan:</label>
                                <select id="month-filter" v-model="localFilters.month"
                                    class="block w-full pl-3 pr-8 py-1.5 border border-gray-300 rounded-md text-sm bg-white shadow-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option v-for="month in monthNames" :key="month.value" :value="month.value">
                                        {{ month.name }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Kartu Statistik -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-white border-2 border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div class="flex flex-col">
                                    <span class="text-gray-500 text-sm font-medium">Total Pekerjaan</span>
                                    <span class="text-3xl font-bold text-gray-800">{{ stats.total }}</span>
                                </div>
                                <div class="p-3 rounded-lg bg-yellow-100 text-yellow-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a2 2 0 012 2v2" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border-2 border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div class="flex flex-col">
                                    <span class="text-gray-500 text-sm font-medium">In Progress</span>
                                    <span class="text-3xl font-bold text-gray-800">{{ stats.onProgress }}</span>
                                </div>
                                <div class="p-3 rounded-lg bg-blue-100 text-blue-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border-2 border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div class="flex flex-col">
                                    <span class="text-gray-500 text-sm font-medium">Pekerjaan Terlambat</span>
                                    <span class="text-3xl font-bold text-gray-800">{{ stats.overdue }}</span>
                                </div>
                                <div class="p-3 rounded-lg bg-red-100 text-red-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white border-2 border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start">
                                <div class="flex flex-col">
                                    <span class="text-gray-500 text-sm font-medium">Rata-rata Waktu Penyelesaian</span>
                                    <span class="text-3xl font-bold text-gray-800">
                                        {{ completionTime.averageOverall }} <span class="text-xl">Hari</span>
                                    </span>
                                </div>
                                <div class="p-3 rounded-lg bg-green-100 text-green-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bagian Grafik: Pie Chart -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <Card class="flex flex-col">
                            <h2 class="text-base font-bold text-gray-900 mb-2">Total Pekerjaan Berdasarkan Status</h2>
                            <div class="flex-grow flex items-center justify-center min-h-[300px]">
                                <PieChart v-if="stats.total > 0" :chart-data="workStatusData"
                                    :options="pieChartOptions" />
                                <p v-else class="text-gray-500">Tidak ada data.</p>
                            </div>
                        </Card>
                        <Card class="flex flex-col">
                            <h2 class="text-base font-bold text-gray-900 mb-2">Pekerjaan Berdasarkan Jenis</h2>
                            <div class="flex-grow flex items-center justify-center min-h-[300px]">
                                <PieChart v-if="stats.total > 0" :chart-data="workTypeData"
                                    :options="pieChartOptions" />
                                <p v-else class="text-gray-500">Tidak ada data.</p>
                            </div>
                        </Card>
                    </div>

                    <!-- Ketepatan Waktu & Beban Kerja Engineer -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <Card>
                            <h2 class="text-base font-bold text-gray-900 mb-2">Ketepatan Waktu Penyelesaian</h2>
                            <div class="flex-grow flex items-center justify-center min-h-[300px]">
                                <PieChart v-if="onTimeCompletion.summary" :chart-data="onTimeCompletionPieData"
                                    :options="pieChartOptions" />
                                <p v-else class="text-gray-500">Tidak ada data penyelesaian.</p>
                            </div>
                        </Card>
                        <Card>
                            <h2 class="text-base font-bold text-gray-900 mb-4">Beban Kerja Engineer (Sedang Berjalan)
                            </h2>
                            <div class="min-h-[300px]">
                                <BarChart v-if="engineerWorkloadData.labels.length" :chart-data="engineerWorkloadData"
                                    :options="horizontalBarOptions" />
                                <div v-else class="flex items-center justify-center h-full text-gray-500">
                                    Tidak ada data pekerjaan aktif per engineer.
                                </div>
                            </div>
                        </Card>
                    </div>

                    <!-- Rata-rata Penyelesaian per Jenis & Tabel Terlambat -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <Card>
                            <h2 class="text-base font-bold text-gray-900 mb-4">Rata-rata Waktu Penyelesaian per Jenis
                                Pekerjaan (Hari)</h2>
                            <div class="min-h-[350px]">
                                <BarChart v-if="avgCompletionTimeByTypeData.labels.length"
                                    :chart-data="avgCompletionTimeByTypeData" :options="barChartOptions" />
                                <div v-else class="flex items-center justify-center h-full text-gray-500">
                                    Tidak ada data penyelesaian.
                                </div>
                            </div>
                        </Card>
                        <Card>
                            <h2 class="text-base font-bold text-gray-900 mb-4">Daftar Pekerjaan Terlambat</h2>
                            <div class="overflow-y-auto max-h-[350px]">
                                <table v-if="onTimeCompletion.unfinishedLateWorks.length"
                                    class="min-w-full bg-white text-sm">
                                    <thead class="sticky top-0 bg-gray-50">
                                        <tr>
                                            <th class="p-2 text-left font-semibold text-gray-600">ERF</th>
                                            <th class="p-2 text-left font-semibold text-gray-600">Lead Engineer</th>
                                            <th class="p-2 text-right font-semibold text-gray-600">Terlambat</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="work in onTimeCompletion.unfinishedLateWorks" :key="work.slug"
                                            class="hover:bg-gray-50">
                                            <td class="p-2">
                                                <Link :href="route('works.show', work.slug)"
                                                    class="text-blue-600 hover:underline">{{ work.erf_number }}</Link>
                                            </td>
                                            <td class="p-2 text-gray-700">{{ work.lead_engineer || 'Tidak ada' }}</td>
                                            <td class="p-2 text-right text-red-600 font-medium">{{ work.days_late }}
                                                Hari</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div v-else class="flex items-center justify-center h-full text-gray-500 py-10">
                                    Tidak ada pekerjaan yang terlambat.
                                </div>
                            </div>
                        </Card>
                    </div>

                    <!-- Bagian Grafik: Bar Chart -->
                    <div class="grid grid-cols-1 gap-6">
                        <Card>
                            <h2 class="text-base font-bold text-gray-900 mb-4">Detail Pekerjaan Berdasarkan Status per
                                Plant</h2>
                            <div class="min-h-[350px]">
                                <BarChart v-if="workByPlantData.labels.length" :chart-data="workByPlantData"
                                    :options="barChartOptions" />
                                <div v-else class="flex items-center justify-center h-full text-gray-500">
                                    Tidak ada data pekerjaan per plant.
                                </div>
                            </div>
                        </Card>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <Card>
                            <h2 class="text-base font-bold text-gray-900 mb-4">Status per Jenis Pekerjaan</h2>
                            <div class="min-h-[350px]">
                                <BarChart v-if="workByTypeAndStatusData.labels.length"
                                    :chart-data="workByTypeAndStatusData" :options="barChartOptions" />
                                <div v-else class="flex items-center justify-center h-full text-gray-500">
                                    Tidak ada data.
                                </div>
                            </div>
                        </Card>
                        <Card>
                            <h2 class="text-base font-bold text-gray-900 mb-4">Pekerjaan per Lead Engineer & Status</h2>
                            <div class="min-h-[350px]">
                                <BarChart v-if="workByLeadEngineerData.labels.length"
                                    :chart-data="workByLeadEngineerData" :options="barChartOptions" />
                                <div v-else class="flex items-center justify-center h-full text-gray-500">
                                    Tidak ada data pekerjaan per lead engineer.
                                </div>
                            </div>
                        </Card>
                    </div>

                    <!-- Detail Status Pekerjaan per Fase -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <Card v-for="(data, workType) in phaseDetails" :key="workType">
                            <div class="text-center">
                                <h3 class="font-bold text-gray-700 text-lg">DETAIL STATUS PEKERJAAN</h3>
                                <h2 class="text-lg font-bold text-gray-800">{{ workType.toUpperCase() }}</h2>
                                <p class="text-sm font-bold text-red-600">TOTAL PEKERJAAN = {{ data.total }} ERF</p>
                            </div>
                            <div class="mt-4 min-h-[250px]">
                                <BarChart v-if="data.total > 0" :chart-data="phaseChartData[workType]"
                                    :options="getPhaseChartOptions(workType)" />
                                <div v-else class="flex items-center justify-center h-full text-gray-500">
                                    Tidak ada data.
                                </div>
                            </div>
                        </Card>
                    </div>

                    <!-- Tabel Rekap Mingguan -->
                    <div>
                        <Card>
                            <h2 class="text-base font-bold text-gray-900 mb-4">Total Pekerjaan Minggu Ini ({{
                                formatDate(weeklySummary.startOfWeek) }} - {{ formatDate(weeklySummary.endOfWeek) }})
                            </h2>
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white text-center">
                                    <thead class="bg-gray-50 text-sm">
                                        <tr>
                                            <th rowspan="2"
                                                class="p-3 border-b border-gray-200 align-middle text-left font-semibold text-gray-600">
                                                Pekerjaan Engineering</th>
                                            <th colspan="2"
                                                class="p-3 border-b border-gray-200 font-semibold text-gray-600">
                                                Aktivitas Minggu Ini</th>
                                            <th colspan="3"
                                                class="p-3 border-b border-gray-200 font-semibold text-gray-600">Status
                                                Pekerjaan Aktif (Total)</th>
                                        </tr>
                                        <tr>
                                            <th class="p-3 border-b border-gray-200 font-semibold text-gray-600">Masuk
                                            </th>
                                            <th class="p-3 border-b border-gray-200 font-semibold text-gray-600">Selesai
                                            </th>
                                            <th class="p-3 border-b border-gray-200 font-semibold text-gray-600">Fase
                                                Initiating</th>
                                            <th class="p-3 border-b border-gray-200 font-semibold text-gray-600">Fase
                                                Executing</th>
                                            <th class="p-3 border-b border-gray-200 font-semibold text-gray-600">Total
                                                Sedang Berjalan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        <tr v-for="(data, type) in weeklySummary.summary" :key="type"
                                            class="hover:bg-gray-50 text-sm">
                                            <td class="p-3 text-left">{{ type }}</td>
                                            <td class="p-3">{{ data.masuk }}</td>
                                            <td class="p-3">{{ data.selesai }}</td>
                                            <td class="p-3">{{ data.initiating }}</td>
                                            <td class="p-3">{{ data.executing }}</td>
                                            <td class="p-3 font-bold">{{ data.initiating + data.executing }}</td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="bg-gray-100 font-bold text-sm">
                                        <tr>
                                            <td class="p-3 text-left">Total Pekerjaan</td>
                                            <td class="p-3">{{ weeklySummaryTotals.masuk }}</td>
                                            <td class="p-3">{{ weeklySummaryTotals.selesai }}</td>
                                            <td class="p-3">{{ weeklySummaryTotals.initiating }}</td>
                                            <td class="p-3">{{ weeklySummaryTotals.executing }}</td>
                                            <td class="p-3">{{ weeklySummaryTotals.inProgressTotal }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </Card>
                    </div>

                    <!-- <section>
                        <div
                            class="bg-yellow-200 border-l-4 border-yellow-500 text-yellow-700 p-4 text-center mb-4 rounded-r-lg">
                            <h2 class="font-bold">MONITORING KPI UNIT SITE ENGINEERING SEMESTER {{
                                kpiMonitoring.currentSemester }} TAHUN {{ filters.year }}</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <Card v-for="(data, workType) in kpiMonitoring.data" :key="workType">
                                <div class="text-center space-y-1">
                                    <h3 class="font-bold text-gray-800">{{ workType }}</h3>
                                    <p class="text-xs text-gray-600">Desain & Engineering</p>
                                    <p class="text-sm font-semibold text-red-600">Periode {{ data.period_start }} s/d {{
                                        data.period_end }}</p>
                                </div>
                                <GaugeChart :percentage="data.percentage" :target="85" />
                            </Card>
                        </div>
                    </section> -->
                </div>
            </div>
        </div>
    </div>
</template>
