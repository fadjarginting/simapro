<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { ref, computed, onMounted, watch } from "vue";
import Swal from "sweetalert2";

defineOptions({
    layout: AuthenticatedLayout,
});

const props = defineProps({
    priorities: Object,
    filters: Object,
    errors: Object,
});

const expandedRows = ref({});
const showAddPriorityModal = ref(false);
const showEditPriorityModal = ref(false);
const selectedPriority = ref(null);

// Form for adding a new priorities
const createForm = useForm({
    name: "",
});

// Form for editing an existing priorities
const editForm = useForm({
    id: "",
    name: "",
});

// Form state that syncs with URL parameters
const form = ref({
    search: props.filters.search || "",
    perPage: props.filters.perPage || 10,
    page: props.priorities.current_page || 1,
});

// Initialize search query from filters
onMounted(() => {
    form.value.search = props.filters?.search || "";
});

// Debounce search to prevent too many requests
let timeout;
const performSearch = () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            route("priorities.index"),
            {
                search: form.value.search,
                perPage: form.value.perPage,
                page: 1, // Always reset to first page on new search
            },
            {
                preserveState: true,
                replace: true,
            }
        );
    }, 300);
};

// Watch for changes in search to trigger search
watch(() => form.value.search, performSearch);
watch(
    () => form.value.perPage,
    () => {
        router.get(
            route("priorities.index"),
            {
                search: form.value.search,
                perPage: form.value.perPage,
                page: 1, // Reset to first page when changing items per page
            },
            {
                preserveState: true,
                replace: true,
            }
        );
    }
);

const togglePriority = (id) => {
    expandedRows.value[id] = !expandedRows.value[id];
};

const openAddModal = () => {
    createForm.reset();
    showAddPriorityModal.value = true;
};

const openEditModal = (priority) => {
    selectedPriority.value = priority;
    editForm.id = priority.id;
    editForm.name = priority.name;
    showEditPriorityModal.value = true;
};

