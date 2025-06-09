<script setup>
import { ref, watch, onMounted, onBeforeUnmount, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
    work: Object,
    notes: Array,
})

// Fungsi untuk menentukan class berdasarkan prioritas
const getPriorityClass = (priority) => {
    switch (priority) {
        case 'HIGH':
            return 'bg-red-100 text-red-800 ring-red-600/20'
        case 'MEDIUM':
            return 'bg-yellow-100 text-yellow-800 ring-yellow-600/20'
        default:
            return 'bg-gray-100 text-gray-800 ring-gray-600/20'
    }
}

// Modal dan search state
const showLeadModal = ref(false)
const searchQuery = ref('')
const users = ref([])
const isLoading = ref(false)
const isSubmitting = ref(false)
const error = ref('')
const selectedUser = ref(null)
const isDropdownOpen = ref(true)
const userSelectRef = ref(null)

// Edit state
const isEditing = ref(false)
const editForm = ref({
    note_id: '',
    lead_engineer_id: null
})

// Form data
const form = ref({
    lead_engineer_id: null
})

// Get selected note content for display
const getSelectedNoteContent = () => {
    if (!editForm.value.note_id) return 'Tidak ada catatan'

    const selectedNote = notesOptions.value.find(note => note.id == editForm.value.note_id)
    return selectedNote ? selectedNote.content : 'Tidak ada catatan'
}

// Toggle edit mode
const toggleEditMode = () => {
    if (isEditing.value) {
        // Cancel edit - reset form to original values
        isEditing.value = false
        editForm.value.note_id = props.work.note?.id || ''
        editForm.value.lead_engineer_id = props.work.lead_engineer?.id || null
        selectedUser.value = props.work.lead_engineer || null
        error.value = ''
    } else {
        // Start edit - initialize form with current values
        isEditing.value = true
        editForm.value.note_id = props.work.note?.id || ''
        editForm.value.lead_engineer_id = props.work.lead_engineer?.id || null
        selectedUser.value = props.work.lead_engineer || null
        error.value = ''
    }
}

// Save edit changes
const saveChanges = () => {
    isSubmitting.value = true
    error.value = ''

    const data = {
        note_id: editForm.value.note_id || null,
        lead_engineer_id: editForm.value.lead_engineer_id || null
    }

    router.put(route('works.update-basic-info', props.work.slug), data, {
        preserveState: true,
        onSuccess: () => {
            isEditing.value = false
            error.value = ''
        },
        onError: (errors) => {
            console.error('Update error:', errors)
            error.value = errors.note_id || errors.lead_engineer_id || errors.error || 'Gagal menyimpan perubahan'
        },
        onFinish: () => {
            isSubmitting.value = false
        }
    })
}

const openLeadModal = () => {
    showLeadModal.value = true
    searchQuery.value = ''
    users.value = []
    selectedUser.value = isEditing.value ? selectedUser.value : null
    error.value = ''
    isDropdownOpen.value = true
    form.value.lead_engineer_id = null
    // Load initial users
    searchUsers()
}

const closeLeadModal = () => {
    showLeadModal.value = false
    searchQuery.value = ''
    users.value = []
    error.value = ''
    form.value.lead_engineer_id = null
    if (!isEditing.value) {
        selectedUser.value = null
    }
}

// Search users function
const searchUsers = async (query = '') => {
    if (query.length < 2 && query.length > 0) {
        return; // Don't search for single characters
    }

    isLoading.value = true
    error.value = ''

    try {
        const response = await axios.get('/api/users/search', {
            params: {
                search: query,
                limit: 10 // Limit results for better performance
            }
        })

        users.value = response.data.data || response.data.users || response.data || []
        isLoading.value = false
    } catch (e) {
        error.value = 'Gagal mencari user'
        users.value = []
        isLoading.value = false
        console.error('Search error:', e)
    }
}

// Watch search query changes dengan debounce
let searchTimeout = null
watch(searchQuery, (newQuery) => {
    if (searchTimeout) {
        clearTimeout(searchTimeout)
    }
    searchTimeout = setTimeout(() => {
        searchUsers(newQuery)
    }, 300)
})

// Select user function
const selectUser = (user) => {
    selectedUser.value = user
    if (isEditing.value) {
        editForm.value.lead_engineer_id = user.id
    } else {
        form.value.lead_engineer_id = user.id
    }
}

// Clear selection
const clearSelection = () => {
    selectedUser.value = isEditing.value ? null : null
    searchQuery.value = ''
    if (isEditing.value) {
        editForm.value.lead_engineer_id = null
    } else {
        form.value.lead_engineer_id = null
    }
}

// Handle input focus
const handleInputFocus = () => {
    isDropdownOpen.value = true
    if (users.value.length === 0) {
        searchUsers() // Load initial users
    }
}

