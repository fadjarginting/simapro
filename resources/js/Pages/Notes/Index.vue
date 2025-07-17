<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Modal from "@/Components/Modal.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";

defineOptions({
    layout: AuthenticatedLayout,
});

// Props
const props = defineProps({
    notes: {
        type: Object,
        default: () => ({})
    },
    filters: {
        type: Object,
        default: () => ({})
    }
});

// Reactive data
const showAddNoteModal = ref(false);
const showEditNoteModal = ref(false);
const showDeleteModal = ref(false);
const selectedNote = ref(null);
const search = ref(props.filters.search || '');

// Forms
const createForm = useForm({
    content: '',
});

const editForm = useForm({
    id: null,
    content: '',
});

const deleteForm = useForm({});

// Computed
const filteredNotes = computed(() => {
    // The search is now handled by the backend, so we just display the data from props
    return props.notes.data || [];
});

// Modal methods
const openAddModal = () => {
    createForm.reset();
    createForm.clearErrors();
    showAddNoteModal.value = true;
};

const openEditModal = (note) => {
    selectedNote.value = note;
    editForm.id = note.id;
    editForm.content = note.content;
    editForm.clearErrors();
    showEditNoteModal.value = true;
};

const openDeleteModal = (note) => {
    selectedNote.value = note;
    showDeleteModal.value = true;
};

const cancelCreate = () => {
    showAddNoteModal.value = false;
    createForm.reset();
    createForm.clearErrors();
};

const cancelEdit = () => {
    showEditNoteModal.value = false;
    editForm.reset();
    editForm.clearErrors();
    selectedNote.value = null;
};

const cancelDelete = () => {
    showDeleteModal.value = false;
    selectedNote.value = null;
};

// CRUD operations
const createNote = () => {
    createForm.post(route('notes.store'), {
        onSuccess: () => {
            showAddNoteModal.value = false;
            createForm.reset();
        },
    });
};

const updateNote = () => {
    editForm.put(route('notes.update', editForm.id), {
        onSuccess: () => {
            showEditNoteModal.value = false;
            editForm.reset();
            selectedNote.value = null;
        },
    });
};

const deleteNote = () => {
    deleteForm.delete(route('notes.destroy', selectedNote.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            selectedNote.value = null;
        },
    });
};

// Search functionality
const performSearch = () => {
    router.get(route('notes.index'), { search: search.value }, {
        preserveState: true,
        replace: true,
    });
};

const clearSearch = () => {
    search.value = '';
    router.get(route('notes.index'), {}, {
        preserveState: true,
        replace: true,
    });
};

