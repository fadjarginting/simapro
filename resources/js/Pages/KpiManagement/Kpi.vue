<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";

defineOptions({
    layout: AuthenticatedLayout,
});

// Data untuk KPI monitoring
const kpiData = ref([
    {
        id: 1,
        title: "% Penyelesaian FEED - DED Design & Engineering",
        period: "Periode 01 January 25 s/d 05 May 25",
        percentage: 18.75,
        target: 85.0,
    },
    {
        id: 2,
        title: "% Penyelesaian Kajian Teknis Design & Engineering",
        period: "Periode 01 January 25 s/d 05 May 25",
        percentage: 0,
        target: 85.0,
    },
    {
        id: 3,
        title: "% Penyelesaian Technical Assist",
        period: "Periode 01 January 25 s/d 05 May 25",
        percentage: 33.33,
        target: 85.0,
    },
]);

// Data untuk ERP items (keeping this from the original code)
const erpItems = ref([
    {
        id: 1,
        no: 1,
        jobTitle: "Stock Opname Material Local Support",
        urPekerjaan: "Unit Ins DP",
        load: "ALIAS PRADANA",
        tglErpTerkirim: "22-Oct-16",
        tglSelesaiTarget: "23-Jan-25",
        faseExecutionTarget: "(blank)",
        statusVerifikasi: "Finish Verifikasi",
        statusPull: "In Progress",
    },
    {
        id: 2,
        no: 2,
        jobTitle: "Stock Opname Material Local Support",
        urPekerjaan: "Unit Ins DP",
        load: "ALIAS PRADANA",
        tglErpTerkirim: "22-Oct-24",
        tglSelesaiTarget: "23-Jan-25",
        faseExecutionTarget: "(blank)",
        statusVerifikasi: "Finish Verifikasi",
        statusPull: "In Progress",
    },
    {
        id: 3,
        no: 3,
        jobTitle: "Stock Opname Material Local Boiler",
        urPekerjaan: "Unit Ins DP",
        load: "ALIAS PRADANA",
        tglErpTerkirim: "22-Oct-24",
        tglSelesaiTarget: "23-Jan-25",
        faseExecutionTarget: "(blank)",
        statusVerifikasi: "Finish Verifikasi",
        statusPull: "In Progress",
    },
    {
        id: 4,
        no: 4,
        jobTitle: "Stock Opname Material Local Boiler",
        urPekerjaan: "Unit Ins DP",
        load: "ALIAS PRADANA",
        tglErpTerkirim: "22-Oct-16",
        tglSelesaiTarget: "23-Jan-25",
        faseExecutionTarget: "(blank)",
        statusVerifikasi: "Finish Verifikasi",
        statusPull: "In Progress",
    },
]);

// Pagination untuk ERP
const erpCurrentPage = ref(1);
const erpItemsPerPage = 10;

const paginatedErpItems = computed(() => {
    const start = (erpCurrentPage.value - 1) * erpItemsPerPage;
    const end = start + erpItemsPerPage;
    return erpItems.value.slice(start, end);
});

const erpTotalPages = computed(() => {
    return Math.ceil(erpItems.value.length / erpItemsPerPage);
});

function prevErpPage() {
    if (erpCurrentPage.value > 1) {
        erpCurrentPage.value--;
    }
}

function nextErpPage() {
    if (erpCurrentPage.value < erpTotalPages.value) {
        erpCurrentPage.value++;
    }
}

