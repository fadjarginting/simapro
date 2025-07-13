<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2'

const props = defineProps({
    work: Object,
    canEdit: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['updated']);

// Edit mode state
const isEditMode = ref(false);
const editingItem = ref(null);

// Form for editing dates
const form = useForm({
    field: '',
    date: '',
    work_id: props.work.id
});

// Utility functions
const formatDate = (date) => {
    if (!date) return null;
    return new Date(date).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const formatDateForInput = (date) => {
    if (!date) return '';
    return new Date(date).toISOString().split('T')[0];
};

// Timeline data computed from work object
const timelineItems = computed(() => [
    {
        label: 'ERF Masuk',
        field: 'entry_date',
        date: props.work.entry_date,
        color: 'bg-blue-600',
        completed: !!props.work.entry_date
    },
    {
        label: 'ERF Disetujui',
        field: 'erf_approved_date',
        date: props.work.erf_approved_date,
        color: 'bg-green-600',
        completed: !!props.work.erf_approved_date
    },
    {
        label: 'ERF Diklarifikasi',
        field: 'clarification_date',
        date: props.work.clarification_date,
        color: 'bg-yellow-600',
        completed: !!props.work.clarification_date
    },
    {
        label: 'ERF Disahkan',
        field: 'erf_validated_date',
        date: props.work.erf_validated_date,
        color: 'bg-purple-600',
        completed: !!props.work.erf_validated_date
    },
    {
        label: 'Fase Inisiasi Selesai',
        field: 'initiating_target_date',
        date: props.work.initiating_target_date,
        color: 'bg-indigo-600',
        completed: !!props.work.initiating_target_date
    },
    {
        label: 'Start Fase Executing',
        field: 'executing_start_date',
        date: props.work.executing_start_date,
        color: 'bg-orange-600',
        completed: !!props.work.executing_start_date
    },
    {
        label: 'Target Selesai',
        field: 'executing_target_date',
        date: props.work.executing_target_date,
        color: 'bg-red-600',
        completed: !!props.work.executing_target_date
    },
    {
        label: 'Realisasi Selesai',
        field: 'executing_actual_date',
        date: props.work.executing_actual_date,
        color: 'bg-emerald-600',
        completed: !!props.work.executing_actual_date
    }
]);


// Edit functions
const startEdit = (item) => {
    editingItem.value = item.field;
    form.field = item.field;
    form.date = formatDateForInput(item.date);
    form.work_id = props.work.id; // Pastikan work_id selalu ter-set
};

const cancelEdit = () => {
    editingItem.value = null;
    form.reset();
    form.clearErrors();
    // Reset form dengan data awal
    form.field = '';
    form.date = '';
    form.work_id = props.work.id;
};

const saveEdit = () => {
    // Validasi sebelum submit
    if (!form.field) {
        console.error('Field tidak boleh kosong');
        return;
    }

    console.log('Submitting form data:', {
        field: form.field,
        date: form.date,
        work_id: form.work_id
    });

    // Gunakan ID konsisten (bukan slug)
    form.put(route('works.update-timeline', props.work.slug), {
        preserveScroll: true,
        onSuccess: (page) => {
            console.log('Update berhasil');
            editingItem.value = null;
            form.reset();
            form.field = '';
            form.date = '';
            form.work_id = props.work.id;
            emit('updated');
        },
        onError: (errors) => {
            console.error('Update gagal:', errors);
        },
        onFinish: () => {
            console.log('Request selesai');
        }
    });
};
const deleteDate = (item) => {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus ${item.label}? Tindakan ini tidak dapat dibatalkan.`,
        icon: 'warning',
        iconColor: '#F59E0B',
        showCancelButton: true,
        confirmButtonText: 'Hapus',
        confirmButtonColor: '#DC2626', // Warna merah yang lebih pekat
        cancelButtonText: 'Batal',
        cancelButtonColor: '#374151', // Warna abu-abu yang lebih gelap/pekat (gray-700)
        width: '350px',
        backdrop: 'rgba(0, 0, 0, 0.3)',
        customClass: {
            popup: 'rounded-xl shadow-lg',
            confirmButton: 'px-4 py-2 rounded-lg font-medium bg-red-600 hover:bg-red-700',
            cancelButton: 'px-4 py-2 rounded-lg font-medium bg-gray-700 hover:bg-gray-800 text-white'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            form.field = item.field;
            form.date = '';
            form.work_id = props.work.id;

            form.put(route('works.update-timeline', props.work.slug), {
                preserveScroll: true,
                onSuccess: () => {
                    form.reset();
                    form.field = '';
                    form.date = '';
                    form.work_id = props.work.id;
                    emit('updated');
                },
                onError: (errors) => {
                    console.error('Hapus gagal:', errors);
                }
            });
        }
    });
};

const toggleEditMode = () => {
    isEditMode.value = !isEditMode.value;
    if (!isEditMode.value) {
        cancelEdit();
    }
};
</script>

<template>
    <div class="bg-white shadow-sm border border-gray-200 sm:rounded-lg">
        <div class="px-4 py-4 sm:px-6 sm:py-5">
            <div class="flex items-center justify-between mb-3 lg:mb-4">
                <h3 class="text-base font-semibold leading-6 text-gray-900">
                    Timeline
                </h3>

                <!-- Edit Toggle Button -->
                <div v-if="canEdit" class="flex items-center space-x-2">
                    <button @click="toggleEditMode"
                        :class="isEditMode ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-blue-100 text-blue-700 hover:bg-blue-200'"
                        class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium transition-colors duration-200">
                        <i :class="isEditMode ? 'fas fa-times' : 'fas fa-edit'" class="mr-1"></i>
                        {{ isEditMode ? 'Batal' : 'Edit' }}
                    </button>
                </div>
            </div>

            <!-- Debug Info (hapus setelah testing) -->
            <div v-if="form.processing" class="mb-2 p-2 bg-yellow-50 border border-yellow-200 rounded text-xs">
                <div>Processing: {{ form.processing }}</div>
                <div>Field: {{ form.field }}</div>
                <div>Date: {{ form.date }}</div>
                <div>Work ID: {{ form.work_id }}</div>
            </div>

            <!-- Vertical Timeline -->
            <div class="relative">
                <!-- Timeline Line -->
                <div class="absolute left-4 top-6 bottom-6 w-0.5 bg-gray-200" aria-hidden="true"></div>

                <!-- Timeline Items -->
                <div class="space-y-6">
                    <div v-for="(item, index) in timelineItems" :key="index" class="relative flex items-start group">
                        <div :class="item.completed ? item.color : 'bg-gray-300'"
                            class="relative z-10 h-3 w-3 rounded-full ring-4 ring-white transition-colors duration-200">
                            <div v-if="item.completed" class="absolute inset-0 flex items-center justify-center">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                        </div>

                        <div class="ml-4 flex-1">
                            <div class="flex items-center justify-between">
                                <div class="text-xs font-medium text-gray-900">{{ item.label }}</div>
                                <div class="flex items-center space-x-2">
                                    

                                    <!-- Edit Actions -->
                                    <div v-if="isEditMode && canEdit"
                                        class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <button @click="startEdit(item)" :disabled="editingItem === item.field"
                                            class="p-1 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded disabled:opacity-50"
                                            title="Edit tanggal">
                                            <i class="fas fa-pencil-alt text-xs"></i>
                                        </button>
                                        <button v-if="item.completed" @click="deleteDate(item)"
                                            :disabled="form.processing"
                                            class="p-1 text-red-600 hover:text-red-800 hover:bg-red-50 rounded disabled:opacity-50"
                                            title="Hapus tanggal">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Date Display/Edit -->
                            <div class="mt-1">
                                <!-- Edit Mode -->
                                <div v-if="editingItem === item.field" class="flex items-center space-x-2">
                                    <input v-model="form.date" type="date"
                                        class="text-xs border border-gray-300 rounded px-2 py-1 focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                                        :class="{ 'border-red-300': form.errors.date }" />
                                    <button @click="saveEdit" :disabled="form.processing"
                                        class="text-xs bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700 disabled:opacity-50">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button @click="cancelEdit" :disabled="form.processing"
                                        class="text-xs bg-gray-500 text-white px-2 py-1 rounded hover:bg-gray-600 disabled:opacity-50">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <!-- Display Mode -->
                                <div v-else class="text-xs text-gray-500">
                                    {{ formatDate(item.date) || "-" }}
                                </div>

                                <!-- Error Message -->
                                <div v-if="form.errors.date && editingItem === item.field"
                                    class="text-xs text-red-600 mt-1">
                                    {{ form.errors.date }}
                                </div>

                                <!-- Global Errors -->
                                <div v-if="form.errors.error && editingItem === item.field"
                                    class="text-xs text-red-600 mt-1">
                                    {{ form.errors.error }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div v-if="form.processing"
                class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-lg z-50">
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <i class="fas fa-spinner fa-spin"></i>
                    <span>Menyimpan...</span>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.group:hover .group-hover\:opacity-100 {
    opacity: 1;
}
</style>