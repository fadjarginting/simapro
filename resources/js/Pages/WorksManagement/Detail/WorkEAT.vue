<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import EATCreateModal from './EATCreateModal.vue';
import ActivityProgressModal from './ActivityProgressModal.vue';
import EATApprovalModal from './EATApprovalModal.vue'; // Import the new approval modal
import axios from 'axios';
import { usePage } from "@inertiajs/vue3";

// Composable untuk permissions
function usePermissions() {
    const hasRole = (name) => usePage().props.auth.user.data.roles.includes(name);
    const hasPermission = (name) => usePage().props.auth.user.data.permissions.includes(name);
    return { hasRole, hasPermission };
}

// Props from parent component
const props = defineProps({
    work: {
        type: Object,
        required: true
    }
});

// Reactive data
const eatData = ref(null);
const disciplines = ref([]);
const users = ref([]);
const loading = ref(false);
const error = ref(null);
const showEatModal = ref(false);
const refreshTrigger = ref(0);

// Updated approval modal state
const showApprovalModal = ref(false);
const selectedApproval = ref(null);
const approvalAction = ref('');

// Progress modal state
const showProgressModal = ref(false);
const selectedActivity = ref(null);

// Permission composable
const { hasRole, hasPermission } = usePermissions();

// Computed properties
const hasEAT = computed(() => !!eatData.value);

// Permission computed properties
const currentUser = computed(() => usePage().props.auth.user.data);

const isWorkLead = computed(() => {
    return props.work.lead_engineer_id === currentUser.value.id ||
        props.work.lead_engineer?.id === currentUser.value.id;
});

const canCreateEAT = computed(() => {
    return isWorkLead.value;
});

const canUpdateEAT = computed(() => {
    return isWorkLead.value && eatData.value?.status !== 'submitted' && eatData.value?.status !== 'approved';
});

const canViewEAT = computed(() => {
    return isWorkLead.value || hasPermission('eat_schedule.view') || hasPermission('eat_schedule.view');
});

const canManageEATActivities = computed(() => {
    return isWorkLead.value;
});

const canUpdateProgress = computed(() => {
    return eatData.value?.status === 'approved';
});

function isPicForActivity(activity) {
    return activity.pics?.some(pic =>
        pic.id === currentUser.value.id 
    );
}

// Check if current user can approve for specific discipline
const canApproveForDiscipline = (disciplineId) => {
    if (!eatData.value?.approvals) return false;

    const approval = eatData.value.approvals.find(a =>
        a.discipline_id === disciplineId &&
        a.approver_id === currentUser.value.id
    );

    return approval && approval.status === 'pending';
};

// Check if current user has any pending approvals
const hasPendingApprovals = computed(() => {
    if (!eatData.value?.approvals) return false;

    return eatData.value.approvals.some(approval =>
        approval.approver_id === currentUser.value.id &&
        approval.status === 'pending'
    );
});

// Get pending approvals for current user
const myPendingApprovals = computed(() => {
    if (!eatData.value?.approvals) return [];

    return eatData.value.approvals.filter(approval =>
        approval.approver_id === currentUser.value.id &&
        approval.status === 'pending'
    );
});

// Check if EAT can be submitted
const canSubmitEAT = computed(() => {
    return isWorkLead.value &&
        eatData.value?.status === 'draft' &&
        eatData.value?.activities?.length > 0;
});

// Group activities by discipline
const groupedActivities = computed(() => {
    if (!eatData.value?.activities) return {};

    const grouped = {};
    eatData.value.activities.forEach(activity => {
        const disciplineName = activity.discipline?.name || 'Tidak Ada Disiplin';
        if (!grouped[disciplineName]) {
            grouped[disciplineName] = [];
        }
        grouped[disciplineName].push(activity);
    });

    return grouped;
});

// Calculate overall progress
const overallProgress = computed(() => {
    if (!eatData.value?.activities?.length) return 0;

    const totalProgress = eatData.value.activities.reduce((sum, activity) => {
        return sum + (activity.latest_progress?.percentage || 0);
    }, 0);

    return (totalProgress / eatData.value.activities.length).toFixed(2);
});

// Methods
const fetchEATData = async (showLoadingState = true) => {
    if (!canViewEAT.value) {
        error.value = 'Anda tidak memiliki izin untuk melihat EAT';
        return;
    }

    try {
        if (showLoadingState) {
            loading.value = true;
        }
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
        if (showLoadingState) {
            loading.value = false;
        }
    }
};

