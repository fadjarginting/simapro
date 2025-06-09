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
    searchUsers(newQuery);
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
};

// Handle input focus
const handleInputFocus = () => {
    isDropdownOpen.value = true;
    if (users.value.length === 0) {
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
    searchUsers();
});

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
    // Tambahkan lead_engineer_id ke form data jika ada user yang dipilih
    const formData = {
        ...form.data(),
        lead_engineer_id: selectedUser.value ? selectedUser.value.id : null,
    };

    form.put(route("works.update", props.work.slug), {
        data: formData,
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
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div
                    class="flex items-center justify-between p-6 pb-0 mb-0 border-b-0 border-b-solid border-b-transparent">
                    <h1 class="text-lg font-semibold text-gray-800 leading-tight">
                        Edit Pekerjaan
                        <p class="mt-1 text-sm text-gray-600">
                            Form ini digunakan untuk mengubah data esensial pekerjaan. Hanya data dasar yang dapat
                            diubah pada halaman ini.
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
                        <!-- Legenda Keterangan -->
                        <div class="mb-4">
                            <div class="inline-flex items-center space-x-2">
                                <span class="text-red-600 text-lg">*</span>
                                <span class="text-sm text-gray-600">Wajib diisi</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                            <!-- ERF Number -->
                            <div class="mb-4">
                                <InputLabel for="erf_number">
                                    <span>No ERF</span>
                                </InputLabel>
                                <TextInput id="erf_number" v-model="form.erf_number" placeholder="No ERF"
                                    class="mt-1 block w-full" />
                                <InputError :message="form.errors.erf_number" />
                            </div>

                            <!-- Description -->
                            <div class="mb-4">
                                <InputLabel for="description">
                                    <span>Deskripsi Pekerjaan <span class="text-red-600">*</span></span>
                                </InputLabel>
                                <TextInput id="description" v-model="form.description" placeholder="Deskripsi Pekerjaan"
                                    class="mt-1 block w-full" required />
                                <InputError :message="form.errors.description" />
                            </div>

                            <!-- Plant -->
                            <div class="mb-4 relative">
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
                                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.plant_id" />
                            </div>

                            <!-- Requester Unit -->
                            <div class="mb-4">
                                <InputLabel for="requester_unit">
                                    <span>Unit Kerja Peminta <span class="text-red-600">*</span></span>
                                </InputLabel>
                                <TextInput id="requester_unit" v-model="form.requester_unit"
                                    placeholder="Unit Kerja Peminta" class="mt-1 block w-full" required />
                                <InputError :message="form.errors.requester_unit" />
                            </div>

                            <!-- Work Priority -->
                            <div class="mb-4 relative">
                                <InputLabel for="work_priority">
                                    <span>Prioritas Pekerjaan <span class="text-red-600">*</span></span>
                                </InputLabel>
                                <div class="relative">
                                    <select id="work_priority" v-model="form.work_priority" required
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10">
                                        <option disabled value="">
                                            Pilih Prioritas Pekerjaan
                                        </option>
                                        <option v-for="priority in workPriorities" :key="priority" :value="priority">
                                            {{ priority }}
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.work_priority" />
                            </div>

                            <!-- Work Type -->
                            <div class="mb-4 relative">
                                <InputLabel for="work_type">
                                    <span>Jenis Pekerjaan <span class="text-red-600">*</span></span>
                                </InputLabel>
                                <div class="relative">
                                    <select id="work_type" v-model="form.work_type" required
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10">
                                        <option disabled value="">
                                            Pilih Jenis Pekerjaan
                                        </option>
                                        <option v-for="type in workTypes" :key="type" :value="type">
                                            {{ type }}
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.work_type" />
                            </div>

                            <!-- Request Category -->
                            <div class="mb-4 relative">
                                <InputLabel for="request_category">
                                    <span>Kategori Permintaan <span class="text-red-600">*</span></span>
                                </InputLabel>
                                <div class="relative">
                                    <select id="request_category" v-model="form.request_category" required
                                        class="mt-1 block w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:shadow-primary-outline dark:bg-gray-950 dark:placeholder:text-white/80 dark:text-white/80 text-sm leading-5.6 appearance-none pr-10">
                                        <option disabled value="">
                                            Pilih Kategori Permintaan
                                        </option>
                                        <option v-for="category in requestCategories" :key="category" :value="category">
                                            {{ category }}
                                        </option>
                                    </select>
                                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                        <i class="fa fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                <InputError :message="form.errors.request_category" />
                            </div>

                            <!-- Lead Engineer - Searchable Select -->
                            <div class="mb-4 relative" ref="userSelectRef">
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

                                <span class="text-xs text-gray-500">Lead Engineer dapat diubah jika diperlukan</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6 flex items-center gap-4">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Perubahan
                            </button>

                            <Link :href="route('works.show', work.slug)"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring focus:ring-gray-200 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-eye mr-2"></i>
                            Lihat Detail
                            </Link>
                        </div>

                        <!-- Info Box -->
                        <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-amber-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-amber-800">
                                        Informasi
                                    </h3>
                                    <div class="mt-2 text-sm text-amber-700">
                                        <p>Halaman ini hanya untuk mengedit data esensial pekerjaan. Untuk mengubah
                                            tanggal target, status, atau data dinamis lainnya, gunakan fitur khusus di
                                            halaman detail pekerjaan.</p>
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