// Handle click outside to close dropdown
const handleClickOutside = (event) => {
    if (userSelectRef.value && !userSelectRef.value.contains(event.target)) {
        // Di modal, tidak perlu menutup dropdown
        // isDropdownOpen.value = false
    }
}

// Filtered users based on search
const filteredUsers = computed(() => {
    if (!searchQuery.value) return users.value
    return users.value.filter(user =>
        user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        user.email.toLowerCase().includes(searchQuery.value.toLowerCase())
    )
})

// Computed property untuk memastikan notes selalu array
const notesOptions = computed(() => {
    return props.notes && Array.isArray(props.notes) ? props.notes : []
})

// Fungsi untuk submit form assign lead engineer (untuk mode non-edit)
const submitAssignLead = () => {
    if (!form.value.lead_engineer_id) {
        error.value = 'Silakan pilih lead engineer terlebih dahulu'
        return
    }

    isSubmitting.value = true
    error.value = ''

    router.post(route('works.assign-lead', props.work.slug), {
        lead_engineer_id: form.value.lead_engineer_id
    }, {
        preserveState: true,
        onSuccess: () => {
            closeLeadModal()
        },
        onError: (errors) => {
            console.error('Assign lead error:', errors)
            error.value = errors.lead_engineer_id || errors.error || 'Gagal menetapkan lead engineer'
        },
        onFinish: () => {
            isSubmitting.value = false
        }
    })
}

// Fungsi untuk confirm pilihan lead engineer dalam mode edit
const confirmLeadSelection = () => {
    closeLeadModal()
}

// Lifecycle hooks
onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside)
    if (searchTimeout) {
        clearTimeout(searchTimeout)
    }
})
</script>

