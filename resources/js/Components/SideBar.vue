<script setup>
import { defineProps, ref, computed, onMounted, onBeforeUnmount } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { usePermissions } from "@/composables/permissions";
import { router } from "@inertiajs/vue3";
import axios from "axios";

const { hasPermission, hasRole } = usePermissions();
const page = usePage();

// Compute document categories from shared resources
const documentCategories = computed(() => page.props.documentCategories || []);

const props = defineProps({
    isOpen: Boolean,
});

// Current URL computation
const currentUrl = computed(() => page.url);

function isActiveRoute(routeFragment) {
    return currentUrl.value ? currentUrl.value.includes(routeFragment) : false;
}

// State management for plants dropdown
const showPlants = ref(false);
const plants = ref([]);
const loadingPlants = ref(false);
const plantsExpanded = ref({});

// State management for dropdowns - PERBAIKAN: Tambahkan variabel yang hilang
const showSettings = ref(false);
const showUserManagement = ref(false);
const showLogAudit = ref(false);

// Function to toggle plants dropdown
function togglePlants() {
    showPlants.value = !showPlants.value;
    if (showPlants.value && plants.value.length === 0) {
        loadPlants();
    }
}

// Function to load plants data asynchronously using axios
async function loadPlants() {
    loadingPlants.value = true;
    try {
        const response = await axios.get(route('plants.with-departments'));
        plants.value = response.data.data;
    } catch (error) {
        console.error('Failed to load plants:', error);
    } finally {
        loadingPlants.value = false;
    }
}

// Function to toggle department visibility for a specific plant
function toggleDepartments(plantId) {
    plantsExpanded.value = {
        ...plantsExpanded.value,
        [plantId]: !plantsExpanded.value[plantId]
    };
}

// Scroll management code
const sideElement = ref(null);
const activeMenuItem = ref(null);

function scrollToMenuItem(event) {
    activeMenuItem.value = event.currentTarget;
    if (sideElement.value && activeMenuItem.value) {
        const containerTop = sideElement.value.getBoundingClientRect().top;
        const elementTop = activeMenuItem.value.getBoundingClientRect().top;
        const offset = 20;
        sideElement.value.scrollTop = sideElement.value.scrollTop + (elementTop - containerTop - offset);
    }
}

onMounted(() => {
    router.on('navigate', () => {
        if (sideElement.value) {
            setTimeout(() => {
                const activeItem = document.querySelector('.bg-blue-500\\/13');
                if (activeItem) {
                    const containerTop = sideElement.value.getBoundingClientRect().top;
                    const elementTop = activeItem.getBoundingClientRect().top;
                    const offset = 20;
                    sideElement.value.scrollTop = sideElement.value.scrollTop + (elementTop - containerTop - offset);
                } else {
                    sideElement.value.scrollTop = 0;
                }
            }, 100);
        }
    });
});
</script>

