<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import Modal from "@/Components/Modal.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import Swal from 'sweetalert2';
import { ref, watch, onMounted } from "vue";

defineOptions({
    layout: AuthenticatedLayout,
});

const props = defineProps({
    disciplines: Object,
    filters: Object,
});

// Reactive variables
const search = ref(props.filters.search || '');
const showModal = ref(false);
const isEditing = ref(false);
const editingDiscipline = ref(null);

// Form data
const form = ref({
    name: '',
    description: '',
});

// Search functionality
watch(search, (newValue) => {
    router.get(route('disciplines.index'),
        { search: newValue },
        {
            preserveState: true,
            preserveScroll: true,
            debounce: 300
        }
    );
});

// Modal functions
const openCreateModal = () => {
    isEditing.value = false;
    editingDiscipline.value = null;
    form.value = {
        name: '',
        description: '',
    };
    showModal.value = true;
};

const openEditModal = (discipline) => {
    isEditing.value = true;
    editingDiscipline.value = discipline;
    form.value = {
        name: discipline.name,
        description: discipline.description || '',
    };
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.value = {
        name: '',
        description: '',
    };
    isEditing.value = false;
    editingDiscipline.value = null;
};

// Submit form
const submitForm = () => {
    const options = {
        onSuccess: () => {
            closeModal();
            Swal.fire({
                title: 'Success!',
                text: `Discipline ${isEditing.value ? 'updated' : 'created'} successfully`,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        },
        onError: (errors) => {
            // This part can be enhanced to show specific validation errors
            Swal.fire({
                title: 'Error!',
                text: `Failed to ${isEditing.value ? 'update' : 'create'} discipline`,
                icon: 'error'
            });
        }
    };

    if (isEditing.value) {
        router.put(route('disciplines.update', editingDiscipline.value.id), form.value, options);
    } else {
        router.post(route('disciplines.store'), form.value, options);
    }
};

// Delete discipline
const deleteDiscipline = (discipline) => {
    Swal.fire({
        title: 'Are you sure?',
        text: `Delete discipline "${discipline.name}"? This action cannot be undone!`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('disciplines.destroy', discipline.id), {
                onSuccess: () => {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Discipline has been deleted.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                onError: () => {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to delete discipline',
                        icon: 'error'
                    });
                }
            });
        }
    });
};

// Handle flash messages
onMounted(() => {
    const page = usePage();
    if (page.props.flash?.success) {
        Swal.fire({
            title: 'Success!',
            text: page.props.flash.success,
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    }
    if (page.props.flash?.error) {
        Swal.fire({
            title: 'Error!',
            text: page.props.flash.error,
            icon: 'error'
        });
    }
});
</script>

<template>

    <Head title="Disciplines Management" />

    <div class="py-6">
        <div class="mx-auto max-w-full sm:px-6 lg:px-8">
            <div
                class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border rounded-2xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100">
                    <div class="flex flex-wrap justify-between items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                                <i class="fas fa-book text-white text-lg"></i>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold text-gray-900 tracking-tight">
                                    Disciplines Management
                                </h1>
                                <p class="text-sm text-gray-600">
                                    Create, search, and manage disciplines.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <PrimaryButton @click="openCreateModal">
                                <i class="fas fa-plus mr-2"></i>
                                Add Discipline
                            </PrimaryButton>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="p-6 space-y-6">
                    <!-- Table Card -->
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <!-- Filters and search -->
                        <div class="p-6 border-b border-gray-200">
                            <InputLabel for="search" value="Search Discipline" />
                            <TextInput id="search" v-model="search" type="text" class="mt-1 block w-full md:w-1/2"
                                placeholder="Search by name or description..." />
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead class="bg-gray-50">
                                    <tr class="text-sm font-semibold text-gray-600 text-left">
                                        <th class="px-6 py-3">Discipline Name</th>
                                        <th class="px-6 py-3">Description</th>
                                        <th class="px-6 py-3">Created At</th>
                                        <th class="px-6 py-3 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm text-gray-700 divide-y divide-gray-200">
                                    <tr v-if="disciplines.data.length > 0" v-for="discipline in disciplines.data"
                                        :key="discipline.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium">
                                            {{ discipline.name }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span v-if="discipline.description" class="text-gray-600">
                                                {{ discipline.description.length > 50
                                                ? discipline.description.substring(0, 50) + '...'
                                                : discipline.description }}
                                            </span>
                                            <span v-else class="text-gray-400 italic">No description</span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-500">
                                            {{ new Date(discipline.created_at).toLocaleDateString() }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex justify-center space-x-3">
                                                <button @click="openEditModal(discipline)"
                                                    class="text-blue-500 hover:text-blue-700 transition-colors duration-200"
                                                    title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button @click="deleteDiscipline(discipline)"
                                                    class="text-red-500 hover:text-red-700 transition-colors duration-200"
                                                    title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-else>
                                        <td colspan="4" class="px-6 py-16 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                                <p class="text-lg font-semibold">No disciplines found</p>
                                                <p class="text-sm text-gray-400 mt-1">
                                                    {{ search ? 'Try adjusting your search criteria' : 'Click "Add Discipline" to create one.' }}
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="disciplines.links && disciplines.links.length > 3"
                            class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                            <nav class="flex justify-center">
                                <div class="flex space-x-1">
                                    <template v-for="(link, index) in disciplines.links" :key="index">
                                        <button v-if="link.url" @click="router.get(link.url, {}, { preserveScroll: true })" :class="[
                                                'px-3 py-1.5 text-sm border rounded-md transition',
                                                link.active
                                                    ? 'bg-blue-500 text-white border-blue-500 shadow-sm'
                                                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'
                                            ]" v-html="link.label">
                                        </button>
                                        <span v-else :class="[
                                                'px-3 py-1.5 text-sm border rounded-md',
                                                'bg-gray-100 text-gray-400 border-gray-300 cursor-not-allowed'
                                            ]" v-html="link.label">
                                        </span>
                                    </template>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Add/Edit Discipline -->
    <Modal :show="showModal" @close="closeModal" max-width="lg">
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                {{ isEditing ? 'Edit Discipline' : 'Add New Discipline' }}
            </h3>

            <form @submit.prevent="submitForm" class="space-y-4">
                <div>
                    <InputLabel for="name" value="Discipline Name *" />
                    <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full"
                        placeholder="Enter discipline name" required maxlength="255" />
                </div>

                <div>
                    <InputLabel for="description" value="Description" />
                    <textarea id="description" v-model="form.description"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        rows="4" placeholder="Enter discipline description (optional)" maxlength="1000"></textarea>
                    <p class="text-xs text-gray-500 mt-1 text-right">
                        {{ form.description ? form.description.length : 0 }}/1000
                    </p>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton type="submit" :disabled="!form.name">
                        {{ isEditing ? 'Update' : 'Create' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>
