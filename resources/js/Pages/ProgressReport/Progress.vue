<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePage } from "@inertiajs/vue3";

defineOptions({
    layout: AuthenticatedLayout,
});

// Ambil data dari props yang dikirim oleh backend
const { props } = usePage();
const documents = ref(props.progresses || []);

// Filter values
const filterValues = ref({
    requestCategory: '',
    statusVerifikasi: '',
    jobTitle: '',
});

// Computed property untuk filtered data
const filteredDocuments = computed(() => {
    return documents.value.filter(doc => {
        return (
            (filterValues.value.requestCategory === '' || (doc.request_category && doc.request_category.toLowerCase().includes(filterValues.value.requestCategory.toLowerCase()))) &&
            (filterValues.value.statusVerifikasi === '' || (doc.status_verifikasi && doc.status_verifikasi.toLowerCase().includes(filterValues.value.statusVerifikasi.toLowerCase()))) &&
            (filterValues.value.jobTitle === '' || 
                (doc.pic_mekanikal && doc.pic_mekanikal.toLowerCase().includes(filterValues.value.jobTitle.toLowerCase())))
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
    return Math.ceil(filteredDocuments.value.length / itemsPerPage) || 1;
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

// Membuat object reaktif untuk menyimpan status expand tiap baris
const expandedRows = ref({});

// Fungsi untuk toggle tampilan baris deskripsi berdasarkan id
function toggleDescription(id) {
    // Jika id sudah ada, invert nilainya, jika belum, set ke true
    expandedRows.value[id] = !expandedRows.value[id];
}

// Function to update progress
function updateProgress(code) {
    // You should implement navigation to your update page
    window.location.href = route('progress.edit', code);
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
                            
                            <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-2">
                                <select v-model="filterValues.statusVerifikasi"
                                    class="w-full px-4 py-3 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Status</option>
                                    <option value="New">New</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                    <!-- Tambahkan opsi lain sesuai kebutuhan -->
                                </select>
                            </div>
                            <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-2">
                                <input v-model="filterValues.requestCategory" type="text" placeholder="Request Category"
                                    class="w-full px-4 py-3 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                            <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-2">
                                <input v-model="filterValues.jobTitle" type="text" placeholder="Job Title"
                                    class="w-full px-4 py-3 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                            </div>
                        </div>

                        <div class="flex-auto px-0 pt-0 pb-2">
                            <div class="p-0 overflow-x-auto">
                                <table class="w-full table-auto">
                                    <thead class="bg-gray-50">
                                        <tr class="text-sm font-normal text-gray-600 border-t border-b text-left">
                                            <th class="px-4 py-3">
                                                No.ERF/Request Category
                                            </th>
                                            <th class="px-4 py-3">Status Verifikasi</th>
                                            <th class="px-4 py-3">Job Title/PIC</th>
                                            <th class="px-4 py-3">Progress</th>
                                            <th class="px-4 py-3">Note</th>
                                            <th class="px-4 py-3">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm font-normal text-gray-700">
                                        <template v-if="paginatedDocuments.length">
                                            <tr v-for="(doc, index) in paginatedDocuments" :key="doc.id"
                                                class="border-b border-gray-200 hover:bg-gray-100">
                                                <td class="px-4 py-3">
                                                    <div class="flex-1">
                                                        <div class="font-medium dark:text-white">
                                                            {{ doc.request_category }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex-1">
                                                        <div class="font-medium dark:text-white">
                                                            {{ doc.status_verifikasi }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex-1">
                                                        <div class="font-medium dark:text-white">
                                                            {{ doc.pic_mekanikal }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex-1">
                                                        <div class="font-medium dark:text-white">
                                                            {{ doc.progress_mekanikal }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex-1">
                                                        <div class="font-medium dark:text-white">
                                                            {{ doc.note }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <button @click="toggleDescription(`doc-${index}`)"
                                                        class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-blue-500 transition duration-300 hover:bg-gradient-to-tl hover:from-blue-500 hover:to-blue-400 hover:text-white">
                                                        <i class="fas fa-info-circle mr-2"></i>
                                                        Detail
                                                    </button>
                                                    <button @click="updateProgress(doc.id)"
                                                        class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-yellow-500 transition duration-300 hover:bg-gradient-to-tl hover:from-yellow-500 hover:to-yellow-400 hover:text-white ml-2">
                                                        <i class="fas fa-edit mr-2 text-xs leading-none"></i>
                                                        <span>Update</span>
                                                    </button>
                                                </td>
                                            </tr>
                                            <!-- Expanded row for details -->
                                            <tr v-for="(doc, index) in paginatedDocuments" :key="`detail-${doc.id}`" 
                                                v-show="expandedRows[`doc-${index}`]" 
                                                class="bg-gray-50">
                                                <td colspan="6" class="px-4 py-4">
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <h4 class="font-bold">Detail Progress:</h4>
                                                            <p>{{ doc.detail_progress || 'No detail provided' }}</p>
                                                        </div>
                                                        <div>
                                                            <h4 class="font-bold">Other PIC:</h4>
                                                            <p><strong>Sipil:</strong> {{ doc.pic_sipil || 'N/A' }}</p>
                                                            <p><strong>Elinst:</strong> {{ doc.pic_elinst || 'N/A' }}</p>
                                                            <p><strong>Proses:</strong> {{ doc.pic_proses || 'N/A' }}</p>
                                                        </div>
                                                        <div>
                                                            <h4 class="font-bold">Progress by Department:</h4>
                                                            <p><strong>Sipil:</strong> {{ doc.progress_sipil || 'N/A' }}</p>
                                                            <p><strong>Elinst:</strong> {{ doc.progress_elinst || 'N/A' }}</p>
                                                            <p><strong>Proses:</strong> {{ doc.progress_proses || 'N/A' }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr v-else class="border-b border-gray-200">
                                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                                No progress records found
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- pagination -->
                            <div class="flex justify-between items-center p-4 bg-gray-50">
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
    </div>
</template>