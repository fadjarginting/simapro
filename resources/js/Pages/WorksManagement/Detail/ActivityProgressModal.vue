<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';

// Props
const props = defineProps({
    activity: {
        type: Object,
        required: true
    }
});

// Emits
const emit = defineEmits(['close', 'success', 'error']);

// Reactive data
const progressData = ref({
    progress_percentage: 0,
    progress_description: ''
});

const progressHistory = ref([]);
const submitting = ref(false);
const showValidation = ref(false);

// Computed
const isValid = computed(() => {
    return progressData.value.progress_percentage >= 0 &&
        progressData.value.progress_percentage <= 100 &&
        progressData.value.progress_description.trim().length > 0;
});

// Methods
const formatDate = (dateStr) => {
    if (!dateStr) return 'N/A';
    const date = new Date(dateStr);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};

const showErrorAlert = (message) => {
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: message,
        confirmButtonText: 'OK',
        confirmButtonColor: '#ef4444',
        background: '#ffffff',
        customClass: {
            popup: 'rounded-lg shadow-lg',
            title: 'text-lg font-semibold text-gray-900',
            content: 'text-sm text-gray-600',
            confirmButton: 'px-4 py-2 text-sm font-medium rounded-md'
        }
    });
};

const showSuccessAlert = (message) => {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: message,
        confirmButtonText: 'OK',
        confirmButtonColor: '#10b981',
        background: '#ffffff',
        timer: 3000,
        timerProgressBar: true,
        customClass: {
            popup: 'rounded-lg shadow-lg',
            title: 'text-lg font-semibold text-gray-900',
            content: 'text-sm text-gray-600',
            confirmButton: 'px-4 py-2 text-sm font-medium rounded-md'
        }
    });
};

const showValidationAlert = () => {
    let errorMessage = '';

    if (progressData.value.progress_percentage < 0 || progressData.value.progress_percentage > 100) {
        errorMessage = 'Persentase progress harus antara 0-100%';
    } else if (progressData.value.progress_description.trim().length === 0) {
        errorMessage = 'Deskripsi progress tidak boleh kosong';
    } else {
        errorMessage = 'Mohon lengkapi semua field yang diperlukan';
    }

    Swal.fire({
        icon: 'warning',
        title: 'Periksa Input',
        text: errorMessage,
        confirmButtonText: 'OK',
        confirmButtonColor: '#f59e0b',
        background: '#ffffff',
        customClass: {
            popup: 'rounded-lg shadow-lg',
            title: 'text-lg font-semibold text-gray-900',
            content: 'text-sm text-gray-600',
            confirmButton: 'px-4 py-2 text-sm font-medium rounded-md'
        }
    });
};

const submitProgress = async () => {
    showValidation.value = true;

    if (!isValid.value) {
        showValidationAlert();
        return;
    }

    try {
        submitting.value = true;
        const payload = {
            progress_percentage: progressData.value.progress_percentage,
            progress_description: progressData.value.progress_description,
        };

        const response = await axios.post(route('activities.add-progress', props.activity.id), payload);

        if (response.data.success) {
            showSuccessAlert('Progress berhasil disimpan!');
            emit('success', {
                activity: props.activity,
                progress: response.data.progress,
                data: response.data
            });
        } else {
            showErrorAlert(response.data.message || 'Gagal menyimpan progress');
        }
    } catch (error) {
        console.error('Error submitting progress:', error);

        let errorMessage = 'Gagal menyimpan progress';

        if (error.response?.status === 422) {
            // Validation errors
            if (error.response.data.errors) {
                const firstError = Object.values(error.response.data.errors)[0];
                errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
            } else if (error.response.data.message) {
                errorMessage = error.response.data.message;
            }
        } else if (error.response?.status === 403) {
            errorMessage = 'Anda tidak memiliki akses untuk mengupdate progress aktivitas ini';
        } else if (error.response?.status === 404) {
            errorMessage = 'Aktivitas tidak ditemukan';
        } else if (error.response?.status === 500) {
            errorMessage = 'Terjadi kesalahan server. Silakan coba lagi nanti.';
        } else if (error.response?.data?.message) {
            errorMessage = error.response.data.message;
        }

        showErrorAlert(errorMessage);
        emit('error', errorMessage);
    } finally {
        submitting.value = false;
    }
};

