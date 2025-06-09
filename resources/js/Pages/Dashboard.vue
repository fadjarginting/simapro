<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, computed, onMounted, onUnmounted } from "vue";

// Import Komponen
import Card from "@/Components/Card.vue";
import LineChart from "@/Components/LineChart.vue";
import PieChart from "@/Components/PieChart.vue";
import BarChart from "@/Components/BarChart.vue";
import MultiBarChart from "@/Components/MultiBarChart.vue"; // Pastikan Anda membuat komponen ini

// Tentukan layout
defineOptions({
    layout: AuthenticatedLayout,
});

import { defineProps } from "vue";
defineProps({
    header: String,
});

// Real-time tanggal dan waktu
const currentDateTime = ref(new Date());
const timerInterval = ref(null);

// Update waktu setiap detik
onMounted(() => {
    timerInterval.value = setInterval(() => {
        currentDateTime.value = new Date();
    }, 1000);
});

// Cleanup interval saat komponen dihapus
onUnmounted(() => {
    if (timerInterval.value) {
        clearInterval(timerInterval.value);
    }
});

// Format tanggal dan waktu
const formattedDate = computed(() => {
    return `${currentDateTime.value.getDate()} ${getMonthName(
        currentDateTime.value.getMonth()
    )} ${currentDateTime.value.getFullYear()}`;
});

const formattedTime = computed(() => {
    return `${currentDateTime.value
        .getHours()
        .toString()
        .padStart(2, "0")}:${currentDateTime.value
        .getMinutes()
        .toString()
        .padStart(2, "0")} WIB`;
});

function getMonthName(month) {
    const months = [
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember",
    ];
    return months[month];
}

// State untuk filter tahun
const selectedYear = ref("2025");
const yearOptions = ["2023", "2024", "2025", "All"];

// Data chart dan warna
const lineData2023 = [
    { name: "Jan", value: 20 },
    { name: "Feb", value: 30 },
    { name: "Mar", value: 25 },
    { name: "Apr", value: 40 },
    { name: "May", value: 45 },
    { name: "Jun", value: 50 },
    { name: "Jul", value: 55 },
    { name: "Aug", value: 60 },
    { name: "Sep", value: 65 },
    { name: "Oct", value: 70 },
    { name: "Nov", value: 75 },
    { name: "Dec", value: 80 },
];

const lineData2024 = [
    { name: "Jan", value: 25 },
    { name: "Feb", value: 40 },
    { name: "Mar", value: 35 },
    { name: "Apr", value: 55 },
    { name: "May", value: 50 },
    { name: "Jun", value: 65 },
    { name: "Jul", value: 70 },
    { name: "Aug", value: 80 },
    { name: "Sep", value: 85 },
    { name: "Oct", value: 95 },
    { name: "Nov", value: 100 },
    { name: "Dec", value: 110 },
];

const lineData2025 = [
    { name: "Jan", value: 30 },
    { name: "Feb", value: 50 },
    { name: "Mar", value: 40 },
    { name: "Apr", value: 70 },
    { name: "May", value: 60 },
    { name: "Jun", value: 80 },
    { name: "Jul", value: 90 },
    { name: "Aug", value: 100 },
    { name: "Sep", value: 100 },
    { name: "Oct", value: 120 },
    { name: "Nov", value: 125 },
    { name: "Dec", value: 130 },
];

// Filter data berdasarkan tahun yang dipilih
const lineChartData = computed(() => {
    if (selectedYear.value === "All") {
        return {
            multiSeries: true,
            series: [
                { name: "2023", data: lineData2023 },
                { name: "2024", data: lineData2024 },
                { name: "2025", data: lineData2025 },
            ],
        };
    } else if (selectedYear.value === "2023") {
        return lineData2023;
    } else if (selectedYear.value === "2024") {
        return lineData2024;
    } else {
        return lineData2025;
    }
});