const openModal = async () => {
    if (!canCreateEAT.value && !canUpdateEAT.value) {
        error.value = 'Anda tidak memiliki izin untuk mengelola EAT';
        return;
    }
    showEatModal.value = true;
};

// Progress modal methods
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

const handleProgressSuccess = async (data) => {
    try {
        // Update local state jika diperlukan
        if (data.activity && eatData.value?.activities) {
            const activityIndex = eatData.value.activities.findIndex(a => a.id === data.activity.id);
            if (activityIndex !== -1) {
                eatData.value.activities[activityIndex].progress = data.progress.progress_percentage;
            }
        }

        // Refresh data untuk memastikan sinkronisasi
        await fetchEATData(false);

        // Tutup modal
        closeProgressModal();

    } catch (error) {
        console.error('Error handling progress success:', error);
        error.value = 'Gagal memperbarui progress';
    }
};

const handleProgressError = (errorMessage) => {
    console.error('Progress error:', errorMessage);
    error.value = errorMessage;
    // Modal tetap terbuka untuk memungkinkan user mencoba lagi
};

const handleEATUpdated = async (updatedData = null) => {
    try {
        showEatModal.value = false;

        if (updatedData) {
            eatData.value = updatedData;
        }

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
        if (response && response.data) {
            eatData.value = response.data;
        }

        showEatModal.value = false;
        await fetchEATData(false);
        // showSuccessNotification('EAT berhasil disimpan');

    } catch (error) {
        console.error('Error handling form success:', error);
    }
};

const handleProgressUpdateSuccess = async (activityId, newProgress) => {
    try {
        if (eatData.value && eatData.value.activities) {
            const activity = eatData.value.activities.find(a => a.id === activityId);
            if (activity) {
                activity.progress = newProgress;
            }
        }

        await fetchEATData(false);
        showSuccessNotification('Progress berhasil diperbarui');

    } catch (error) {
        console.error('Error updating progress:', error);
    }
};

// Updated approval methods for the new modal
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
        // Update local state
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

        // Close modal
        closeApprovalModal();

        // // Show success notification
        // showSuccessNotification(
        //     `Persetujuan berhasil ${data.action === 'approved' ? 'disetujui' : 'ditolak'}`
        // );

        // Refresh data
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

// Watch untuk auto-refresh ketika work ID berubah
watch(
    () => props.work.id,
    async (newWorkId, oldWorkId) => {
        if (newWorkId !== oldWorkId) {
            await fetchEATData();
        }
    }
);

// Watch untuk refresh trigger
watch(refreshTrigger, async () => {
    await fetchEATData(false);
});

// Lifecycle
onMounted(() => {
    fetchEATData();
});

// Expose methods untuk digunakan dari parent component
defineExpose({
    fetchEATData,
    refreshTrigger
});
</script>

