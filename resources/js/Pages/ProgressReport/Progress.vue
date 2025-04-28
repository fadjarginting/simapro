<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";

defineOptions({
    layout: AuthenticatedLayout,
});
// Data contoh (ganti dengan data props dari backend)
const documents = ref([
    {
        id: 1,
        doc_code: '6001.51.4001-1',
        title: 'Flow Sheet With Capacity Indarung VI',
        version: 'P1',
        status: 'Finish',
        publish_date: '10-03-2025'
    },
    {
        id: 1,
        doc_code: '5001.51.4001-1',
        title: 'Flow Sheet With Capacity Indarung V',
        version: 'P1',
        status: 'Finish',
        publish_date: '10-04-2025'
    },
    // Tambahkan data lain sesuai kebutuhan
]);

// Filter values
const filterValues = ref({
    docCode: '',
    title: '',
    version: '',
    status: '',
    publishDate: ''
});

// Computed property untuk filtered data
const filteredDocuments = computed(() => {
    return documents.value.filter(doc => {
        return (
            doc.doc_code.toLowerCase().includes(filterValues.value.docCode.toLowerCase()) &&
            doc.title.toLowerCase().includes(filterValues.value.title.toLowerCase()) &&
            doc.version.toString().includes(filterValues.value.version) &&
            doc.status.toLowerCase().includes(filterValues.value.status.toLowerCase()) &&
            doc.publish_date.includes(filterValues.value.publishDate)
        );
    });
});

// Pagination
const currentPage = ref(1);
const itemsPerPage = 10;

const paginatedDocuments = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return filteredDocuments.value.slice(start, end);
});

const totalPages = computed(() => {
    return Math.ceil(filteredDocuments.value.length / itemsPerPage);
});

function prevPage() {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
}

function nextPage() {
    if (currentPage.value < totalPages.value) {
        currentPage.value++;
    }
}
// Membuat object reaktif untuk menyimpan status expand tiap baris,
// misalnya dengan key 'user1', 'user2', dll.
const expandedRows = ref({});

// Fungsi untuk toggle tampilan baris deskripsi berdasarkan id
function toggleDescription(id) {
    // Jika id sudah ada, invert nilainya, jika belum, set ke true
    expandedRows.value[id] = !expandedRows.value[id];
}
</script>

