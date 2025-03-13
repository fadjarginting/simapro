<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref, computed } from "vue";

defineOptions({
    layout: AuthenticatedLayout,
});

// Data untuk ERP items
const erpItems = ref([
    {
        id: 1,
        no: 1,
        jobTitle: 'Stock Opname Material Local Support',
        urPekerjaan: 'Unit Ins DP',
        load: 'ALIAS PRADANA',
        tglErpTerkirim: '22-Oct-16',
        tglSelesaiTarget: '23-Jan-25',
        faseExecutionTarget: '(blank)',
        statusVerifikasi: 'Finish Verifikasi',
        statusPull: 'In Progress'
    },
    {
        id: 2,
        no: 2,
        jobTitle: 'Stock Opname Material Local Support',
        urPekerjaan: 'Unit Ins DP',
        load: 'ALIAS PRADANA',
        tglErpTerkirim: '22-Oct-24',
        tglSelesaiTarget: '23-Jan-25',
        faseExecutionTarget: '(blank)',
        statusVerifikasi: 'Finish Verifikasi',
        statusPull: 'In Progress'
    },
    {
        id: 3,
        no: 3,
        jobTitle: 'Stock Opname Material Local Boiler',
        urPekerjaan: 'Unit Ins DP',
        load: 'ALIAS PRADANA',
        tglErpTerkirim: '22-Oct-24',
        tglSelesaiTarget: '23-Jan-25',
        faseExecutionTarget: '(blank)',
        statusVerifikasi: 'Finish Verifikasi',
        statusPull: 'In Progress'
    },
    {
        id: 4,
        no: 4,
        jobTitle: 'Stock Opname Material Local Boiler',
        urPekerjaan: 'Unit Ins DP',
        load: 'ALIAS PRADANA',
        tglErpTerkirim: '22-Oct-16',
        tglSelesaiTarget: '23-Jan-25',
        faseExecutionTarget: '(blank)',
        statusVerifikasi: 'Finish Verifikasi',
        statusPull: 'In Progress'
    }
]);

