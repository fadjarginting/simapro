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
const progress = ref(props.progresses || []);

// Filter values
const filterValues = ref({
    statusVerifikasi: "",
    title: "",
});

// Computed property for filtered data
const filteredDocuments = computed(() => {
    return progress.value.filter((progres) => {
        return (
            (filterValues.value.statusVerifikasi === "" ||
                (progres.status_verifikasi &&
                    progres.status_verifikasi
                        .toLowerCase()
                        .includes(
                            filterValues.value.statusVerifikasi.toLowerCase()
                        ))) &&
            (filterValues.value.title === "" ||
                (progres.title &&
                    progres.title
                        .toLowerCase()
                        .includes(filterValues.value.title.toLowerCase())))
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
    router.visit(route("progress.edit", id));
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
                    progress.value = progress.value.filter(
                        (progres) => progres.id !== id
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

//tanggal
function formatDate(dateStr) {
    if (!dateStr) return "N/A";
    const date = new Date(dateStr);
    return date.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    });
}
</script>

<template>
    <Head title="Progress Report" />

    <template name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Progress Report
        </h2>
    </template>

    <div class=" px-4 py-12">
        <div class=" sm:px-6 lg:px-8">
            <!-- table docs -->
            <div class="flex flex-wrap -mx-3">
                <div class="flex-none w-full  px-3">
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
                                    v-model="filterValues.title"
                                    type="text"
                                    placeholder="Job Title"
                                    class="w-full px-4 py-3 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                            </div>
                            <button
                                @click="
                                    filterValues = {
                                        statusVerifikasi: '',
                                        title: '',
                                    }
                                "
                                class="ml-2 bg-gray-200 px-4 py-2 rounded text-sm text-gray-700 hover:bg-gray-300"
                            >
                                Reset Filter
                            </button>
                        </div>

                        <!-- tampilan -->
                        <div class="flex-auto px-0 pt-0 pb-2">
                            <div class="p-0 overflow-x-auto">
                                <table class="w-full table-auto">
                                    <thead class="bg-gray-200">
                                        <tr
                                            class="text-sm font-normal text-gray-600 border-t border-b text-left"
                                        >
                                            <th class="px-4 py-3">No ERF</th>
                                            <th class="px-4 py-3">Job Title</th>
                                            <th class="px-4 py-3">Plant</th>
                                            <th class="px-4 py-3">
                                                ERF Approved Date
                                            </th>
                                            <th class="px-4 py-3">Job Type</th>
                                            <th class="px-4 py-3">
                                                Lead Engineering
                                            </th>
                                            <th class="px-4 py-3">
                                                Deadline Executing Phase
                                            </th>
                                            <th class="px-4 py-3">Fase</th>
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
                                                    progres, index
                                                ) in paginatedDocuments"
                                                :key="progres.id"
                                                class="border-b border-gray-200 hover:bg-gray-100"
                                            >
                                                <td class="px-4 py-3">
                                                    <div class="flex-1">
                                                        <div
                                                            class="font-medium dark:text-white"
                                                        >
                                                            {{ progres.no_erf }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex-1">
                                                        <div
                                                            class="font-medium dark:text-white"
                                                        >
                                                            {{ progres.title }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex-1">
                                                        <div
                                                            class="font-medium dark:text-white"
                                                        >
                                                            {{ progres.plant }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex-1">
                                                        <div
                                                            class="font-medium dark:text-white"
                                                        >
                                                            {{
                                                                formatDate(
                                                                    progres.erf_approved_date
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
                                                            {{
                                                                progres.job_type
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
                                                                progres.lead_engineering
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
                                                                formatDate(
                                                                    progres.deadline_executing
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
                                                            {{ progres.fase }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex-1">
                                                        <Link
                                                            :href="
                                                                route(
                                                                    'progress.show',
                                                                    progres.id
                                                                )
                                                            "
                                                            class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-blue-500 transition duration-300 hover:bg-gradient-to-tl hover:from-blue-500 hover:to-blue-400 hover:text-white"
                                                        >
                                                            <i
                                                                class="fas fa-info-circle mr-2"
                                                            ></i>
                                                            <span>Detail</span>
                                                        </Link>
                                                        <Link
                                                            :href="
                                                                route(
                                                                    'progress.edit',
                                                                    progres.id
                                                                )
                                                            "
                                                            class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-yellow-500 transition duration-300 hover:bg-gradient-to-tl hover:from-yellow-500 hover:to-yellow-400 hover:text-white"
                                                        >
                                                            <i
                                                                class="fas fa-edit mr-2 text-xs leading-none"
                                                            ></i>
                                                            <span>Update</span>
                                                        </Link>
                                                        <button
                                                            @click="
                                                                deleteProgress(
                                                                    progres.id
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
                                        </template>
                                        <tr
                                            v-else
                                            class="border-b border-gray-200"
                                        >
                                            <td
                                                colspan="8"
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
