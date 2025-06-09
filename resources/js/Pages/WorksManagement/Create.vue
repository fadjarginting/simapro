<script setup>
import { useForm, Head, router, Link } from "@inertiajs/vue3";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Swal from "sweetalert2";
import { ref, onMounted, onBeforeUnmount, reactive, watch, computed } from 'vue';
import axios from 'axios';

defineOptions({
    layout: AuthenticatedLayout,
});

const props = defineProps({
    errors: Object,
    success: Object,
    auth: Object,
    flash: Object,
    plants: Array,
    workPriorities: Array,
    workTypes: Array,
    requestCategories: Array,
    verificationStatuses: Array,
    projectStatuses: Array,
    currentPhases: Array,
    notes: Array
});

// Updated form structure to match the Work model and database migration
const form = useForm({
    erf_number: "",
    description: "",
    plant_id: "",
    requester_unit: "",
    work_priority: "",
    request_category: "",
    work_type: "",
    entry_date: "",
    erf_approved_date: null,
    clarification_date: null,
    erf_validated_date: null,
    initiating_target_date: null,
    executing_start_date: null,
    executing_target_date: null,
    executing_actual_date: null,
    verification_status: "",
    project_status: "",
    current_phase: "",
    lead_engineer_id: null,
    note_id: null,
});

// User search functionality for lead engineer
const users = ref([]);
const searchQuery = ref('');
const isDropdownOpen = ref(false);
const isLoading = ref(false);
const selectedUser = ref(null);
const userSelectRef = ref(null);

// Search users function
const searchUsers = async (query = '') => {
    if (query.length < 2 && query.length > 0) {
        return; // Don't search for single characters
    }

    isLoading.value = true;
    try {
        const response = await axios.get('/api/users/search', {
            params: {
                search: query,
                limit: 10 // Limit results for better performance
            }
        });
        users.value = response.data.data || response.data;
    } catch (error) {
        console.error('Error searching users:', error);
        users.value = [];
    } finally {
        isLoading.value = false;
    }
};

// Watch search query changes
watch(searchQuery, (newQuery) => {
    searchUsers(newQuery);
}, { debounce: 300 }); // Debounce to avoid too many API calls

// Select user function
const selectUser = (user) => {
    selectedUser.value = user;
    form.lead_engineer_id = user.id;
    searchQuery.value = user.name;
    isDropdownOpen.value = false;
};

// Clear selection
const clearSelection = () => {
    selectedUser.value = null;
    form.lead_engineer_id = null;
    searchQuery.value = '';
    isDropdownOpen.value = false;
};

// Handle input focus
const handleInputFocus = () => {
    isDropdownOpen.value = true;
    if (users.value.length === 0) {
        searchUsers(); // Load initial users
    }
};

// Handle click outside to close dropdown
const handleClickOutside = (event) => {
    if (userSelectRef.value && !userSelectRef.value.contains(event.target)) {
        isDropdownOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    // Load initial users
    searchUsers();
});

// Clean up event listener
onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});

// Filtered users based on search
const filteredUsers = computed(() => {
    if (!searchQuery.value) return users.value;
    return users.value.filter(user =>
        user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        user.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Submit form
const submit = () => {
    form.post(route("works.store"), {
        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: "Data pekerjaan baru berhasil ditambahkan.",
                showConfirmButton: false,
                timer: 1500,
            });
        },
        onError: (errors) => {
            console.error(errors);
        },
    });
};
</script>