// Function to draw gauge charts
function drawGaugeCharts() {
    kpiData.value.forEach((item, index) => {
        const canvas = document.getElementById(`gauge-chart-${index}`);
        if (canvas) {
            const ctx = canvas.getContext("2d");
            const centerX = canvas.width / 2;
            const centerY = canvas.height - 30;
            const radius = Math.min(centerX, centerY) - 10;

            // Clear canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Draw gray background arc (full gauge)
            ctx.beginPath();
            ctx.arc(centerX, centerY, radius, Math.PI, 0, false);
            ctx.lineWidth = 40;
            ctx.strokeStyle = "#E5E5E5";
            ctx.stroke();

            // Draw green progress arc
            const percentage = item.percentage / 100;
            const startAngle = Math.PI;
            const endAngle = Math.PI - Math.PI * percentage;

            ctx.beginPath();
            ctx.arc(centerX, centerY, radius, startAngle, endAngle, true);
            ctx.lineWidth = 40;
            ctx.strokeStyle = "#8BC34A";
            ctx.stroke();

            // Draw percentage text
            ctx.font = "bold 30px Arial";
            ctx.fillStyle = percentage > 0 ? "#4CAF50" : "#000000";
            ctx.textAlign = "center";
            ctx.fillText(`${item.percentage}%`, centerX, centerY);

            // Draw target indicator line and text
            const targetAngle = Math.PI - Math.PI * (item.target / 100);
            const targetX1 = centerX + (radius - 30) * Math.cos(targetAngle);
            const targetY1 = centerY + (radius - 30) * Math.sin(targetAngle);
            const targetX2 = centerX + (radius + 30) * Math.cos(targetAngle);
            const targetY2 = centerY + (radius + 30) * Math.sin(targetAngle);

            ctx.beginPath();
            ctx.moveTo(targetX1, targetY1);
            ctx.lineTo(targetX2, targetY2);
            ctx.lineWidth = 2;
            ctx.strokeStyle = "#000000";
            ctx.stroke();

            // Target text
            ctx.font = "12px Arial";
            ctx.fillStyle = "#000000";
            ctx.textAlign = "left";
            ctx.fillText(Target, targetX2 + 5, targetY2 - 10);
            ctx.font = "bold 12px Arial";
            ctx.fillText(
                `${item.target.toFixed(2)}%`,
                targetX2 + 5,
                targetY2 + 5
            );
        }
    });
}

onMounted(() => {
    // Draw gauge charts when component is mounted
    setTimeout(drawGaugeCharts, 100);

    // Redraw on window resize
    window.addEventListener("resize", drawGaugeCharts);
});
</script>

