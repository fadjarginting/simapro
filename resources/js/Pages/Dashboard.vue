<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, computed } from "vue";

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

// Tanggal dan waktu
const currentDate = new Date();
const formattedDate = `${currentDate.getDate()} ${getMonthName(currentDate.getMonth())} ${currentDate.getFullYear()}`;
const formattedTime = `${currentDate.getHours().toString().padStart(2, '0')}:${currentDate.getMinutes().toString().padStart(2, '0')} WIB`;

function getMonthName(month) {
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    return months[month];
}

// State untuk filter tahun
const selectedYear = ref("2025");
const yearOptions = ["2023", "2024", "2025", "All"];

// Data chart dan warna
const lineData2023 = [
    { name: "Jan", value: 20 }, { name: "Feb", value: 30 }, { name: "Mar", value: 25 },
    { name: "Apr", value: 40 }, { name: "May", value: 45 }, { name: "Jun", value: 50 },
    { name: "Jul", value: 55 }, { name: "Aug", value: 60 }, { name: "Sep", value: 65 },
    { name: "Oct", value: 70 }, { name: "Nov", value: 75 }, { name: "Dec", value: 80 },
];

const lineData2024 = [
    { name: "Jan", value: 25 }, { name: "Feb", value: 40 }, { name: "Mar", value: 35 },
    { name: "Apr", value: 55 }, { name: "May", value: 50 }, { name: "Jun", value: 65 },
    { name: "Jul", value: 70 }, { name: "Aug", value: 80 }, { name: "Sep", value: 85 },
    { name: "Oct", value: 95 }, { name: "Nov", value: 100 }, { name: "Dec", value: 110 },
];

const lineData2025 = [
    { name: "Jan", value: 30 }, { name: "Feb", value: 50 }, { name: "Mar", value: 40 },
    { name: "Apr", value: 70 }, { name: "May", value: 60 }, { name: "Jun", value: 80 },
    { name: "Jul", value: 90 }, { name: "Aug", value: 100 }, { name: "Sep", value: 110 },
    { name: "Oct", value: 120 }, { name: "Nov", value: 130 }, { name: "Dec", value: 140 },
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
            ]
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
    { name: "Plant A", value: 40 },
    { name: "Plant B", value: 30 },
    { name: "Plant C", value: 20 },
    { name: "Plant D", value: 10 },
];

const departmentData = [
    { name: "Production", value: 45 },
    { name: "HR", value: 15 },
    { name: "Engineering", value: 25 },
    { name: "QA", value: 15 },
];

// Data untuk masing-masing Plant Engineering
const plantEngineeringData = [
    { name: "Working", value: 55 },
    { name: "Planning", value: 25 },
    { name: "Closing", value: 12 },
    { name: "Hold", value: 5 },
    { name: "Cancel", value: 3 },
];

// Data untuk Digital Data HR
const digitalHRData = [
    { name: "Working", value: 50 },
    { name: "Planning", value: 30 },
    { name: "Closing", value: 15 },
    { name: "Hold", value: 3 },
    { name: "Cancel", value: 2 },
];

// Data untuk Technical Audit
const technicalAuditData = [
    { name: "Working", value: 45 },
    { name: "Planning", value: 35 },
    { name: "Closing", value: 10 },
    { name: "Hold", value: 6 },
    { name: "Cancel", value: 4 },
];

// Data untuk multi-bar chart
const multiBarData = {
    categories: ["Plant A", "Plant B", "Plant C", "Plant D", "Plant E", "Plant F", "Plant G", "Plant H"],
    series: [
        { name: "Working", data: [35, 28, 15, 22, 30, 25, 18, 40], color: "#6A39F7" },
        { name: "Planning", data: [20, 15, 10, 12, 18, 15, 10, 22], color: "#44BFD6" },
        { name: "Hold", data: [8, 5, 3, 6, 10, 8, 5, 12], color: "#93CAED" },
        { name: "Cancel", data: [2, 3, 2, 1, 4, 3, 2, 5], color: "#FF0800" }
    ]
};

const COLORS = ["#6A39F7", "#44BFD6", "#FF8042", "#FF0800"];
const PIE_COLORS = ["#6A39F7", "#44BFD6", "#93CAED", "#CABFEB"];
</script>

