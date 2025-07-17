<template>
    <div class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border rounded-2xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                        <i class="fas fa-folder-open text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight">
                            Work Documents
                        </h2>
                    </div>
                </div>
                <button 
                    v-if="documents.length > 0"
                    @click="showAddDocModal = true"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-sm font-semibold rounded-md hover:from-blue-700 hover:to-purple-700 shadow transition"
                >
                    <i class="fas fa-plus text-xs"></i>
                    Add Document
                </button>
            </div>
        </div>

        <!-- Content Body -->
        <div class="p-4">
            <!-- Document List -->
            <div v-if="documents.length > 0" :class="documentContainerClass">
                <div v-for="(document, index) in documents" :key="index" 
                    class="flex items-center justify-between p-3 rounded-lg border border-gray-200 bg-white hover:bg-blue-50 hover:shadow-md transition-all duration-200">
                    <div class="flex items-center space-x-3 min-w-0">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center border border-blue-200">
                                <i class="fas fa-file-alt text-blue-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate" :title="document.title">{{ document.title }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ document.document_name }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-1.5 flex-shrink-0 ml-2">
                        <button
                            @click="downloadDocument(document)"
                            class="w-7 h-7 flex items-center justify-center text-blue-600 bg-blue-100 hover:bg-blue-200 rounded-full transition shadow-sm"
                            title="Download"
                        >
                            <i class="fas fa-download text-sm"></i>
                        </button>
                        <button
                            @click="removeDocument(document.id)"
                            class="w-7 h-7 flex items-center justify-center text-red-600 bg-red-100 hover:bg-red-200 rounded-full transition shadow-sm"
                            title="Delete"
                        >
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Empty State -->
            <div v-else class="py-10 text-center">
                <div class="w-14 h-14 bg-gradient-to-br from-gray-100 to-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow">
                    <i class="fas fa-file-alt text-blue-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No Documents Yet</h3>
                <p class="text-sm text-gray-500 mb-6 max-w-sm mx-auto">Upload documents related to this work to get started.</p>
                <button 
                    @click="showAddDocModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 shadow transition"
                >
                    <i class="fas fa-plus"></i>
                    Add New Document
                </button>
            </div>
        </div>

        <!-- Add Document Modal -->
        <div v-if="showAddDocModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
            <div class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-xl bg-white">
                <div class="mt-3">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">
                            Add New Document
                        </h3>
                        <button @click="closeAddDocModal" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label for="doc-title" class="block text-sm font-medium text-gray-700 mb-1">Document Title</label>
                            <input
                                id="doc-title"
                                v-model="newDocument.title"
                                type="text"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 transition"
                                placeholder="e.g., Engineering Drawing"
                            />
                        </div>
                        <div>
                            <label for="doc-file" class="block text-sm font-medium text-gray-700 mb-1">Upload File</label>
                            <input
                                id="doc-file"
                                type="file"
                                @change="handleFileUpload"
                                class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                            />
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button 
                            @click="closeAddDocModal"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition"
                        >
                            Cancel
                        </button>
                        <button 
                            @click="uploadDocument"
                            :disabled="!isFormValid || isUploading"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                        >
                            <span v-if="isUploading">
                                <i class="fas fa-spinner fa-spin mr-2"></i>Uploading...
                            </span>
                            <span v-else>Upload</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    work: Object,
    documents: {
        type: Array,
        default: () => []
    }
});

const showAddDocModal = ref(false)
const isUploading = ref(false)

const newDocument = reactive({
    title: '',
    type: '',
    file: null
})

const isFormValid = computed(() => {
    return newDocument.title && newDocument.file;
});

const documentContainerClass = computed(() => {
    return props.documents.length > 5 
        ? 'space-y-3 overflow-y-auto max-h-[22rem] pr-2'
        : 'space-y-3';
});

const handleFileUpload = (event) => {
    newDocument.file = event.target.files[0];
}

const closeAddDocModal = () => {
    showAddDocModal.value = false;
    newDocument.title = '';
    newDocument.type = '';
    newDocument.file = null;
}

const uploadDocument = () => {
    if (!isFormValid.value) return;

    isUploading.value = true;
    
    const formData = new FormData();
    formData.append('title', newDocument.title);
    formData.append('type', newDocument.type);
    formData.append('file', newDocument.file);
    formData.append('work_id', props.work.id);

    router.post(route('works.documents.store', props.work.slug), formData, {
        onSuccess: () => {
            closeAddDocModal();
        },
        onFinish: () => {
            isUploading.value = false;
        }
    });
}

const downloadDocument = (document) => {
    window.open(document.file_url, '_blank');
}

const removeDocument = (documentId) => {
    if (confirm('Are you sure you want to delete this document?')) {
        router.delete(route('works.documents.destroy', [props.work.id, documentId]), {
            preserveScroll: true
        });
    }
}

const formatDate = (dateString) => {
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '';
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    }).format(date);
}
</script>
