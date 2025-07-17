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
    const percentage = Number(progressData.value.progress_percentage);
    return percentage >= 0 &&
        percentage <= 100 &&
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
        title: 'Oops... Terjadi Kesalahan',
        text: message,
        confirmButtonText: 'Mengerti',
        confirmButtonColor: '#ef4444',
        background: '#fff',
        customClass: {
            popup: 'rounded-xl shadow-lg',
            title: 'text-lg font-bold text-gray-800',
            htmlContainer: 'text-sm text-gray-600',
            confirmButton: 'px-4 py-2 text-sm font-semibold rounded-lg'
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
        background: '#fff',
        timer: 2000,
        timerProgressBar: true,
        customClass: {
            popup: 'rounded-xl shadow-lg',
            title: 'text-lg font-bold text-gray-800',
            htmlContainer: 'text-sm text-gray-600',
            confirmButton: 'px-4 py-2 text-sm font-semibold rounded-lg'
        }
    });
};

const showValidationAlert = () => {
    let errorMessage = '';
    const percentage = Number(progressData.value.progress_percentage);

    if (percentage < 0 || percentage > 100) {
        errorMessage = 'Persentase progress harus antara 0 hingga 100.';
    } else if (progressData.value.progress_description.trim().length === 0) {
        errorMessage = 'Deskripsi progress tidak boleh kosong.';
    } else {
        errorMessage = 'Mohon lengkapi semua field yang diperlukan.';
    }

    Swal.fire({
        icon: 'warning',
        title: 'Periksa Kembali Input Anda',
        text: errorMessage,
        confirmButtonText: 'Baik',
        confirmButtonColor: '#f59e0b',
        background: '#fff',
        customClass: {
            popup: 'rounded-xl shadow-lg',
            title: 'text-lg font-bold text-gray-800',
            htmlContainer: 'text-sm text-gray-600',
            confirmButton: 'px-4 py-2 text-sm font-semibold rounded-lg'
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
            progress_percentage: Number(progressData.value.progress_percentage),
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
            if (error.response.data.errors) {
                const firstError = Object.values(error.response.data.errors)[0];
                errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
            } else if (error.response.data.message) {
                errorMessage = error.response.data.message;
            }
        } else if (error.response?.status === 403) {
            errorMessage = 'Anda tidak memiliki akses untuk mengupdate progress aktivitas ini.';
        } else if (error.response?.status === 404) {
            errorMessage = 'Aktivitas tidak ditemukan.';
        } else if (error.response?.status === 500) {
            errorMessage = 'Terjadi kesalahan pada server. Silakan coba lagi nanti.';
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
        progress_percentage: Number(props.activity.latest_progress?.percentage) || 0,
        progress_description: ''
    };
    showValidation.value = false;
};

// Lifecycle
onMounted(() => {
    resetForm();
    if (props.activity.progress_history) {
        progressHistory.value = props.activity.progress_history;
    }
});

defineExpose({ resetForm });
</script>

<template>
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4">
        <div class="bg-gradient-to-br from-gray-50 via-white to-blue-50 rounded-2xl shadow-xl max-w-xl w-full max-h-[90vh] flex flex-col">
            <!-- Header -->
            <div class="p-4 border-b border-gray-200 flex justify-between items-center sticky top-0 bg-white/80 backdrop-blur-sm z-10 rounded-t-2xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                        <i class="fas fa-edit text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Update Progress Aktivitas</h3>
                        <p class="text-xs text-gray-500">Perbarui progress untuk aktivitas ini</p>
                    </div>
                </div>
                <button @click="$emit('close')" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-red-100 hover:text-red-600 rounded-full transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Content -->
            <div class="p-5 overflow-y-auto space-y-6">
                <!-- Activity Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <h4 class="text-base font-semibold text-gray-900 mb-2">{{ activity?.activity_name }}</h4>
                    <div class="flex justify-between text-xs text-gray-600 mb-1">
                        <span>Progress Saat Ini</span>
                        <span class="font-bold">{{ activity?.latest_progress?.percentage || 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 shadow-inner">
                        <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-2.5 rounded-full transition-all duration-300"
                            :style="{ width: (activity?.latest_progress?.percentage || 0) + '%' }"></div>
                    </div>
                </div>

                <!-- Progress Form -->
                <div class="space-y-5">
                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1.5 block">Persentase Progress Baru</label>
                        <div class="flex items-center gap-4">
                            <input type="range" v-model.number="progressData.progress_percentage" min="0" max="100" step="1"
                                class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
                            <input type="number" v-model.number="progressData.progress_percentage" min="0" max="100" step="1"
                                class="w-20 p-2 text-sm border border-gray-300 rounded-md text-center focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition">
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700 mb-1.5 block">Deskripsi / Catatan Progress</label>
                        <textarea v-model="progressData.progress_description"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md resize-none focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
                            rows="4" placeholder="Jelaskan progress yang telah dicapai atau catatan penting lainnya..."></textarea>
                    </div>
                </div>

                <!-- Progress History -->
                <div v-if="progressHistory.length > 0" class="space-y-3">
                    <h4 class="text-base font-bold text-gray-900">Riwayat Progress</h4>
                    <div class="space-y-3 max-h-48 overflow-y-auto pr-2">
                        <div v-for="progress in progressHistory" :key="progress.id"
                            class="border rounded-lg p-3 transition-all hover:shadow-md bg-white/50">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-700">{{ progress.progress_description }}</p>
                                    <p class="text-xs text-gray-500 mt-1.5">
                                        oleh <span class="font-medium">{{ progress.reported_by?.name || 'N/A' }}</span>
                                        - <span class="italic">{{ formatDate(progress.created_at) }}</span>
                                    </p>
                                </div>
                                <div class="ml-4 text-right">
                                    <span class="text-sm font-bold text-blue-600">{{ progress.progress_percentage }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 p-4 border-t border-gray-200 sticky bottom-0 bg-white/80 backdrop-blur-sm rounded-b-2xl">
                <button @click="$emit('close')"
                    class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition shadow-sm">
                    Batal
                </button>
                <button @click="submitProgress" :disabled="submitting || !isValid"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 shadow-md transition disabled:from-blue-300 disabled:to-purple-300 disabled:cursor-not-allowed">
                    <i v-if="submitting" class="fas fa-spinner fa-spin"></i>
                    <span>{{ submitting ? 'Menyimpan...' : 'Simpan Progress' }}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom slider styles to match the new theme */
.slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    background: #fff;
    border: 3px solid #3B82F6; /* blue-500 */
    border-radius: 50%;
    cursor: pointer;
    margin-top: -8px; /* Center thumb on track */
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: background-color 0.2s;
}

.slider:hover::-webkit-slider-thumb {
    background: #eff6ff; /* blue-50 */
}

.slider::-moz-range-thumb {
    width: 20px;
    height: 20px;
    background: #fff;
    border: 3px solid #3B82F6; /* blue-500 */
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: background-color 0.2s;
}

.slider:hover::-moz-range-thumb {
    background: #eff6ff; /* blue-50 */
}

.slider::-webkit-slider-runnable-track {
    width: 100%;
    height: 4px;
    cursor: pointer;
    background: #d1d5db; /* gray-300 */
    border-radius: 9999px;
}

.slider::-moz-range-track {
    width: 100%;
    height: 4px;
    cursor: pointer;
    background: #d1d5db; /* gray-300 */
    border-radius: 9999px;
}

.slider:focus {
    outline: none;
}

.slider:focus::-webkit-slider-thumb {
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3);
}

.slider:focus::-moz-range-thumb {
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3);
}
</style>