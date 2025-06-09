<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    work: Object
})

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
    'Cancelled'
]

const currentPhaseOptions = [
    'Not started',
    'Initiating',
    'Executing',
    'Closing',
    'Hold',
    'Reject'
]

// Style functions
const getVerificationClass = (status) => {
    const classes = {
        "Belum Verifikasi": "bg-gray-50 text-gray-600 ring-gray-500/10",
        "In Progress Verifikasi": "bg-yellow-50 text-yellow-800 ring-yellow-600/20",
        "Finish Verifikasi": "bg-green-50 text-green-700 ring-green-600/20",
    };
    return classes[status] || "bg-gray-50 text-gray-600 ring-gray-500/10";
};

const getStatusClass = (status) => {
    const classes = {
        "Not Started": "bg-gray-50 text-gray-600 ring-gray-500/10",
        "In Progress": "bg-yellow-50 text-yellow-800 ring-yellow-600/20",
        "Finish": "bg-green-50 text-green-700 ring-green-600/20",
        "On Hold": "bg-orange-50 text-orange-700 ring-orange-600/20",
        "Cancelled": "bg-red-50 text-red-700 ring-red-600/20",
    };
    return classes[status] || "bg-gray-50 text-gray-600 ring-gray-500/10";
};

const getPhaseClass = (phase) => {
    const classes = {
        "Not started": "bg-gray-50 text-gray-600 ring-gray-500/10",
        "Initiating": "bg-blue-50 text-blue-700 ring-blue-600/20",
        "Executing": "bg-yellow-50 text-yellow-800 ring-yellow-600/20",
        "Closing": "bg-green-50 text-green-700 ring-green-600/20",
        "Hold": "bg-orange-50 text-orange-700 ring-orange-600/20",
        "Reject": "bg-red-50 text-red-700 ring-red-600/20",
    };
    return classes[phase] || "bg-gray-50 text-gray-600 ring-gray-500/10";
};

// Toggle edit mode
const toggleEditMode = () => {
    if (isEditing.value) {
        // Cancel edit - reset form to original values
        isEditing.value = false
        editForm.value.verification_status = props.work.verification_status || ''
        editForm.value.project_status = props.work.project_status || ''
        editForm.value.current_phase = props.work.current_phase || ''
        error.value = ''
    } else {
        // Start edit - initialize form with current values
        isEditing.value = true
        editForm.value.verification_status = props.work.verification_status || ''
        editForm.value.project_status = props.work.project_status || ''
        editForm.value.current_phase = props.work.current_phase || ''
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
        onSuccess: () => {
            isEditing.value = false
            error.value = ''
        },
        onError: (errors) => {
            console.error('Update status error:', errors)
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
    <div class="bg-white shadow-sm border border-gray-200 sm:rounded-lg">
        <div class="px-4 py-4 sm:px-6 sm:py-5">
            <!-- Header dengan tombol edit -->
            <div class="flex items-center justify-between mb-3 lg:mb-4">
                <h3 class="text-base font-semibold leading-6 text-gray-900">
                    Status
                </h3>
                <div class="flex items-center space-x-2">
                    <button v-if="!isEditing" @click="toggleEditMode"
                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Status
                    </button>
                    <div v-else class="flex items-center space-x-2">
                        <button @click="saveChanges" :disabled="isSubmitting"
                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <i class="fas fa-save mr-2" v-if="!isSubmitting"></i>
                            <i class="fas fa-spinner fa-spin mr-2" v-else></i>
                            {{ isSubmitting ? 'Menyimpan...' : 'Simpan' }}
                        </button>
                        <button @click="toggleEditMode" :disabled="isSubmitting"
                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Batal
                        </button>
                    </div>
                </div>
            </div>

            <!-- Error message -->
            <div v-if="error && isEditing" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-red-400 mr-2 mt-0.5"></i>
                    <span class="text-sm text-red-700">{{ error }}</span>
                </div>
            </div>

            <dl class="space-y-3 lg:space-y-4">
                <!-- Status Verifikasi -->
                <div>
                    <dt class="text-sm font-medium text-gray-500 mb-1">
                        Status Verifikasi ERF
                    </dt>
                    <dd>
                        <!-- Mode Edit -->
                        <div v-if="isEditing">
                            <select v-model="editForm.verification_status"
                                :disabled="currentVerificationStatus === 'Finish Verifikasi'"
                                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option v-for="option in verificationStatusOptions" :key="option" :value="option">
                                    {{ option }}
                                </option>
                            </select>
                        </div>
                        <!-- Mode View -->
                        <div v-else>
                            <span :class="getVerificationClass(currentVerificationStatus)"
                                class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset w-full justify-center">
                                {{ currentVerificationStatus }}
                            </span>
                        </div>
                    </dd>
                </div>

                <!-- Status Proyek -->
                <div>
                    <dt class="text-sm font-medium text-gray-500 mb-1">
                        Status Pekerjaan
                    </dt>
                    <dd>
                        <!-- Mode Edit -->
                        <div v-if="isEditing">
                            <select v-model="editForm.project_status"
                                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option v-for="option in projectStatusOptions" :key="option" :value="option">
                                    {{ option }}
                                </option>
                            </select>
                        </div>
                        <!-- Mode View -->
                        <div v-else>
                            <span :class="getStatusClass(currentProjectStatus)"
                                class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset w-full justify-center">
                                {{ currentProjectStatus }}
                            </span>
                        </div>
                    </dd>
                </div>

                <!-- Fase Saat Ini -->
                <div>
                    <dt class="text-sm font-medium text-gray-500 mb-1">
                        Fase Pekerjaan Saat Ini
                        <br>
                        <small class="text-sm text-gray-500">
                          Tanggal: {{ new Date().toISOString().slice(0, 10) }}
                        </small>
                    </dt>
                    <dd>
                        <!-- Mode Edit -->
                        <div v-if="isEditing">
                            <select v-model="editForm.current_phase"
                                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option v-for="option in currentPhaseOptions" :key="option" :value="option">
                                    {{ option }}
                                </option>
                            </select>
                        </div>
                        <!-- Mode View -->
                        <div v-else>
                            <span :class="getPhaseClass(currentPhase)"
                                class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset w-full justify-center">
                                {{ currentPhase }}
                            </span>
                        </div>
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</template>