// Format date
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>

    <Head>
        <title>Notes Management</title>
    </Head>

    <div class="py-6">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border rounded-2xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100">
                    <div class="flex flex-wrap justify-between items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                                <i class="fas fa-sticky-note text-white text-lg"></i>
                            </div>
                            <div>
                                <h1 class="text-xl font-bold text-gray-900 tracking-tight">
                                    Notes Management
                                </h1>
                                <p class="text-sm text-gray-600">
                                    Create, view, and manage your notes.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <!-- Search -->
                            <div class="relative">
                                <input v-model="search" @keyup.enter="performSearch" type="text"
                                    placeholder="Search notes..."
                                    class="w-full sm:w-64 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm pr-10" />
                                <button @click="performSearch"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-search text-sm"></i>
                                </button>
                            </div>
                            <button v-if="search" @click="clearSearch"
                                class="px-2 py-2 text-sm text-gray-500 hover:text-gray-700" title="Clear search">
                                <i class="fas fa-times"></i>
                            </button>

                            <!-- Add Button -->
                            <button @click="openAddModal"
                                class="bg-gradient-to-r from-green-500 to-teal-400 px-4 py-2 text-sm rounded-lg font-semibold text-white transition duration-300 hover:from-green-600 hover:to-teal-500 shadow-md hover:shadow-lg flex items-center">
                                <i class="fas fa-plus mr-2 text-sm"></i>
                                Add Note
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Content Area -->
                <div class="p-6 space-y-6">
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full table-auto">
                                <thead class="bg-gray-50">
                                    <tr class="text-sm font-semibold text-gray-700 border-b border-gray-200">
                                        <th class="px-6 py-4 text-left">Content</th>
                                        <th class="px-6 py-4 text-left">Created By</th>
                                        <th class="px-6 py-4 text-left">Created At</th>
                                        <th class="px-6 py-4 text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm text-gray-700 divide-y divide-gray-200">
                                    <tr v-for="note in filteredNotes" :key="note.id"
                                        class="hover:bg-gray-50 transition-colors duration-200">
                                        <td class="px-6 py-4">
                                            <div class="max-w-md truncate" :title="note.content">
                                                {{ note.content }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                    <i class="fas fa-user text-blue-600 text-xs"></i>
                                                </div>
                                                <span class="font-medium">{{ note.creator?.name || 'Unknown'
                                                    }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600">
                                            {{ formatDate(note.created_at) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-center space-x-2">
                                                <button @click="openEditModal(note)"
                                                    class="bg-blue-100 hover:bg-blue-200 text-blue-600 px-3 py-2 rounded-md text-xs font-medium transition duration-200 flex items-center"
                                                    title="Edit note">
                                                    <i class="fas fa-edit mr-1"></i>
                                                    Edit
                                                </button>
                                                <button @click="openDeleteModal(note)"
                                                    class="bg-red-100 hover:bg-red-200 text-red-600 px-3 py-2 rounded-md text-xs font-medium transition duration-200 flex items-center"
                                                    title="Delete note">
                                                    <i class="fas fa-trash mr-1"></i>
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Empty State -->
                                    <tr v-if="!filteredNotes.length">
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <i class="fas fa-sticky-note text-4xl text-gray-300 mb-4"></i>
                                                <p class="text-lg font-medium">No notes found</p>
                                                <p class="text-sm">{{ search ? 'Try adjusting your search criteria'
                                                    :
                                                    'Get started by creating your first note' }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="props.notes.links && props.notes.links.length > 3"
                            class="flex justify-between items-center p-4 bg-gray-50 border-t">
                            <div class="flex items-center text-sm text-gray-700">
                                Showing {{ props.notes.from || 0 }} to {{ props.notes.to || 0 }}
                                of {{ props.notes.total || 0 }} results
                            </div>
                            <div class="flex items-center space-x-1">
                                <template v-for="link in props.notes.links" :key="link.label">
                                    <button v-if="link.url" @click="router.get(link.url, { search: search }, { preserveState: true })" :class="[
                                            'px-3 py-1 text-sm rounded-md',
                                            link.active
                                                ? 'bg-blue-500 text-white'
                                                : 'bg-white text-gray-700 hover:bg-gray-100 border border-gray-300'
                                        ]" v-html="link.label" />
                                    <span v-else :class="[
                                            'px-3 py-1 text-sm rounded-md',
                                            'bg-gray-100 text-gray-400 cursor-not-allowed'
                                        ]" v-html="link.label" />
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Note Modal -->
    <Modal :show="showAddNoteModal" @close="cancelCreate" maxWidth="lg">
        <div class="p-6">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-plus text-green-600"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-900">Add New Note</h2>
            </div>

            <form @submit.prevent="createNote">
                <div class="mb-6">
                    <InputLabel for="content" value="Note Content" />
                    <textarea id="content" v-model="createForm.content"
                        class="mt-2 block w-full h-32 px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                        placeholder="Enter your note content here..." required autofocus></textarea>
                    <InputError :message="createForm.errors.content" class="mt-2" />
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" @click="cancelCreate"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </button>
                    <button type="submit" :disabled="createForm.processing"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                        <i class="fas fa-save mr-2"></i>
                        {{ createForm.processing ? 'Saving...' : 'Save Note' }}
                    </button>
                </div>
            </form>
        </div>
    </Modal>

    <!-- Edit Note Modal -->
    <Modal :show="showEditNoteModal" @close="cancelEdit" maxWidth="lg">
        <div class="p-6">
            <div class="flex items-center mb-6">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-edit text-blue-600"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-900">Edit Note</h2>
            </div>

            <form @submit.prevent="updateNote">
                <div class="mb-6">
                    <InputLabel for="editContent" value="Note Content" />
                    <textarea id="editContent" v-model="editForm.content"
                        class="mt-2 block w-full h-32 px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                        placeholder="Enter your note content here..." required autofocus></textarea>
                    <InputError :message="editForm.errors.content" class="mt-2" />
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" @click="cancelEdit"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </button>
                    <button type="submit" :disabled="editForm.processing"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                        <i class="fas fa-save mr-2"></i>
                        {{ editForm.processing ? 'Updating...' : 'Update Note' }}
                    </button>
                </div>
            </form>
        </div>
    </Modal>

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="cancelDelete" maxWidth="md">
        <div class="p-6">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Delete Note</h2>
                    <p class="text-sm text-gray-600 mt-1">This action cannot be undone</p>
                </div>
            </div>

            <div class="mb-6">
                <p class="text-gray-700">
                    Are you sure you want to delete this note?
                    <span v-if="selectedNote?.works_count > 0" class="text-red-600 font-medium">
                        This note has {{ selectedNote.works_count }} associated work(s) that may be affected.
                    </span>
                </p>
                <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm font-medium text-gray-900">Note content:</p>
                    <p class="text-sm text-gray-600 mt-1">{{ selectedNote?.content }}</p>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" @click="cancelDelete"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    <i class="fas fa-times mr-2"></i>
                    Cancel
                </button>
                <button @click="deleteNote" :disabled="deleteForm.processing"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50">
                    <i class="fas fa-trash mr-2"></i>
                    {{ deleteForm.processing ? 'Deleting...' : 'Delete Note' }}
                </button>
            </div>
        </div>
    </Modal>
</template>