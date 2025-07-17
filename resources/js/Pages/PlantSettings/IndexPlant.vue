<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { ref, onMounted, watch } from "vue";
import Swal from 'sweetalert2';

defineOptions({
    layout: AuthenticatedLayout,
});

const props = defineProps({
    plants: Object,
    filters: Object,
    errors: Object,
});

const showAddPlantModal = ref(false);
const showEditPlantModal = ref(false);
const selectedPlant = ref(null);

// Form for adding a new plant
const createForm = useForm({
    name: '',
});

// Form for editing an existing plant
const editForm = useForm({
    id: '',
    name: '',
});

// Form state that syncs with URL parameters
const form = ref({
    search: props.filters.search || '',
    perPage: props.filters.perPage || 10,
    page: props.plants.current_page || 1
});

// Initialize search query from filters
onMounted(() => {
    form.value.search = props.filters?.search || '';
});

// Debounce search to prevent too many requests
let timeout;
const performSearch = () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route('plants.index'), {
            search: form.value.search,
            perPage: form.value.perPage,
            page: 1 // Always reset to first page on new search
        }, {
            preserveState: true,
            replace: true
        });
    }, 300);
};

// Watch for changes in search to trigger search
watch(() => form.value.search, performSearch);
watch(() => form.value.perPage, () => {
    router.get(route('plants.index'), {
        search: form.value.search,
        perPage: form.value.perPage,
        page: 1 // Reset to first page when changing items per page
    }, {
        preserveState: true,
        replace: true
    });
});

const openAddModal = () => {
    createForm.reset();
    showAddPlantModal.value = true;
};

const openEditModal = (plant) => {
    selectedPlant.value = plant;
    editForm.id = plant.id;
    editForm.name = plant.name;
    showEditPlantModal.value = true;
};

// submit with sweetalert2 confirmation
const submitCreateForm = () => {
    showAddPlantModal.value = false;
    Swal.fire({
        title: 'Add Plant',
        text: "Are you sure you want to add this plant?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Add',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 mr-2',
            cancelButton: 'bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 ml-2'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            createForm.post(route('plants.store'), {
                onSuccess: () => {
                    createForm.reset();
                    showAddPlantModal.value = false;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'The plant has been added successfully.',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                },
                onError: (errors) => {
                    showAddPlantModal.value = true;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: errors.name || 'Failed to add the plant. Please try again.',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
            });
        } else {
            showAddPlantModal.value = true;
        }
    });
};

const submitEditForm = () => {
    showEditPlantModal.value = false;
    Swal.fire({
        title: 'Update Plant',
        text: "Are you sure you want to update this plant?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Update',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 mr-2',
            cancelButton: 'bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 ml-2'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            editForm.put(route('plants.update', editForm.id ), {
                onSuccess: () => {
                    editForm.reset();
                    showEditPlantModal.value = false;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'The plant has been updated successfully.',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                },
                onError: (errors) => {
                    showEditPlantModal.value = true;
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: errors.name || 'Failed to update the plant. Please try again.',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                }
            });
        } else {
            showEditPlantModal.value = true;
        }
    });
};

const cancelCreate = () => {
    createForm.reset();
    showAddPlantModal.value = false;
};

const cancelEdit = () => {
    editForm.reset();
    showEditPlantModal.value = false;
};

// Function to delete a plant with SweetAlert2 confirmation
const deletePlant = (id) => {
    Swal.fire({
        title: 'Confirm Deletion',
        text: "Are you sure you want to delete this plant? This action cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#9CA3AF',
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'rounded-lg shadow-lg',
            title: 'text-lg font-semibold text-gray-800',
            htmlContainer: 'text-sm text-gray-600',
            confirmButton: 'px-4 py-2 rounded-md text-white bg-red-500 hover:bg-red-600',
            cancelButton: 'px-4 py-2 rounded-md text-gray-700 bg-gray-200 hover:bg-gray-300'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('plants.destroy', id), {
                onSuccess: (page) => {
                    if (page.props.flash.error) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: page.props.flash.error,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'rounded-lg shadow-lg',
                                title: 'text-sm font-semibold text-gray-800',
                            }
                        });
                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'The plant has been successfully deleted.',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            customClass: {
                                popup: 'rounded-lg shadow-lg',
                                title: 'text-sm font-semibold text-gray-800',
                            }
                        });
                    }
                },
                onError: (errors) => {
                    Swal.fire(
                        'Error!',
                        'An error occurred while deleting the plant.',
                        'error'
                    );
                }
            });
        }
    });
};

