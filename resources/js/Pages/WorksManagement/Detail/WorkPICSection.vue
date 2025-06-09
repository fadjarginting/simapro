<template>
    <div class="bg-white shadow-sm border border-gray-200 sm:rounded-lg">
        <div class="px-4 py-4 sm:px-6 sm:py-5">
            <div class="flex justify-between items-center mb-3 lg:mb-4">
                <h3 class="text-base font-semibold leading-6 text-gray-900">
                    Person In Charge (PIC)
                </h3>
                <button 
                    @click="showAssignModal = true"
                    class="px-3 py-1 text-xs bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                >
                    Assign PIC
                </button>
            </div>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Project Manager -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                    <div class="mb-2">
                        <p class="text-xs font-medium text-blue-600 uppercase tracking-wide">Project Manager</p>
                    </div>
                    <div v-if="picData.projectManager && picData.projectManager.length > 0" class="space-y-2">
                        <div v-for="pic in picData.projectManager" :key="pic.id" class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white text-xs"></i>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ pic.name }}</p>
                                <p class="text-xs text-gray-600">{{ pic.email }}</p>
                            </div>
                            <button 
                                @click="removePIC('projectManager', pic.id)"
                                class="text-red-500 hover:text-red-700 text-xs"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <button 
                            @click="openAssignDialog('projectManager')"
                            class="text-blue-600 text-xs hover:text-blue-800 flex items-center mt-2"
                        >
                            <i class="fas fa-plus mr-1"></i>
                            Add More
                        </button>
                    </div>
                    <div v-else class="flex items-center justify-center h-16">
                        <button 
                            @click="openAssignDialog('projectManager')"
                            class="text-blue-600 text-sm hover:text-blue-800 flex items-center"
                        >
                            <i class="fas fa-plus mr-1"></i>
                            Assign Project Manager
                        </button>
                    </div>
                </div>

                <!-- Engineering Lead -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                    <div class="mb-2">
                        <p class="text-xs font-medium text-green-600 uppercase tracking-wide">Engineering Lead</p>
                    </div>
                    <div v-if="picData.engineeringLead && picData.engineeringLead.length > 0" class="space-y-2">
                        <div v-for="pic in picData.engineeringLead" :key="pic.id" class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-600 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ pic.name }}</p>
                                <p class="text-xs text-gray-600">{{ pic.email }}</p>
                            </div>
                            <button 
                                @click="removePIC('engineeringLead', pic.id)"
                                class="text-red-500 hover:text-red-700 text-xs"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <button 
                            @click="openAssignDialog('engineeringLead')"
                            class="text-green-600 text-xs hover:text-green-800 flex items-center mt-2"
                        >
                            <i class="fas fa-plus mr-1"></i>
                            Add More
                        </button>
                    </div>
                    <div v-else class="flex items-center justify-center h-16">
                        <button 
                            @click="openAssignDialog('engineeringLead')"
                            class="text-green-600 text-sm hover:text-green-800 flex items-center"
                        >
                            <i class="fas fa-plus mr-1"></i>
                            Assign Engineering Lead
                        </button>
                    </div>
                </div>

                <!-- Site Supervisor -->
                <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-4 border border-yellow-200">
                    <div class="mb-2">
                        <p class="text-xs font-medium text-yellow-600 uppercase tracking-wide">Site Supervisor</p>
                    </div>
                    <div v-if="picData.siteSupervisor && picData.siteSupervisor.length > 0" class="space-y-2">
                        <div v-for="pic in picData.siteSupervisor" :key="pic.id" class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ pic.name }}</p>
                                <p class="text-xs text-gray-600">{{ pic.email }}</p>
                            </div>
                            <button 
                                @click="removePIC('siteSupervisor', pic.id)"
                                class="text-red-500 hover:text-red-700 text-xs"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <button 
                            @click="openAssignDialog('siteSupervisor')"
                            class="text-yellow-600 text-xs hover:text-yellow-800 flex items-center mt-2"
                        >
                            <i class="fas fa-plus mr-1"></i>
                            Add More
                        </button>
                    </div>
                    <div v-else class="flex items-center justify-center h-16">
                        <button 
                            @click="openAssignDialog('siteSupervisor')"
                            class="text-yellow-600 text-sm hover:text-yellow-800 flex items-center"
                        >
                            <i class="fas fa-plus mr-1"></i>
                            Assign Site Supervisor
                        </button>
                    </div>
                </div>

                <!-- QC Inspector -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                    <div class="mb-2">
                        <p class="text-xs font-medium text-purple-600 uppercase tracking-wide">QC Inspector</p>
                    </div>
                    <div v-if="picData.qcInspector && picData.qcInspector.length > 0" class="space-y-2">
                        <div v-for="pic in picData.qcInspector" :key="pic.id" class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ pic.name }}</p>
                                <p class="text-xs text-gray-600">{{ pic.email }}</p>
                            </div>
                            <button 
                                @click="removePIC('qcInspector', pic.id)"
                                class="text-red-500 hover:text-red-700 text-xs"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <button 
                            @click="openAssignDialog('qcInspector')"
                            class="text-purple-600 text-xs hover:text-purple-800 flex items-center mt-2"
                        >
                            <i class="fas fa-plus mr-1"></i>
                            Add More
                        </button>
                    </div>
                    <div v-else class="flex items-center justify-center h-16">
                        <button 
                            @click="openAssignDialog('qcInspector')"
                            class="text-purple-600 text-sm hover:text-purple-800 flex items-center"
                        >
                            <i class="fas fa-plus mr-1"></i>
                            Assign QC Inspector
                        </button>
                    </div>
                </div>

                <!-- HSE Officer -->
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-4 border border-red-200">
                    <div class="mb-2">
                        <p class="text-xs font-medium text-red-600 uppercase tracking-wide">HSE Officer</p>
                    </div>
                    <div v-if="picData.hseOfficer && picData.hseOfficer.length > 0" class="space-y-2">
                        <div v-for="pic in picData.hseOfficer" :key="pic.id" class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ pic.name }}</p>
                                <p class="text-xs text-gray-600">{{ pic.email }}</p>
                            </div>
                            <button 
                                @click="removePIC('hseOfficer', pic.id)"
                                class="text-red-500 hover:text-red-700 text-xs"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <button 
                            @click="openAssignDialog('hseOfficer')"
                            class="text-red-600 text-xs hover:text-red-800 flex items-center mt-2"
                        >
                            <i class="fas fa-plus mr-1"></i>
                            Add More
                        </button>
                    </div>
                    <div v-else class="flex items-center justify-center h-16">
                        <button 
                            @click="openAssignDialog('hseOfficer')"
                            class="text-red-600 text-sm hover:text-red-800 flex items-center"
                        >
                            <i class="fas fa-plus mr-1"></i>
                            Assign HSE Officer
                        </button>
                    </div>
                </div>

                <!-- Procurement Lead -->
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-lg p-4 border border-indigo-200">
                    <div class="mb-2">
                        <p class="text-xs font-medium text-indigo-600 uppercase tracking-wide">Procurement Lead</p>
                    </div>
                    <div v-if="picData.procurementLead && picData.procurementLead.length > 0" class="space-y-2">
                        <div v-for="pic in picData.procurementLead" :key="pic.id" class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ pic.name }}</p>
                                <p class="text-xs text-gray-600">{{ pic.email }}</p>
                            </div>
                            <button 
                                @click="removePIC('procurementLead', pic.id)"
                                class="text-red-500 hover:text-red-700 text-xs"
                            >
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <button 
                            @click="openAssignDialog('procurementLead')"
                            class="text-indigo-600 text-xs hover:text-indigo-800 flex items-center mt-2"
                        >
                            <i class="fas fa-plus mr-1"></i>
                            Add More
                        </button>
                    </div>
                    <div v-else class="flex items-center justify-center h-16">
                        <button 
                            @click="openAssignDialog('procurementLead')"
                            class="text-indigo-600 text-sm hover:text-indigo-800 flex items-center"
                        >
                            <i class="fas fa-plus mr-1"></i>
                            Assign Procurement Lead
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assign PIC Modal -->
        <div v-if="showAssignModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        Assign {{ currentRole.replace(/([A-Z])/g, ' $1').trim() }}
                    </h3>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select User</label>
                        <select v-model="selectedUserId" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="">Select a user...</option>
                            <option 
                                v-for="user in availableUsersForRole" 
                                :key="user.id" 
                                :value="user.id"
                            >
                                {{ user.name }} - {{ user.email }}
                            </option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button 
                            @click="closeAssignModal"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400"
                        >
                            Cancel
                        </button>
                        <button 
                            @click="assignPIC"
                            :disabled="!selectedUserId"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50"
                        >
                            Assign
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    work: Object,
    availableUsers: {
        type: Array,
        default: () => []
    }
});

