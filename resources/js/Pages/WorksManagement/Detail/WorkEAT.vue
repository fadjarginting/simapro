<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import EATCreateModal from './EATCreateModal.vue';
import ActivityProgressModal from './ActivityProgressModal.vue';
import ActivityProgressHistoryModal from './ActivityProgressHistoryModal.vue';
import EATApprovalModal from './EATApprovalModal.vue';
import axios from 'axios';
import { usePage } from "@inertiajs/vue3";
import { usePermissions } from "@/composables/permissions";

// Composable untuk permissions


const props = defineProps({
    work: {
        type: Object,
        required: true
    }
});

const eatData = ref(null);
const disciplines = ref([]);
const users = ref([]);
const loading = ref(false);
const error = ref(null);
const showEatModal = ref(false);
const refreshTrigger = ref(0);

const showApprovalModal = ref(false);
const selectedApproval = ref(null);
const approvalAction = ref('');

const showProgressModal = ref(false);
const selectedActivity = ref(null);

const showProgressHistoryModal = ref(false);
const selectedActivityForHistory = ref(null);

const { hasRole, hasPermission } = usePermissions();

const hasEAT = computed(() => !!eatData.value);

const currentUser = computed(() => usePage().props.auth.user.data);

const isWorkLead = computed(() => {
    return props.work.lead_engineer_id === currentUser.value.id ||
        props.work.lead_engineer?.id === currentUser.value.id;
});

const canCreateEAT = computed(() => isWorkLead.value);

const canUpdateEAT = computed(() => {
    // The work lead can edit the EAT if it exists and is in a state that allows modification (e.g., draft or rejected).
    return isWorkLead.value && hasEAT.value && (eatData.value.status === 'draft' || eatData.value.status === 'rejected');
});

const canViewEAT = computed(() => {
    // This check allows anyone with the base permission or the work lead to attempt to view.
    // The backend will ultimately decide if they can see the specific EAT data.
    // We add a check for PIC status after data is loaded.
    if (eatData.value) {
        const isPic = eatData.value.activities?.some(activity =>
            activity.pics?.some(pic => pic.id === currentUser.value.id)
        );
        const isApprover = eatData.value.approvals?.some(approval =>
            approval.approver_id === currentUser.value.id
        );
        return isWorkLead.value || hasPermission('manajemen_pekerjaan.view') || isPic || isApprover;
    }
    // Before data is loaded, we allow the fetch to proceed if the user is lead or has general view permission.
    // The backend will return 403/404 if they are not involved (e.g., as PIC/Approver) and lack general permissions.
    return isWorkLead.value || hasPermission('manajemen_pekerjaan.view');
});

const canUpdateProgress = computed(() => {
    return eatData.value?.status === 'approved';
});

function isPicForActivity(activity) {
    return activity.pics?.some(pic =>
        pic.id === currentUser.value.id 
    );
}

const canApproveForDiscipline = (disciplineId) => {
    if (!eatData.value?.approvals) return false;
    const approval = eatData.value.approvals.find(a =>
        a.discipline_id === disciplineId &&
        a.approver_id === currentUser.value.id
    );
    return approval && approval.status === 'pending';
};

const hasPendingApprovals = computed(() => {
    if (!eatData.value?.approvals) return false;
    return eatData.value.approvals.some(approval =>
        approval.approver_id === currentUser.value.id &&
        approval.status === 'pending'
    );
});

const myPendingApprovals = computed(() => {
    if (!eatData.value?.approvals) return [];
    return eatData.value.approvals.filter(approval =>
        approval.approver_id === currentUser.value.id &&
        approval.status === 'pending'
    );
});

const canSubmitEAT = computed(() => {
    return isWorkLead.value &&
        eatData.value?.status === 'draft' &&
        eatData.value?.activities?.length > 0;
});

