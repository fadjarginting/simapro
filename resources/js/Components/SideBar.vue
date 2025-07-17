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
    // More specific check to avoid partial matches like 'users' matching 'users-permissions'
    const currentPath = page.url.split('?')[0];
    const routeParts = currentPath.split('/').filter(p => p);
    return routeParts.includes(routeFragment);
}

// State management for dropdowns
const showSettings = ref(false);
const showUserManagement = ref(false);
const showLogAudit = ref(false);

// Auto-expand dropdowns based on current route
onMounted(() => {
    const url = currentUrl.value;
    if (url.includes('/plants') || url.includes('/notes') || url.includes('/disciplines') || url.includes('/categories')) {
        showSettings.value = true;
    }
    if (url.includes('/users') || url.includes('/roles')) {
        showUserManagement.value = true;
    }
    if (url.includes('/workaudit') || url.includes('/loginaudit')) {
        showLogAudit.value = true;
    }
});


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
                const activeItem = document.querySelector('.sidebar-active-item');
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
    <!-- Sidebar with updated styling -->
    <aside :class="[
        isOpen ? 'translate-x-0' : '-translate-x-full xl:translate-x-0',
        'fixed inset-y-0 flex flex-col bg-white dark:bg-slate-900 border-r border-gray-200 dark:border-slate-800 shadow-lg w-64 ease-nav-brand my-4 z-990 xl:ml-6 rounded-2xl xl:left-0 xl:translate-x-0 transition-transform duration-200',
    ]">
        <!-- HEADER -->
        <div
            class="h-19 px-6 py-6 dark:text-white text-slate-800 border-b border-gray-200 dark:border-slate-800 flex items-center justify-center">
            <a class="text-sm whitespace-nowrap flex items-center" href="#">
                <img src="/assets/img/semen-padang.png"
                    class="inline h-full max-w-full transition-all duration-200 ease-nav-brand max-h-8"
                    alt="main_logo" />
                <span
                    class="ml-3 justify-center font-bold text-xl tracking-tight transition-all duration-200 ease-nav-brand">
                    SIMAPRO
                </span>
            </a>
            <!-- Close button -->
            <button
                class="absolute top-2 right-2 p-4 opacity-60 cursor-pointer fas fa-times dark:text-white text-slate-500 xl:hidden"
                @click="$emit('close')"></button>
        </div>

        <!-- CONTENT -->
        <div ref="sideElement" class="flex-1 overflow-y-auto w-64 p-3">
            <ul class="flex flex-col pl-0 mb-0 space-y-1">
                <!-- Dashboard -->
                <li v-if="hasPermission('dashboard.view') && hasRole('Admin')" @click="scrollToMenuItem">
                    <Link :href="route('dashboard')"
                        class="flex items-center gap-3 text-sm font-semibold dark:text-slate-300 text-slate-700 transition-all px-3 py-2.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800"
                        :class="{ 'sidebar-active-item bg-blue-50 dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm': isActiveRoute('dashboard') }">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-400 to-blue-500 text-white shadow-md">
                        <i class="ni ni-tv-2 text-sm leading-normal"></i>
                    </div>
                    <span class="ml-1">Dashboard</span>
                    </Link>
                </li>

                <li v-if="hasPermission('dashboard.view') && hasRole('User') || hasRole('Lead')" @click="scrollToMenuItem">
                    <Link :href="route('my-dashboard')"
                        class="flex items-center gap-3 text-sm font-semibold dark:text-slate-300 text-slate-700 transition-all px-3 py-2.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800"
                        :class="{ 'sidebar-active-item bg-blue-50 dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm': isActiveRoute('my-dashboard') }">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-400 to-blue-500 text-white shadow-md">
                        <i class="ni ni-tv-2 text-sm leading-normal"></i>
                    </div>
                    <span class="ml-1">My Dashboard</span>
                    </Link>
                </li>

                <!-- Progress Report -->
                <li v-if="hasPermission('manajemen_pekerjaan.view') && hasRole('Admin')" @click="scrollToMenuItem">
                    <Link :href="route('works.index')"
                        class="flex items-center gap-3 text-sm font-semibold dark:text-slate-300 text-slate-700 transition-all px-3 py-2.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800"
                        :class="{ 'sidebar-active-item bg-blue-50 dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm': isActiveRoute('works') }">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-yellow-400 to-yellow-500 text-white shadow-md">
                        <i class="ni ni-folder-17 text-sm leading-normal"></i>
                    </div>
                    <span class="ml-1">Manajemen Pekerjaan</span>
                    </Link>
                </li>

                <!-- Pekerjaan saya (pekerjaan spesifik user) -->
                <li v-if="hasPermission('my_works.view')" @click="scrollToMenuItem">
                    <Link :href="route('my-works.index')"
                        class="flex items-center gap-3 text-sm font-semibold dark:text-slate-300 text-slate-700 transition-all px-3 py-2.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800"
                        :class="{ 'sidebar-active-item bg-blue-50 dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm': isActiveRoute('my-works') || isActiveRoute('works') }">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-teal-400 to-teal-500 text-white shadow-md">
                        <i class="ni ni-bullet-list-67 text-sm leading-normal"></i>
                    </div>
                    <span class="ml-1">Pekerjaan Saya</span>
                    </Link>
                </li>

                <!-- Laporan Pagi -->
                <li v-if="hasPermission('morning_report.view')" @click="scrollToMenuItem">
                    <Link :href="route('morning.index')"
                        class="flex items-center gap-3 text-sm font-semibold dark:text-slate-300 text-slate-700 transition-all px-3 py-2.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800"
                        :class="{ 'sidebar-active-item bg-blue-50 dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm': isActiveRoute('morning') }">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-green-400 to-green-500 text-white shadow-md">
                        <i class="ni ni-cloud-download-95 text-sm leading-normal"></i>
                    </div>
                    <span class="ml-1">Laporan Pagi</span>
                    </Link>
                </li>
                <!-- Key Performance Indicator -->
                <li v-if="hasPermission('kpi_management.view')" @click="scrollToMenuItem">
                    <Link :href="route('kpis')"
                        class="flex items-center gap-3 text-sm font-semibold dark:text-slate-300 text-slate-700 transition-all px-3 py-2.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800"
                        :class="{ 'sidebar-active-item bg-blue-50 dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm': isActiveRoute('kpis') }">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-red-400 to-red-500 text-white shadow-md">
                        <i class="ni ni-single-copy-04 text-sm leading-normal"></i>
                    </div>
                    <span class="ml-1">Key Performance Indicator</span>
                    </Link>
                </li>

                <!-- Settings Section -->
                <template
                    v-if="hasPermission('plant_settings.view') || hasPermission('noted.view') || hasPermission('noted_management.view') || hasPermission('work_priority.view') || hasPermission('category.view')">
                    <li class="w-full mt-4">
                        <h6
                            class="pl-3 ml-2 text-xs font-bold leading-tight uppercase text-slate-500 dark:text-slate-400">
                            Settings
                        </h6>
                    </li>
                    <li class="w-full">
                        <div class="flex items-center justify-between cursor-pointer px-3 py-2.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg"
                            @click="showSettings = !showSettings">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-gray-400 to-gray-500 text-white shadow-md">
                                    <i class="ni ni-settings-gear-65 text-sm leading-normal"></i>
                                </div>
                                <span class="text-sm font-semibold dark:text-slate-300 text-slate-700">Settings</span>
                            </div>
                            <div :class="{ 'transform rotate-90': showSettings }"
                                class="transition-transform duration-200">
                                <i class="ni ni-bold-right text-xs dark:text-white text-slate-700"></i>
                            </div>
                        </div>
                        <div v-show="showSettings" class="overflow-hidden transition-all duration-200 ease-in-out mt-1">
                            <ul class="pl-6 space-y-1">
                                <li v-if="hasPermission('plant_settings.view')" @click="scrollToMenuItem">
                                    <Link :href="route('plants.index')"
                                        class="flex items-center text-sm dark:text-slate-400 text-slate-600 transition-colors py-2 px-3 whitespace-nowrap rounded-md hover:bg-slate-100 dark:hover:bg-slate-800"
                                        :class="{ 'sidebar-active-item bg-blue-50 dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm': isActiveRoute('plants') }">
                                    <i class="ni ni-building text-xs mr-2 text-blue-500"></i><span>Daftar Plant</span>
                                    </Link>
                                </li>
                                <li v-if="hasPermission('noted_management.view')" @click="scrollToMenuItem">
                                    <Link :href="route('notes.index')"
                                        class="flex items-center text-sm dark:text-slate-400 text-slate-600 transition-colors py-2 px-3 whitespace-nowrap rounded-md hover:bg-slate-100 dark:hover:bg-slate-800"
                                        :class="{ 'sidebar-active-item bg-blue-50 dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm': isActiveRoute('noteds') }">
                                    <i class="ni ni-paper-diploma text-xs mr-2 text-green-500"></i><span>Keterangan
                                        Progress</span>
                                    </Link>
                                </li>
                                <li v-if="hasPermission('noted_management.view')" @click="scrollToMenuItem">
                                    <Link :href="route('disciplines.index')"
                                        class="flex items-center text-sm dark:text-slate-400 text-slate-600 transition-colors py-2 px-3 whitespace-nowrap rounded-md hover:bg-slate-100 dark:hover:bg-slate-800"
                                        :class="{ 'sidebar-active-item bg-blue-50 dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm': isActiveRoute('disciplines') }">
                                    <i class="ni ni-books text-xs mr-2 text-cyan-500"></i><span>Daftar Disiplin</span>
                                    </Link>
                                </li>

                            </ul>
                        </div>
                    </li>
                </template>

                <!-- User Management Section -->
                <template v-if="hasPermission('user_management.view') || hasPermission('role_management.view')">
                    <li class="w-full mt-4">
                        <h6
                            class="pl-3 ml-2 text-xs font-bold leading-tight uppercase text-slate-500 dark:text-slate-400">
                            User Management
                        </h6>
                    </li>
                    <li class="w-full">
                        <div class="flex items-center justify-between cursor-pointer px-3 py-2.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg"
                            @click="showUserManagement = !showUserManagement">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-indigo-400 to-indigo-500 text-white shadow-md">
                                    <i class="ni ni-circle-08 text-sm leading-normal"></i>
                                </div>
                                <span class="text-sm font-semibold dark:text-slate-300 text-slate-700">User
                                    Management</span>
                            </div>
                            <div :class="{ 'transform rotate-90': showUserManagement }"
                                class="transition-transform duration-200">
                                <i class="ni ni-bold-right text-xs dark:text-white text-slate-700"></i>
                            </div>
                        </div>
                        <div v-show="showUserManagement"
                            class="overflow-hidden transition-all duration-200 ease-in-out mt-1">
                            <ul class="pl-6 space-y-1">
                                <li v-if="hasPermission('user_management.view')" @click="scrollToMenuItem">
                                    <Link :href="route('users.index')"
                                        class="flex items-center text-sm dark:text-slate-400 text-slate-600 transition-colors py-2 px-3 whitespace-nowrap rounded-md hover:bg-slate-100 dark:hover:bg-slate-800"
                                        :class="{ 'sidebar-active-item bg-blue-50 dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm': isActiveRoute('users') }">
                                    <i class="ni ni-single-02 text-xs mr-2 text-blue-500"></i><span>Users</span>
                                    </Link>
                                </li>
                                <li v-if="hasPermission('role_management.view')" @click="scrollToMenuItem">
                                    <Link :href="route('roles.index')"
                                        class="flex items-center text-sm dark:text-slate-400 text-slate-600 transition-colors py-2 px-3 whitespace-nowrap rounded-md hover:bg-slate-100 dark:hover:bg-slate-800"
                                        :class="{ 'sidebar-active-item bg-blue-50 dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm': isActiveRoute('roles') }">
                                    <i class="ni ni-badge text-xs mr-2 text-orange-500"></i><span>Roles</span>
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </li>
                </template>

                <!-- Log & Audit Trail Section -->
                <template v-if="hasPermission('work_audit.view') || hasPermission('login_audit_trail.view')">
                    <li class="w-full mt-4">
                        <h6
                            class="pl-3 ml-2 text-xs font-bold leading-tight uppercase text-slate-500 dark:text-slate-400">
                            Log & Audit Trail
                        </h6>
                    </li>
                    <li class="w-full">
                        <div class="flex items-center justify-between cursor-pointer px-3 py-2.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg"
                            @click="showLogAudit = !showLogAudit">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-purple-400 to-purple-500 text-white shadow-md">
                                    <i class="ni ni-bullet-list-67 text-sm leading-normal"></i>
                                </div>
                                <span class="text-sm font-semibold dark:text-slate-300 text-slate-700">Log & Audit
                                    Trail</span>
                            </div>
                            <div :class="{ 'transform rotate-90': showLogAudit }"
                                class="transition-transform duration-200">
                                <i class="ni ni-bold-right text-xs dark:text-white text-slate-700"></i>
                            </div>
                        </div>
                        <div v-show="showLogAudit" class="overflow-hidden transition-all duration-200 ease-in-out mt-1">
                            <ul class="pl-6 space-y-1">
                                <li v-if="hasPermission('work_audit.view')" @click="scrollToMenuItem">
                                    <Link :href="route('workaudit')"
                                        class="flex items-center text-sm dark:text-slate-400 text-slate-600 transition-colors py-2 px-3 whitespace-nowrap rounded-md hover:bg-slate-100 dark:hover:bg-slate-800"
                                        :class="{ 'sidebar-active-item bg-blue-50 dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm': isActiveRoute('workaudit') }">
                                    <i class="ni ni-archive-2 text-xs mr-2 text-purple-500"></i><span>Work Audit</span>
                                    </Link>
                                </li>
                                <li v-if="hasPermission('login_audit_trail.view')" @click="scrollToMenuItem">
                                    <Link :href="route('loginaudit')"
                                        class="flex items-center text-sm dark:text-slate-400 text-slate-600 transition-colors py-2 px-3 whitespace-nowrap rounded-md hover:bg-slate-100 dark:hover:bg-slate-800"
                                        :class="{ 'sidebar-active-item bg-blue-50 dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm': isActiveRoute('loginaudit') }">
                                    <i class="ni ni-key-25 text-xs mr-2 text-pink-500"></i><span>Login Audit
                                        Trail</span>
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </li>
                </template>

                <!-- Account Pages -->
                <li class="w-full mt-4">
                    <h6 class="pl-3 ml-2 text-xs font-bold leading-tight uppercase text-slate-500 dark:text-slate-400">
                        Account pages
                    </h6>
                </li>
                <li @click="scrollToMenuItem">
                    <Link :href="route('profile.edit')"
                        class="flex items-center gap-3 text-sm font-semibold dark:text-slate-300 text-slate-700 transition-all px-3 py-2.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800"
                        :class="{ 'sidebar-active-item bg-blue-50 dark:bg-slate-800 text-blue-600 dark:text-blue-400 shadow-sm': isActiveRoute('profile') }">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-slate-400 to-slate-500 text-white shadow-md">
                        <i class="ni ni-single-02 text-sm leading-normal"></i>
                    </div>
                    <span class="ml-1">Profile</span>
                    </Link>
                </li>
                <!-- Logout -->
                <li @click="scrollToMenuItem">
                    <Link :href="route('logout')" method="post"
                        class="flex items-center gap-3 text-sm font-semibold dark:text-slate-300 text-slate-700 transition-all px-3 py-2.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
                    <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-red-500 to-pink-500 text-white shadow-md">
                        <i class="ni ni-button-power text-sm leading-normal"></i>
                    </div>
                    <span class="ml-1">Logout</span>
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
    background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #d1d5db; /* gray-300 */
    border-radius: 10px;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #4b5563; /* gray-600 */
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #9ca3af; /* gray-400 */
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #6b7280; /* gray-500 */
}
</style>