// Handle page change for pagination links
const changePage = (url) => {
    if (!url) return;

    router.visit(url, {
        preserveState: true,
        replace: true
    });
};
</script>

<template>
    <Head>
        <title>Plant Settings</title>
    </Head>

    <div class="py-6">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border rounded-2xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100">
                    <div class="flex flex-wrap justify-between items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                                <i class="fas fa-industry text-white text-lg"></i>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold text-gray-900 tracking-tight">
                                    Plant Settings
                                </h1>
                                <p class="text-sm text-gray-600">
                                    Manage plant data for the system.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                             <button @click="openAddModal"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <i class="fas fa-plus"></i>
                                <span>Add Plant</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6 space-y-6">
                    <!-- Filters and search -->
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <InputLabel for="search" value="Search" class="font-semibold" />
                                <div class="relative mt-1">
                                    <TextInput v-model="form.search" type="text" placeholder="Search plants..."
                                        class="w-full" />
                                    <span v-if="form.search" @click="form.search = ''"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-times"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table and Data -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead class="bg-gray-50">
                                    <tr class="text-sm font-semibold text-gray-600 text-left">
                                        <th class="px-6 py-3">Plant Name</th>
                                        <th class="px-6 py-3 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm text-gray-700 divide-y divide-gray-200">
                                    <tr v-if="plants.data.length === 0">
                                        <td colspan="2" class="px-6 py-16 text-center text-gray-500">
                                            No plants found.
                                        </td>
                                    </tr>
                                    <tr v-for="plant in plants.data" :key="plant.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ plant.name }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <button @click="openEditModal(plant)"
                                                    class="text-blue-600 hover:text-blue-900 transition duration-150 ease-in-out">
                                                    <i class="fas fa-edit mr-1"></i>
                                                    <span>Edit</span>
                                                </button>
                                                <button @click="deletePlant(plant.id)"
                                                    class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out">
                                                    <i class="fas fa-trash mr-1"></i>
                                                    <span>Delete</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Pagination -->
                        <div class="flex flex-wrap justify-between items-center p-4 bg-gray-50 border-t">
                            <div class="flex items-center text-sm text-gray-700">
                                <span>Show</span>
                                <select v-model="form.perPage"
                                    class="mx-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                    <option value="20">20</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                </select>
                                <span>entries</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <template v-for="(link, i) in plants.links" :key="i">
                                    <button @click="changePage(link.url)" :disabled="!link.url"
                                        class="px-3 py-1 text-sm rounded-md min-w-[32px]" :class="{
                                            'bg-blue-500 text-white': link.active,
                                            'bg-white text-gray-700 hover:bg-gray-200 border': !link.active && link.url,
                                            'bg-gray-50 text-gray-400 cursor-default': !link.url,
                                        }" v-html="link.label">
                                    </button>
                                </template>
                            </div>
                            <div class="text-sm text-gray-700">
                                Showing {{ plants.from }} to {{ plants.to }} of {{ plants.total }} results
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Plant Modal -->
        <Modal :show="showAddPlantModal" @close="showAddPlantModal = false" maxWidth="md">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Add New Plant</h2>
                <form @submit.prevent="submitCreateForm">
                    <div class="mb-4">
                        <InputLabel for="plantName" value="Plant Name" />
                        <TextInput id="name" type="text" class="mt-1 block w-full" autofocus v-model="createForm.name"
                            required />
                        <InputError :message="createForm.errors.name" />
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" @click="cancelCreate" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" :disabled="createForm.processing">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Edit Plant Modal -->
        <Modal :show="showEditPlantModal" @close="showEditPlantModal = false" maxWidth="md">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Edit Plant</h2>
                <form @submit.prevent="submitEditForm">
                    <div class="mb-4">
                        <InputLabel for="editPlantName" value="Plant Name" />
                        <TextInput id="editName" type="text" class="mt-1 block w-full" autofocus v-model="editForm.name"
                            required />
                        <InputError :message="editForm.errors.name" />
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                         <button type="button" @click="cancelEdit" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" :disabled="editForm.processing">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </div>
</template>
