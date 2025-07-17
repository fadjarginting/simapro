<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { usePermissions } from "@/composables/permissions";
import { usePage } from "@inertiajs/vue3";

const { hasPermission, hasRole } = usePermissions();

const props = defineProps({
    work: Object
})

const currentUser = computed(() => usePage().props.auth.user.data);

const isWorkLead = computed(() => {
    return props.work.lead_engineer_id === currentUser.value.id;
});

// Edit state
const isEditing = ref(false)
const isSubmitting = ref(false)
const error = ref('')

// Form data untuk edit
const editForm = ref({
    verification_status: '',
    project_status: '',
    current_phase: ''
})

// Options untuk dropdown berdasarkan enum
const verificationStatusOptions = [
    'Belum Verifikasi',
    'Finish Verifikasi',
    'In Progress Verifikasi'
]

const projectStatusOptions = [
    'Not Started',
    'In Progress',
    'Finish',
    'On Hold',
    'Cancel'
]

const currentPhaseOptions = [
    'Not started',
    'Initiating',
    'Planning',
    'Executing',
    'Closing',
    'Hold',
    'Cancel'
]

// Style functions
const getVerificationClass = (status) => {
    const classes = {
        "Belum Verifikasi": "bg-gray-100 text-gray-700",
        "In Progress Verifikasi": "bg-yellow-100 text-yellow-700",
        "Finish Verifikasi": "bg-green-100 text-green-700",
    };
    return classes[status] || "bg-gray-100 text-gray-700";
};

const getStatusClass = (status) => {
    const classes = {
        "Not Started": "bg-gray-100 text-gray-700",
        "In Progress": "bg-yellow-100 text-yellow-700",
        "Finish": "bg-green-100 text-green-700",
        "On Hold": "bg-orange-100 text-orange-700",
        "Cancel": "bg-red-100 text-red-700",
    };
    return classes[status] || "bg-gray-100 text-gray-700";
};

const getPhaseClass = (phase) => {
    const classes = {
        "Not started": "bg-gray-100 text-gray-700",
        "Initiating": "bg-blue-100 text-blue-700",
        "Planning": "bg-purple-100 text-purple-700",
        "Executing": "bg-yellow-100 text-yellow-700",
        "Closing": "bg-green-100 text-green-700",
        "Hold": "bg-orange-100 text-orange-700",
        "Reject": "bg-red-100 text-red-700",
    };
    return classes[phase] || "bg-gray-100 text-gray-700";
};

// Toggle edit mode
const toggleEditMode = () => {
    if (isEditing.value) {
        // Cancel edit - reset form to original values
        isEditing.value = false
        error.value = ''
    } else {
        // Start edit - initialize form with current values
        editForm.value.verification_status = props.work.verification_status || 'Belum Verifikasi'
        editForm.value.project_status = props.work.project_status || 'Not Started'
        editForm.value.current_phase = props.work.current_phase || 'Not started'
        isEditing.value = true
        error.value = ''
    }
}

// Save changes
const saveChanges = () => {
    isSubmitting.value = true
    error.value = ''

    const data = {
        verification_status: editForm.value.verification_status,
        project_status: editForm.value.project_status,
        current_phase: editForm.value.current_phase
    }

    router.put(route('works.update-status', props.work.slug), data, {
        preserveState: true,
        onSuccess: (page) => {
            if (page.props.flash && page.props.flash.error) {
            error.value = page.props.flash.error;
            isEditing.value = true;
            } else {
            isEditing.value = false
            error.value = ''
            }
        },
        onError: (errors) => {
            console.error('Update status error:', errors)
            isEditing.value = true
            error.value = errors.verification_status ||
                errors.project_status ||
                errors.current_phase ||
                errors.error ||
                'Gagal menyimpan perubahan status'
        },
        onFinish: () => {
            isSubmitting.value = false
        }
    })
}

// Computed untuk menampilkan nilai saat ini atau fallback
const currentVerificationStatus = computed(() => {
    return props.work.verification_status || 'Belum Verifikasi'
})

const currentProjectStatus = computed(() => {
    return props.work.project_status || 'Not Started'
})

