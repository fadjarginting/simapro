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
    work: Object, // Data pekerjaan yang akan diedit
    plants: Array,
    workPriorities: Array,
    workTypes: Array,
    requestCategories: Array,
});

// Form structure - hanya data esensial yang bisa diedit
const form = useForm({
    erf_number: props.work?.erf_number || "",
    description: props.work?.description || "",
    plant_id: props.work?.plant_id || "",
    requester_unit: props.work?.requester_unit || "",
    work_priority: props.work?.work_priority || "",
    request_category: props.work?.request_category || "",
    work_type: props.work?.work_type || "",
});

// User search functionality for lead engineer
const users = ref([]);
const searchQuery = ref(props.work?.lead_engineer?.name || '');
const isDropdownOpen = ref(false);
const isLoading = ref(false);
const selectedUser = ref(props.work?.lead_engineer || null);
const userSelectRef = ref(null);

// Search users function
const searchUsers = async (query = '') => {
    if (query.length < 2 && query.length > 0) {
        users.value = [];
        return;
    }

    isLoading.value = true;
    try {
        const response = await axios.get('/api/users/search', {
            params: {
                search: query,
                limit: 10
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
    if (!selectedUser.value || newQuery !== selectedUser.value.name) {
        searchUsers(newQuery);
    }
}, { debounce: 300 });


// Select user function
const selectUser = (user) => {
    selectedUser.value = user;
    searchQuery.value = user.name;
    isDropdownOpen.value = false;
};

// Clear selection
const clearSelection = () => {
    selectedUser.value = null;
    searchQuery.value = '';
    isDropdownOpen.value = false;
    searchUsers('');
};

// Handle input focus
const handleInputFocus = () => {
    isDropdownOpen.value = true;
    if (users.value.length === 0 && !searchQuery.value) {
        searchUsers();
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
    if (!props.work?.lead_engineer) {
        searchUsers();
    }
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});

// Filtered users based on search
const filteredUsers = computed(() => {
    if (!searchQuery.value || (selectedUser.value && searchQuery.value === selectedUser.value.name)) {
        return users.value;
    }
    return users.value.filter(user =>
        user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        user.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Submit form
const submit = () => {
    const formData = {
        ...form.data(),
        lead_engineer_id: selectedUser.value ? selectedUser.value.id : null,
    };

    router.put(route("works.update", props.work.slug), formData, {
        onSuccess: () => {
            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: "Data pekerjaan berhasil diperbarui.",
                showConfirmButton: false,
                timer: 1500,
            });
        },
        onError: (errors) => {
            console.error(errors);
            Swal.fire({
                icon: "error",
                title: "Gagal",
                text: "Terdapat kesalahan pada input Anda.",
            });
        },
    });
};
</script>

<template>

    <Head>
        <title>Edit Pekerjaan</title>
    </Head>

    <div class="py-2">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div
                class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border rounded-2xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                                <i class="fas fa-edit text-white text-lg"></i>
                            </div>
                            <div>
                                <h1 class="text-lg font-bold text-gray-900 tracking-tight">
                                    Edit Pekerjaan
                                </h1>
                                <p class="mt-1 text-xs text-gray-600">
                                    Form ini digunakan untuk mengubah data esensial pekerjaan.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-end">
                            <Link :href="route('works.index')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-200 text-gray-800 text-xs font-semibold rounded-md hover:bg-gray-300 shadow-sm transition">
                            <i class="fas fa-arrow-left text-xs"></i>
                            <span>Kembali</span>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Form-->
                <div class="p-4">
                    <form @submit.prevent="submit">
                        <!-- Legenda Keterangan -->
                        <div class="mb-3">
                            <div class="inline-flex items-center space-x-2">
                                <span class="text-red-600 text-sm">*</span>
                                <span class="text-xs text-gray-600">Wajib diisi</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">

                            <!-- ERF Number -->
                            <div class="mb-2">
                                <InputLabel for="erf_number" value="No ERF" class="text-xs" />
                                <TextInput id="erf_number" v-model="form.erf_number" placeholder="No ERF"
                                    class="mt-1 block w-full text-sm" />
                                <InputError :message="form.errors.erf_number" class="mt-1" />
                            </div>

                            <!-- Description -->
                            <div class="mb-2">
                                <InputLabel for="description" class="text-xs">
                                    <span>Deskripsi Pekerjaan <span class="text-red-600">*</span></span>
                                </InputLabel>
                                <TextInput id="description" v-model="form.description" placeholder="Deskripsi Pekerjaan"
                                    class="mt-1 block w-full text-sm" required />
                                <InputError :message="form.errors.description" class="mt-1" />
                            </div>

                            <!-- Plant -->
                            <div class="mb-2">
                                <InputLabel for="plant_id" class="text-xs">
                                    <span>Plant <span class="text-red-600">*</span></span>
                                </InputLabel>
                                <div class="relative mt-1">
                                    <select id="plant_id" v-model="form.plant_id" required
                                        class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm appearance-none pr-8">
                                        <option disabled value="">Pilih Plant</option>
                                        <option v-for="plant in plants" :key="plant.id" :value="plant.id">
                                            {{ plant.name }}
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-2 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400 text-xs"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.plant_id" class="mt-1" />
                            </div>

                            <!-- Requester Unit -->
                            <div class="mb-2">
                                <InputLabel for="requester_unit" class="text-xs">
                                    <span>Unit Kerja Peminta <span class="text-red-600">*</span></span>
                                </InputLabel>
                                <TextInput id="requester_unit" v-model="form.requester_unit"
                                    placeholder="Unit Kerja Peminta" class="mt-1 block w-full text-sm" required />
                                <InputError :message="form.errors.requester_unit" class="mt-1" />
                            </div>

                            <!-- Work Priority -->
                            <div class="mb-2">
                                <InputLabel for="work_priority" class="text-xs">
                                    <span>Prioritas Pekerjaan <span class="text-red-600">*</span></span>
                                </InputLabel>
                                <div class="relative mt-1">
                                    <select id="work_priority" v-model="form.work_priority" required
                                        class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm appearance-none pr-8">
                                        <option disabled value="">Pilih Prioritas</option>
                                        <option v-for="priority in workPriorities" :key="priority" :value="priority">
                                            {{ priority }}
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-2 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400 text-xs"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.work_priority" class="mt-1" />
                            </div>

                            <!-- Work Type -->
                            <div class="mb-2">
                                <InputLabel for="work_type" class="text-xs">
                                    <span>Jenis Pekerjaan <span class="text-red-600">*</span></span>
                                </InputLabel>
                                <div class="relative mt-1">
                                    <select id="work_type" v-model="form.work_type" required
                                        class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm appearance-none pr-8">
                                        <option disabled value="">Pilih Jenis</option>
                                        <option v-for="type in workTypes" :key="type" :value="type">
                                            {{ type }}
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-2 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400 text-xs"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.work_type" class="mt-1" />
                            </div>

                            <!-- Request Category -->
                            <div class="mb-2">
                                <InputLabel for="request_category" class="text-xs">
                                    <span>Kategori Permintaan <span class="text-red-600">*</span></span>
                                </InputLabel>
                                <div class="relative mt-1">
                                    <select id="request_category" v-model="form.request_category" required
                                        class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm appearance-none pr-8">
                                        <option disabled value="">Pilih Kategori</option>
                                        <option v-for="category in requestCategories" :key="category" :value="category">
                                            {{ category }}
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-2 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400 text-xs"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.request_category" class="mt-1" />
                            </div>

                            <!-- Lead Engineer - Searchable Select -->
                            <div class="mb-2 relative" ref="userSelectRef">
                                <InputLabel for="lead_search" value="Lead Engineer (opsional)" class="text-xs" />
                                <div class="relative mt-1">
                                    <TextInput id="lead_search" type="text" v-model="searchQuery"
                                        @focus="handleInputFocus" @input="isDropdownOpen = true"
                                        placeholder="Cari lead engineer..." class="w-full text-sm pr-10"
                                        autocomplete="off" />

                                    <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                                        <button v-if="selectedUser" type="button" @click="clearSelection"
                                            class="text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-times text-xs"></i>
                                        </button>
                                        <i v-if="!isLoading && !selectedUser"
                                            class="fas fa-search text-gray-400 ml-2"></i>
                                        <i v-if="isLoading" class="fas fa-spinner fa-spin text-gray-400 ml-2"></i>
                                    </div>

                                    <div v-if="isDropdownOpen"
                                        class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                                        <div v-if="isLoading" class="p-2 text-center text-xs text-gray-500">Mencari...
                                        </div>
                                        <div v-else-if="filteredUsers.length === 0"
                                            class="p-2 text-center text-xs text-gray-500">
                                            {{ searchQuery ? 'Tidak ditemukan' : 'Ketik untuk mencari' }}
                                        </div>
                                        <div v-else>
                                            <button v-for="user in filteredUsers" :key="user.id" type="button"
                                                @click="selectUser(user)"
                                                class="w-full px-3 py-2 text-left hover:bg-gray-100 flex items-center space-x-2 border-b border-gray-100 last:border-b-0"
                                                :class="{ 'bg-blue-50': selectedUser && selectedUser.id === user.id }">
                                                <div
                                                    class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center text-xs font-semibold text-gray-600">
                                                    {{ user.name.charAt(0).toUpperCase() }}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="font-medium text-gray-900 truncate text-sm">{{ user.name
                                                        }}</div>
                                                    <div class="text-xs text-gray-500 truncate">{{ user.email }}</div>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <InputError :message="form.errors.lead_engineer_id" class="mt-1" />
                            </div>
                        </div>

                        <!-- Submit & Action Buttons -->
                        <div class="mt-4 flex items-center gap-2">
                            <button type="submit" :disabled="form.processing"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white text-sm font-semibold rounded-md hover:from-blue-700 hover:to-purple-700 shadow transition disabled:opacity-50">
                                <i class="fas fa-save text-xs"></i>
                                Simpan
                            </button>

                            <Link :href="route('works.show', work.slug)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 text-sm font-semibold rounded-md hover:bg-gray-50 shadow-sm transition">
                            <i class="fas fa-eye text-xs"></i>
                            Lihat Detail
                            </Link>
                        </div>

                        <!-- Info Box -->
                        <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-amber-400 mt-0.5"></i>
                                </div>
                                <div class="ml-2">
                                    <h3 class="text-sm font-medium text-amber-800">
                                        Informasi
                                    </h3>
                                    <div class="mt-1 text-xs text-amber-700">
                                        <p>Untuk mengubah tanggal target, status, atau data dinamis lainnya, gunakan
                                            fitur khusus di halaman detail pekerjaan.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
