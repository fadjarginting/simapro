<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2'

const props = defineProps({
    work: Object,
});
// Current user data
const currentUser = computed(() => usePage().props.auth.user.data);

const isWorkLead = computed(() => {
    return props.work.lead_engineer_id === currentUser.value.id;
});


const canEdit = computed(() => {
    return currentUser.value?.roles.includes('Admin') || isWorkLead.value;
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

const error = ref('')

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
        color: 'bg-blue-500',
        icon: 'fas fa-inbox',
        completed: !!props.work.entry_date
    },
    {
        label: 'ERF Disetujui',
        field: 'erf_approved_date',
        date: props.work.erf_approved_date,
        color: 'bg-green-500',
        icon: 'fas fa-thumbs-up',
        completed: !!props.work.erf_approved_date
    },
    {
        label: 'ERF Diklarifikasi',
        field: 'clarification_date',
        date: props.work.clarification_date,
        color: 'bg-yellow-500',
        icon: 'fas fa-comments',
        completed: !!props.work.clarification_date
    },
    {
        label: 'ERF Disahkan',
        field: 'erf_validated_date',
        date: props.work.erf_validated_date,
        color: 'bg-purple-500',
        icon: 'fas fa-stamp',
        completed: !!props.work.erf_validated_date
    },
    {
        label: 'Fase Inisiasi Selesai',
        field: 'initiating_target_date',
        date: props.work.initiating_target_date,
        color: 'bg-indigo-500',
        icon: 'fas fa-flag-checkered',
        completed: !!props.work.initiating_target_date
    },
    {
        label: 'Start Fase Executing',
        field: 'executing_start_date',
        date: props.work.executing_start_date,
        color: 'bg-orange-500',
        icon: 'fas fa-play-circle',
        completed: !!props.work.executing_start_date
    },
    {
        label: 'Target Selesai',
        field: 'executing_target_date',
        date: props.work.executing_target_date,
        color: 'bg-red-500',
        icon: 'fas fa-bullseye',
        completed: !!props.work.executing_target_date
    },
    {
        label: 'Realisasi Selesai',
        field: 'executing_actual_date',
        date: props.work.executing_actual_date,
        color: 'bg-emerald-500',
        icon: 'fas fa-check-double',
        completed: !!props.work.executing_actual_date
    }
]);


// Edit functions
const startEdit = (item) => {
    editingItem.value = item.field;
    form.field = item.field;
    form.date = formatDateForInput(item.date);
    form.work_id = props.work.id;
};

const cancelEdit = () => {
    editingItem.value = null;
    form.reset();
    form.clearErrors();
};

const saveEdit = () => {
    if (!form.field) {
        console.error('Field tidak boleh kosong');
        return;
    }
    form.put(route('works.update-timeline', props.work.slug), {
        preserveScroll: true,
        onSuccess: () => {
            const pageProps = usePage().props;
            if (pageProps.flash && pageProps.flash.error) {
            form.setError('error', pageProps.flash.error);
            } else {
            editingItem.value = null;
            form.reset();
            emit('updated');
            }
        },
        onError: (errors) => {
            console.error('Update gagal:', errors);
        }
    });
};
const deleteDate = (item) => {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: `Apakah Anda yakin ingin menghapus tanggal untuk "${item.label}"?`,
        icon: 'warning',
        iconColor: '#F59E0B',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus',
        confirmButtonColor: '#DC2626',
        cancelButtonText: 'Batal',
        cancelButtonColor: '#374151',
        width: '350px',
        backdrop: 'rgba(0, 0, 0, 0.4)',
        customClass: {
            popup: 'rounded-xl shadow-lg',
            confirmButton: 'px-4 py-2 rounded-lg font-semibold text-white bg-red-600 hover:bg-red-700 transition',
            cancelButton: 'px-4 py-2 rounded-lg font-semibold bg-gray-600 hover:bg-gray-700 text-white transition'
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
    <div class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border rounded-2xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="border-b p-3 bg-gradient-to-r from-blue-100 via-white to-purple-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div
                        class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                        <i class="fas fa-stream text-white text-base"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 tracking-tight">
                            Timeline Pekerjaan
                        </h2>
                        <p class="text-xs text-gray-500">
                            Lacak progres tanggal penting.
                        </p>
                    </div>
                </div>

                <!-- Edit Toggle Button -->
                <div v-if="canEdit" class="flex items-center">
                    <button @click="toggleEditMode"
                        :class="isEditMode ? 'bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700' : 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700'"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-white text-xs font-semibold rounded-md shadow transition">
                        <i :class="isEditMode ? 'fas fa-times' : 'fas fa-edit'" class="text-xs"></i>
                        {{ isEditMode ? 'Batal' : 'Edit' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Body -->
        <div class="p-4 relative">
            <!-- Vertical Timeline -->
            <div class="relative">
                <!-- Timeline Line -->
                <div class="absolute left-3.5 top-0 bottom-0 w-0.5 bg-gray-200" aria-hidden="true"></div>

                <!-- Timeline Items -->
                <div class="space-y-4">
                    <div v-for="(item) in timelineItems" :key="item.field" class="relative flex items-start group">
                        <!-- Timeline Icon -->
                        <div class="relative z-10 flex items-center justify-center">
                            <div :class="[item.completed ? item.color : 'bg-gray-300', 'w-7 h-7 rounded-full flex items-center justify-center ring-4 ring-white']">
                                <i :class="[item.icon, item.completed ? 'text-white' : 'text-gray-500']" class="text-xs"></i>
                            </div>
                        </div>

                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-800">{{ item.label }}</p>
                                
                                <!-- Edit Actions -->
                                <div v-if="isEditMode && canEdit"
                                    class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                    <button @click="startEdit(item)" :disabled="editingItem === item.field"
                                        class="w-6 h-6 flex items-center justify-center text-blue-600 hover:bg-blue-100 rounded-full transition shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                        title="Edit tanggal">
                                        <i class="fas fa-pencil-alt text-xs"></i>
                                    </button>
                                    <button v-if="item.completed" @click="deleteDate(item)"
                                        :disabled="form.processing"
                                        class="w-6 h-6 flex items-center justify-center text-red-600 hover:bg-red-100 rounded-full transition shadow-sm disabled:opacity-50"
                                        title="Hapus tanggal">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Date Display/Edit -->
                            <div class="mt-0.5">
                                <!-- Edit Mode -->
                                <div v-if="editingItem === item.field" class="flex items-center space-x-1.5">
                                    <input v-model="form.date" type="date"
                                        class="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 py-1 px-2"
                                        :class="{ 'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.date }" />
                                    <button @click="saveEdit" :disabled="form.processing"
                                        class="flex-shrink-0 w-7 h-7 flex items-center justify-center bg-green-600 text-white rounded-md hover:bg-green-700 disabled:opacity-50 transition shadow">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                    <button @click="cancelEdit" :disabled="form.processing"
                                        class="flex-shrink-0 w-7 h-7 flex items-center justify-center bg-gray-500 text-white rounded-md hover:bg-gray-600 disabled:opacity-50 transition shadow">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>

                                <!-- Display Mode -->
                                <div v-else class="text-xs text-gray-500">
                                    {{ formatDate(item.date) || "Belum diatur" }}
                                </div>

                                <!-- Error Message -->
                                
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
                class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-lg z-20">
                <div class="flex items-center gap-2 text-sm text-gray-600 bg-gray-100 px-4 py-2 rounded-lg shadow">
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