<template>
    <div class="bg-white shadow-sm border border-gray-200 sm:rounded-lg">
        <div class="px-4 py-4 sm:px-6 sm:py-5">
            <!-- Header dengan tombol edit -->
            <div class="flex items-center justify-between mb-3 lg:mb-4">
                <h3 class="text-base font-semibold leading-6 text-gray-900">
                    Informasi Dasar
                </h3>
                <div class="flex items-center space-x-2">
                    <button v-if="!isEditing" @click="toggleEditMode"
                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
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

            <!-- Error message untuk edit -->
            <div v-if="error && isEditing" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                <div class="flex">
                    <i class="fas fa-exclamation-triangle text-red-400 mr-2 mt-0.5"></i>
                    <span class="text-sm text-red-700">{{ error }}</span>
                </div>
            </div>

            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2 lg:gap-y-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Deskripsi Pekerjaan</dt>
                    <dd class="mt-1 text-sm text-gray-900 break-words">{{ work.description }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Lead Engineer</dt>
                    <dd class="mt-1 flex items-center gap-2">
                        <!-- Mode Edit -->
                        <div v-if="isEditing" class="flex items-center gap-2 flex-1">
                            <div v-if="selectedUser" class="flex items-center gap-2">
                                <div
                                    class="w-6 h-6 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-xs font-semibold text-white">
                                    {{ selectedUser.name.charAt(0).toUpperCase() }}
                                </div>
                                <span class="text-sm text-gray-900">{{ selectedUser.name }}</span>
                                <button @click="openLeadModal"
                                    class="text-blue-600 hover:text-blue-800 transition-colors"
                                    title="Ganti Lead Engineer">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                            </div>
                            <div v-else class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Belum ditentukan
                                </span>
                                <button @click="openLeadModal"
                                    class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-colors"
                                    title="Pilih Lead Engineer">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Mode View -->
                        <div v-else class="flex items-center gap-2">
                            <span v-if="!work.lead_engineer?.name || work.lead_engineer.name === ''"
                                class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Belum ditentukan
                            </span>
                            <span v-else class="text-sm text-gray-900">{{ work.lead_engineer.name }}</span>
                        </div>
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Unit Peminta</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ work.requester_unit }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Prioritas</dt>
                    <dd class="mt-1">
                        <span :class="getPriorityClass(work.work_priority)"
                            class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset">
                            {{ work.work_priority }}
                        </span>
                    </dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Jenis Pekerjaan</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ work.work_type }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Kategori Permintaan</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ work.request_category }}</dd>
                </div>

                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Catatan</dt>
                    <dd class="mt-1">
                        <!-- Mode Edit with select dropdown -->
                        <div v-if="isEditing" class="flex flex-col">
                            <select v-model="editForm.note_id"
                                class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                <option value="">Pilih catatan</option>
                                <option v-for="note in notesOptions" :key="note.id" :value="note.id">
                                    {{ note.content }}
                                </option>
                            </select>
                            <!-- Preview selected note -->
                            <div v-if="editForm.note_id"
                                class="mt-2 p-2 bg-gray-50 border border-gray-200 rounded text-sm text-gray-700">
                                <strong>Preview:</strong> {{ getSelectedNoteContent() }}
                            </div>
                        </div>
                        <!-- Mode View -->
                        <div v-else class="text-sm text-gray-900 break-words">
                            <span v-if="work.note">
                                {{ work.note.content }}
                            </span>
                            <span v-else class="text-gray-500 italic">
                                Tidak ada catatan
                            </span>
                        </div>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Modal Pilih Lead Engineer -->
    <div v-if="showLeadModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 max-h-[90vh] flex flex-col">
            <!-- Header Modal -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h4 class="text-lg font-semibold text-gray-900">
                    {{ isEditing ? 'Ubah Lead Engineer' : 'Pilih Lead Engineer' }}
                </h4>
                <button class="text-gray-400 hover:text-gray-600 transition-colors" @click="closeLeadModal"
                    :disabled="isSubmitting">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>

            <!-- Body Modal -->
            <div class="flex-1 overflow-hidden p-6">
                <!-- Search Input -->
                <div class="mb-4" ref="userSelectRef">
                    <div class="relative">
                        <input type="text" v-model="searchQuery" @focus="handleInputFocus"
                            placeholder="Cari nama atau email pengguna..."
                            class="w-full p-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-10"
                            autocomplete="off" :disabled="isSubmitting" />

                        <!-- Clear button -->
                        <button v-if="searchQuery" type="button" @click="clearSelection"
                            class="absolute inset-y-0 right-8 flex items-center text-gray-400 hover:text-gray-600"
                            :disabled="isSubmitting">
                            <i class="fas fa-times"></i>
                        </button>

                        <!-- Search/Loading icon -->
                        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400" v-if="!isLoading"></i>
                            <i class="fas fa-spinner fa-spin text-gray-400" v-else></i>
                        </div>
                    </div>
                </div>

                <!-- Selected user display -->
                <div v-if="selectedUser" class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-sm font-semibold text-white">
                            {{ selectedUser.name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-blue-900 truncate">{{ selectedUser.name }}</div>
                            <div class="text-sm text-blue-700 truncate" v-if="selectedUser.email">{{ selectedUser.email
                            }}</div>
                        </div>
                        <button type="button" @click="clearSelection"
                            class="text-blue-600 hover:text-blue-800 transition-colors" :disabled="isSubmitting">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Error Message -->
                <div v-if="error" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-red-400 mr-2 mt-0.5"></i>
                        <span class="text-sm text-red-700">{{ error }}</span>
                    </div>
                </div>

                <!-- Results Container (hanya tampil jika belum ada yang dipilih) -->
                <div v-if="!selectedUser" class="border border-gray-200 rounded-md max-h-80 overflow-y-auto">
                    <!-- Loading state -->
                    <div v-if="isLoading" class="p-4 text-center text-gray-500">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        <span>Mencari pengguna...</span>
                    </div>

                    <!-- No results -->
                    <div v-else-if="filteredUsers.length === 0" class="p-4 text-center text-gray-500">
                        <i class="fas fa-search mr-2"></i>
                        <span>{{ searchQuery ? 'Tidak ada pengguna yang ditemukan' : 'Ketik untuk mencari pengguna'
                        }}</span>
                    </div>

                    <!-- User list -->
                    <div v-else class="divide-y divide-gray-100">
                        <button v-for="user in filteredUsers" :key="user.id" type="button" @click="selectUser(user)"
                            class="w-full px-4 py-3 text-left hover:bg-gray-50 focus:bg-gray-50 focus:outline-none transition-colors flex items-center space-x-3"
                            :disabled="isSubmitting">
                            <!-- User avatar -->
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-sm font-semibold text-white shadow-sm">
                                {{ user.name.charAt(0).toUpperCase() }}
                            </div>

                            <!-- User info -->
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900 truncate">{{ user.name }}</div>
                                <div class="text-sm text-gray-500 truncate" v-if="user.email">{{ user.email }}</div>
                            </div>

                            <!-- Select indicator -->
                            <div class="text-blue-600">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Help text -->
                <div v-if="!selectedUser" class="mt-4 text-xs text-gray-500 text-center">
                    Ketik minimal 2 karakter untuk mencari pengguna
                </div>
            </div>

            <!-- Footer Modal dengan tombol submit -->
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" @click="closeLeadModal"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                    :disabled="isSubmitting">
                    Batal
                </button>
                <button type="button" @click="isEditing ? confirmLeadSelection() : submitAssignLead()"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="!selectedUser || isSubmitting">
                    <span v-if="isSubmitting && !isEditing">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Menetapkan...
                    </span>
                    <span v-else-if="isEditing">
                        <i class="fas fa-check mr-2"></i>
                        Pilih
                    </span>
                    <span v-else>
                        <i class="fas fa-check mr-2"></i>
                        Tetapkan Lead Engineer
                    </span>
                </button>
            </div>
        </div>
    </div>
</template>