const currentPhase = computed(() => {
    return props.work.current_phase || 'Not started'
})
</script>

<template>
    <div class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border rounded-2xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                        <i class="fas fa-clipboard-check text-white text-lg"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight">
                            Status Pekerjaan
                        </h2>
                    </div>
                </div>
                <div v-if="hasRole('Admin') || isWorkLead" class="flex items-center space-x-2">
                    <button v-if="!isEditing" @click="toggleEditMode"
                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-xs font-semibold rounded-md hover:from-blue-700 hover:to-purple-700 shadow transition">
                        <i class="fas fa-edit"></i>
                        Edit
                    </button>
                    <div v-else class="flex items-center space-x-2">
                        <button @click="saveChanges" :disabled="isSubmitting"
                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-xs font-semibold rounded-md hover:from-blue-700 hover:to-purple-700 shadow transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-spinner fa-spin" v-if="isSubmitting"></i>
                            <i class="fas fa-save" v-else></i>
                            {{ isSubmitting ? 'Menyimpan' : 'Simpan' }}
                        </button>
                        <button @click="toggleEditMode" :disabled="isSubmitting"
                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-white text-gray-700 text-xs font-semibold rounded-md hover:bg-gray-50 border border-gray-300 shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-times"></i>
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Body -->
        <div class="p-4">
            <!-- Error message -->
            <div v-if="error && isEditing" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg shadow-sm">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                    <span class="text-sm font-medium text-red-800">{{ error }}</span>
                </div>
            </div>

            <dl class="space-y-4">
                <!-- Status Verifikasi -->
                <div
                    class="border rounded-lg p-3 transition-all hover:shadow-md bg-gradient-to-br from-white to-blue-50">
                    <dt class="text-sm font-semibold text-gray-700 mb-2">
                        Status Verifikasi ERF
                    </dt>
                    <dd>
                        <!-- Mode Edit -->
                        <div v-if="isEditing">
                            <select v-model="editForm.verification_status"
                                :disabled="currentVerificationStatus === 'Finish Verifikasi'"
                                class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm disabled:bg-gray-100 disabled:cursor-not-allowed">
                                <option v-for="option in verificationStatusOptions" :key="option" :value="option">
                                    {{ option }}
                                </option>
                            </select>
                        </div>
                        <!-- Mode View -->
                        <div v-else>
                            <span :class="getVerificationClass(currentVerificationStatus)"
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold shadow-sm w-full justify-center">
                                {{ currentVerificationStatus }}
                            </span>
                        </div>
                    </dd>
                </div>

                <!-- Status Proyek -->
                <div
                    class="border rounded-lg p-3 transition-all hover:shadow-md bg-gradient-to-br from-white to-blue-50">
                    <dt class="text-sm font-semibold text-gray-700 mb-2">
                        Status Pekerjaan
                    </dt>
                    <dd>
                        <!-- Mode Edit -->
                        <div v-if="isEditing">
                            <select v-model="editForm.project_status"
                                class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option v-for="option in projectStatusOptions" :key="option" :value="option">
                                    {{ option }}
                                </option>
                            </select>
                        </div>
                        <!-- Mode View -->
                        <div v-else>
                            <span :class="getStatusClass(currentProjectStatus)"
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold shadow-sm w-full justify-center">
                                {{ currentProjectStatus }}
                            </span>
                        </div>
                    </dd>
                </div>

                <!-- Fase Saat Ini -->
                <div
                    class="border rounded-lg p-3 transition-all hover:shadow-md bg-gradient-to-br from-white to-blue-50">
                    <dt class="text-sm font-semibold text-gray-700 mb-2">
                        Fase Pekerjaan Saat Ini
                    </dt>
                    <dd>
                        <!-- Mode Edit -->
                        <div v-if="isEditing">
                            <select v-model="editForm.current_phase"
                                class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option v-for="option in currentPhaseOptions" :key="option" :value="option">
                                    {{ option }}
                                </option>
                            </select>
                        </div>
                        <!-- Mode View -->
                        <div v-else>
                            <span :class="getPhaseClass(currentPhase)"
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold shadow-sm w-full justify-center">
                                {{ currentPhase }}
                            </span>
                        </div>
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</template>