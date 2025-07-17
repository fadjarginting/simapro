<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-gradient-to-br from-blue-50 via-white to-purple-50 rounded-2xl shadow-lg w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
            <!-- Header -->
            <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                            <i class="fas fa-history text-white text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 tracking-tight">Riwayat Progress</h2>
                            <p class="text-sm text-gray-600">{{ activityName }}</p>
                        </div>
                    </div>
                    <button @click="$emit('close')"
                        class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-200 rounded-full transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="p-6 text-center flex-1 flex flex-col justify-center">
                <div class="w-8 h-8 border-4 border-blue-400 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                <p class="text-sm text-gray-500">Memuat riwayat progress...</p>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="p-6 text-center flex-1 flex flex-col justify-center">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3 shadow">
                    <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Gagal Memuat Data</h3>
                <p class="text-sm text-gray-500 mb-3">{{ error }}</p>
                <button @click="fetchProgressHistory"
                    class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 shadow transition mx-auto">
                    <i class="fas fa-refresh text-xs"></i>
                    Coba Lagi
                </button>
            </div>

            <!-- Progress History Content -->
            <div v-else class="flex-1 overflow-y-auto">
                <!-- Current Progress Summary -->
                <div class="p-4 bg-gradient-to-r from-green-50 to-blue-50 border-b sticky top-0 z-10">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-semibold text-gray-700">Progress Saat Ini</h3>
                        <span class="text-2xl font-bold text-green-600">{{ currentProgress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 shadow-inner">
                        <div class="bg-gradient-to-r from-green-500 to-blue-500 h-2.5 rounded-full transition-all duration-300"
                            :style="{ width: currentProgress + '%' }"></div>
                    </div>
                </div>

                <!-- Progress History Timeline -->
                <div class="p-4">
                    <div v-if="progressHistory.length === 0" class="py-10 text-center">
                        <div class="w-14 h-14 bg-gradient-to-br from-gray-100 to-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow">
                            <i class="fas fa-chart-line text-blue-400 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Riwayat</h3>
                        <p class="text-sm text-gray-500">Belum ada pembaruan progress yang tercatat untuk aktivitas ini.</p>
                    </div>

                    <div v-else class="space-y-4">
                        <h3 class="text-base font-bold text-gray-900">Riwayat Pembaruan</h3>

                        <!-- Timeline -->
                        <div class="relative">
                            <!-- Timeline line -->
                            <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gradient-to-b from-blue-200 to-purple-200"></div>

                            <!-- Timeline items -->
                            <div v-for="(progress, index) in progressHistory" :key="progress.id" class="relative flex items-start gap-4 pb-6">
                                <!-- Timeline dot -->
                                <div class="relative z-10 w-10 h-10 bg-white border-4 border-blue-300 rounded-full flex items-center justify-center shadow-md mt-1">
                                    <div class="w-3 h-3 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full"></div>
                                </div>

                                <!-- Progress content -->
                                <div class="flex-1 bg-white border rounded-xl p-3 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg font-bold text-gray-900">{{ progress.percentage }}%</span>
                                            <span v-if="index === 0" class="px-2 py-0.5 bg-green-100 text-green-800 text-xs font-semibold rounded-full shadow-sm">
                                                Terbaru
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-medium text-gray-800">{{ progress.reporter || 'System' }}</div>
                                            <div class="text-xs text-gray-500">{{ formatDateTime(progress.updated_at) }}</div>
                                        </div>
                                    </div>

                                    <!-- Progress bar -->
                                    <div class="w-full bg-gray-200 rounded-full h-2 mb-3 shadow-inner">
                                        <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full"
                                            :style="{ width: progress.percentage + '%' }"></div>
                                    </div>

                                    <!-- Description -->
                                    <div v-if="progress.description" class="bg-gray-50 rounded-lg p-2.5 border">
                                        <div class="flex items-start gap-2">
                                            <i class="fas fa-comment-alt text-gray-400 mt-1 text-xs"></i>
                                            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ progress.description }}</p>
                                        </div>
                                    </div>

                                    <!-- Progress difference -->
                                    <div v-if="index < progressHistory.length - 1" class="mt-2 flex items-center gap-2 text-xs">
                                        <div class="flex items-center gap-1" :class="{
                                            'text-green-600': getProgressDifference(index) > 0,
                                            'text-red-600': getProgressDifference(index) < 0,
                                            'text-gray-500': getProgressDifference(index) === 0
                                        }">
                                            <i class="fas fa-arrow-up" v-if="getProgressDifference(index) > 0"></i>
                                            <i class="fas fa-arrow-down" v-else-if="getProgressDifference(index) < 0"></i>
                                            <i class="fas fa-minus" v-else></i>
                                            <span class="font-medium">
                                                {{ Math.abs(getProgressDifference(index)) }}% dari sebelumnya
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-4 bg-gray-50 border-t">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        <span v-if="progressHistory.length > 0">
                            Total <span class="font-semibold">{{ progressHistory.length }}</span> pembaruan
                        </span>
                    </div>
                    <button @click="$emit('close')"
                        class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-semibold rounded-lg hover:bg-gray-300 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    activityId: {
        type: [Number, String],
        required: true
    },
    activityName: {
        type: String,
        required: true
    }
});

const emit = defineEmits(['close', 'error']);

const progressHistory = ref([]);
const loading = ref(false);
const error = ref(null);

const currentProgress = computed(() => {
    if (progressHistory.value.length > 0) {
        return progressHistory.value[0].percentage;
    }
    return 0;
});

const getProgressDifference = (index) => {
    if (index >= progressHistory.value.length - 1) return 0;
    const current = progressHistory.value[index].percentage;
    const previous = progressHistory.value[index + 1].percentage;
    return current - previous;
};

const fetchProgressHistory = async () => {
    try {
        loading.value = true;
        error.value = null;

        const response = await axios.get(route('activities.progress-history', props.activityId));
        if (response.data.success) {
            progressHistory.value = response.data.progress_history || [];
        } else {
            throw new Error(response.data.message || 'Gagal mengambil data riwayat.');
        }

    } catch (err) {
        console.error('Error fetching progress history:', err);
        error.value = err.response?.data?.message || err.message || 'Gagal memuat riwayat progress';
        emit('error', error.value);
    } finally {
        loading.value = false;
    }
};

const formatDateTime = (dateStr) => {
    if (!dateStr) return 'N/A';
    const date = new Date(dateStr);
    return date.toLocaleString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

onMounted(() => {
    if (props.activityId) {
        fetchProgressHistory();
    } else {
        error.value = "Activity ID tidak ditemukan.";
        loading.value = false;
    }
});
</script>