<template>

    <Head title="Progress Report" />

    <template name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Progress Report
        </h2>
    </template>

    <div class="container mx-auto px-4 py-12">
        <div class="mx-auto max-w-full sm:px-6 lg:px-8">
            <!-- table docs -->
            <div class="flex flex-wrap -mx-3">
                <div class="flex-none w-full max-w-full px-3">
                    <div
                        class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl sm:rounded-lg bg-clip-border">
                        <div
                            class="flex items-center justify-between p-6 pb-0 mb-0 border-b-0 border-b-solid border-b-transparent">
                            <!-- Judul Tabel -->
                            <h6 class="text-2xl font-bold text-gray-900">
                                Progress Report
                            </h6>
                            <!-- Tombol Add Docs -->
                            <div class="flex items-center justify-end">
                                <Link :href="route('progress.create')"
                                    class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-green-500 transition duration-300 hover:bg-gradient-to-tl hover:from-green-500 hover:to-teal-400 hover:text-white">
                                <i class="fas fa-plus mr-2 text-xs leading-none"></i>
                                <span>Add New Progress</span>
                                <i class="ni ni-folder-17 ml-2 leading-none"></i>
                                </Link>
                            </div>
                        </div>

                        <!-- Filter -->
                        <div
                            class="flex flex-wrap items-center p-6 pb-0 mb-0 border-b-0 border-b-solid border-b-transparent bg-gray-50">
                            
                            <div class="w-full md:w-1/2 lg:w-1/6 px-2 mb-2">
                                <select v-model="filterValues.status"
                                    class="w-full px-4 py-3 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Status</option>
                                    <option value="New">New</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                    <!-- Tambahkan opsi lain sesuai kebutuhan -->
                                </select>
                            </div>
                            <div class="w-full md:w-1/2 lg:w-1/6 px-2 mb-2">
                                <input v-model="filterValues.publishDate" type="date" placeholder="Filter Publish Date"
                                    class="w-full px-4 py-3 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                            <div class="w-full md:w-1/2 lg:w-1/6 px-2 mb-2">
                                <input v-model="filterValues.docCode" type="text" placeholder="Document Code"
                                    class="w-full px-4 py-3 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                            <div class="w-full md:w-1/2 lg:w-1/6 px-2 mb-2">
                                <input v-model="filterValues.title" type="text" placeholder="Filter Title"
                                    class="w-full px-4 py-3 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                            <div class="w-full md:w-1/2 lg:w-1/6 px-2 mb-2">
                                <input v-model="filterValues.version" type="text" placeholder="Filter Version"
                                    class="w-full px-4 py-3 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                        </div>


                        <div class="flex-auto px-0 pt-0 pb-2">

                            <div class="p-0 overflow-x-auto">
                                <table class="w-full table-auto">
                                    <thead class="bg-gray-50">
                                        <tr class="text-sm font-normal text-gray-600 border-t border-b text-left">
                                            <th class="px-4 py-3">
                                                No.ERF
                                            </th>
                                            <th class="px-4 py-3">Entry Date</th>
                                            <th class="px-4 py-3">Job Title</th>
                                            <th class="px-4 py-3">Status</th>
                                            <th class="px-4 py-3">Plant</th>
                                            <th class="px-4 py-3">
                                                Action
                                            </th>
                                            <th class="px-4 py-3"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm font-normal text-gray-700">
                                        <tr v-for="(doc, index) in filteredDocuments" :key="doc.id"
                                            class="border-b border-gray-200 hover:bg-gray-100">
                                            <td class="px-4">
                                                <div class="flex-1">
                                                    <div class="font-medium dark:text-white">
                                                        {{ doc.doc_code }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4">
                                                <div class="flex-1">
                                                    <div class="font-medium dark:text-white">
                                                        {{ doc.publish_date }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4">
                                                <div class="flex-1">
                                                    <div class="font-medium dark:text-white">
                                                        {{ doc.title }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4">
                                                <div class="flex-1">
                                                    <div class="font-medium dark:text-white">
                                                        {{ doc.status }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4">
                                                <div class="flex-1">
                                                    <div class="font-medium dark:text-white">
                                                        {{ doc.version }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <button @click="toggleDescription(`doc-${index}`)"
                                                    class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-blue-500 transition duration-300 hover:bg-gradient-to-tl hover:from-blue-500 hover:to-blue-400 hover:text-white">
                                                    <i class="fas fa-info-circle mr-2"></i>
                                                    Detail
                                                </button>
                                                <button @click="updateProgress(doc.code)"
                                                    class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-yellow-500 transition duration-300 hover:bg-gradient-to-tl hover:from-yellow-500 hover:to-yellow-400 hover:text-white">
                                                    <i class="fas fa-edit mr-2 text-xs leading-none"></i>
                                                    <span>Update</span>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- pagination -->
                            <div class="flex justify-between items-center p-4 bg-gray-50"></div>
                            <button @click="prevPage" :disabled="currentPage === 1"
                                class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-blue-500 transition duration-300 hover:bg-gradient-to-tl hover:from-blue-500 hover:to-blue-400 hover:text-white disabled:opacity-50">
                                Previous
                            </button>
                            <span class="text-sm text-gray-700">
                                Page {{ currentPage }} of {{ totalPages }}
                            </span>
                            <button @click="nextPage" :disabled="currentPage === totalPages"
                                class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-blue-500 transition duration-300 hover:bg-gradient-to-tl hover:from-blue-500 hover:to-blue-400 hover:text-white disabled:opacity-50">
                                Next
                            </button>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>


</template>
