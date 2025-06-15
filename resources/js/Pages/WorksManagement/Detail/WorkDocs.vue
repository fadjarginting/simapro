<template>
    <div class="bg-white shadow-sm border border-gray-200 sm:rounded-lg">
        <div class="px-4 py-4 sm:px-6 sm:py-5">
            <div class="flex justify-between items-center mb-3 lg:mb-4">
                <h3 class="text-base font-semibold leading-6 text-gray-900">
                    Work Documents
                </h3>
                <button 
                    @click="showAddDocModal = true"
                    class="px-3 py-1 text-xs bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                >
                    Add Document
                </button>
            </div>
            
            <div v-if="documents.length > 0" :class="documentContainerClass">
                <div v-for="(document, index) in documents" :key="index" 
                    class="flex items-center justify-between p-4 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-alt text-white"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ document.title }}</p>
                            <p class="text-xs text-gray-600">{{ document.document_name }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button
                            @click="downloadDocument(document)"
                            class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition-colors"
                            title="Download"
                        >
                            <i class="fas fa-download"></i>
                        </button>
                        <button
                            @click="removeDocument(document.id)"
                            class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors"
                            title="Delete"
                        >
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div v-else class="flex flex-col items-center justify-center py-12 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-3">
                    <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                </div>
                <h3 class="text-base font-medium text-gray-900 mb-1">No documents yet</h3>
                <p class="text-sm text-gray-500 mb-4">Upload documents related to this work</p>
                <button 
                    @click="showAddDocModal = true"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                >
                    Add New Document
                </button>
            </div>
        </div>

        <!-- Add Document Modal -->
        <div v-if="showAddDocModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Add New Document
                    </h3>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Document Title</label>
                        <input
                            v-model="newDocument.title"
                            type="text"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md"
                            placeholder="Enter document title"
                        />
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload File</label>
                        <input
                            type="file"
                            @change="handleFileUpload"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md"
                        />
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button 
                            @click="closeAddDocModal"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400"
                        >
                            Cancel
                        </button>
                        <button 
                            @click="uploadDocument"
                            :disabled="!isFormValid || isUploading"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
                        >
                            <span v-if="isUploading">Uploading...</span>
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
    // If there are more than 5 documents, add scroll properties
    return props.documents.length > 5 
        ? 'space-y-4 overflow-y-auto max-h-80'
        : 'space-y-4';
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
            isUploading.value = false;
        },
        onError: () => {
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