// Data untuk berbagai chart
const workStatusData = [
    { name: "Cancel", value: 35 },
    { name: "Finish", value: 45 },
    { name: "Hold", value: 15 },
    { name: "In Progress", value: 5 },
];

const plantTypeData = [
    { name: "Kajian Engineering", value: 30 },
    { name: "FEED/DED", value: 55 },
    { name: "Technical Assist", value: 20 },
];

const workData = [
    { name: "In Progress", value: 78.9 },
    { name: "Closing", value: 4.5 },
    { name: "Hold", value: 1.1 },
    { name: "Reject", value: 1.1 },
];

const departmentData = [
    { name: "Executing,", value: 20.24 },
    { name: "Initiating", value: 58.69 },
];

// Data untuk masing-masing Plant Engineering
const plantEngineeringData = [
    { name: "Initiating", value: 55 },
    { name: "Executing", value: 25 },
    { name: "Closing", value: 12 },
    { name: "Hold", value: 5 },
    { name: "Reject", value: 3 },
];

// Data untuk Digital Data HR
const digitalHRData = [
    { name: "Initiating", value: 50 },
    { name: "Executing", value: 30 },
    { name: "Closing", value: 15 },
    { name: "Hold", value: 3 },
    { name: "Reject", value: 2 },
];

// Data untuk Technical Audit
const technicalAuditData = [
    { name: "Initiating", value: 45 },
    { name: "Executing", value: 35 },
    { name: "Closing", value: 10 },
    { name: "Hold", value: 6 },
    { name: "Reject", value: 4 },
];

// Data untuk multi-bar chart
const multiBarData = {
    categories: [
        "Tambang",
        "Indarung II/III",
        "Indarung IV",
        "Indarung V",
        "Indarung VI",
        "PPI",
        "PP MALAHAYATI",
        "GP DUMAI",
    ],
    series: [
        {
            name: "Initiating",
            data: [35, 28, 15, 22, 30, 25, 18, 40],
            color: "#6A39F7",
        },
        {
            name: "Executing",
            data: [20, 15, 10, 12, 18, 15, 10, 22],
            color: "#44BFD6",
        },
        { name: "Hold", data: [8, 5, 3, 6, 10, 8, 5, 12], color: "#93CAED" },
        { name: "Closing", data: [2, 3, 2, 1, 4, 3, 2, 5], color: "#FF0800" },
        { name: "Reject", data: [1, 2, 1, 1, 2, 1, 1, 1], color: "#FF0800" },
        { name: "Total", data: [3, 4, 2, 5, 6, 4, 3, 5], color: "#FF8042" },
    ],
};

const COLORS = ["#6A39F7", "#44BFD6", "#FF8042", "#FF0800"];
const PIE_COLORS = ["#6A39F7", "#44BFD6", "#93CAED", "#CABFEB"];
</script>