<template>
    <Head title="Dashboard" />

    <div class="py-6">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg p-6">
                <!-- Header dengan tanggal dan waktu -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Dashboard Monitoring Admin</h1>
                    <div class="text-right text-gray-600">
                        <div class="text-lg font-semibold">{{ formattedDate }}</div>
                        <div>{{ formattedTime }}</div>
                    </div>
                </div>

                <!-- Bagian Kartu Statistik -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <Card title="Total Work" :value="129">
                        <div class="flex items-center text-green-600 text-sm mt-2">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            <span>5.5% up from past week</span>
                        </div>
                    </Card>
                    <Card title="In Progress / Ongoing Work" :value="45">
                        <div class="flex items-center text-blue-600 text-sm mt-2">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            <span>1% up from yesterday</span>
                        </div>
                    </Card>
                    <Card title="Work Overdue" :value="11">
                        <div class="flex items-center text-green-600 text-sm mt-2">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                            </svg>
                            <span>0.5% up from yesterday</span>
                        </div>
                    </Card>
                </div>

                <!-- Bagian Chart Utama dengan ukuran yang tepat -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <Card class="h-auto flex flex-col">
                        <div class="flex justify-between items-center mb-2">
                            <h2 class="text-xl font-bold">Total Work by Month</h2>
                            <select v-model="selectedYear" class="border rounded px-2 py-1 text-sm">
                                <option v-for="year in yearOptions" :key="year" :value="year">{{ year }}</option>
                            </select>
                        </div>
                        <div class="flex-grow" style="height: 300px;">
                            <LineChart :chart-data="lineChartData" :height="300" />
                        </div>
                    </Card>
                    <Card class="h-auto flex flex-col">
                        <h2 class="text-xl font-bold mb-2">Total Work by Status</h2>
                        <div class="flex-grow flex items-center justify-center" style="min-height: 300px;">
                            <PieChart :chart-data="workStatusData" :colors="PIE_COLORS" :height="280" />
                        </div>
                    </Card>
                </div>

                <!-- Chart Row 2 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <Card class="h-auto flex flex-col">
                        <h2 class="text-xl font-bold mb-2">Plant Work Job Engineering</h2>
                        <div class="text-sm text-gray-500 mb-2">Total Pekerjaan: 12,809</div>
                        <div class="flex-grow" style="height: 300px;">
                            <BarChart :chart-data="plantEngineeringData" :height="300" />
                        </div>
                    </Card>
                    <Card class="h-auto flex flex-col">
                        <h2 class="text-xl font-bold mb-2">Digital Data Job HR</h2>
                        <div class="text-sm text-gray-500 mb-2">Total Pekerjaan: 10,680</div>
                        <div class="flex-grow" style="height: 300px;">
                            <BarChart :chart-data="digitalHRData" :height="300" />
                        </div>
                    </Card>
                </div>

                <!-- Chart Row 3 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <Card class="h-auto flex flex-col">
                        <h2 class="text-xl font-bold mb-2">Detail Work Job Technical Audit</h2>
                        <div class="text-sm text-gray-500 mb-2">Total Pekerjaan: 8,459</div>
                        <div class="flex-grow" style="height: 300px;">
                            <BarChart :chart-data="technicalAuditData" :height="300" />
                        </div>
                    </Card>
                    <Card class="h-auto flex flex-col">
                        <h2 class="text-xl font-bold mb-2">Total Pekerjaan Berdasarkan Plant</h2>
                        <div class="text-sm text-gray-500 mb-2">Total Pekerjaan: 34,879</div>
                        <div class="flex-grow flex items-center justify-center" style="min-height: 300px;">
                            <PieChart :chart-data="plantTypeData" :colors="PIE_COLORS" :height="300" />
                        </div>
                    </Card>
                </div>

                <!-- Chart tambahan - PieChart sejajar -->
                <div class="mb-6">
                    <Card class="h-auto flex flex-col">
                        <h2 class="text-xl font-bold mb-2">Total Pekerjaan Berdasarkan Plant Tahun Ini</h2>
                        <div class="text-sm text-gray-500 mb-2">Plant Per Bulan</div>
                        <div class="text-sm text-gray-500 mb-2">Total Pekerjaan: 34,679</div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex-grow flex items-center justify-center" style="min-height: 300px;">
                                <PieChart :chart-data="plantTypeData" :colors="PIE_COLORS" :height="250" />
                            </div>
                            <div class="flex-grow flex items-center justify-center" style="min-height: 300px;">
                                <PieChart :chart-data="departmentData" :colors="PIE_COLORS" :height="250" />
                            </div>
                        </div>
                    </Card>
                </div>

                <!-- Multi-bar Chart -->
                <div class="mb-6">
                    <Card class="h-auto flex flex-col">
                        <h2 class="text-xl font-bold mb-2">DETAIL PEKERJAAN BERDASARKAN STATUS PER MASING-MASING PLANT</h2>
                        <div class="flex flex-wrap gap-4 mb-2">
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-purple-600 rounded mr-1"></div>
                                <span class="text-sm">Working</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-blue-400 rounded mr-1"></div>
                                <span class="text-sm">Planning</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-blue-200 rounded mr-1"></div>
                                <span class="text-sm">Hold</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-4 h-4 bg-red-500 rounded mr-1"></div>
                                <span class="text-sm">Cancel</span>
                            </div>
                        </div>
                        <div class="flex-grow" style="height: 350px;">
                            <!-- MultiBarChart komponennya -->
                            <MultiBarChart :data="multiBarData" :height="350" />
                        </div>
                    </Card>
                </div>
            </div>
        </div>
    </div>
</template>