<template>

    <Head>
        <title>Create New Work</title>
    </Head>

    <div class="py-2">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div
                    class="flex items-center justify-between p-6 pb-0 mb-0 border-b-0 border-b-solid border-b-transparent">
                    <h1 class="text-lg font-semibold text-gray-800 leading-tight">
                        Tambah Pekerjaan Baru
                        <p class="mt-1 text-sm text-gray-600">
                            Form ini digunakan untuk menambahkan pekerjaan baru ke dalam sistem. Pastikan semua data
                            yang dimasukkan sudah benar sebelum menyimpan.
                        </p>
                    </h1>
                    <!-- kembali ke list -->
                    <div class="flex items-center justify-end">
                        <Link :href="route('works.index')"
                            class="bg-gray-200 px-3 py-2 text-xs rounded inline-block whitespace-nowrap text-center font-bold leading-none text-gray-700 transition duration-300 hover:bg-gray-300">
                        <i class="fas fa-arrow-left mr-2 text-xs leading-none"></i>
                        <span>Kembali</span>
                        </Link>
                    </div>
                </div>

                <!-- Form-->
                <div class="max-w-full mt-0 pt-0 p-6 bg-white rounded-lg shadow-md">
                    <form @submit.prevent="submit">
                        <!-- Input data Pekerjaan -->
                        <!-- Legenda Keterangan -->
                        <div class="mb-4">
                            <div class="inline-flex items-center space-x-2">
                                <span class="text-red-600 text-lg">*</span>
                                <span class="text-sm text-gray-600">Wajib diisi</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2">

                            <!-- ERF Number -->
                            <div class="mb-2">
                                <InputLabel for="erf_number">
                                    <span>No ERF</span>
                                </InputLabel>
                                <TextInput id="erf_number" v-model="form.erf_number" placeholder="No ERF"
                                    class="mt-1 block w-full" />
                                <InputError :message="form.errors.erf_number" />
                            </div>

                            <!-- Description -->
                            <div class="mb-2">
                                <InputLabel for="description">
                                    <span>Deskripsi Pekerjaan <span class="text-red-600">*</span></span>
                                </InputLabel>
                                <TextInput id="description" v-model="form.description" placeholder="Deskripsi Pekerjaan"
                                    class="mt-1 block w-full" required />
                                <InputError :message="form.errors.description" />
                            </div>

                            <!-- Plant -->
                            <div class="mb-2 relative">
                                <InputLabel for="plant_id">
                                    <span>Plant <span class="text-red-600">*</span></span>
                                </InputLabel>
                                <div class="relative">
                                    <select id="plant_id" v-model="form.plant_id" required
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10">
                                        <option disabled value="">
                                            Pilih Plant
                                        </option>
                                        <option v-for="plant in plants" :key="plant.id" :value="plant.id">
                                            {{ plant.name }}
                                        </option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.plant_id" />
                            </div>

                            <!-- Requester Unit -->
                            <div class="mb-2 relative">
                                <InputLabel for="requester_unit">
                                    <span>Unit Kerja Peminta <span class="text-red-600">*</span></span>
                                </InputLabel>
                                <TextInput id="requester_unit" v-model="form.requester_unit"
                                    placeholder="Unit Kerja Peminta" class="mt-1 block w-full" required />
                                <InputError :message="form.errors.requester_unit" />
                            </div>

                            <!-- Work Priority -->
                            <div class="mb-2 relative">
                                <InputLabel for="work_priority">
                                    <span>Prioritas Pekerjaan <span class="text-red-600">*</span></span>
                                </InputLabel>
                                <div class="relative">
                                    <select id="work_priority" v-model="form.work_priority" required
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10">
                                        <option disabled value="">
                                            Pilih Prioritas Pekerjaan
                                        </option>
                                        <option v-for="priority in workPriorities" :key="priority"
                                            :value="priority">
                                            {{ priority }}
                                        </option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.work_priority" />
                            </div>

                            <!-- Work Type -->
                            <div class="mb-2 relative">
                                <InputLabel for="work_type">
                                    <span>Jenis Pekerjaan <span class="text-red-600">*</span></span>
                                </InputLabel>
                                <div class="relative">
                                    <select id="work_type" v-model="form.work_type" required
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10">
                                        <option disabled value="">
                                            Pilih Jenis Pekerjaan
                                        </option>
                                        <option v-for="type in workTypes" :key="type.value" :value="type">
                                            {{ type }}
                                        </option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.work_type" />
                            </div>

                            <!-- Request Category -->
                            <div class="mb-2 relative">
                                <InputLabel for="request_category">
                                    <span>Kategori Permintaan <span class="text-red-600">*</span></span>
                                </InputLabel>
                                <div class="relative">
                                    <select id="request_category" v-model="form.request_category" required
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10">
                                        <option disabled value="">
                                            Pilih Kategori Permintaan
                                        </option>
                                        <option v-for="category in requestCategories" :key="category"
                                            :value="category">
                                            {{ category }}
                                        </option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.request_category" />
                            </div>

                            <!-- Lead Engineer - Searchable Select -->
                            <div class="mb-2 relative" ref="userSelectRef">
                                <InputLabel for="lead_search" value="Lead Engineer (opsional)" />
                                <div class="relative">
                                    <input id="lead_search" type="text" v-model="searchQuery" @focus="handleInputFocus"
                                        @input="isDropdownOpen = true" placeholder="Cari dan pilih lead engineer..."
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 pr-10"
                                        autocomplete="off" />

                                    <!-- Clear button -->
                                    <button v-if="selectedUser" type="button" @click="clearSelection"
                                        class="absolute inset-y-0 right-8 flex items-center">
                                        <i class="fas fa-times text-gray-400 hover:text-gray-600"></i>
                                    </button>

                                    <!-- Search icon -->
                                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400" v-if="!isLoading"></i>
                                        <i class="fas fa-spinner fa-spin text-gray-400" v-if="isLoading"></i>
                                    </div>

                                    <!-- Dropdown -->
                                    <div v-if="isDropdownOpen"
                                        class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                                        <!-- Loading state -->
                                        <div v-if="isLoading" class="p-3 text-center text-gray-500">
                                            <i class="fas fa-spinner fa-spin mr-2"></i>
                                            Mencari pengguna...
                                        </div>

                                        <!-- No results -->
                                        <div v-else-if="filteredUsers.length === 0"
                                            class="p-3 text-center text-gray-500">
                                            {{ searchQuery ? 'Tidak ada pengguna yang ditemukan' : 'Ketik untuk mencari pengguna' }}
                                        </div>

                                        <!-- User list -->
                                        <div v-else>
                                            <button v-for="user in filteredUsers" :key="user.id" type="button"
                                                @click="selectUser(user)"
                                                class="w-full px-3 py-2 text-left hover:bg-gray-100 flex items-center space-x-3 border-b border-gray-100 last:border-b-0"
                                                :class="{ 'bg-blue-50': selectedUser && selectedUser.id === user.id }">
                                                <!-- User avatar placeholder -->
                                                <div
                                                    class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-xs font-semibold text-gray-600">
                                                    {{ user.name.charAt(0).toUpperCase() }}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="font-medium text-gray-900 truncate">{{ user.name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500 truncate" v-if="user.email">{{
                                                        user.email }}</div>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Selected user display -->
                                <div v-if="selectedUser" class="mt-2 p-2 bg-blue-50 rounded-md border border-blue-200">
                                    <div class="flex items-center space-x-2">
                                        <div
                                            class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-xs font-semibold text-white">
                                            {{ selectedUser.name.charAt(0).toUpperCase() }}
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-blue-900">{{ selectedUser.name }}</div>
                                            <div class="text-xs text-blue-600" v-if="selectedUser.email">{{
                                                selectedUser.email }}</div>
                                        </div>
                                    </div>
                                </div>

                                <span class="text-xs text-gray-500">Dapat diisi nanti jika belum ada keputusan</span>
                                <InputError :message="form.errors.lead_engineer_id" />
                            </div>

                            <!-- Entry Date -->
                            <div class="mb-2">
                                <InputLabel for="entry_date">
                                    <span>Tanggal Masuk <span class="text-red-600">*</span></span>
                                </InputLabel>
                                <TextInput id="entry_date" type="date" v-model="form.entry_date"
                                    class="mt-1 block w-full" required />
                                <InputError :message="form.errors.entry_date" />
                            </div>

                            <!-- ERF Approved Date -->
                            <div class="mb-2">
                                <InputLabel for="erf_approved_date" value="Tanggal Persetujuan ERF" />
                                <TextInput id="erf_approved_date" type="date" v-model="form.erf_approved_date"
                                    class="mt-1 block w-full" />
                                <InputError :message="form.errors.erf_approved_date" />
                            </div>

                            <!-- Clarification Date -->
                            <div class="mb-2">
                                <InputLabel for="clarification_date" value="Tanggal Klarifikasi" />
                                <TextInput id="clarification_date" type="date" v-model="form.clarification_date"
                                    class="mt-1 block w-full" />
                                <InputError :message="form.errors.clarification_date" />
                            </div>

                            <!-- ERF Validated Date -->
                            <div class="mb-2">
                                <InputLabel for="erf_validated_date" value="Tanggal Validasi ERF" />
                                <TextInput id="erf_validated_date" type="date" v-model="form.erf_validated_date"
                                    class="mt-1 block w-full" />
                                <InputError :message="form.errors.erf_validated_date" />
                            </div>

                            <!-- Initiating Target Date -->
                            <div class="mb-2">
                                <InputLabel for="initiating_target_date" value="Tanggal Target Inisiasi Selesai" />
                                <TextInput id="initiating_target_date" type="date" v-model="form.initiating_target_date"
                                    class="mt-1 block w-full" />
                                <InputError :message="form.errors.initiating_target_date" />
                            </div>

                            <!-- Verification Status -->
                            <div class="mb-2 relative">
                                <InputLabel for="verification_status">
                                    <span>Status Verifikasi <span class="text-gray-400">(opsional)</span></span>
                                </InputLabel>
                                <div class="relative">
                                    <select id="verification_status" v-model="form.verification_status"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10">
                                        <option disabled value="">
                                            Pilih Status Verifikasi
                                        </option>
                                        <option v-for="ver_status in verificationStatuses" :key="ver_status"
                                            :value="ver_status">
                                            {{ ver_status }}
                                        </option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.verification_status" />
                            </div>

                            <!-- Project Status -->
                            <div class="mb-2 relative">
                                <InputLabel for="project_status">
                                    <span>Status Proyek <span class="text-gray-400">(opsional)</span></span>
                                </InputLabel>
                                <div class="relative">
                                    <select id="project_status" v-model="form.project_status"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10">
                                        <option disabled value="">
                                            Pilih Status Proyek
                                        </option>
                                        <option v-for="status_project in projectStatuses" :key="status_project"
                                            :value="status_project">
                                            {{ status_project }}
                                        </option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.project_status" />
                            </div>
                            <!-- Notes / Catatan -->
                            <div class="mb-2 relative">
                                <InputLabel for="note_id">
                                    <span>Catatan <span class="text-gray-400">(opsional)</span></span>
                                </InputLabel>
                                <div class="relative">
                                    <select id="note_id" v-model="form.note_id"
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10">
                                        <option value="null">
                                            Pilih Catatan
                                        </option>
                                        <option v-for="note in notes" :key="note.id" :value="note.id">
                                            {{ note.content }}
                                        </option>
                                    </select>
                                    <!-- Ikon panah bawah -->
                                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.note_id" />
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-4">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition ease-in-out duration-150">
                                Simpan Pekerjaan Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>