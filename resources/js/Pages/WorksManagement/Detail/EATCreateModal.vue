<script setup>
import { ref, reactive, watch, computed, onMounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    work: Object,
    disciplines: Array,
    users: Array,
    isEdit: {
        type: Boolean,
        default: false
    },
    existingData: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['close']);

const currentStep = ref(0);
const modalBody = ref(null);
const isLoading = ref(false);

// Local reactive data for disciplines and users
const disciplinesData = ref([]);
const usersData = ref([]);
const usersByDiscipline = reactive({});
const leadsByDiscipline = reactive({}); // Add this for leads/managers

const steps = [
    { title: 'Detail EAT' },
    { title: 'Aktivitas' },
    { title: 'Approval' },
    { title: 'Review' }
];

// Initialize form with database structure
const form = useForm({
    work_id: props.work?.id,
    start_date: '',
    end_date: '',
    total_duration_days: 0,
    activities: [],
    approvals: [],
    isDraft: false,
});

// Initialize existing data if in edit mode
if (props.isEdit && props.existingData) {
    form.work_id = props.existingData.work_id;
    form.start_date = props.existingData.start_date ? new Date(props.existingData.start_date).toISOString().split('T')[0] : '';
    form.end_date = props.existingData.end_date ? new Date(props.existingData.end_date).toISOString().split('T')[0] : '';
    form.total_duration_days = props.existingData.total_duration_days || 0;

    // Map existing activities
    if (props.existingData.activities) {
        form.activities = props.existingData.activities.map(activity => ({
            id: activity.id,
            discipline_id: activity.discipline_id,
            activity_name: activity.activity_name,
            activity_description: activity.activity_description || '',
            duration_days: activity.duration_days || 0,
            start_date: activity.start_date ? new Date(activity.start_date).toISOString().split('T')[0] : '',
            end_date: activity.end_date ? new Date(activity.end_date).toISOString().split('T')[0] : '',
            sequence_order: activity.sequence_order || 1,
            pic_ids: activity.pics ? activity.pics.map(pic => pic.id) : []
        }));
    }
}

// If activities are empty on create, add one default
if (!props.isEdit && form.activities.length === 0) {
    form.activities.push({
        discipline_id: '',
        activity_name: '',
        activity_description: '',
        duration_days: 0,
        start_date: form.start_date,
        end_date: form.start_date,
        sequence_order: 1,
        pic_ids: [],
    });
}

// State for approver selections in the UI
const approvalSelections = reactive({});
if (props.isEdit && props.existingData?.approvals) {
    props.existingData.approvals.forEach(approval => {
        const approverDisciplineId = approval.approver?.discipline_id;
        if (approverDisciplineId) {
            approvalSelections[approverDisciplineId] = approval.approver_id;
        }
    });
}

// Fetch data on component mount
onMounted(async () => {
    await fetchRequiredData();
});

const fetchRequiredData = async () => {
    isLoading.value = true;

    try {
        // Use provided data first, then fetch if not available
        if (props.disciplines && props.disciplines.length > 0) {
            disciplinesData.value = props.disciplines;
        } else {
            await fetchDisciplines();
        }
        
    } catch (error) {
        console.error('Error fetching required data:', error);
        alert('Terjadi kesalahan saat memuat data. Silakan coba lagi.');
    } finally {
        isLoading.value = false;
    }
};

const fetchDisciplines = async () => {
    try {
        const response = await axios.get(route('disciplines.api.all'));
        disciplinesData.value = response.data;
    } catch (error) {
        console.error('Error fetching disciplines:', error);
        throw error;
    }
};

const fetchUsersByDiscipline = async (disciplineId) => {
    if (!disciplineId || usersByDiscipline[disciplineId]) return;

    try {
        const response = await axios.get(route('users.by-discipline', disciplineId));
        usersByDiscipline[disciplineId] = response.data.data;
    } catch (error) {
        console.error('Error fetching users by discipline:', error);
        usersByDiscipline[disciplineId] = [];
    }
};

// Add new function to fetch leads/managers for discipline
const fetchLeadsForDiscipline = async (disciplineId) => {
    if (!disciplineId || leadsByDiscipline[disciplineId]) return;

    try {
        const response = await axios.get(route('users.for-discipline'), {
            params: { discipline_id: disciplineId }
        });
        leadsByDiscipline[disciplineId] = response.data.data;
    } catch (error) {
        console.error('Error fetching leads for discipline:', error);
        leadsByDiscipline[disciplineId] = [];
    }
};