const resetForm = () => {
    progressData.value = {
        progress_percentage: Number(props.activity.progress) || 0,
        progress_description: ''
    };
    showValidation.value = false;
};

// Lifecycle
onMounted(() => {
    resetForm();
});

defineExpose({ resetForm });
</script>

<template>
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-md max-w-md w-full mx-4 max-h-[85vh] overflow-y-auto">
            <!-- Header -->
            <div class="p-3 border-b flex justify-between items-center sticky top-0 bg-white z-10">
                <h3 class="font-medium">Update Progress</h3>
                <button @click="$emit('close')" class="text-gray-400 hover:text-gray-600">
                    &times;
                </button>
            </div>

            <!-- Content -->
            <div class="p-4">
                <!-- Activity Name -->
                <h4 class="text-sm font-medium mb-3">{{ activity?.activity_name }}</h4>

                <!-- Current Progress -->
                <div class="mb-6">
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>Progress Saat Ini</span>
                        <span>{{ activity?.latest_progress?.progress_percentage || 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                        <div class="bg-blue-500 h-1.5 rounded-full"
                            :style="{ width: Number(activity?.latest_progress?.percentage || 0) + '%' }"></div>
                    </div>
                </div>

                <!-- Progress Form -->
                <div class="space-y-4">
                    <div>
                        <label class="text-xs text-gray-600 mb-1.5 block">Persentase Progress</label>
                        <div class="flex items-center gap-3">
                            <input type="range" v-model.number="progressData.progress_percentage" min="0" max="100"
                                step="1"
                                class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                            <input type="number" v-model.number="progressData.progress_percentage" min="0" max="100"
                                step="1"
                                class="w-16 p-1 text-sm border border-gray-200 rounded text-center focus:outline-none focus:border-blue-300">
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-gray-600 mb-1.5 block">Deskripsi Progress</label>
                        <textarea v-model="progressData.progress_description"
                            class="w-full px-2 py-1.5 text-sm border border-gray-200 rounded resize-none focus:outline-none focus:border-blue-300"
                            rows="3" placeholder="Jelaskan progress yang telah dicapai..."></textarea>
                    </div>
                </div>

                <!-- Progress History -->
                <div v-if="progressHistory.length > 0" class="mt-6">
                    <h4 class="text-xs uppercase text-gray-500 font-medium mb-2">Riwayat Progress</h4>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                        <div v-for="progress in progressHistory" :key="progress.id"
                            class="text-sm border-l-2 border-blue-400 pl-2 py-1">
                            <div class="flex justify-between">
                                <span class="font-medium text-blue-600">{{ progress.progress_percentage }}%</span>
                                <span class="text-xs text-gray-500">{{ formatDate(progress.created_at) }}</span>
                            </div>
                            <p class="text-xs mt-1 text-gray-600">{{ progress.progress_description }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">oleh {{ progress.reported_by?.name || 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-2 p-3 border-t sticky bottom-0 bg-white">
                <button @click="$emit('close')"
                    class="px-3 py-1.5 text-xs text-gray-600 border border-gray-200 rounded hover:bg-gray-50">
                    Batal
                </button>
                <button @click="submitProgress" :disabled="submitting"
                    class="px-3 py-1.5 text-xs text-white bg-blue-500 rounded hover:bg-blue-600 disabled:bg-blue-300">
                    {{ submitting ? 'Menyimpan...' : 'Simpan Progress' }}
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom slider styles */
.slider::-webkit-slider-thumb {
    appearance: none;
    height: 16px;
    width: 16px;
    border-radius: 50%;
    background: #3B82F6;
    cursor: pointer;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.slider::-webkit-slider-track {
    width: 100%;
    height: 8px;
    cursor: pointer;
    background: #E5E7EB;
    border-radius: 4px;
}

.slider::-moz-range-thumb {
    height: 16px;
    width: 16px;
    border-radius: 50%;
    background: #3B82F6;
    cursor: pointer;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.slider::-moz-range-track {
    width: 100%;
    height: 8px;
    cursor: pointer;
    background: #E5E7EB;
    border-radius: 4px;
    border: none;
}

.slider:focus {
    outline: none;
}

.slider:focus::-webkit-slider-thumb {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
}

.slider:focus::-moz-range-thumb {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
}
</style>