// Warna progress bar berbeda-beda
const progressBarColors = [
    'bg-gradient-to-r from-blue-400 to-blue-600',
    'bg-gradient-to-r from-green-400 to-green-600',
    'bg-gradient-to-r from-pink-400 to-pink-600',
    'bg-gradient-to-r from-yellow-400 to-yellow-600',
    'bg-gradient-to-r from-purple-400 to-purple-600',
    'bg-gradient-to-r from-teal-400 to-teal-600',
    'bg-gradient-to-r from-orange-400 to-orange-600',
    'bg-gradient-to-r from-indigo-400 to-indigo-600',
    'bg-gradient-to-r from-red-400 to-red-600',
    'bg-gradient-to-r from-cyan-400 to-cyan-600',
];
const getProgressBarColor = (idx) => progressBarColors[idx % progressBarColors.length];

const groupedActivities = computed(() => {
    if (!eatData.value?.activities) return {};
    const grouped = {};
    eatData.value.activities.forEach(activity => {
        const disciplineName = activity.discipline || 'Tidak Ada Disiplin';
        if (!grouped[disciplineName]) {
            grouped[disciplineName] = [];
        }
        grouped[disciplineName].push(activity);
    });
    return grouped;
});

const overallProgress = computed(() => {
    if (!eatData.value?.activities?.length) return 0;
    const totalProgress = eatData.value.activities.reduce((sum, activity) => {
        return sum + (activity.latest_progress?.percentage || 0);
    }, 0);
    return (totalProgress / eatData.value.activities.length).toFixed(2);
});

const fetchEATData = async (showLoadingState = true) => {
    if (!canViewEAT.value) {
        error.value = 'Anda tidak memiliki izin untuk melihat EAT';
        return;
    }
    try {
        if (showLoadingState) loading.value = true;
        error.value = null;
        const response = await axios.get(route('eat.by-work', props.work.id), {
            params: { _t: Date.now() }
        });
        eatData.value = response.data;
    } catch (err) {
        if (err.response?.status === 404) {
            eatData.value = null;
        } else if (err.response?.status === 403) {
            error.value = 'Anda tidak memiliki izin untuk mengakses EAT ini';
        } else {
            error.value = 'Gagal memuat data EAT';
            console.error('Error fetching EAT data:', err);
        }
    } finally {
        if (showLoadingState) loading.value = false;
    }
};

const openModal = async () => {
    if (!canCreateEAT.value && !canUpdateEAT.value) {
        error.value = 'Anda tidak memiliki izin untuk mengelola EAT';
        return;
    }
    showEatModal.value = true;
};

const openProgressModal = (activity) => {
    if (!isPicForActivity || !canUpdateProgress.value) {
        error.value = 'Anda tidak memiliki izin untuk mengelola progress aktivitas ini';
        return;
    }
    selectedActivity.value = activity;
    showProgressModal.value = true;
};

const closeProgressModal = () => {
    showProgressModal.value = false;
    selectedActivity.value = null;
};

const openProgressHistoryModal = (activity) => {
    selectedActivityForHistory.value = activity;
    showProgressHistoryModal.value = true;
};

const closeProgressHistoryModal = () => {
    showProgressHistoryModal.value = false;
    selectedActivityForHistory.value = null;
};

const handleProgressSuccess = async (data) => {
    try {
        if (data.activity && eatData.value?.activities) {
            const activityIndex = eatData.value.activities.findIndex(a => a.id === data.activity.id);
            if (activityIndex !== -1) {
                eatData.value.activities[activityIndex].progress = data.progress.progress_percentage;
            }
        }
        await fetchEATData(false);
        closeProgressModal();
    } catch (error) {
        console.error('Error handling progress success:', error);
        error.value = 'Gagal memperbarui progress';
    }
};

const handleProgressError = (errorMessage) => {
    console.error('Progress error:', errorMessage);
    error.value = errorMessage;
    showProgressModal.value = true;
};

const handleEATUpdated = async (updatedData = null) => {
    try {
        showEatModal.value = false;
        if (updatedData) eatData.value = updatedData;
        await fetchEATData(false);
        setTimeout(async () => {
            await fetchEATData(false);
        }, 300);
        console.log('EAT berhasil diperbarui');
    } catch (error) {
        console.error('Error saat refresh data:', error);
    }
};