// Watchers to calculate durations automatically
watch([() => form.start_date, () => form.end_date], ([start, end]) => {
    if (start && end) {
        const startDate = new Date(start);
        const endDate = new Date(end);
        if (endDate >= startDate) {
            const diffTime = Math.abs(endDate - startDate);
            form.total_duration_days = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        } else {
            form.total_duration_days = 0;
        }
    }
}, { immediate: true });

// Watch activity dates to calculate duration
const watchActivityDuration = (activity) => {
    watch([() => activity.start_date, () => activity.end_date], ([start, end]) => {
        if (start && end) {
            const startDate = new Date(start);
            const endDate = new Date(end);
            if (endDate >= startDate) {
                const diffTime = Math.abs(endDate - startDate);
                activity.duration_days = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            } else {
                activity.duration_days = 0;
            }
        }
    }, { immediate: true });
};

// Watch for discipline changes to fetch users
watch(() => form.activities.map(a => a.discipline_id), async (disciplineIds, oldDisciplineIds) => {
    for (let i = 0; i < disciplineIds.length; i++) {
        const disciplineId = disciplineIds[i];
        const oldDisciplineId = oldDisciplineIds?.[i];

        if (disciplineId && disciplineId !== oldDisciplineId) {
            await fetchUsersByDiscipline(disciplineId);
            // Reset PIC jika discipline berubah
            if (oldDisciplineId) {
                form.activities[i].pic_ids = [];
            }
        }
    }
}, { deep: true });

// Apply duration watcher to existing activities
form.activities.forEach(activity => {
    watchActivityDuration(activity);
});

// Watch for discipline changes to fetch users
const watchDisciplineChange = (activity) => {
    watch(() => activity.discipline_id, async (newDisciplineId) => {
        if (newDisciplineId) {
            await fetchUsersByDiscipline(newDisciplineId);
            // Reset PIC selection when discipline changes
            activity.pic_ids = [];
        }
    });
};

// Apply discipline watcher to existing activities
form.activities.forEach(activity => {
    watchDisciplineChange(activity);
    // Fetch users for existing discipline
    if (activity.discipline_id) {
        fetchUsersByDiscipline(activity.discipline_id);
    }
});

const involvedDisciplines = computed(() => {
    if (!disciplinesData.value) return [];
    const disciplineIds = [...new Set(form.activities.map(act => act.discipline_id).filter(id => id))];
    return disciplinesData.value.filter(d => disciplineIds.includes(d.id));
});

// Watch involved disciplines to fetch leads and initialize selections
watch(involvedDisciplines, async (newDisciplines) => {
    // Fetch leads for each discipline
    for (const discipline of newDisciplines) {
        await fetchLeadsForDiscipline(discipline.id);
    }

    // Initialize approval selections
    newDisciplines.forEach(d => {
        if (!approvalSelections[d.id]) {
            approvalSelections[d.id] = null;
        }
    });
});

const getUsersForDiscipline = (disciplineId) => {
    if (!disciplineId) return [];
    return usersByDiscipline[disciplineId] || [];
};

// Update this function to use the new leadsByDiscipline data
const getLeadsForDiscipline = (disciplineId) => {
    if (!disciplineId) return [];
    return leadsByDiscipline[disciplineId] || [];
};

const nextStep = () => {
    if (currentStep.value < steps.length - 1) {
        currentStep.value++;
        modalBody.value.scrollTop = 0;
    }
};

const prevStep = () => {
    if (currentStep.value > 0) {
        currentStep.value--;
        modalBody.value.scrollTop = 0;
    }
};

const addActivity = () => {
    const newActivity = reactive({
        discipline_id: '',
        activity_name: '',
        activity_description: '',
        duration_days: 0,
        start_date: form.start_date,
        end_date: form.start_date,
        sequence_order: form.activities.length + 1,
        pic_ids: [],
    });

    form.activities.push(newActivity);

    watchActivityDuration(newActivity);
    watchDisciplineChange(newActivity);

    // Automatically calculate duration if start and end dates are set
    if (newActivity.start_date && newActivity.end_date) {
        const startDate = new Date(newActivity.start_date);
        const endDate = new Date(newActivity.end_date);
        if (endDate >= startDate) {
            const diffTime = Math.abs(endDate - startDate);
            newActivity.duration_days = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        }
    }
};