<template>
    <!-- Template with fixed width sidebar -->
    <aside :class="[
        isOpen ? 'translate-x-0' : '-translate-x-full xl:translate-x-0',
        'fixed inset-y-0 flex flex-wrap flex-col bg-white dark:bg-slate-850 border-0 shadow-xl dark:shadow-none w-64 ease-nav-brand my-4 z-990 xl:ml-6 rounded-2xl xl:left-0 xl:translate-x-0 transition-transform duration-200',
    ]">
        <!-- HEADER  -->
        <div
            class="h-19 px-8 py-6 dark:text-white text-slate-700 border-b border-gray-200 dark:border-slate-700 flex items-center justify-center">
            <a class="text-sm whitespace-nowrap flex items-center" href="#">
                <img src="/assets/img/semen-padang.png"
                    class="inline h-full max-w-full transition-all duration-200 dark:hidden ease-nav-brand max-h-8"
                    alt="main_logo" />

                <span class="ml-2 justify-center font-bold text-lg transition-all duration-200 ease-nav-brand">
                    SIMAPRO
                </span>
            </a>
            <!-- Close button -->
            <button
                class="absolute top-0 right-0 p-4 opacity-50 cursor-pointer fas fa-times dark:text-white text-slate-400 xl:hidden"
                @click="$emit('close')"></button>
        </div>

        <!-- CONTENT with fixed width -->
        <div ref="sideElement" class="py-2 relative flex-1 overflow-y-auto w-64">
            <ul class="flex flex-col pl-0 mb-0">
                <!-- Dashboard -->
                <li v-if="hasPermission('dashboard.view')" :class="[
                    'mt-0.5 w-full',
                    isActiveRoute('dashboard')
                        ? 'bg-blue-500/13 rounded-lg'
                        : '',
                ]" class="hover:bg-gray-100 dark:hover:bg-gray-700" @click="scrollToMenuItem">
                    <Link
                        class="flex items-center text-sm font-semibold dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                        :href="route('dashboard')">
                    <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-blue-500">
                        <i class="ni ni-tv-2 text-sm leading-normal"></i>
                    </div>
                    <span class="ml-1">Dashboard</span>
                    </Link>
                </li>
                <!-- Progress Report -->
                <li :class="[
                    'mt-0.5 w-full',
                    isActiveRoute('works')
                        ? 'bg-blue-500/13 rounded-lg'
                        : '',
                ]" class="hover:bg-gray-100 dark:hover:bg-gray-700" @click="scrollToMenuItem">
                    <Link
                        class="flex items-center text-sm font-semibold dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                        :href="route('works.index')">
                    <!-- Updated href to route('progress') -->
                    <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-yellow-500">
                        <i class="ni ni-folder-17 text-sm leading-normal"></i>
                    </div>
                    <span class="ml-1">Manajemen Pekerjaan</span>
                    </Link>
                </li>

                <!-- Morning Report -->
                <li v-if="hasPermission('morning_report.view')" :class="[
                    'mt-0.5 w-full',
                    isActiveRoute('morning')
                        ? 'bg-blue-500/13 rounded-lg'
                        : '',
                ]" class="hover:bg-gray-100 dark:hover:bg-gray-700" @click="scrollToMenuItem">
                    <Link
                        class="flex items-center text-sm font-semibold dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                        :href="route('morning.index')">
                    <!-- Updated href to route('morning') -->
                    <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-green-500">
                        <i class="ni ni-cloud-download-95 text-sm leading-normal"></i>
                    </div>
                    <span class="ml-1">Morning Report</span>
                    <!-- Updated span text to 'Morning Report' -->
                    </Link>
                </li>

                <!-- Key Performance Indicator -->
                <li v-if="hasPermission('kpi_management.view')" :class="[
                    'mt-0.5 w-full',
                    isActiveRoute('kpis')
                        ? 'bg-blue-500/13 rounded-lg'
                        : '',
                ]" class="hover:bg-gray-100 dark:hover:bg-gray-700" @click="scrollToMenuItem">
                    <Link
                        class="flex items-center text-sm font-semibold dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                        :href="route('kpis')">
                    <!-- Updated href to route('kpis') -->
                    <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-red-500">
                        <i class="ni ni-single-copy-04 text-sm leading-normal"></i>
                    </div>
                    <span class="ml-1">Key Performance Indicator</span>
                    <!-- Updated span text to 'Key Performance Indicator' -->
                    </Link>
                </li>

                <!-- Rest of the sidebar content remains the same -->
                <!-- plants & noted settings -->
                <li v-if="
                    hasPermission('plant_settings.view') ||
                    hasPermission('noted.view') ||
                    hasPermission('noted_management.view') ||
                    hasPermission('work_priority.view') ||
                    hasPermission('category.view')
                " class="w-full mt-4">
                    <h6 class="pl-6 ml-2 text-xs font-bold leading-tight uppercase dark:text-white opacity-60">
                        Settings
                    </h6>
                </li>

                <!-- Dropdown for Settings - PERBAIKAN: Perbaiki struktur dan icon -->
                <li v-if="
                    hasPermission('plant_settings.view') ||
                    hasPermission('noted.view') ||
                    hasPermission('noted_management.view') ||
                    hasPermission('work_priority.view') ||
                    hasPermission('category.view')
                " class="w-full">
                    <div class="flex items-center justify-between cursor-pointer px-6 py-2.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg mt-0.5"
                        @click="showSettings = !showSettings">
                        <div class="flex items-center">
                            <div
                                class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-gray-500">
                                <i class="ni ni-settings-gear-65 text-sm leading-normal"></i>
                            </div>
                            <span class="text-sm font-semibold dark:text-white dark:opacity-80">Settings</span>
                        </div>
                        <!-- PERBAIKAN: Gunakan icon yang lebih sederhana dan konsisten -->
                        <div :class="{ 'transform rotate-90': showSettings }" class="transition-transform duration-200">
                            <i class="ni ni-bold-right text-xs dark:text-white text-slate-700"></i>
                        </div>
                    </div>

                    <!-- PERBAIKAN: Perbaiki transition dan styling untuk submenu -->
                    <div v-show="showSettings" class="overflow-hidden transition-all duration-200 ease-in-out">
                        <ul class="pl-14 py-1">
                            <li v-if="hasPermission('plant_settings.view')" :class="[
                                'mt-0.5 w-full',
                                isActiveRoute('plants')
                                    ? 'bg-blue-500/13 rounded-lg'
                                    : '',
                            ]" class="hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
                                @click="scrollToMenuItem">
                                <Link
                                    class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors py-2.5 px-3 whitespace-nowrap"
                                    :href="route('plants.index')">
                                <i class="ni ni-building text-xs mr-2 text-blue-500"></i>
                                <span>Daftar Plant</span>
                                </Link>
                            </li>
                            <li v-if="hasPermission('noted_management.view')" :class="[
                                'mt-0.5 w-full',
                                isActiveRoute('noteds')
                                    ? 'bg-blue-500/13 rounded-lg'
                                    : '',
                            ]" class="hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
                                @click="scrollToMenuItem">
                                <Link
                                    class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors py-2.5 px-3 whitespace-nowrap"
                                    :href="route('notes.index')">
                                <i class="ni ni-paper-diploma text-xs mr-2 text-green-500"></i>
                                <span>Keterangan Progress</span>
                                </Link>
                            </li>
                            <li v-if="hasPermission('noted_management.view')" :class="[
                                'mt-0.5 w-full',
                                isActiveRoute('disciplines')
                                    ? 'bg-blue-500/13 rounded-lg'
                                    : '',
                            ]" class="hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
                                @click="scrollToMenuItem">  
                                <Link
                                    class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors py-2.5 px-3 whitespace-nowrap"
                                    :href="route('disciplines.index')">
                                <i class="ni ni-books text-xs mr-2 text-cyan-500"></i>
                                <span>Daftar Disiplin</span>
                                </Link>
                            </li>
                            <li v-if="hasPermission('category.view')" :class="[
                                'mt-0.5 w-full',
                                isActiveRoute('categories')
                                    ? 'bg-blue-500/13 rounded-lg'
                                    : '',
                            ]" class="hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
                                @click="scrollToMenuItem">
                                <Link
                                    class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors py-2.5 px-3 whitespace-nowrap"
                                    :href="route('categories.index')">
                                <i class="ni ni-tag text-xs mr-2 text-purple-500"></i>
                                <span>Request Category</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- User Management Section -->
                <li v-if="
                    hasPermission('user_management.view') ||
                    hasPermission('role_management.view')
                " class="w-full mt-4">
                    <h6 class="pl-6 ml-2 text-xs font-bold leading-tight uppercase dark:text-white opacity-60">
                        User Management
                    </h6>
                </li>

                <!-- Dropdown for User Management -->
                <li v-if="
                    hasPermission('user_management.view') ||
                    hasPermission('role_management.view')
                " class="w-full">
                    <div class="flex items-center justify-between cursor-pointer px-6 py-2.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg mt-0.5"
                        @click="showUserManagement = !showUserManagement">
                        <div class="flex items-center">
                            <div
                                class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-indigo-500">
                                <i class="ni ni-circle-08 text-sm leading-normal"></i>
                            </div>
                            <span class="text-sm font-semibold dark:text-white dark:opacity-80">User Management</span>
                        </div>
                        <div :class="{ 'transform rotate-90': showUserManagement }"
                            class="transition-transform duration-200">
                            <i class="ni ni-bold-right text-xs dark:text-white text-slate-700"></i>
                        </div>
                    </div>

                    <div v-show="showUserManagement" class="overflow-hidden transition-all duration-200 ease-in-out">
                        <ul class="pl-14 py-1">
                            <li v-if="hasPermission('user_management.view')" :class="[
                                'mt-0.5 w-full',
                                isActiveRoute('users')
                                    ? 'bg-blue-500/13 rounded-lg'
                                    : '',
                            ]" class="hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
                                @click="scrollToMenuItem">
                                <Link
                                    class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors py-2.5 px-3 whitespace-nowrap"
                                    :href="route('users.index')">
                                <i class="ni ni-single-02 text-xs mr-2 text-blue-500"></i>
                                <span>Users</span>
                                </Link>
                            </li>
                            <li v-if="hasPermission('role_management.view')" :class="[
                                'mt-0.5 w-full',
                                isActiveRoute('roles')
                                    ? 'bg-blue-500/13 rounded-lg'
                                    : '',
                            ]" class="hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
                                @click="scrollToMenuItem">
                                <Link
                                    class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors py-2.5 px-3 whitespace-nowrap"
                                    :href="route('roles.index')">
                                <i class="ni ni-badge text-xs mr-2 text-orange-500"></i>
                                <span>Roles</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Log & Audit Trail Section -->
                <li v-if="
                    hasPermission('work_audit.view') ||
                    hasPermission('login_audit_trail.view')
                " class="w-full mt-4">
                    <h6 class="pl-6 ml-2 text-xs font-bold leading-tight uppercase dark:text-white opacity-60">
                        Log & Audit Trail
                    </h6>
                </li>

                <!-- Dropdown for Log & Audit Trail -->
                <li v-if="
                    hasPermission('work_audit.view') ||
                    hasPermission('login_audit_trail.view')
                " class="w-full">
                    <div class="flex items-center justify-between cursor-pointer px-6 py-2.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg mt-0.5"
                        @click="showLogAudit = !showLogAudit">
                        <div class="flex items-center">
                            <div
                                class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-purple-500">
                                <i class="ni ni-bullet-list-67 text-sm leading-normal"></i>
                            </div>
                            <span class="text-sm font-semibold dark:text-white dark:opacity-80">Log & Audit Trail</span>
                        </div>
                        <div :class="{ 'transform rotate-90': showLogAudit }" class="transition-transform duration-200">
                            <i class="ni ni-bold-right text-xs dark:text-white text-slate-700"></i>
                        </div>
                    </div>

                    <div v-show="showLogAudit" class="overflow-hidden transition-all duration-200 ease-in-out">
                        <ul class="pl-14 py-1">
                            <li v-if="hasPermission('work_audit.view')" :class="[
                                'mt-0.5 w-full',
                                isActiveRoute('workaudit')
                                    ? 'bg-blue-500/13 rounded-lg'
                                    : '',
                            ]" class="hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
                                @click="scrollToMenuItem">
                                <Link
                                    class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors py-2.5 px-3 whitespace-nowrap"
                                    :href="route('workaudit')">
                                <i class="ni ni-archive-2 text-xs mr-2 text-purple-500"></i>
                                <span>Work Audit</span>
                                </Link>
                            </li>
                            <li v-if="hasPermission('login_audit_trail.view')" :class="[
                                'mt-0.5 w-full',
                                isActiveRoute('loginaudit')
                                    ? 'bg-blue-500/13 rounded-lg'
                                    : '',
                            ]" class="hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
                                @click="scrollToMenuItem">
                                <Link
                                    class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors py-2.5 px-3 whitespace-nowrap"
                                    :href="route('loginaudit')">
                                <i class="ni ni-key-25 text-xs mr-2 text-pink-500"></i>
                                <span>Login Audit Trail</span>
                                </Link>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="w-full mt-4">
                    <h6 class="pl-6 ml-2 text-xs font-bold leading-tight uppercase dark:text-white opacity-60">
                        Account pages
                    </h6>
                </li>

                <li :class="[
                    'mt-0.5 w-full',
                    isActiveRoute('profile')
                        ? 'bg-blue-500/13 rounded-lg'
                        : '',
                ]" class="hover:bg-gray-100 dark:hover:bg-gray-700" @click="scrollToMenuItem">
                    <Link :href="route('profile.edit')"
                        class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap">
                    <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-slate-700">
                        <i class="ni ni-single-02 text-sm leading-normal"></i>
                    </div>
                    <span>Profile</span>
                    </Link>
                </li>

                <!-- logout -->
                <li class="mt-0.5 w-full hover:bg-gray-100 dark:hover:bg-gray-700" @click="scrollToMenuItem">
                    <Link
                        class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                        :href="route('logout')" method="post">
                    <div class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-red-500">
                        <i class="ni ni-button-power text-sm leading-normal"></i>
                    </div>
                    <span>Logout</span>
                    </Link>
                </li>
            </ul>
        </div>
    </aside>
</template>


<style scoped>
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
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