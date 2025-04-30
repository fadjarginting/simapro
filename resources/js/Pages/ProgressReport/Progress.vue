<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import Swal from "sweetalert2";

defineOptions({
    layout: AuthenticatedLayout,
});

// Get data from props sent by backend
const { props } = usePage();
const documents = ref(props.progresses || []);

// Filter values
const filterValues = ref({
    requestCategory: "",
    statusVerifikasi: "",
    picMekanikal: "",
});

// Computed property for filtered data
const filteredDocuments = computed(() => {
    return documents.value.filter((doc) => {
        return (
            (filterValues.value.requestCategory === "" ||
                (doc.request_category &&
                    doc.request_category
                        .toLowerCase()
                        .includes(
                            filterValues.value.requestCategory.toLowerCase()
                        ))) &&
            (filterValues.value.statusVerifikasi === "" ||
                (doc.status_verifikasi &&
                    doc.status_verifikasi
                        .toLowerCase()
                        .includes(
                            filterValues.value.statusVerifikasi.toLowerCase()
                        ))) &&
            (filterValues.value.picMekanikal === "" ||
                (doc.pic_mekanikal &&
                    doc.pic_mekanikal
                        .toLowerCase()
                        .includes(
                            filterValues.value.picMekanikal.toLowerCase()
                        )))
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

// Reactive object to store expanded row status
const expandedRows = ref({});

// Function to toggle description row visibility based on id
function toggleDescription(id) {
    expandedRows.value[id] = !expandedRows.value[id];
}

// Function to update progress
function updateProgress(id) {
    window.location.href = route("progress.edit", id);
}

// Function to delete progress with SweetAlert2 confirmation
function deleteProgress(id) {
    Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("progress.destroy", id), {
                onSuccess: () => {
                    // Remove the deleted document from the local array
                    documents.value = documents.value.filter(
                        (doc) => doc.id !== id
                    );

                    // Show success message
                    Swal.fire({
                        icon: "success",
                        title: "Deleted!",
                        text: "Progress has been deleted.",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                },
                onError: () => {
                    Swal.fire({
                        icon: "error",
                        title: "Oops...",
                        text: "There was an error deleting the progress!",
                    });
                },
            });
        }
    });
}

// Format percentage for display
function formatPercentage(value) {
    if (!value && value !== 0) return "N/A";
    return `${value}%`;
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
                        class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl sm:rounded-lg bg-clip-border"
                    >
                        <div
                            class="flex items-center justify-between p-6 pb-0 mb-0 border-b-0 border-b-solid border-b-transparent"
                        >
                            <!-- Table Title -->
                            <h6 class="text-2xl font-bold text-gray-900">
                                Progress Report
                            </h6>
                            <!-- Add New Progress Button -->
                            <div class="flex items-center justify-end">
                                <Link
                                    :href="route('progress.create')"
                                    class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-green-500 transition duration-300 hover:bg-gradient-to-tl hover:from-green-500 hover:to-teal-400 hover:text-white"
                                >
                                    <i
                                        class="fas fa-plus mr-2 text-xs leading-none"
                                    ></i>
                                    <span>Add New Progress</span>
                                    <i
                                        class="ni ni-folder-17 ml-2 leading-none"
                                    ></i>
                                </Link>
                            </div>
                        </div>

                        <!-- Filter -->
                        <div
                            class="flex flex-wrap items-center p-6 pb-0 mb-0 border-b-0 border-b-solid border-b-transparent bg-gray-50"
                        >
                            <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-2">
                                <select
                                    v-model="filterValues.statusVerifikasi"
                                    class="w-full px-4 py-3 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="">Select Status</option>
                                    <option value="New">New</option>
                                    <option value="In Progress">
                                        In Progress
                                    </option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-2">
                                <input
                                    v-model="filterValues.requestCategory"
                                    type="text"
                                    placeholder="Request Category"
                                    class="w-full px-4 py-3 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                            <div class="w-full md:w-1/2 lg:w-1/4 px-2 mb-2">
                                <input
                                    v-model="filterValues.picMekanikal"
                                    type="text"
                                    placeholder="PIC Mekanikal"
                                    class="w-full px-4 py-3 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                        </div>

                        <div class="flex-auto px-0 pt-0 pb-2">
                            <div class="p-0 overflow-x-auto">
                                <table class="w-full table-auto">
                                    <thead class="bg-gray-50">
                                        <tr
                                            class="text-sm font-normal text-gray-600 border-t border-b text-left"
                                        >
                                            <th class="px-4 py-3">
                                                Request Category
                                            </th>
                                            <th class="px-4 py-3">
                                                Status Verifikasi
                                            </th>
                                            <th class="px-4 py-3">
                                                PIC Mekanikal
                                            </th>
                                            <th class="px-4 py-3">Progress</th>
                                            <th class="px-4 py-3">Note</th>
                                            <th class="px-4 py-3">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="text-sm font-normal text-gray-700"
                                    >
                                        <template
                                            v-if="paginatedDocuments.length"
                                        >
                                            <tr
                                                v-for="(
                                                    doc, index
                                                ) in paginatedDocuments"
                                                :key="doc.id"
                                                class="border-b border-gray-200 hover:bg-gray-100"
                                            >
                                                <td class="px-4 py-3">
                                                    <div class="flex-1">
                                                        <div
                                                            class="font-medium dark:text-white"
                                                        >
                                                            {{
                                                                doc.request_category
                                                            }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex-1">
                                                        <div
                                                            class="font-medium dark:text-white"
                                                        >
                                                            {{
                                                                doc.status_verifikasi
                                                            }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex-1">
                                                        <div
                                                            class="font-medium dark:text-white"
                                                        >
                                                            {{
                                                                doc.pic_mekanikal
                                                            }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex-1">
                                                        <div
                                                            class="font-medium dark:text-white"
                                                        >
                                                            {{
                                                                formatPercentage(
                                                                    doc.progress_mekanikal
                                                                )
                                                            }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex-1">
                                                        <div
                                                            class="font-medium dark:text-white"
                                                        >
                                                            {{ doc.note }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex space-x-2">
                                                        <button
                                                            @click="
                                                                toggleDescription(
                                                                    `doc-${index}`
                                                                )
                                                            "
                                                            class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-blue-500 transition duration-300 hover:bg-gradient-to-tl hover:from-blue-500 hover:to-blue-400 hover:text-white"
                                                        >
                                                            <i
                                                                class="fas fa-info-circle mr-2"
                                                            ></i>
                                                            Detail
                                                        </button>
                                                        <button
                                                            @click="
                                                                updateProgress(
                                                                    doc.id
                                                                )
                                                            "
                                                            class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-yellow-500 transition duration-300 hover:bg-gradient-to-tl hover:from-yellow-500 hover:to-yellow-400 hover:text-white"
                                                        >
                                                            <i
                                                                class="fas fa-edit mr-2 text-xs leading-none"
                                                            ></i>
                                                            <span>Update</span>
                                                        </button>
                                                        <button
                                                            @click="
                                                                deleteProgress(
                                                                    doc.id
                                                                )
                                                            "
                                                            class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-red-500 transition duration-300 hover:bg-gradient-to-tl hover:from-red-500 hover:to-red-400 hover:text-white"
                                                        >
                                                            <i
                                                                class="fas fa-trash mr-2 text-xs leading-none"
                                                            ></i>
                                                            <span>Delete</span>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <!-- Expanded row for details -->
                                            <tr
                                                v-for="(
                                                    doc, index
                                                ) in paginatedDocuments"
                                                :key="`detail-${doc.id}`"
                                                v-show="
                                                    expandedRows[`doc-${index}`]
                                                "
                                                class="bg-gray-50"
                                            >
                                                <td
                                                    colspan="6"
                                                    class="px-4 py-4"
                                                >
                                                    <div
                                                        class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                                    >
                                                        <div>
                                                            <h4
                                                                class="font-bold"
                                                            >
                                                                Detail Progress:
                                                            </h4>
                                                            <p>
                                                                {{
                                                                    doc.detail_progress ||
                                                                    "No detail provided"
                                                                }}
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <h4
                                                                class="font-bold"
                                                            >
                                                                Other PIC:
                                                            </h4>
                                                            <p>
                                                                <strong
                                                                    >Sipil:</strong
                                                                >
                                                                {{
                                                                    doc.pic_sipil ||
                                                                    "N/A"
                                                                }}
                                                            </p>
                                                            <p>
                                                                <strong
                                                                    >Elinst:</strong
                                                                >
                                                                {{
                                                                    doc.pic_elinst ||
                                                                    "N/A"
                                                                }}
                                                            </p>
                                                            <p>
                                                                <strong
                                                                    >Proses:</strong
                                                                >
                                                                {{
                                                                    doc.pic_proses ||
                                                                    "N/A"
                                                                }}
                                                            </p>
                                                        </div>
                                                        <div>
                                                            <h4
                                                                class="font-bold"
                                                            >
                                                                Progress by
                                                                Department:
                                                            </h4>
                                                            <p>
                                                                <strong
                                                                    >Sipil:</strong
                                                                >
                                                                {{
                                                                    formatPercentage(
                                                                        doc.progress_sipil
                                                                    )
                                                                }}
                                                            </p>
                                                            <p>
                                                                <strong
                                                                    >Elinst:</strong
                                                                >
                                                                {{
                                                                    formatPercentage(
                                                                        doc.progress_elinst
                                                                    )
                                                                }}
                                                            </p>
                                                            <p>
                                                                <strong
                                                                    >Proses:</strong
                                                                >
                                                                {{
                                                                    formatPercentage(
                                                                        doc.progress_proses
                                                                    )
                                                                }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr
                                            v-else
                                            class="border-b border-gray-200"
                                        >
                                            <td
                                                colspan="6"
                                                class="px-4 py-6 text-center text-gray-500"
                                            >
                                                No progress records found
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
                                    @click="prevPage"
                                    :disabled="currentPage === 1"
                                    class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-blue-500 transition duration-300 hover:bg-gradient-to-tl hover:from-blue-500 hover:to-blue-400 hover:text-white disabled:opacity-50"
                                >
                                    Previous
                                </button>
                                <span class="text-sm text-gray-700">
                                    Page {{ currentPage }} of {{ totalPages }}
                                </span>
                                <button
                                    @click="nextPage"
                                    :disabled="currentPage === totalPages"
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
    </div>
</template>