const removeActivity = (index) => {
    form.activities.splice(index, 1);
    // Update sequence orders
    form.activities.forEach((activity, idx) => {
        activity.sequence_order = idx + 1;
    });
};

const closeModal = () => {
    emit('close');
};

const submitEAT = (isDraft = false) => {
    // Map approvals to match database structure
    form.approvals = Object.entries(approvalSelections)
        .filter(([, approver_id]) => approver_id !== null)
        .map(([discipline_id, approver_id]) => ({
            approver_id: approver_id
        }));

    form.isDraft = isDraft;

    const url = props.isEdit ? route('eat.update', props.existingData.id) : route('eat.store');
    const method = props.isEdit ? 'put' : 'post';

    form.submit(method, url, {
        onSuccess: (response) => {
            emit('success', response.data);
            emit('updated', response.data);
            closeModal();
        },
        
        onError: (errors) => {
            console.error("Validation Errors:", errors);
            const errorKeys = Object.keys(errors);
            if (errorKeys.some(k => k.startsWith('activities'))) {
                currentStep.value = 1;
            } else if (errorKeys.some(k => k.startsWith('approval'))) {
                currentStep.value = 2;
            } else {
                currentStep.value = 0;
            }
        }
    });
};

// Helper functions for review step
const getDisciplineName = (id) => disciplinesData.value?.find(d => d.id === id)?.name || 'N/A';

const getUserName = (id) => {
    // Search in usersByDiscipline first
    for (const disciplineUsers of Object.values(usersByDiscipline)) {
        const user = disciplineUsers.find(u => u.id === id);
        if (user) return user.name;
    }

    // Search in leadsByDiscipline
    for (const disciplineLeads of Object.values(leadsByDiscipline)) {
        const lead = disciplineLeads.find(u => u.id === id);
        if (lead) return lead.name;
    }

    // Fallback to usersData
    return usersData.value?.find(u => u.id === id)?.name || 'N/A';
};

const getPicNames = (ids) => {
    if (!ids || ids.length === 0) return 'Belum ada PIC';
    return ids.map(id => getUserName(id)).join(', ');
};

const getApproverName = (disciplineId) => getUserName(approvalSelections[disciplineId]) || 'Belum Dipilih';

const formatDate = (dateStr) => {
    if (!dateStr) return 'N/A';
    return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};

// Helper function to handle multi-select
const handlePicSelection = (activity, event) => {
    const selectedOptions = Array.from(event.target.selectedOptions);
    activity.pic_ids = selectedOptions.map(option => parseInt(option.value));
};

const isPicSelected = (activity, userId) => {
    return activity.pic_ids.includes(userId);
};
</script>