<template>
    <div class="bg-white border rounded-lg shadow-sm">
        <!-- Permission Denied -->
        <div v-if="!canViewEAT" class="p-8 text-center">
            <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-lock text-red-500"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Akses Dibatasi</h3>
            <p class="text-sm text-gray-500">
                Anda tidak memiliki izin untuk melihat EAT ini. Hanya lead pekerjaan yang dapat mengelola EAT.
            </p>
        </div>

        <!-- Loading State -->
        <div v-else-if="loading" class="p-8 text-center">
            <div class="w-8 h-8 border-2 border-blue-600 border-t-transparent rounded-full animate-spin mx-auto mb-3">
            </div>
            <p class="text-sm text-gray-500">Memuat data EAT...</p>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="p-8 text-center">
            <div class="w-12 h-12 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-exclamation-triangle text-red-500"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Gagal Memuat Data</h3>
            <p class="text-sm text-gray-500 mb-4">{{ error }}</p>
            <button v-if="canViewEAT" @click="fetchEATData"
                class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-blue-600 bg-blue-50 rounded-md hover:bg-blue-100 transition">
                <i class="fas fa-refresh text-xs"></i>
                Coba Lagi
            </button>
        </div>

        <!-- Content -->
        <div v-else>
            <!-- Header -->
            <div class="border-b p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-tasks text-white text-sm"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">
                                Engineering Assignment Task
                            </h2>
                            <div class="flex items-center gap-2 mt-1">
                                <span :class="getStatusClass(eatData?.status)"
                                    class="px-2 py-1 text-xs font-medium rounded-full">
                                    {{ eatData?.status || 'draft' }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    <i class="fas fa-calendar-alt text-gray-400 text-xs"></i>
                                    <span class="text-xs text-gray-500"> Tanggal Mulai: {{
                                        formatDate(eatData?.start_date) || 'N/A' }}</span>
                                    <span class="text-xs text-gray-500"> | Tanggal Selesai: {{
                                        formatDate(eatData?.end_date) || 'N/A' }}</span>
                                </span>
                                <span v-if="hasEAT" class="text-xs text-gray-500">
                                    Progress: {{ overallProgress }}%
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button v-if="canUpdateEAT" @click="openModal"
                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                            <i class="fas fa-edit text-xs"></i>
                            Edit
                        </button>
                        <button v-else-if="canCreateEAT && !hasEAT" @click="openModal"
                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                            <i class="fas fa-plus text-xs"></i>
                            Buat EAT
                        </button>
                    </div>
                </div>
            </div>

            <!-- Permission info banner -->
            <div v-if="!isWorkLead && hasEAT" class="bg-blue-50 border-b p-3">
                <div class="flex items-center gap-2 text-blue-700">
                    <i class="fas fa-info-circle text-xs"></i>
                    <span class="text-xs">
                        Hanya lead pekerjaan (<strong>{{ props.work.lead_engineer?.name || 'N/A' }}</strong>) yang dapat
                        melakukan perubahan
                    </span>
                </div>
            </div>

            <!-- Content Body -->
            <div class="p-4">
                <!-- My Pending Approvals Alert -->
                <div v-if="hasPendingApprovals" class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                        <span class="text-sm font-medium text-yellow-800">Persetujuan Menunggu</span>
                    </div>
                    <p class="text-xs text-yellow-700 mb-2">
                        Anda memiliki {{ myPendingApprovals.length }} persetujuan yang menunggu tindakan
                    </p>
                    <div class="flex gap-2">
                        <span v-for="approval in myPendingApprovals" :key="approval.id"
                            class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">
                            {{ approval.discipline?.name }}
                        </span>
                    </div>
                </div>

                <!-- Tampilan jika EAT sudah ada -->
                <div v-if="hasEAT" class="space-y-6">
                    <!-- Overall Progress -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-gray-700">Progress Keseluruhan</span>
                            <span class="text-sm font-semibold text-gray-900">{{ overallProgress }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                :style="{ width: overallProgress + '%' }"></div>
                        </div>
                    </div>

                    <!-- Grouped Activities -->
                    <div class="space-y-4">
                        <h3 class="text-base font-semibold text-gray-900">Aktivitas per Disiplin</h3>

                        <div v-for="(activities, disciplineName) in groupedActivities" :key="disciplineName"
                            class="border rounded-lg overflow-hidden">
                            <!-- Discipline Header -->
                            <div class="bg-gray-50 border-b px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-sm font-medium text-gray-900">{{ disciplineName }}</h4>
                                    <span class="text-xs text-gray-500">{{ activities.length }} aktivitas</span>
                                </div>
                            </div>

                            <!-- Activities List -->
                            <div class="divide-y">
                                <div v-for="activity in activities" :key="activity.id" class="p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <h5 class="text-sm font-medium text-gray-900 mb-1">
                                                {{ activity.activity_name }}
                                            </h5>
                                            <div class="flex items-center gap-4 text-xs text-gray-500">
                                                <span v-if="activity.pics?.length">
                                                    <i class="fas fa-user text-xs mr-1"></i>
                                                    {{activity.pics.map(p => p.name).join(', ')}}
                                                </span>
                                                <span>
                                                    <i class="fas fa-calendar text-xs mr-1"></i>
                                                    {{ formatDate(activity.start_date) }}
                                                    -
                                                    {{ formatDate(activity.end_date) }}
                                                </span>

                                                <!-- last updated progress -->
                                                <span v-if="activity.latest_progress">
                                                    <i class="fas fa-clock text-xs mr-1"></i>
                                                    {{ formatDate(activity.latest_progress.updated_at) }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- last updated progress -->

                                        <div class="flex items-center gap-2 ml-4">
                                            <div class="text-right">
                                                <span class="text-xs font-medium text-gray-900">
                                                    {{ Number(activity.latest_progress?.percentage || 0) }}%
                                                </span>
                                                <div class="w-16 bg-gray-200 rounded-full h-1.5 mt-1">
                                                    <div class="bg-blue-600 h-1.5 rounded-full transition-all"
                                                        :style="{ width: Number(activity.latest_progress?.percentage || 0) + '%' }"></div>
                                                </div>
                                            </div>

                                            <button v-if="isPicForActivity(activity) && canUpdateProgress"
                                                @click="openProgressModal(activity)"
                                                class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition"
                                                title="Update Progress">
                                                <i class="fas fa-edit text-xs"></i>
                                            </button>
                                            <span v-else-if="!isPicForActivity(activity)"
                                                class="p-1.5 text-gray-300 cursor-not-allowed"
                                                title="Anda bukan PIC untuk aktivitas ini">
                                                <i class="fas fa-user-slash text-xs"></i>
                                            </span>
                                            <span v-else-if="canUpdateProgress"
                                                class="p-1.5 text-gray-300 cursor-not-allowed"
                                                title="Tunggu semua persetujuan selesai untuk mengupdate progress">
                                                <i class="fas fa-edit text-xs"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Approvals Section -->
                    <div v-if="eatData.approvals?.length" class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900">Persetujuan</h3>
                            <span class="text-xs text-gray-500">
                                {{eatData.approvals.filter(a => a.status === 'approved').length}} / {{
                                eatData.approvals.length }} disetujui
                            </span>
                        </div>

                        <div class="grid grid-cols-1 gap-3">
                            <div v-for="approval in eatData.approvals" :key="approval.id"
                                class="border rounded-lg p-3 transition-all hover:shadow-sm">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <i :class="getApprovalIcon(approval.status)" class="text-xs"></i>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ approval.discipline || 'N/A' }}
                                            </p>
                                        </div>
                                        <p class="text-xs text-gray-500 mb-2">
                                            Approver: {{ approval.approver || 'N/A' }}
                                        </p>
                                        <p v-if="approval.notes" class="text-xs text-gray-600 bg-gray-50 p-2 rounded">
                                            <i class="fas fa-comment text-xs mr-1"></i>
                                            {{ approval.notes }}
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span :class="getApprovalStatusClass(approval.status)"
                                            class="px-2 py-1 text-xs font-medium rounded border">
                                            {{ approval.status === 'pending' ? 'Menunggu' :
                                            approval.status === 'approved' ? 'Disetujui' : 'Ditolak' }}
                                        </span>

                                        <!-- Approval Actions -->
                                        <div v-if="approval.approver_id === currentUser.id && canApproveForDiscipline(approval.discipline_id)" class="flex gap-1">
                                            <button @click="openApprovalModal(approval, 'approved')"
                                                class="p-1.5 text-green-600 hover:bg-green-50 rounded transition"
                                                title="Setujui">
                                                <i class="fas fa-check text-xs"></i>
                                            </button>
                                            <button @click="openApprovalModal(approval, 'rejected')"
                                                class="p-1.5 text-red-600 hover:bg-red-50 rounded transition"
                                                title="Tolak">
                                                <i class="fas fa-times text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Approval timestamp -->
                                <p v-if="approval.approval_date" class="text-xs text-gray-400 mt-2">
                                    {{ approval.status === 'approved' ? 'Disetujui' : 'Ditolak' }} pada:
                                    {{ formatDate(approval.approval_date) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tampilan jika EAT belum ada -->
                <div v-else class="py-12 text-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-signature text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">EAT Belum Dikonfigurasi</h3>
                    <p class="text-sm text-gray-500 mb-6 max-w-sm mx-auto">
                        Atur aktivitas engineering, PIC, dan alur persetujuan dengan membuat EAT untuk pekerjaan ini.
                    </p>

                    <button v-if="canCreateEAT" @click="openModal"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 transition">
                        <i class="fas fa-plus text-xs"></i>
                        Buat EAT Baru
                    </button>

                    <div v-else class="text-center">
                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm text-gray-500 bg-gray-100 rounded-md">
                            <i class="fas fa-lock text-xs"></i>
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

        <!-- Modal untuk persetujuan EAT -->
        <EATApprovalModal v-if="showApprovalModal && selectedApproval" :approval="selectedApproval"
            :action="approvalAction" @close="closeApprovalModal" @success="handleApprovalSuccess"
            @error="handleApprovalError" />
    </div>
</template>