// Data untuk ENGDOC items
const engdocItems = ref([
    {
        id: 1,
        no: 1,
        jobTitle: 'Pengadaan Kendaraan DCT Ruas Mk3 Motoring 10',
        load: 0,
        persentaseProgress: 0
    },
    {
        id: 2,
        no: 2,
        jobTitle: 'Pengadaan Kendaraan DCT Ruas Mk3 Motoring 11',
        load: 0,
        persentaseProgress: 0
    },
    {
        id: 3,
        no: 3,
        jobTitle: 'Pengadaan Kendaraan DCT Ruas Mk3 Motoring 15',
        load: 0,
        persentaseProgress: 0
    },
    {
        id: 4,
        no: 4,
        jobTitle: 'Pengadaan Kendaraan DCT Ruas Mk3 Motoring 16',
        load: 0,
        persentaseProgress: 0
    }
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

// Pagination untuk ENGDOC
const engdocCurrentPage = ref(1);
const engdocItemsPerPage = 10;

const paginatedEngdocItems = computed(() => {
    const start = (engdocCurrentPage.value - 1) * engdocItemsPerPage;
    const end = start + engdocItemsPerPage;
    return engdocItems.value.slice(start, end);
});

const engdocTotalPages = computed(() => {
    return Math.ceil(engdocItems.value.length / engdocItemsPerPage);
});

function prevEngdocPage() {
    if (engdocCurrentPage.value > 1) {
        engdocCurrentPage.value--;
    }
}

function nextEngdocPage() {
    if (engdocCurrentPage.value < engdocTotalPages.value) {
        engdocCurrentPage.value++;
    }
}
</script>

<template>
    <Head title="Monitoring Progress" />

    <template name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Morning Report
        </h2>
    </template>

    <div class="container mx-auto px-4 py-12">
        <div class="mx-auto max-w-full sm:px-6 lg:px-8">
            <!-- ERP Table -->
            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="flex-none w-full max-w-full px-3">
                    <div
                        class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl sm:rounded-lg bg-clip-border">
                        <div
                            class="flex items-center justify-between p-6 pb-0 mb-0 border-b-0 border-b-solid border-b-transparent">
                            <!-- Judul Tabel -->
                            <h1 class="text-2xl font-bold text-gray-900">
                                Morning Report
                            </h1>
                        </div>
                        <div
                            class="flex items-center justify-between p-6 pb-0 mb-0 border-b-0 border-b-solid border-b-transparent">
                            <!-- Judul Tabel -->
                            <h6 class="dark:text-white text-base font-bold">
                                ERF YANG MASUK HARI INI & MINGGU INI
                            </h6>
                        </div>

                        <div class="flex-auto px-0 pt-0 pb-2">
                            <div class="p-0 overflow-x-auto">
                                <table class="w-full table-auto">
                                    <thead class="bg-gray-50">
                                        <tr class="text-sm font-normal text-gray-600 border-t border-b text-left">
                                            <th class="px-4 py-3">No</th>
                                            <th class="px-4 py-3">Job Title</th>
                                            <th class="px-4 py-3">UK Peminta</th>
                                            <th class="px-4 py-3">Lead</th>
                                            <th class="px-4 py-3">Tgl. ERF Disetujui</th>
                                            <th class="px-4 py-3">Fase Initiating Target Selesai</th>
                                            <th class="px-4 py-3">Fase Executing & Closing Target Selesai</th>
                                            <th class="px-4 py-3">Status Verifikasi ERF</th>
                                            <th class="px-4 py-3">Status Pek. Engineering</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm font-normal text-gray-700">
                                        <tr v-for="item in paginatedErpItems" :key="item.id"
                                            class="border-b border-gray-200 hover:bg-gray-100">
                                            <td class="px-4">
                                                <div class="flex-1">
                                                    <div class="font-medium dark:text-white">
                                                        {{ item.no }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4">
                                                <div class="flex-1">
                                                    <div class="font-medium dark:text-white">
                                                        {{ item.jobTitle }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4">
                                                <div class="flex-1">
                                                    <div class="font-medium dark:text-white">
                                                        {{ item.urPekerjaan }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4">
                                                <div class="flex-1">
                                                    <div class="font-medium dark:text-white">
                                                        {{ item.load }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4">
                                                <div class="flex-1">
                                                    <div class="font-medium dark:text-white">
                                                        {{ item.tglErpTerkirim }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4">
                                                <div class="flex-1">
                                                    <div class="font-medium dark:text-white">
                                                        {{ item.tglSelesaiTarget }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4">
                                                <div class="flex-1">
                                                    <div class="font-medium dark:text-white">
                                                        {{ item.faseExecutionTarget }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4">
                                                <div class="flex-1">
                                                    <div class="font-medium dark:text-white">
                                                        {{ item.statusVerifikasi }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4">
                                                <div class="flex-1">
                                                    <div class="font-medium dark:text-white">
                                                        {{ item.statusPull }}
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- pagination -->
                            <div class="flex justify-between items-center p-4 bg-gray-50">
                                <button @click="prevErpPage" :disabled="erpCurrentPage === 1"
                                    class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-blue-500 transition duration-300 hover:bg-gradient-to-tl hover:from-blue-500 hover:to-blue-400 hover:text-white disabled:opacity-50">
                                    Previous
                                </button>
                                <span class="text-sm text-gray-700">
                                    Page {{ erpCurrentPage }} of {{ erpTotalPages }}
                                </span>
                                <button @click="nextErpPage" :disabled="erpCurrentPage === erpTotalPages"
                                    class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-blue-500 transition duration-300 hover:bg-gradient-to-tl hover:from-blue-500 hover:to-blue-400 hover:text-white disabled:opacity-50">
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ENGDOC Table -->
            <div class="flex flex-wrap -mx-3">
                <div class="flex-none w-full max-w-full px-3">
                    <div
                        class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl sm:rounded-lg bg-clip-border">
                        <div
                            class="flex items-center justify-between p-6 pb-0 mb-0 border-b-0 border-b-solid border-b-transparent">
                            <!-- Judul Tabel -->
                            <h6 class="dark:text-white text-base font-bold">
                                ENG. DOC. YANG PERLU APPROVAL KA. UNIT
                            </h6>
                        </div>

                        <div class="flex-auto px-0 pt-0 pb-2">
                            <div class="p-0 overflow-x-auto">
                                <table class="w-full table-auto">
                                    <thead class="bg-gray-50">
                                        <tr class="text-sm font-normal text-gray-600 border-t border-b text-left">
                                            <th class="px-4 py-3">No</th>
                                            <th class="px-4 py-3">Job Title</th>
                                            <th class="px-4 py-3">Lead</th>
                                            <th class="px-4 py-3">Keterangan Progress</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm font-normal text-gray-700">
                                        <tr v-for="item in paginatedEngdocItems" :key="item.id"
                                            class="border-b border-gray-200 hover:bg-gray-100">
                                            <td class="px-4">
                                                <div class="flex-1">
                                                    <div class="font-medium dark:text-white">
                                                        {{ item.no }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4">
                                                <div class="flex-1">
                                                    <div class="font-medium dark:text-white">
                                                        {{ item.jobTitle }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4">
                                                <div class="flex-1">
                                                    <div class="font-medium dark:text-white">
                                                        {{ item.load }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4">
                                                <div class="flex-1">
                                                    <div class="font-medium dark:text-white">
                                                        {{ item.persentaseProgress }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- pagination -->
                            <div class="flex justify-between items-center p-4 bg-gray-50">
                                <button @click="prevEngdocPage" :disabled="engdocCurrentPage === 1"
                                    class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-blue-500 transition duration-300 hover:bg-gradient-to-tl hover:from-blue-500 hover:to-blue-400 hover:text-white disabled:opacity-50">
                                    Previous
                                </button>
                                <span class="text-sm text-gray-700">
                                    Page {{ engdocCurrentPage }} of {{ engdocTotalPages }}
                                </span>
                                <button @click="nextEngdocPage" :disabled="engdocCurrentPage === engdocTotalPages"
                                    class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-blue-500 transition duration-300 hover:bg-gradient-to-tl hover:from-blue-500 hover:to-blue-400 hover:text-white disabled:opacity-50">
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>