<template>
    <!-- Modal Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
        <div class="bg-gradient-to-br from-blue-50 via-white to-purple-50 rounded-2xl shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Modal Header -->
            <div class="border-b p-4 bg-gradient-to-r from-blue-100/50 via-white to-purple-100/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow-md">
                            <i class="fas fa-tasks text-white text-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900 tracking-tight">
                                {{ isEdit ? 'Edit EAT' : 'Buat Engineering Assignment Task (EAT) Baru' }}
                            </h1>
                            <p class="text-gray-600 text-sm mt-1">
                                Ikuti langkah-langkah untuk membuat tugas engineering baru.
                            </p>
                        </div>
                    </div>
                    <button @click="closeModal" class="text-gray-500 hover:text-red-500 w-8 h-8 flex items-center justify-center rounded-full hover:bg-red-100 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="flex-1 overflow-y-auto" ref="modalBody">
                <div class="p-6">
                    <!-- Loading State -->
                    <div v-if="isLoading" class="flex flex-col items-center justify-center py-8">
                        <div class="w-8 h-8 border-4 border-blue-400 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                        <span class="ml-2 text-gray-600">Memuat data...</span>
                    </div>

                    <!-- Content when loaded -->
                    <div v-else>
                        <!-- Progress Steps -->
                        <div class="flex items-center justify-between mb-8">
                            <div v-for="(step, index) in steps" :key="index" class="flex items-center w-full">
                                <div class="flex items-center">
                                    <div :class="`w-10 h-10 rounded-full flex items-center justify-center font-bold text-white transition-all duration-300 ${currentStep > index ? 'bg-green-500 shadow-green-300' :
                                        currentStep === index ? 'bg-blue-500 shadow-blue-300 scale-110' : 'bg-gray-400'
                                        } shadow-md`">
                                        <i v-if="currentStep > index" class="fas fa-check"></i>
                                        <span v-else>{{ index + 1 }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="font-semibold text-gray-900">{{ step.title }}</div>
                                    </div>
                                </div>
                                <div v-if="index < steps.length - 1" class="flex-1 h-1 bg-gray-200 mx-4 rounded-full">
                                    <div class="h-1 rounded-full bg-gradient-to-r from-green-500 to-blue-500 transition-all duration-500" :style="{ width: currentStep > index ? '100%' : '0%' }"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Container -->
                        <div class="bg-white/50 rounded-lg p-6 shadow-inner border border-gray-200/80">
                            <!-- Step 1: Detail EAT -->
                            <div v-show="currentStep === 0">
                                <h2 class="text-lg font-bold mb-6 text-gray-900">Langkah 1: Detail EAT Utama</h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium mb-2 text-gray-700">Tanggal Mulai EAT</label>
                                        <input v-model="form.start_date" type="date" class="form-input" required>
                                        <div v-if="form.errors.start_date" class="text-red-500 text-xs mt-1">{{ form.errors.start_date }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-2 text-gray-700">Tanggal Selesai EAT</label>
                                        <input v-model="form.end_date" type="date" class="form-input" required>
                                        <div v-if="form.errors.end_date" class="text-red-500 text-xs mt-1">{{ form.errors.end_date }}</div>
                                    </div>
                                    <div class="col-span-2 mt-2">
                                        <div class="text-sm text-gray-700 bg-blue-50 border border-blue-200 p-3 rounded-lg shadow-sm">
                                            <span class="font-medium">Total Durasi EAT:</span>
                                            <span class="ml-2 font-bold text-blue-800">{{ form.total_duration_days }} hari</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Tambah Aktivitas -->
                            <div v-show="currentStep === 1">
                                <div class="flex justify-between items-center mb-6">
                                    <h2 class="text-lg font-bold text-gray-900">Langkah 2: Tambah Aktivitas</h2>
                                    <button @click="addActivity" type="button" class="btn btn-primary">
                                        <i class="fas fa-plus"></i>
                                        <span>Tambah Aktivitas</span>
                                    </button>
                                </div>

                                <div class="space-y-4 max-h-[50vh] overflow-y-auto p-1">
                                    <div v-for="(activity, index) in form.activities" :key="index" class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm relative hover:shadow-md transition-shadow">
                                        <button v-if="form.activities.length > 1" @click="removeActivity(index)" type="button" class="absolute top-2 right-2 text-red-400 hover:text-red-600 w-7 h-7 flex items-center justify-center rounded-full hover:bg-red-100 transition-colors">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>

                                        <h3 class="font-bold mb-4 text-gray-800">Aktivitas #{{ index + 1 }}</h3>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="form-label">Nama Aktivitas</label>
                                                <input v-model="activity.activity_name" type="text" class="form-input" placeholder="e.g., BOQ, Design, etc.">
                                                <div v-if="form.errors[`activities.${index}.activity_name`]" class="form-error">{{ form.errors[`activities.${index}.activity_name`] }}</div>
                                            </div>
                                            <div>
                                                <label class="form-label">Disiplin</label>
                                                <select v-model="activity.discipline_id" class="form-select">
                                                    <option value="">Pilih Disiplin</option>
                                                    <option v-for="discipline in disciplinesData" :key="discipline.id" :value="discipline.id">{{ discipline.name }}</option>
                                                </select>
                                                <div v-if="form.errors[`activities.${index}.discipline_id`]" class="form-error">{{ form.errors[`activities.${index}.discipline_id`] }}</div>
                                            </div>
                                            <div class="col-span-2">
                                                <label class="form-label">Person-in-Charge (PIC)</label>
                                                <div v-if="activity.discipline_id" class="bg-gray-50 p-3 rounded-lg border border-gray-200 max-h-40 overflow-y-auto">
                                                    <div class="space-y-2">
                                                        <div v-for="user in getUsersForDiscipline(activity.discipline_id)" :key="user.id" class="flex items-center space-x-3">
                                                            <input type="checkbox" :id="`pic-${index}-${user.id}`" :value="user.id" v-model="activity.pic_ids" class="form-checkbox">
                                                            <label :for="`pic-${index}-${user.id}`" class="text-sm text-gray-700 cursor-pointer">{{ user.name }}</label>
                                                        </div>
                                                    </div>
                                                    <div v-if="getUsersForDiscipline(activity.discipline_id).length === 0" class="text-sm text-gray-500 text-center py-2">Tidak ada user tersedia untuk disiplin ini</div>
                                                </div>
                                                <div v-if="activity.pic_ids.length > 0" class="mt-2">
                                                    <div class="text-sm text-gray-600 mb-1">PIC yang dipilih:</div>
                                                    <div class="flex flex-wrap gap-1.5">
                                                        <span v-for="picId in activity.pic_ids" :key="picId" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 shadow-sm">
                                                            {{ getUserName(picId) }}
                                                            <button @click="activity.pic_ids = activity.pic_ids.filter(id => id !== picId)" class="ml-1.5 text-blue-500 hover:text-blue-700"><i class="fas fa-times text-xs"></i></button>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div v-if="!activity.discipline_id" class="text-sm text-gray-500 mt-1 p-3 bg-gray-50 rounded-lg border border-gray-200">Pilih disiplin terlebih dahulu untuk memilih PIC</div>
                                                <div v-if="form.errors[`activities.${index}.pic_ids`]" class="form-error">{{ form.errors[`activities.${index}.pic_ids`] }}</div>
                                            </div>
                                            <div>
                                                <label class="form-label">Tanggal Mulai Aktivitas</label>
                                                <input v-model="activity.start_date" type="date" class="form-input">
                                                <div v-if="form.errors[`activities.${index}.start_date`]" class="form-error">{{ form.errors[`activities.${index}.start_date`] }}</div>
                                            </div>
                                            <div>
                                                <label class="form-label">Tanggal Selesai Aktivitas</label>
                                                <input v-model="activity.end_date" type="date" class="form-input">
                                                <div v-if="form.errors[`activities.${index}.end_date`]" class="form-error">{{ form.errors[`activities.${index}.end_date`] }}</div>
                                            </div>
                                            <div class="col-span-2 mt-1">
                                                <div class="text-sm text-gray-700 bg-gray-100 p-2 rounded-md"><span class="font-medium">Durasi aktivitas:</span><span class="ml-1 font-semibold">{{ activity.duration_days }} hari</span></div>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label class="form-label">Deskripsi Aktivitas</label>
                                                <textarea v-model="activity.activity_description" rows="3" class="form-textarea" placeholder="Deskripsi singkat mengenai aktivitas"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3: Tentukan Approval -->
                            <div v-show="currentStep === 2">
                                <h2 class="text-lg font-bold mb-2 text-gray-900">Langkah 3: Tentukan Approver</h2>
                                <p class="text-gray-600 mb-6 text-sm">Pilih lead atau manager yang bertanggung jawab untuk menyetujui pekerjaan dari setiap disiplin yang terlibat.</p>
                                <div class="space-y-4">
                                    <div v-if="involvedDisciplines.length === 0" class="text-center text-gray-500 p-6 bg-gray-50 rounded-lg border-2 border-dashed">Pilih disiplin pada aktivitas di langkah sebelumnya untuk menentukan approver.</div>
                                    <div v-for="discipline in involvedDisciplines" :key="discipline.id" class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                                        <div class="flex flex-col md:flex-row justify-between md:items-center">
                                            <label class="block font-medium mb-2 md:mb-0 text-gray-700">Approver untuk Disiplin: <span class="font-bold text-blue-800">{{ discipline.name }}</span></label>
                                            <div class="w-full md:w-64">
                                                <select v-model="approvalSelections[discipline.id]" class="form-select">
                                                    <option :value="null">Pilih Lead/Manager</option>
                                                    <option v-for="lead in getLeadsForDiscipline(discipline.id)" :key="lead.id" :value="lead.id">{{ lead.name }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="form.errors.approvals" class="form-error mt-2">{{ form.errors.approvals }}</div>
                                </div>
                            </div>

                            <!-- Step 4: Review & Kirim -->
                            <div v-show="currentStep === 3">
                                <h2 class="text-lg font-bold mb-6 text-gray-900">Langkah 4: Review dan Kirim</h2>
                                <div class="space-y-6">
                                    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                                        <h3 class="font-bold mb-4 text-gray-900">Ringkasan EAT</h3>
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div><span class="text-gray-600">Work ID:</span><span class="ml-2 text-gray-900 font-semibold">{{ form.work_id }}</span></div>
                                            <div><span class="text-gray-600">Total Durasi:</span><span class="ml-2 text-gray-900 font-semibold">{{ form.total_duration_days }} hari</span></div>
                                            <div><span class="text-gray-600">Tanggal Mulai:</span><span class="ml-2 text-gray-900 font-semibold">{{ formatDate(form.start_date) }}</span></div>
                                            <div><span class="text-gray-600">Tanggal Selesai:</span><span class="ml-2 text-gray-900 font-semibold">{{ formatDate(form.end_date) }}</span></div>
                                        </div>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                                        <h3 class="font-bold mb-4 text-gray-900">Ringkasan Aktivitas ({{ form.activities.length }})</h3>
                                        <div class="space-y-3 max-h-48 overflow-y-auto p-1">
                                            <div v-for="(activity, index) in form.activities" :key="index" class="bg-gray-50 rounded-lg p-3 text-sm border border-gray-200/80">
                                                <p class="font-semibold text-gray-900">{{ activity.sequence_order }}. {{ activity.activity_name }} ({{ activity.duration_days }} hari)</p>
                                                <p class="text-gray-600">Disiplin: <span class="font-medium">{{ getDisciplineName(activity.discipline_id) }}</span></p>
                                                <p class="text-gray-600">PIC: <span class="font-medium">{{ getPicNames(activity.pic_ids) }}</span></p>
                                                <p class="text-gray-600">{{ formatDate(activity.start_date) }} - {{ formatDate(activity.end_date) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                                        <h3 class="font-bold mb-4 text-gray-900">Ringkasan Approval</h3>
                                        <div class="space-y-2">
                                            <div v-for="discipline in involvedDisciplines" :key="discipline.id" class="flex justify-between text-sm p-2 bg-gray-50 rounded-md">
                                                <span class="text-gray-900">Disiplin: {{ discipline.name }}</span>
                                                <span class="font-semibold text-gray-700">{{ getApproverName(discipline.id) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50/80 px-6 py-4 border-t border-gray-200 flex-none">
                <div class="flex justify-between">
                    <div>
                        <button v-if="currentStep > 0" @click="prevStep" type="button" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            <span>Kembali</span>
                        </button>
                    </div>
                    <div class="flex gap-2">
                        <button @click="closeModal" type="button" class="btn btn-light">Batal</button>
                        <button v-if="currentStep < 3" @click="nextStep" type="button" class="btn btn-primary">
                            <span>Lanjutkan</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                        <button v-if="currentStep === 3" @click="submitEAT(true)" type="button" :disabled="form.processing" class="btn btn-warning">
                            <i class="fas fa-save"></i>
                            <span>Simpan Draft</span>
                        </button>
                        <button v-if="currentStep === 3" @click="submitEAT(false)" type="button" :disabled="form.processing" class="btn btn-success">
                            <i class="fas fa-paper-plane"></i>
                            <span>{{ form.processing ? 'Mengirim...' : 'Kirim Persetujuan' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="postcss">
.form-input, .form-select, .form-textarea {
    @apply w-full bg-white border border-gray-300 rounded-lg px-3 py-2 text-gray-900 shadow-sm transition-colors duration-200;
    @apply focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400;
}

.form-checkbox {
    @apply w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer;
}

.form-label {
    @apply block text-sm font-medium mb-2 text-gray-700;
}

.form-error {
    @apply text-red-500 text-xs mt-1;
}

.btn {
    @apply inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold rounded-lg shadow-md transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed;
    @apply focus:outline-none focus:ring-2 focus:ring-offset-2;
}

.btn-primary {
    @apply bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 focus:ring-blue-500;
}

.btn-secondary {
    @apply bg-gray-500 text-white hover:bg-gray-600 focus:ring-gray-400;
}

.btn-success {
    @apply bg-gradient-to-r from-green-500 to-teal-500 text-white hover:from-green-600 hover:to-teal-600 focus:ring-green-500;
}

.btn-warning {
    @apply bg-gradient-to-r from-yellow-500 to-orange-500 text-white hover:from-yellow-600 hover:to-orange-600 focus:ring-yellow-500;
}

.btn-light {
    @apply bg-gray-200 text-gray-800 hover:bg-gray-300 focus:ring-gray-300;
}

.overflow-y-auto::-webkit-scrollbar {
    width: 8px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