const showAssignModal = ref(false)
const currentRole = ref('')
const selectedUserId = ref('')

const picData = reactive({
    projectManager: Array.isArray(props.work?.project_manager) ? props.work.project_manager : (props.work?.project_manager ? [props.work.project_manager] : []),
    engineeringLead: Array.isArray(props.work?.engineering_lead) ? props.work.engineering_lead : (props.work?.engineering_lead ? [props.work.engineering_lead] : []),
    siteSupervisor: Array.isArray(props.work?.site_supervisor) ? props.work.site_supervisor : (props.work?.site_supervisor ? [props.work.site_supervisor] : []),
    qcInspector: Array.isArray(props.work?.qc_inspector) ? props.work.qc_inspector : (props.work?.qc_inspector ? [props.work.qc_inspector] : []),
    hseOfficer: Array.isArray(props.work?.hse_officer) ? props.work.hse_officer : (props.work?.hse_officer ? [props.work.hse_officer] : []),
    procurementLead: Array.isArray(props.work?.procurement_lead) ? props.work.procurement_lead : (props.work?.procurement_lead ? [props.work.procurement_lead] : []),
})

const availableUsersForRole = computed(() => {
    if (!currentRole.value) return props.availableUsers;
    
    const assignedIds = picData[currentRole.value].map(pic => pic.id);
    return props.availableUsers.filter(user => !assignedIds.includes(user.id));
})

const openAssignDialog = (role) => {
    currentRole.value = role
    showAssignModal.value = true
}

const closeAssignModal = () => {
    showAssignModal.value = false
    currentRole.value = ''
    selectedUserId.value = ''
}

const assignPIC = () => {
    if (!selectedUserId.value) return

    const selectedUser = props.availableUsers.find(user => user.id == selectedUserId.value)
    
    router.post(route('works.assign-pic', props.work.id), {
        role: currentRole.value,
        user_id: selectedUserId.value
    }, {
        onSuccess: () => {
            picData[currentRole.value].push(selectedUser)
            closeAssignModal()
        }
    })
}

const removePIC = (role, userId) => {
    router.delete(route('works.remove-pic', props.work.id), {
        data: {
            role: role,
            user_id: userId
        },
        onSuccess: () => {
            const index = picData[role].findIndex(pic => pic.id === userId)
            if (index > -1) {
                picData[role].splice(index, 1)
            }
        }
    })
}
</script>