<template>
    <Head title="Dashboard" />

    <div class="py-6">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                <!-- Header dengan tanggal dan waktu -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">
                        Dashboard Monitoring Admin
                    </h1>
                    <div class="text-right text-gray-600">
                        <div class="text-lg font-semibold">
                            {{ formattedDate }}
                        </div>
                        <div>{{ formattedTime }}</div>
                    </div>
                </div>

                <!-- Bagian Kartu Statistik dengan icon -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <!-- Total Work Card -->
                    <div
                        class="bg-white rounded-lg shadow p-6 border border-gray-200"
                    >
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <p class="text-sm text-gray-500">Total Work</p>
                                <h2 class="text-3xl font-bold text-gray-800">
                                    129
                                </h2>
                            </div>
                            <div class="bg-yellow-100 p-4 rounded-lg">
                                <!-- Package Icon -->
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6 text-yellow-500"
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"
                                    ></path>
                                    <path
                                        d="m3 9 2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9"
                                    ></path>
                                    <path d="M12 3v6"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-center text-green-600 text-sm">
                            <!-- Trending Up Icon -->
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 mr-1"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="m23 6-9.5 9.5-5-5L1 18"></path>
                                <path d="M17 6h6v6"></path>
                            </svg>
                            <span>5.5% up from past week</span>
                        </div>
                    </div>

                    <!-- In-Progress Work Card -->
                    <div
                        class="bg-white rounded-lg shadow p-6 border border-gray-200"
                    >
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <p class="text-sm text-gray-500">
                                    In-Progress / Ongoing Work
                                </p>
                                <h2 class="text-3xl font-bold text-gray-800">
                                    45
                                </h2>
                            </div>
                            <div class="bg-blue-100 p-4 rounded-lg">
                                <!-- FileText Icon -->
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6 text-blue-500"
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path
                                        d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"
                                    ></path>
                                    <polyline
                                        points="14 2 14 8 20 8"
                                    ></polyline>
                                    <line x1="16" y1="13" x2="8" y2="13"></line>
                                    <line x1="16" y1="17" x2="8" y2="17"></line>
                                    <line x1="10" y1="9" x2="8" y2="9"></line>
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-center text-blue-600 text-sm">
                            <!-- Trending Up Icon -->
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 mr-1"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="m23 6-9.5 9.5-5-5L1 18"></path>
                                <path d="M17 6h6v6"></path>
                            </svg>
                            <span>1% up from yesterday</span>
                        </div>
                    </div>

                    <!-- Work Overdue Card -->
                    <div
                        class="bg-white rounded-lg shadow p-6 border border-gray-200"
                    >
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <p class="text-sm text-gray-500">
                                    Work Overdue
                                </p>
                                <h2 class="text-3xl font-bold text-gray-800">
                                    11
                                </h2>
                            </div>
                            <div class="bg-red-100 p-4 rounded-lg">
                                <!-- Clock Icon -->
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6 text-red-500"
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline
                                        points="12 6 12 12 16 14"
                                    ></polyline>
                                </svg>
                            </div>
                        </div>
                        <div class="flex items-center text-green-600 text-sm">
                            <!-- Trending Up Icon -->
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="w-4 h-4 mr-1"
                                width="24"
                                height="24"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="m23 6-9.5 9.5-5-5L1 18"></path>
                                <path d="M17 6h6v6"></path>
                            </svg>
                            <span>0.5% up from yesterday</span>
                        </div>
                    </div>
                </div>

                <!-- Bagian Chart Utama dengan ukuran yang tepat -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <Card class="h-auto flex flex-col">
                        <div class="flex justify-between items-center mb-2">
                            <h2 class="text-xl font-bold">
                                Total Work by Month
                            </h2>
                            <select
                                v-model="selectedYear"
                                class="border rounded px-2 py-1 text-sm"
                            >
                                <option
                                    v-for="year in yearOptions"
                                    :key="year"
                                    :value="year"
                                >
                                    {{ year }}
                                </option>
                            </select>
                        </div>
                        <div class="flex-grow" style="height: 300px">
                            <LineChart
                                :chart-data="lineChartData"
                                :height="300"
                            />
                        </div>
                    </Card>
                    <Card class="h-auto flex flex-col">
                        <h2 class="text-xl font-bold mb-2">
                            Total Work by Status
                        </h2>
                        <div
                            class="flex-grow flex items-center justify-center"
                            style="min-height: 300px"
                        >
                            <PieChart
                                :chart-data="workStatusData"
                                :colors="PIE_COLORS"
                                :height="280"
                            />
                        </div>
                    </Card>
                </div>

                <!-- Chart Row 2 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <Card class="h-auto flex flex-col">
                        <h2 class="text-xl font-bold mb-2">
                            Detail Stats Job Kajian Engineering
                        </h2>
                        <div class="text-sm text-gray-500 mb-2">
                            Total Pekerjaan: 23 ERF
                        </div>
                        <div class="flex-grow" style="height: 300px">
                            <BarChart
                                :chart-data="plantEngineeringData"
                                :height="300"
                            />
                        </div>
                    </Card>
                    <Card class="h-auto flex flex-col">
                        <h2 class="text-xl font-bold mb-2">
                            Detail Stats Job FEED/DED
                        </h2>
                        <div class="text-sm text-gray-500 mb-2">
                            Total Pekerjaan: 53 ERF
                        </div>
                        <div class="flex-grow" style="height: 300px">
                            <BarChart
                                :chart-data="digitalHRData"
                                :height="300"
                            />
                        </div>
                    </Card>
                </div>

                <!-- Chart Row 3 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <Card class="h-auto flex flex-col">
                        <h2 class="text-xl font-bold mb-2">
                            Detail Stats Job Technical Assist
                        </h2>
                        <div class="text-sm text-gray-500 mb-2">
                            Total Pekerjaan: 8 ERF
                        </div>
                        <div class="flex-grow" style="height: 300px">
                            <BarChart
                                :chart-data="technicalAuditData"
                                :height="300"
                            />
                        </div>
                    </Card>
                    <Card class="h-auto flex flex-col">
                        <h2 class="text-xl font-bold mb-2">
                            Total Pekerjaan Berdasarkan Jenis Pekerjaan
                        </h2>
                        <div class="text-sm text-gray-500 mb-2">
                            Total Pekerjaan: 84 ERF
                        </div>
                        <div
                            class="flex-grow flex items-center justify-center"
                            style="min-height: 300px"
                        >
                            <PieChart
                                :chart-data="plantTypeData"
                                :colors="PIE_COLORS"
                                :height="300"
                            />
                        </div>
                    </Card>
                </div>

                <!-- Chart tambahan - PieChart sejajar -->
                <div class="mb-6">
                    <Card class="h-auto flex flex-col">
                        <h2 class="text-xl font-bold mb-2">
                            Total Pekerjaan Berdasarkan Status
                        </h2>
                        <div class="text-sm text-gray-500 mb-2">
                            Status, Total, %Total
                        </div>
                        <div class="text-sm text-gray-500 mb-2">
                            Total Pekerjaan = 84 ERF
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div
                                class="flex-grow flex items-center justify-center"
                                style="min-height: 300px"
                            >
                                <PieChart
                                    :chart-data="workData"
                                    :colors="PIE_COLORS"
                                    :height="250"
                                />
                            </div>
                            <div
                                class="flex-grow flex items-center justify-center"
                                style="min-height: 300px"
                            >
                                <PieChart
                                    :chart-data="departmentData"
                                    :colors="PIE_COLORS"
                                    :height="250"
                                />
                            </div>
                        </div>
                    </Card>
                </div>

                <!-- Multi-bar Chart -->
                <div class="mb-6">
                    <Card class="h-auto flex flex-col">
                        <h2 class="text-xl font-bold mb-2">
                            DETAIL PEKERJAAN BERDASARKAN STATUS PER
                            MASING-MASING PLANT
                        </h2>
                        <div class="flex flex-wrap gap-4 mb-2">
                            <div class="flex items-center">
                                <div
                                    class="w-4 h-4 bg-purple-600 rounded mr-1"
                                ></div>
                                <span class="text-sm">Working</span>
                            </div>
                            <div class="flex items-center">
                                <div
                                    class="w-4 h-4 bg-blue-400 rounded mr-1"
                                ></div>
                                <span class="text-sm">Planning</span>
                            </div>
                            <div class="flex items-center">
                                <div
                                    class="w-4 h-4 bg-blue-200 rounded mr-1"
                                ></div>
                                <span class="text-sm">Hold</span>
                            </div>
                            <div class="flex items-center">
                                <div
                                    class="w-4 h-4 bg-red-500 rounded mr-1"
                                ></div>
                                <span class="text-sm">Cancel</span>
                            </div>
                        </div>
                        <div class="flex-grow" style="height: 350px">
                            <MultiBarChart :data="multiBarData" :height="350" />
                        </div>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</template>