const handleFormSubmitSuccess = async (response) => {
    try {
        if (response && response.data) eatData.value = response.data;
        showEatModal.value = false;
        await fetchEATData(false);
    } catch (error) {
        console.error('Error handling form success:', error);
    }
};

const handleProgressUpdateSuccess = async (activityId, newProgress) => {
    try {
        if (eatData.value && eatData.value.activities) {
            const activity = eatData.value.activities.find(a => a.id === activityId);
            if (activity) activity.progress = newProgress;
        }
        await fetchEATData(false);
    } catch (error) {
        console.error('Error updating progress:', error);
    }
};

const openApprovalModal = (approval, action) => {
    selectedApproval.value = approval;
    approvalAction.value = action;
    showApprovalModal.value = true;
};

const closeApprovalModal = () => {
    showApprovalModal.value = false;
    selectedApproval.value = null;
    approvalAction.value = '';
};

const handleApprovalSuccess = async (data) => {
    try {
        if (data.approval && eatData.value?.approvals) {
            const approvalIndex = eatData.value.approvals.findIndex(
                a => a.id === data.approval.id
            );
            if (approvalIndex !== -1) {
                eatData.value.approvals[approvalIndex] = {
                    ...eatData.value.approvals[approvalIndex],
                    status: data.action,
                    notes: data.notes,
                    approved_at: new Date().toISOString()
                };
            }
        }
        closeApprovalModal();
        await fetchEATData(false);
    } catch (error) {
        console.error('Error handling approval success:', error);
        error.value = 'Gagal memproses persetujuan';
    }
};

const handleApprovalError = (errorMessage) => {
    console.error('Approval error:', errorMessage);
    error.value = errorMessage;
};

const formatDate = (dateStr) => {
    if (!dateStr) return 'N/A';
    const date = new Date(dateStr);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};

const getStatusClass = (status) => {
    const classes = {
        draft: 'bg-gray-100 text-gray-700',
        submitted: 'bg-blue-100 text-blue-700',
        approved: 'bg-green-100 text-green-700',
        rejected: 'bg-red-100 text-red-700',
    };
    return classes[status] || 'bg-gray-100 text-gray-700';
};

const getApprovalStatusClass = (status) => {
    const classes = {
        pending: 'bg-yellow-100 text-yellow-700 border-yellow-200',
        approved: 'bg-green-100 text-green-700 border-green-200',
        rejected: 'bg-red-100 text-red-700 border-red-200',
    };
    return classes[status] || 'bg-gray-100 text-gray-700 border-gray-200';
};

const getApprovalIcon = (status) => {
    const icons = {
        pending: 'fas fa-clock',
        approved: 'fas fa-check',
        rejected: 'fas fa-times',
    };
    return icons[status] || 'fas fa-question';
};

watch(
    () => props.work.id,
    async (newWorkId, oldWorkId) => {
        if (newWorkId !== oldWorkId) {
            await fetchEATData();
        }
    }
);

watch(refreshTrigger, async () => {
    await fetchEATData(false);
});

onMounted(() => {
    fetchEATData();
});

defineExpose({
    fetchEATData,
    refreshTrigger
});
</script>