// submit with sweetalert2 confirmation
const submitCreateForm = () => {
    showAddPriorityModal.value = false;
    Swal.fire({
        title: "Add Priority",
        text: "Are you sure you want to add this priority?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Add",
        cancelButtonText: "Cancel",
        customClass: {
            confirmButton:
                "bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 mr-2",
            cancelButton:
                "bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 ml-2",
        },
        buttonsStyling: false,
    }).then((result) => {
        if (result.isConfirmed) {
            createForm.post(route("priorities.store"), {
                onSuccess: () => {
                    createForm.reset();
                    showAddPriorityModal.value = false;
                    Swal.fire({
                        toast: true,
                        position: "top-end",
                        icon: "success",
                        title: "The priority has been added successfully.",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                },
                onError: (errors) => {
                    showAddPriorityModal.value = true;
                    Swal.fire({
                        toast: true,
                        position: "top-end",
                        icon: "error",
                        title:
                            errors.name ||
                            "Failed to add the priority. Please try again.",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                },
            });
        } else {
            showAddPriorityModal.value = true;
        }
    });
};

const submitEditForm = () => {
    showEditPriorityModal.value = false;
    Swal.fire({
        title: "Update Priority",
        text: "Are you sure you want to update this priority?",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Update",
        cancelButtonText: "Cancel",
        customClass: {
            confirmButton:
                "bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 mr-2",
            cancelButton:
                "bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 ml-2",
        },
        buttonsStyling: false,
    }).then((result) => {
        if (result.isConfirmed) {
            editForm.put(route("priorities.update", editForm.id), {
                onSuccess: () => {
                    editForm.reset();
                    showEditPriorityModal.value = false;
                    Swal.fire({
                        toast: true,
                        position: "top-end",
                        icon: "success",
                        title: "The priority has been updated successfully.",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                },
                onError: (errors) => {
                    showEditPriorityModal.value = true;
                    Swal.fire({
                        toast: true,
                        position: "top-end",
                        icon: "error",
                        title:
                            errors.name ||
                            "Failed to update the priority. Please try again.",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                },
            });
        } else {
            showEditPriorityModal.value = true;
        }
    });
};

const cancelCreate = () => {
    createForm.reset();
    showAddPriorityModal.value = false;
};

const cancelEdit = () => {
    editForm.reset();
    showEditPriorityModal.value = false;
};

// Function to delete a prioritie with SweetAlert2 confirmation
const deletePriority = (id) => {
    Swal.fire({
        title: "Confirm Deletion",
        text: "Are you sure you want to delete this prioritie? This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#EF4444",
        cancelButtonColor: "#9CA3AF",
        confirmButtonText: "Delete",
        cancelButtonText: "Cancel",
        customClass: {
            popup: "rounded-lg shadow-lg",
            title: "text-lg font-semibold text-gray-800",
            htmlContainer: "text-sm text-gray-600",
            confirmButton:
                "px-4 py-2 rounded-md text-white bg-red-500 hover:bg-red-600",
            cancelButton:
                "px-4 py-2 rounded-md text-gray-700 bg-gray-200 hover:bg-gray-300",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("priorities.destroy", id), {
                onSuccess: (page) => {
                    if (page.props.flash.error) {
                        Swal.fire({
                            toast: true,
                            position: "top-end",
                            icon: "error",
                            title: page.props.flash.error,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            customClass: {
                                popup: "rounded-lg shadow-lg",
                                title: "text-sm font-semibold text-gray-800",
                            },
                        });
                    } else {
                        Swal.fire({
                            toast: true,
                            position: "top-end",
                            icon: "success",
                            title: "The priority has been successfully deleted.",
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            customClass: {
                                popup: "rounded-lg shadow-lg",
                                title: "text-sm font-semibold text-gray-800",
                            },
                        });
                    }
                },
                onError: (errors) => {
                    Swal.fire(
                        "Error!",
                        "An error occurred while deleting the priority.",
                        "error"
                    );
                },
            });
        }
    });
};

// Handle page change for pagination links
const changePage = (url) => {
    if (!url) return;

    router.visit(url, {
        preserveState: true,
        replace: true,
    });
};
</script>

<template>
    <Head>
        <title>Priority Settings</title>
    </Head>

    <template name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Priority Settings
        </h2>
    </template>

    <div class="container mx-auto px-4 py-12">
        <div class="mx-auto max-w-full sm:px-6 lg:px-8">
            <div class="flex flex-wrap -mx-3">
                <div class="flex-none w-full max-w-full px-3">
                    <div
                        class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl sm:rounded-lg bg-clip-border"
                    >
                        <div
                            class="flex items-center justify-between p-6 pb-0 mb-0 border-b-0 border-b-solid border-b-transparent"
                        >
                            <h6 class="dark:text-white text-base font-bold">
                                Priority Settings
                            </h6>
                            <div class="flex items-center justify-end">
                                <button
                                    @click="openAddModal"
                                    class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-green-500 transition duration-300 hover:bg-gradient-to-tl hover:from-green-500 hover:to-teal-400 hover:text-white"
                                >
                                    <i
                                        class="fas fa-plus mr-2 text-xs leading-none"
                                    ></i>
                                    <span>Add Priority</span>
                                    <i
                                        class="ni ni-folder-17 ml-2 leading-none"
                                    ></i>
                                </button>
                            </div>
                        </div>

                        <!-- Filters and search -->
                        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <InputLabel for="search" value="Search" />
                                <div class="relative">
                                    <TextInput
                                        v-model="form.search"
                                        type="text"
                                        placeholder="Search prioritys..."
                                        class="w-full"
                                    />
                                    <span
                                        v-if="form.search"
                                        @click="form.search = ''"
                                        class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white text-red-300 hover:text-gray-700 focus:outline-none mr-2"
                                    >
                                        <i class="fas fa-times"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex-auto px-0 pt-0 pb-2">
                            <div class="p-0 overflow-x-auto">
                                <table class="w-full table-auto">
                                    <thead class="bg-gray-100">
                                        <tr
                                            class="text-sm font-normal text-gray-600 border-t border-b text-left"
                                        >
                                            <th class="px-4 py-3">
                                                Priority Name
                                            </th>
                                            <th class="px-4 py-3 text-center">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="text-sm font-normal text-gray-700"
                                    >
                                        <tr
                                            v-if="priorities.data.length === 0"
                                            class="border-b hover:bg-gray-50"
                                        >
                                            <td
                                                colspan="3"
                                                class="px-4 py-3 text-center text-gray-500"
                                            >
                                                No priorities found
                                            </td>
                                        </tr>
                                        <tr
                                            v-for="priority in priorities.data"
                                            :key="priority.id"
                                            class="border-b border-gray-200 hover:bg-gray-100"
                                        >
                                            <td class="px-4 py-3">
                                                {{ priority.name }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div
                                                    class="flex items-center justify-center space-x-2"
                                                >
                                                    <button
                                                        @click="
                                                            openEditModal(
                                                                priority
                                                            )
                                                        "
                                                        class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-blue-500 transition duration-300 hover:bg-gradient-to-tl hover:from-blue-500 hover:to-blue-400 hover:text-white"
                                                    >
                                                        <i
                                                            class="fas fa-edit mr-2 text-xs leading-none"
                                                        ></i>
                                                        <span>Edit</span>
                                                    </button>
                                                    <button
                                                        @click="
                                                            deletePriority(
                                                                priority.id
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
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div
                            class="flex justify-between items-center p-4 bg-gray-50"
                        >
                            <div class="flex items-center">
                                <span class="mr-2 text-sm text-gray-700"
                                    >Show</span
                                >
                                <select
                                    v-model="form.perPage"
                                    class="px-2 py-1 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                >
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                    <option value="20">20</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                                <span class="ml-2 text-sm text-gray-700"
                                    >rows</span
                                >
                            </div>
                            <!-- Container with horizontal scroll -->
                            <div
                                class="overflow-x-auto bg-gray-50 flex justify-between items-center p-4"
                            >
                                <!-- Inner flex dengan lebar minimal sesuai konten -->
                                <div
                                    class="min-w-max flex items-center space-x-1"
                                >
                                    <template
                                        v-for="(link, i) in priorities.links"
                                        :key="i"
                                    >
                                        <template
                                            v-if="
                                                link.label !==
                                                    '&laquo; Previous' &&
                                                link.label !== 'Next &raquo;'
                                            "
                                        >
                                            <button
                                                @click="changePage(link.url)"
                                                :disabled="!link.url"
                                                class="px-2 py-1 text-xs rounded-md min-w-[28px] text-center"
                                                :class="{
                                                    'bg-blue-500 text-white':
                                                        link.active,
                                                    'bg-gray-50 text-gray-700 hover:bg-blue-500':
                                                        !link.active &&
                                                        link.url,
                                                    'bg-gray-50 text-gray-400':
                                                        !link.url &&
                                                        link.label === '...',
                                                }"
                                            >
                                                <span
                                                    v-html="link.label"
                                                ></span>
                                            </button>
                                        </template>
                                    </template>
                                </div>
                            </div>
                            <!-- Total priorities -->
                            <div class="flex items-center">
                                <span class="text-sm text-gray-700"
                                    >Showing {{ priorities.from }} to
                                    {{ priorities.to }} of
                                    {{ priorities.total }} Prioritys
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Priority Modal -->
        <Modal
            :show="showAddPriorityModal"
            @close="showAddPriorityModal = false"
            maxWidth="md"
        >
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Add New Priority</h2>
                <form @submit.prevent="submitCreateForm">
                    <div class="mb-4">
                        <InputLabel for="plantName" value="Priority Name" />
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full"
                            autofocus
                            v-model="createForm.name"
                            required
                        />
                        <InputError :message="createForm.errors.name" />
                    </div>

                    <div class="flex justify-end gap-2">
                        <button
                            type="submit"
                            class="bg-transparent px-4 rounded-lg text-blue-500 whitespace-nowrap text-center transition duration-300 hover:bg-blue-500 hover:text-white py-1"
                            :disabled="createForm.processing"
                        >
                            <i class="fas fa-save"></i> Save
                        </button>
                        <button
                            type="button"
                            @click="cancelCreate"
                            class="bg-transparent px-4 rounded-lg text-red-500 whitespace-nowrap text-center transition duration-300 hover:bg-red-500 hover:text-white py-1"
                        >
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Edit Priority Modal -->
        <Modal
            :show="showEditPriorityModal"
            @close="showEditPriorityModal = false"
            maxWidth="md"
        >
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Edit Priority</h2>
                <form @submit.prevent="submitEditForm">
                    <div class="mb-4">
                        <InputLabel
                            for="editPriorityName"
                            value="Priority Name"
                        />
                        <TextInput
                            id="editName"
                            type="text"
                            class="mt-1 block w-full"
                            autofocus
                            v-model="editForm.name"
                            required
                        />
                        <InputError :message="editForm.errors.name" />
                    </div>

                    <div class="flex justify-end gap-2">
                        <button
                            type="submit"
                            class="bg-transparent px-4 rounded-lg text-blue-500 whitespace-nowrap text-center transition duration-300 hover:bg-blue-500 hover:text-white py-1"
                            :disabled="editForm.processing"
                        >
                            <i class="fas fa-save"></i> Update
                        </button>
                        <button
                            type="button"
                            @click="cancelEdit"
                            class="bg-transparent px-4 rounded-lg text-red-500 whitespace-nowrap text-center transition duration-300 hover:bg-red-500 hover:text-white py-1"
                        >
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </div>
</template>
