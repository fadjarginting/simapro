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

// Notes dropdown functionality
const isNotesDropdownOpen = ref(false);
const selectedNote = ref(null);
const notesDropdownRef = ref(null);
const notesSearchQuery = ref('');

// Search users function
const searchUsers = async (query = '') => {
    if (query.length < 2 && query.length > 0) {
        users.value = [];
        return;
    }
    isLoading.value = true;
    try {
        const response = await axios.get('/api/users/search', {
            params: { search: query, limit: 10 }
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
}, { debounce: 300 });

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
    if (users.value.length === 0 && !searchQuery.value) {
        searchUsers();
    }
};

// Notes dropdown functions
const selectNoteFunc = (note) => {
    selectedNote.value = note;
    form.note_id = note ? note.id : null;
    notesSearchQuery.value = note ? note.content : '';
    isNotesDropdownOpen.value = false;
};

const handleNotesInputFocus = () => {
    isNotesDropdownOpen.value = true;
};

const clearNotesSelection = () => {
    selectedNote.value = null;
    form.note_id = null;
    notesSearchQuery.value = '';
    isNotesDropdownOpen.value = false;
};

// Handle click outside to close dropdown
const handleClickOutside = (event) => {
    if (userSelectRef.value && !userSelectRef.value.contains(event.target)) {
        isDropdownOpen.value = false;
    }
    if (notesDropdownRef.value && !notesDropdownRef.value.contains(event.target)) {
        isNotesDropdownOpen.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    searchUsers();
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});

// Filtered notes based on search
const filteredNotes = computed(() => {
    if (!notesSearchQuery.value) return props.notes;
    return props.notes.filter(note =>
        note.content.toLowerCase().includes(notesSearchQuery.value.toLowerCase())
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
            <div class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border rounded-2xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                                <i class="fas fa-plus text-white text-lg"></i>
                            </div>
                            <div>
                                <h1 class="text-lg font-bold text-gray-900 tracking-tight">
                                    Tambah Pekerjaan Baru
                                </h1>
                                <p class="text-xs text-gray-600 mt-0.5">
                                    Isi form untuk menambahkan pekerjaan baru ke dalam sistem.
                                </p>
                            </div>
                        </div>
                        <Link :href="route('works.index')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-xs font-semibold rounded-md hover:bg-gray-50 shadow-sm transition">
                        <i class="fas fa-arrow-left text-xs"></i>
                        <span>Kembali</span>
                        </Link>
                    </div>
                </div>

                <!-- Form-->
                <div class="p-4">
                    <form @submit.prevent="submit">
                        <!-- Legenda Keterangan -->
                        <div class="mb-3">
                            <div class="inline-flex items-center space-x-1.5">
                                <span class="text-red-500 text-sm font-bold">*</span>
                                <span class="text-xs text-gray-500">Wajib diisi</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                            <!-- ERF Number -->
                            <div class="mb-1">
                                <InputLabel for="erf_number" value="No ERF" class="text-xs" />
                                <TextInput id="erf_number" v-model="form.erf_number" placeholder="No ERF"
                                    class="mt-1 block w-full text-sm" />
                                <InputError :message="form.errors.erf_number" class="mt-1" />
                            </div>

                            <!-- Description -->
                            <div class="mb-1">
                                <InputLabel for="description" class="text-xs">
                                    <span>Deskripsi Pekerjaan <span class="text-red-500">*</span></span>
                                </InputLabel>
                                <TextInput id="description" v-model="form.description" placeholder="Deskripsi Pekerjaan"
                                    class="mt-1 block w-full text-sm" required />
                                <InputError :message="form.errors.description" class="mt-1" />
                            </div>

                            <!-- Plant -->
                            <div class="mb-1">
                                <InputLabel for="plant_id" class="text-xs">
                                    <span>Plant <span class="text-red-500">*</span></span>
                                </InputLabel>
                                <div class="relative mt-1">
                                    <select id="plant_id" v-model="form.plant_id" required
                                        class="block w-full px-2.5 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm appearance-none pr-8">
                                        <option disabled value="">Pilih Plant</option>
                                        <option v-for="plant in plants" :key="plant.id" :value="plant.id">
                                            {{ plant.name }}
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-2.5 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400 text-xs"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.plant_id" class="mt-1" />
                            </div>

                            <!-- Requester Unit -->
                            <div class="mb-1">
                                <InputLabel for="requester_unit" class="text-xs">
                                    <span>Unit Kerja Peminta <span class="text-red-500">*</span></span>
                                </InputLabel>
                                <TextInput id="requester_unit" v-model="form.requester_unit"
                                    placeholder="Unit Kerja Peminta" class="mt-1 block w-full text-sm" required />
                                <InputError :message="form.errors.requester_unit" class="mt-1" />
                            </div>

                            <!-- Work Priority -->
                            <div class="mb-1">
                                <InputLabel for="work_priority" class="text-xs">
                                    <span>Prioritas Pekerjaan <span class="text-red-500">*</span></span>
                                </InputLabel>
                                <div class="relative mt-1">
                                    <select id="work_priority" v-model="form.work_priority" required
                                        class="block w-full px-2.5 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm appearance-none pr-8">
                                        <option disabled value="">Pilih Prioritas</option>
                                        <option v-for="priority in workPriorities" :key="priority" :value="priority">
                                            {{ priority }}
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-2.5 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400 text-xs"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.work_priority" class="mt-1" />
                            </div>

                            <!-- Work Type -->
                            <div class="mb-1">
                                <InputLabel for="work_type" class="text-xs">
                                    <span>Jenis Pekerjaan <span class="text-red-500">*</span></span>
                                </InputLabel>
                                <div class="relative mt-1">
                                    <select id="work_type" v-model="form.work_type" required
                                        class="block w-full px-2.5 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm appearance-none pr-8">
                                        <option disabled value="">Pilih Jenis</option>
                                        <option v-for="type in workTypes" :key="type.value" :value="type">
                                            {{ type }}
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-2.5 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400 text-xs"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.work_type" class="mt-1" />
                            </div>

                            <!-- Request Category -->
                            <div class="mb-1">
                                <InputLabel for="request_category" class="text-xs">
                                    <span>Kategori Permintaan <span class="text-red-500">*</span></span>
                                </InputLabel>
                                <div class="relative mt-1">
                                    <select id="request_category" v-model="form.request_category" required
                                        class="block w-full px-2.5 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm appearance-none pr-8">
                                        <option disabled value="">Pilih Kategori</option>
                                        <option v-for="category in requestCategories" :key="category" :value="category">
                                            {{ category }}
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-2.5 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400 text-xs"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.request_category" class="mt-1" />
                            </div>

                            <!-- Lead Engineer - Searchable Select -->
                            <div class="mb-1" ref="userSelectRef">
                                <InputLabel for="lead_search" value="Lead Engineer (opsional)" class="text-xs" />
                                <div class="relative mt-1">
                                    <TextInput id="lead_search" type="text" v-model="searchQuery" @focus="handleInputFocus"
                                        @input="isDropdownOpen = true" placeholder="Cari lead engineer..."
                                        class="w-full text-sm pr-12" autocomplete="off" />

                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                                        <button v-if="selectedUser" type="button" @click="clearSelection" class="p-1 text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                        <i class="fas fa-search text-gray-400 ml-1" v-if="!isLoading && !selectedUser"></i>
                                        <i class="fas fa-spinner fa-spin text-gray-400 ml-1" v-if="isLoading"></i>
                                    </div>

                                    <div v-if="isDropdownOpen" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                                        <div v-if="isLoading" class="p-2 text-center text-xs text-gray-500">Mencari...</div>
                                        <div v-else-if="users.length === 0" class="p-2 text-center text-xs text-gray-500">
                                            {{ searchQuery ? 'Tidak ada hasil' : 'Ketik untuk mencari' }}
                                        </div>
                                        <div v-else>
                                            <button v-for="user in users" :key="user.id" type="button" @click="selectUser(user)"
                                                class="w-full px-2 py-1.5 text-left hover:bg-gray-100 flex items-center gap-2 border-b last:border-b-0"
                                                :class="{ 'bg-blue-50': selectedUser && selectedUser.id === user.id }">
                                                <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center text-xs font-semibold text-gray-600">
                                                    {{ user.name.charAt(0).toUpperCase() }}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="font-medium text-gray-900 truncate text-sm">{{ user.name }}</div>
                                                    <div class="text-xs text-gray-500 truncate">{{ user.email }}</div>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <InputError :message="form.errors.lead_engineer_id" class="mt-1" />
                            </div>

                            <!-- Entry Date -->
                            <div class="mb-1">
                                <InputLabel for="entry_date" class="text-xs">
                                    <span>Tanggal Masuk <span class="text-red-500">*</span></span>
                                </InputLabel>
                                <TextInput id="entry_date" type="date" v-model="form.entry_date"
                                    class="mt-1 block w-full text-sm" required />
                                <InputError :message="form.errors.entry_date" class="mt-1" />
                            </div>

                            <!-- ERF Approved Date -->
                            <div class="mb-1">
                                <InputLabel for="erf_approved_date" value="Tgl Persetujuan ERF" class="text-xs" />
                                <TextInput id="erf_approved_date" type="date" v-model="form.erf_approved_date"
                                    class="mt-1 block w-full text-sm" />
                                <InputError :message="form.errors.erf_approved_date" class="mt-1" />
                            </div>

                            <!-- Clarification Date -->
                            <div class="mb-1">
                                <InputLabel for="clarification_date" value="Tgl Klarifikasi" class="text-xs" />
                                <TextInput id="clarification_date" type="date" v-model="form.clarification_date"
                                    class="mt-1 block w-full text-sm" />
                                <InputError :message="form.errors.clarification_date" class="mt-1" />
                            </div>

                            <!-- ERF Validated Date -->
                            <div class="mb-1">
                                <InputLabel for="erf_validated_date" value="Tgl ERF Disahkan" class="text-xs" />
                                <TextInput id="erf_validated_date" type="date" v-model="form.erf_validated_date"
                                    class="mt-1 block w-full text-sm" />
                                <InputError :message="form.errors.erf_validated_date" class="mt-1" />
                            </div>

                            <!-- Initiating Target Date -->
                            <div class="mb-1">
                                <InputLabel for="initiating_target_date" value="Target Inisiasi Selesai" class="text-xs" />
                                <TextInput id="initiating_target_date" type="date" v-model="form.initiating_target_date"
                                    class="mt-1 block w-full text-sm" />
                                <InputError :message="form.errors.initiating_target_date" class="mt-1" />
                            </div>

                            <!-- Verification Status -->
                            <div class="mb-1">
                                <InputLabel for="verification_status" value="Status Verifikasi (opsional)" class="text-xs" />
                                <div class="relative mt-1">
                                    <select id="verification_status" v-model="form.verification_status"
                                        class="block w-full px-2.5 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm appearance-none pr-8">
                                        <option disabled value="">Pilih Status</option>
                                        <option v-for="ver_status in verificationStatuses" :key="ver_status" :value="ver_status">
                                            {{ ver_status }}
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-2.5 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400 text-xs"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.verification_status" class="mt-1" />
                            </div>

                            <!-- Project Status -->
                            <div class="mb-1">
                                <InputLabel for="project_status" value="Status Proyek (opsional)" class="text-xs" />
                                <div class="relative mt-1">
                                    <select id="project_status" v-model="form.project_status"
                                        class="block w-full px-2.5 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-300 text-sm appearance-none pr-8">
                                        <option disabled value="">Pilih Status</option>
                                        <option v-for="status_project in projectStatuses" :key="status_project" :value="status_project">
                                            {{ status_project }}
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-2.5 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400 text-xs"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.project_status" class="mt-1" />
                            </div>

                            <!-- Notes / Catatan -->
                            <div class="mb-1" ref="notesDropdownRef">
                                <InputLabel for="notes_search" value="Catatan (opsional)" class="text-xs" />
                                <div class="relative mt-1">
                                    <TextInput id="notes_search" type="text" v-model="notesSearchQuery"
                                        @focus="handleNotesInputFocus" @input="isNotesDropdownOpen = true"
                                        placeholder="Cari atau pilih catatan..." class="w-full text-sm pr-12" autocomplete="off" />
                                    
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                                        <button v-if="selectedNote" type="button" @click="clearNotesSelection" class="p-1 text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                        <i class="fas fa-search text-gray-400 ml-1" v-if="!selectedNote"></i>
                                    </div>

                                    <div v-if="isNotesDropdownOpen" class="absolute z-50 w-full bottom-full mb-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-48 overflow-auto">
                                        <div v-if="filteredNotes.length === 0" class="p-2 text-center text-xs text-gray-500">
                                            {{ notesSearchQuery ? 'Tidak ada catatan' : 'Ketik untuk mencari' }}
                                        </div>
                                        <div v-else>
                                            <button v-for="note in filteredNotes" :key="note.id" type="button" @click="selectNoteFunc(note)"
                                                class="w-full px-2 py-1.5 text-left hover:bg-gray-100 border-b last:border-b-0"
                                                :class="{ 'bg-blue-50': selectedNote && selectedNote.id === note.id }">
                                                <div class="whitespace-normal leading-relaxed text-xs text-gray-800 break-words">
                                                    {{ note.content }}
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <InputError :message="form.errors.note_id" class="mt-1" />
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6 flex justify-end">
                            <button type="submit" :disabled="form.processing"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-sm font-semibold rounded-lg hover:from-blue-700 hover:to-purple-700 shadow-md transition disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas fa-save"></i>
                                <span>Simpan Pekerjaan</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