<template>
    <div class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border rounded-2xl shadow-lg overflow-hidden">
        <!-- Loading State -->
        <div v-if="loading" class="p-6 text-center">
            <div class="w-8 h-8 border-4 border-blue-400 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
            <p class="text-sm text-gray-500">Memuat data EAT...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="p-6 text-center">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3 shadow">
            <i class="fas fa-exclamation-triangle text-red-500 text-xl"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Gagal Memuat Data</h3>
            <p class="text-sm text-gray-500 mb-3">{{ error }}</p>
            <button @click="fetchEATData"
            class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 shadow transition">
            <i class="fas fa-refresh text-xs"></i>
            Coba Lagi
            </button>
        </div>

        <!-- Content -->
        <div v-else>
            <!-- Header -->
            <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                            <i class="fas fa-tasks text-white text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 tracking-tight">
                                Engineering Assignment Task
                            </h2>
                            <div class="flex items-center gap-2 mt-1 flex-wrap">
                                <span :class="getStatusClass(eatData?.status)"
                                    class="px-2.5 py-0.5 text-xs font-semibold rounded-full shadow">
                                    {{ eatData?.status || 'draft' }}
                                </span>
                                <span class="text-xs text-gray-500 flex items-center gap-1">
                                    <i class="fas fa-calendar-alt text-gray-400"></i>
                                    <span>{{ formatDate(eatData?.start_date) || 'N/A' }}</span>
                                    <span>- {{ formatDate(eatData?.end_date) || 'N/A' }}</span>
                                </span>
                                <span v-if="hasEAT" class="text-xs text-gray-500 flex items-center gap-1">
                                    <i class="fas fa-chart-line text-blue-400"></i>
                                    Progress: <span class="font-semibold">{{ overallProgress }}%</span>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button v-if="canUpdateEAT" @click="openModal"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-sm font-semibold rounded-md hover:from-blue-700 hover:to-purple-700 shadow transition">
                            <i class="fas fa-edit text-xs"></i>
                            Edit
                        </button>
                    </div>
                </div>
            </div>

            <!-- Permission info banner -->
            <div v-if="!isWorkLead && hasEAT" class="bg-blue-50 border-b p-2">
                <div class="flex items-center gap-2 text-blue-700 text-xs">
                    <i class="fas fa-info-circle"></i>
                    <span>
                        Hanya lead pekerjaan (<strong>{{ props.work.lead_engineer?.name || 'N/A' }}</strong>) yang dapat
                        melakukan perubahan.
                    </span>
                </div>
            </div>

            <!-- Content Body -->
            <div class="p-4">
                <!-- My Pending Approvals Alert -->
                <div v-if="hasPendingApprovals" class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg shadow-sm">
                    <div class="flex items-center gap-2 mb-1">
                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                        <span class="text-sm font-semibold text-yellow-800">Persetujuan Menunggu</span>
                    </div>
                    <p class="text-xs text-yellow-700 mb-2">
                        Anda memiliki {{ myPendingApprovals.length }} persetujuan yang menunggu tindakan.
                    </p>
                    <div class="flex gap-1.5 flex-wrap">
                        <span v-for="approval in myPendingApprovals" :key="approval.id"
                            class="px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded-full shadow-sm">
                            {{ approval.discipline?.name }}
                        </span>
                    </div>
                </div>

                <!-- Tampilan jika EAT sudah ada -->
                <div v-if="hasEAT" class="space-y-4">
                    <!-- Overall Progress -->
                    <div class="bg-gradient-to-r from-blue-100 to-purple-100 rounded-lg p-4 shadow-sm">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold text-gray-700">Progress Keseluruhan</span>
                            <span class="text-sm font-bold text-gray-900">{{ overallProgress }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 shadow-inner">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2.5 rounded-full transition-all duration-300"
                                :style="{ width: overallProgress + '%' }"></div>
                        </div>
                    </div>

                    <!-- Grouped Activities -->
                    <div class="space-y-4">
                        <h3 class="text-base font-bold text-gray-900">Aktivitas per Disiplin</h3>
                        <div v-for="(activities, disciplineName) in groupedActivities" :key="disciplineName"
                            class="border rounded-lg overflow-hidden shadow-sm bg-white">
                            <!-- Discipline Header -->
                            <div class="bg-gradient-to-r from-gray-50 to-blue-50 border-b px-4 py-2">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-semibold text-blue-900">{{ disciplineName }}</h4>
                                    <span class="text-xs text-gray-500">{{ activities.length }} aktivitas</span>
                                </div>
                            </div>
                            <!-- Activities List -->
                            <div class="divide-y">
                                <div v-for="(activity, idx) in activities" :key="activity.id" class="p-3 hover:bg-blue-50 transition">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 pr-4">
                                            <h5 class="text-sm font-semibold text-gray-900 mb-1 flex items-center gap-2">
                                                <span class="inline-block w-2 h-2 rounded-full" :class="getProgressBarColor(idx)"></span>
                                                {{ activity.activity_name }}
                                            </h5>
                                            <div class="flex items-center gap-3 text-xs text-gray-500 flex-wrap mb-1.5">
                                                <span v-if="activity.pics?.length" class="flex items-center gap-1">
                                                    <i class="fas fa-user"></i>
                                                    {{activity.pics.map(p => p.name).join(', ')}}
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <i class="fas fa-calendar"></i>
                                                    {{ formatDate(activity.start_date) }} - {{ formatDate(activity.end_date) }}
                                                </span>
                                            </div>
                                            <div v-if="activity.latest_progress?.description" class="mt-1 text-xs text-gray-600 bg-gray-50 p-1.5 rounded border border-gray-200">
                                                <p class="whitespace-pre-wrap"><i class="fas fa-comment-alt mr-1.5 text-gray-400"></i>{{ activity.latest_progress.description }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 ml-2">
                                            <div class="text-right min-w-[50px]">
                                                <span class="text-xs font-bold text-gray-900">
                                                    {{ Number(activity.latest_progress?.percentage || 0) }}%
                                                </span>
                                                <div class="w-20 bg-gray-200 rounded-full h-2 mt-1 shadow-inner">
                                                    <div :class="getProgressBarColor(idx)" class="h-2 rounded-full transition-all"
                                                        :style="{ width: Number(activity.latest_progress?.percentage || 0) + '%' }">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <button @click="openProgressHistoryModal(activity)"
                                                    class="w-7 h-7 flex items-center justify-center text-gray-500 hover:bg-gray-100 rounded-full transition shadow-sm"
                                                    title="Lihat Riwayat Progress">
                                                    <i class="fas fa-history text-sm"></i>
                                                </button>
                                                <button v-if="isPicForActivity(activity) && canUpdateProgress"
                                                    @click="openProgressModal(activity)"
                                                    class="w-7 h-7 flex items-center justify-center text-blue-600 hover:bg-blue-100 rounded-full transition shadow-sm"
                                                    title="Update Progress">
                                                    <i class="fas fa-edit text-sm"></i>
                                                </button>
                                                <span v-else-if="!isPicForActivity(activity)"
                                                    class="w-7 h-7 flex items-center justify-center text-gray-300 cursor-not-allowed"
                                                    title="Anda bukan PIC untuk aktivitas ini">
                                                    <i class="fas fa-user-slash text-sm"></i>
                                                </span>
                                                <span v-else-if="canUpdateProgress"
                                                    class="w-7 h-7 flex items-center justify-center text-gray-300 cursor-not-allowed"
                                                    title="Tunggu semua persetujuan selesai untuk mengupdate progress">
                                                    <i class="fas fa-edit text-sm"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Approvals Section -->
                    <div v-if="eatData.approvals?.length" class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-bold text-gray-900">Persetujuan</h3>
                            <span class="text-xs text-gray-500">
                                {{eatData.approvals.filter(a => a.status === 'approved').length}} / {{ eatData.approvals.length }} disetujui
                            </span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div v-for="approval in eatData.approvals" :key="approval.id"
                                class="border rounded-lg p-3 transition-all hover:shadow-md bg-gradient-to-br from-white to-blue-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <i :class="getApprovalIcon(approval.status)" class="text-base"></i>
                                            <p class="text-sm font-semibold text-gray-900">{{ approval.discipline || 'N/A' }}</p>
                                        </div>
                                        <p class="text-xs text-gray-500 mb-1">Approver: {{ approval.approver || 'N/A' }}</p>
                                        <p v-if="approval.notes" class="text-xs text-gray-600 bg-gray-50 p-1.5 rounded">
                                            <i class="fas fa-comment text-xs mr-1"></i>{{ approval.notes }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span :class="getApprovalStatusClass(approval.status)"
                                            class="px-2 py-0.5 text-xs font-semibold rounded-full border shadow-sm">
                                            {{ approval.status === 'pending' ? 'Menunggu' : approval.status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                                        </span>
                                        <div v-if="approval.approver_id === currentUser.id && canApproveForDiscipline(approval.discipline_id)" class="flex">
                                            <button @click="openApprovalModal(approval, 'approved')" class="w-6 h-6 flex items-center justify-center text-green-600 hover:bg-green-100 rounded-full transition" title="Setujui"><i class="fas fa-check text-sm"></i></button>
                                            <button @click="openApprovalModal(approval, 'rejected')" class="w-6 h-6 flex items-center justify-center text-red-600 hover:bg-red-100 rounded-full transition" title="Tolak"><i class="fas fa-times text-sm"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <p v-if="approval.approval_date" class="text-xs text-gray-400 mt-1.5">
                                    {{ approval.status === 'approved' ? 'Disetujui' : 'Ditolak' }} pada: {{ formatDate(approval.approval_date) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tampilan jika EAT belum ada -->
                <div v-else class="py-10 text-center">
                    <div class="w-14 h-14 bg-gradient-to-br from-gray-100 to-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 shadow">
                        <i class="fas fa-file-signature text-blue-400 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">EAT Belum Dikonfigurasi</h3>
                    <p class="text-sm text-gray-500 mb-6 max-w-sm mx-auto">
                        Atur aktivitas engineering, PIC, dan alur persetujuan dengan membuat EAT untuk pekerjaan ini.
                    </p>
                    <button v-if="canCreateEAT && !hasEAT && props.work.verification_status === 'Finish Verifikasi'" @click="openModal"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 shadow transition">
                        <i class="fas fa-plus"></i>
                        Buat EAT Baru
                    </button>
                    <div v-else class="text-center">
                        <div v-if="props.work.verification_status !== 'Finish Verifikasi'"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm text-gray-500 bg-gray-100 rounded-lg shadow">
                            <i class="fas fa-lock"></i>
                            Verifikasi ERF belum selesai
                        </div>
                        <div v-else
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm text-gray-500 bg-gray-100 rounded-lg shadow">
                            <i class="fas fa-lock"></i>
                            Hanya lead pekerjaan yang dapat membuat EAT
                        </div>
                        <p class="text-xs text-gray-400 mt-2">
                            Lead saat ini: <strong>{{ props.work.lead_engineer?.name || 'Belum ditentukan' }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal untuk membuat/edit EAT -->
        <EATCreateModal v-if="showEatModal && (canCreateEAT || canUpdateEAT)" :work="work" :disciplines="disciplines"
            :users="users" :existing-data="eatData" :is-edit="hasEAT" @close="showEatModal = false"
            @updated="handleEATUpdated" @success="handleFormSubmitSuccess"
            @progress-updated="handleProgressUpdateSuccess" />

        <!-- Modal untuk mengelola progress aktivitas -->
        <ActivityProgressModal v-if="showProgressModal && selectedActivity" :activity="selectedActivity" :users="users"
            :disciplines="disciplines" @close="closeProgressModal" @success="handleProgressSuccess"
            @error="handleProgressError" />

        <!-- Modal untuk riwayat progress aktivitas -->
        <ActivityProgressHistoryModal v-if="showProgressHistoryModal && selectedActivityForHistory"
            :activity-id="selectedActivityForHistory.id"
            :activity-name="selectedActivityForHistory.activity_name"
            @close="closeProgressHistoryModal" />

        <!-- Modal untuk persetujuan EAT -->
        <EATApprovalModal v-if="showApprovalModal && selectedApproval" :approval="selectedApproval"
            :action="approvalAction" @close="closeApprovalModal" @success="handleApprovalSuccess"
            @error="handleApprovalError" />
    </div>
</template>
