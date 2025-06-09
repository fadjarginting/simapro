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
            preserveScroll: true
        }
    );
}, { debounce: 300 });

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
    if (isEditing.value) {
        router.put(route('disciplines.update', editingDiscipline.value.id), form.value, {
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    title: 'Success!',
                    text: 'Discipline updated successfully',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            onError: (errors) => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to update discipline',
                    icon: 'error'
                });
            }
        });
    } else {
        router.post(route('disciplines.store'), form.value, {
            onSuccess: () => {
                closeModal();
                Swal.fire({
                    title: 'Success!',
                    text: 'Discipline created successfully',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            },
            onError: (errors) => {
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to create discipline',
                    icon: 'error'
                });
            }
        });
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

    <template name=header>
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Disciplines Management
        </h2>
    </template>

    <div class="container mx-auto px-4 py-12">
        <div class="mx-auto max-w-full sm:px-6 lg:px-8">
            <div class="flex flex-wrap -mx-3">
                <div class="flex-none w-full max-w-full px-3">
                    <div
                        class="relative flex flex-col min-w-0 mb-6 break-words bg-white border-0 border-transparent border-solid shadow-xl dark:bg-slate-850 dark:shadow-dark-xl sm:rounded-lg bg-clip-border">
                        <div
                            class="flex items-center justify-between p-6 pb-0 mb-0 border-b-0 border-b-solid border-b-transparent">
                            <h6 class="dark:text-white text-base font-bold">
                                Disciplines Management
                            </h6>
                            <div class="flex items-center justify-end">
                                <button @click="openCreateModal"
                                    class="bg-transparent px-2.5 text-xs rounded py-1.4 inline-block whitespace-nowrap text-center font-bold leading-none text-green-500 transition duration-300 hover:bg-gradient-to-tl hover:from-green-500 hover:to-teal-400 hover:text-white">
                                    <i class="fas fa-plus mr-2 text-xs leading-none"></i>
                                    <span>Add Discipline</span>
                                </button>
                            </div>
                        </div>

                        <!-- Filters and search -->
                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="search" value="Search Discipline" />
                                <TextInput id="search" v-model="search" type="text" class="mt-1 block w-full"
                                    placeholder="Search by name or description..." />
                            </div>
                        </div>

                        <div class="flex-auto px-0 pt-0 pb-2">
                            <div class="p-0 overflow-x-auto">
                                <table class="w-full table-auto">
                                    <thead class="bg-gray-100">
                                        <tr class="text-sm font-normal text-gray-600 border-t border-b text-left">
                                            <th class="px-4 py-3">Discipline Name</th>
                                            <th class="px-4 py-3">Description</th>
                                            <th class="px-4 py-3">Created At</th>
                                            <th class="px-4 py-3 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-sm font-normal text-gray-700">
                                        <tr v-if="disciplines.data.length > 0" v-for="discipline in disciplines.data"
                                            :key="discipline.id" class="border-b hover:bg-gray-50">
                                            <td class="px-4 py-3 font-medium">
                                                {{ discipline.name }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <span v-if="discipline.description" class="text-gray-600">
                                                    {{ discipline.description.length > 50
                                                    ? discipline.description.substring(0, 50) + '...'
                                                    : discipline.description }}
                                                </span>
                                                <span v-else class="text-gray-400 italic">No description</span>
                                            </td>
                                            <td class="px-4 py-3 text-gray-500">
                                                {{ new Date(discipline.created_at).toLocaleDateString() }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <div class="flex justify-center space-x-2">
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
                                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                                <div class="flex flex-col items-center">
                                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                                                    <p class="text-lg">No disciplines found</p>
                                                    <p class="text-sm text-gray-400 mt-1">
                                                        {{ search ? 'Try adjusting your search criteria' : 'Click "Add Discipline" to create your first discipline' }}
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div v-if="disciplines.links && disciplines.links.length > 3" class="px-6 py-4">
                                <nav class="flex justify-center">
                                    <div class="flex space-x-1">
                                        <template v-for="link in disciplines.links" :key="link.label">
                                            <button v-if="link.url" @click="router.get(link.url)" :class="[
                                                    'px-3 py-2 text-sm border rounded',
                                                    link.active 
                                                        ? 'bg-blue-500 text-white border-blue-500' 
                                                        : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                                                ]" v-html="link.label">
                                            </button>
                                            <span v-else :class="[
                                                    'px-3 py-2 text-sm border rounded',
                                                    'bg-gray-100 text-gray-400 border-gray-300'
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
    </div>

    <!-- Modal for Add/Edit Discipline -->
    <Modal :show="showModal" @close="closeModal" max-width="md">
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
                    <p class="text-xs text-gray-500 mt-1">
                        {{ form.description ? form.description.length : 0 }}/1000 characters
                    </p>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <SecondaryButton @click="closeModal">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton type="submit">
                        {{ isEditing ? 'Update' : 'Create' }} Discipline
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Modal>
</template>