<template>
    <Head title="KPI Monitoring" />

    <template name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            KPI Monitoring
        </h2>
    </template>

    <div class="container mx-auto px-4 py-6">
        <!-- Header Banner -->
        <div class="w-full mb-4">
            <div class="grid grid-cols-12">
                <!-- Summary header -->
                <div
                    class="col-span-8 bg-teal-600 text-white text-center py-2 font-bold"
                >
                    SUMMARY PEKERJAAN UNIT SITE ENGINEERING
                </div>
                <!-- Status header -->
                <div
                    class="col-span-3 bg-green-700 text-white text-center py-2 font-bold"
                >
                    Status Minggu ke- W2 Mar-25
                </div>
                <!-- Date header -->
                <div
                    class="col-span-1 bg-orange-500 text-white text-center py-2 font-bold"
                >
                    5-May-25
                </div>
            </div>
        </div>

        <!-- KPI Title Banner -->
        <div class="w-full mb-4 bg-green-100 border border-green-200 py-2 px-4">
            <h2 class="text-center font-bold text-black">
                KPI MONITORING UNIT SITE ENGINEERING SEMESTER 1 TAHUN 2025
            </h2>
        </div>

        <!-- KPI Gauge Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Loop through KPI data -->
            <div
                v-for="(item, index) in kpiData"
                :key="item.id"
                class="border border-gray-200 rounded-lg p-4 bg-white shadow-sm"
            >
                <!-- Card title -->
                <div class="text-center mb-2">
                    <h3 class="font-bold text-sm">{{ item.title }}</h3>
                    <p class="text-sm text-red-600">{{ item.period }}</p>
                </div>

                <!-- Gauge chart -->
                <div class="flex justify-center">
                    <canvas
                        :id="`gauge-chart-${index}`"
                        width="300"
                        height="200"
                    ></canvas>
                </div>
            </div>
        </div>

        <!-- ERP Table -->
        <div class="mx-auto max-w-full sm:px-6 lg:px-8">
            <div class="flex-none w-full max-w-full">
                <div
                    class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl sm:rounded-lg bg-clip-border"
                >
                    <div
                        class="flex items-center justify-between p-6 pb-0 mb-0 border-b-0 border-b-solid border-b-transparent"
                    >
                        <!-- Judul Tabel -->
                        <h1 class="text-2xl font-bold text-gray-900">
                            KEY PERFORMANCE INDICATOR
                        </h1>
                    </div>
                    <div
                        class="flex items-center justify-between p-6 pb-0 mb-0 border-b-0 border-b-solid border-b-transparent"
                    >
                        <!-- Judul Tabel -->
                        <h6 class="dark:text-white text-base font-bold">
                            KPI MONITORING S1 2025
                        </h6>
                    </div>

                    <div class="flex-auto px-0 pt-0 pb-2">
                        <div class="p-0 overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead class="bg-gray-50">
                                    <tr
                                        class="text-sm font-normal text-gray-600 border-t border-b text-left"
                                    >
                                        <th class="px-4 py-3">No</th>
                                        <th class="px-4 py-3">Job Title</th>
                                        <th class="px-4 py-3">UK Peminta</th>
                                        <th class="px-4 py-3">Lead</th>
                                        <th class="px-4 py-3">
                                            Tgl. ERF Disetujui
                                        </th>
                                        <th class="px-4 py-3">
                                            Fase Initiating Target Selesai
                                        </th>
                                        <th class="px-4 py-3">
                                            Fase Executing & Closing Target
                                            Selesai
                                        </th>
                                        <th class="px-4 py-3">
                                            Status Verifikasi ERF
                                        </th>
                                        <th class="px-4 py-3">
                                            Status Pek. Engineering
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="text-sm font-normal text-gray-700"
                                >
                                    <tr
                                        v-for="item in paginatedErpItems"
                                        :key="item.id"
                                        class="border-b border-gray-200 hover:bg-gray-100"
                                    >
                                        <td class="px-4">
                                            <div class="flex-1">
                                                <div
                                                    class="font-medium dark:text-white"
                                                >
                                                    {{ item.no }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4">
                                            <div class="flex-1">
                                                <div
                                                    class="font-medium dark:text-white"
                                                >
                                                    {{ item.jobTitle }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4">
                                            <div class="flex-1">
                                                <div
                                                    class="font-medium dark:text-white"
                                                >
                                                    {{ item.urPekerjaan }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4">
                                            <div class="flex-1">
                                                <div
                                                    class="font-medium dark:text-white"
                                                >
                                                    {{ item.load }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4">
                                            <div class="flex-1">
                                                <div
                                                    class="font-medium dark:text-white"
                                                >
                                                    {{ item.tglErpTerkirim }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4">
                                            <div class="flex-1">
                                                <div
                                                    class="font-medium dark:text-white"
                                                >
                                                    {{ item.tglSelesaiTarget }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4">
                                            <div class="flex-1">
                                                <div
                                                    class="font-medium dark:text-white"
                                                >
                                                    {{
                                                        item.faseExecutionTarget
                                                    }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4">
                                            <div class="flex-1">
                                                <div
                                                    class="font-medium dark:text-white"
                                                >
                                                    {{ item.statusVerifikasi }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4">
                                            <div class="flex-1">
                                                <div
                                                    class="font-medium dark:text-white"
                                                >
                                                    {{ item.statusPull }}
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- pagination -->
                        <div
                            class="flex justify-between items-center p-4 bg-gray-50"
                        >
                            <button
                                @click="prevErpPage"
                                :disabled="erpCurrentPage === 1"
                                class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-blue-500 transition duration-300 hover:bg-gradient-to-tl hover:from-blue-500 hover:to-blue-400 hover:text-white disabled:opacity-50"
                            >
                                Previous
                            </button>
                            <span class="text-sm text-gray-700">
                                Page {{ erpCurrentPage }} of {{ erpTotalPages }}
                            </span>
                            <button
                                @click="nextErpPage"
                                :disabled="erpCurrentPage === erpTotalPages"
                                class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-blue-500 transition duration-300 hover:bg-gradient-to-tl hover:from-blue-500 hover:to-blue-400 hover:text-white disabled:opacity-50"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
