<script setup>
import { defineProps, ref, computed, onMounted, onBeforeUnmount } from "vue";
import { Link, usePage, Head } from "@inertiajs/vue3";
import { usePermissions } from "@/composables/permissions";
import { router } from "@inertiajs/vue3";

const { hasPermission } = usePermissions();

const props = defineProps({
    isOpen: Boolean,
});

// Mengambil informasi halaman dari Inertia
const page = usePage();
const currentUrl = computed(() => page.url);

// Fungsi helper untuk memeriksa apakah URL saat ini mengandung fragmen tertentu
function isActiveRoute(routeFragment) {
    return currentUrl.value ? currentUrl.value.includes(routeFragment) : false;
}

// State untuk toggle submenu
const showSubCategories = ref(false);
function toggleSubCategories(e) {
    e.stopPropagation();
    showSubCategories.value = !showSubCategories.value;
}

// Kode lainnya tetap sama seperti aslinya
const sideElement = ref(null);
const activeMenuItem = ref(null);

function scrollToMenuItem(event) {
    activeMenuItem.value = event.currentTarget;
    if (sideElement.value && activeMenuItem.value) {
        const containerTop = sideElement.value.getBoundingClientRect().top;
        const elementTop = activeMenuItem.value.getBoundingClientRect().top;
        const offset = 20;
        sideElement.value.scrollTop =
            sideElement.value.scrollTop + (elementTop - containerTop - offset);
    }
}

onMounted(() => {
    // Kode yang sudah ada
    router.on("navigate", () => {
        if (sideElement.value) {
            setTimeout(() => {
                const activeItem = document.querySelector(".bg-blue-500\\/13");
                if (activeItem) {
                    const containerTop =
                        sideElement.value.getBoundingClientRect().top;
                    const elementTop = activeItem.getBoundingClientRect().top;
                    const offset = 20;
                    sideElement.value.scrollTop =
                        sideElement.value.scrollTop +
                        (elementTop - containerTop - offset);
                } else {
                    sideElement.value.scrollTop = 0;
                }
            }, 100);
        }
    });
});
</script>

<template>
    <aside
        :class="[
            isOpen ? 'translate-x-0' : '-translate-x-full xl:translate-x-0',
            'fixed inset-y-0 flex flex-wrap flex-col bg-white dark:bg-slate-850 border-0 shadow-xl dark:shadow-none max-w-full ease-nav-brand my-4 z-990 xl:ml-6 rounded-2xl xl:left-0 xl:translate-x-0 transition-transform duration-200',
        ]"
        aria-expanded="false"
    >
        <!-- HEADER  -->
        <div
            class="h-19 px-8 py-6 dark:text-white text-slate-700 border-b border-gray-200 dark:border-slate-700 flex items-center justify-between"
        >
            <a class="text-sm whitespace-nowrap flex items-center" href="#">
                <img
                    src="assets/img/semen-padang.png"
                    class="inline h-full max-w-full transition-all duration-200 dark:hidden ease-nav-brand max-h-8"
                    alt="main_logo"
                />

                <span
                    class="ml-1 font-semibold transition-all duration-200 ease-nav-brand"
                >
                    Monitoring Progress
                </span>
            </a>
            <!-- Close button -->
            <button
                class="absolute top-0 right-0 p-4 opacity-50 cursor-pointer fas fa-times dark:text-white text-slate-400 xl:hidden"
                @click="$emit('close')"
            ></button>
        </div>

        <!-- CONTENT -->
        <div ref="sideElement" class="py-2 relative flex-1 overflow-auto">
            <ul class="flex flex-col pl-0 mb-0">
                <!-- Dashboard -->
                <li
                    v-if="hasPermission('dashboard.view')"
                    :class="[
                        'mt-0.5 w-full',
                        isActiveRoute('dashboard')
                            ? 'bg-blue-500/13 rounded-lg'
                            : '',
                    ]"
                    class="hover:bg-gray-100 dark:hover:bg-gray-700"
                    @click="scrollToMenuItem"
                >
                    <Link
                        class="flex items-center text-sm font-semibold dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                        :href="route('dashboard')"
                    >
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-blue-500"
                        >
                            <i class="ni ni-tv-2 text-sm leading-normal"></i>
                        </div>
                        <span class="ml-1">Dashboard</span>
                    </Link>
                </li>

                <!-- ERF Management -->
                <li
                    v-if="hasPermission('erf_management.view')"
                    :class="[
                        'mt-0.5 w-full',
                        isActiveRoute('erfs')
                            ? 'bg-blue-500/13 rounded-lg'
                            : '',
                    ]"
                    class="hover:bg-gray-100 dark:hover:bg-gray-700"
                    @click="scrollToMenuItem"
                >
                    <Link
                        class="flex items-center text-sm font-semibold dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                        :href="route('erfs.index')"
                    >
                        <!-- Updated href to route('erfs') -->
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-blue-500"
                        >
                            <i class="ni ni-tv-2 text-sm leading-normal"></i>
                        </div>
                        <span class="ml-1">ERF Management</span>
                        <!-- Updated span text to 'ERF Management' -->
                    </Link>
                </li>

                <!-- Progress Report -->
                <li
                    v-if="hasPermission('progress_report.view')"
                    :class="[
                        'mt-0.5 w-full',
                        isActiveRoute('progress')
                            ? 'bg-blue-500/13 rounded-lg'
                            : '',
                    ]"
                    class="hover:bg-gray-100 dark:hover:bg-gray-700"
                    @click="scrollToMenuItem"
                >
                    <Link
                        class="flex items-center text-sm font-semibold dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                        :href="route('progress.index')"
                    >
                        <!-- Updated href to route('progress') -->
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-yellow-500"
                        >
                            <i
                                class="ni ni-folder-17 text-sm leading-normal"
                            ></i>
                        </div>
                        <span class="ml-1">Progress Report</span>
                        <!-- Updated span text to 'Progress Report' -->
                    </Link>
                </li>

                <!-- Morning Report -->
                <li
                    v-if="hasPermission('morning_report.view')"
                    :class="[
                        'mt-0.5 w-full',
                        isActiveRoute('morning')
                            ? 'bg-blue-500/13 rounded-lg'
                            : '',
                    ]"
                    class="hover:bg-gray-100 dark:hover:bg-gray-700"
                    @click="scrollToMenuItem"
                >
                    <Link
                        class="flex items-center text-sm font-semibold dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                        :href="route('morning.index')"
                    >
                        <!-- Updated href to route('morning') -->
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-green-500"
                        >
                            <i
                                class="ni ni-cloud-download-95 text-sm leading-normal"
                            ></i>
                        </div>
                        <span class="ml-1">Morning Report</span>
                        <!-- Updated span text to 'Morning Report' -->
                    </Link>
                </li>

                <!-- Key Performance Indicator -->
                <li
                    v-if="hasPermission('kpi_management.view')"
                    :class="[
                        'mt-0.5 w-full',
                        isActiveRoute('kpis')
                            ? 'bg-blue-500/13 rounded-lg'
                            : '',
                    ]"
                    class="hover:bg-gray-100 dark:hover:bg-gray-700"
                    @click="scrollToMenuItem"
                >
                    <Link
                        class="flex items-center text-sm font-semibold dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                        :href="route('kpis')"
                    >
                        <!-- Updated href to route('kpis') -->
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-red-500"
                        >
                            <i
                                class="ni ni-single-copy-04 text-sm leading-normal"
                            ></i>
                        </div>
                        <span class="ml-1">Key Performance Indicator</span>
                        <!-- Updated span text to 'Key Performance Indicator' -->
                    </Link>
                </li>

                <!-- EatSchedule -->
                <li
                    v-if="hasPermission('eat_schedule.view')"
                    :class="[
                        'mt-0.5 w-full',
                        isActiveRoute('eat-schedules')
                            ? 'bg-blue-500/13 rounded-lg'
                            : '',
                    ]"
                    class="hover:bg-gray-100 dark:hover:bg-gray-700"
                    @click="scrollToMenuItem"
                >
                    <Link
                        class="flex items-center text-sm font-semibold dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                        :href="route('eat-schedules.index')"
                    >
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-green-500"
                        >
                            <i
                                class="ni ni-calendar-grid-58 text-sm leading-normal"
                            ></i>
                        </div>
                        <span class="ml-1">Eat Schedule</span>
                    </Link>
                </li>

                <!-- page of users -->
                <li
                    v-if="
                        hasPermission('user_management.view') ||
                        hasPermission('roles_management.view')
                    "
                    class="w-full mt-4"
                >
                    <h6
                        class="pl-6 ml-2 text-xs font-bold leading-tight uppercase dark:text-white opacity-60"
                    >
                        User Management
                    </h6>
                </li>

                <!-- User Management -->
                <li
                    v-if="hasPermission('user_management.view')"
                    :class="[
                        'mt-0.5 w-full',
                        isActiveRoute('users')
                            ? 'bg-blue-500/13 rounded-lg'
                            : '',
                    ]"
                    class="hover:bg-gray-100 dark:hover:bg-gray-700"
                    @click="scrollToMenuItem"
                >
                    <Link
                        :href="route('users.index')"
                        class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                    >
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-indigo-500"
                        >
                            <i
                                class="ni ni-circle-08 text-sm leading-normal"
                            ></i>
                        </div>
                        <span>User</span>
                    </Link>
                </li>

                <!-- Role  -->
                <li
                    v-if="hasPermission('roles_management.view')"
                    :class="[
                        'mt-0.5 w-full',
                        isActiveRoute('roles')
                            ? 'bg-blue-500/13 rounded-lg'
                            : '',
                    ]"
                    class="hover:bg-gray-100 dark:hover:bg-gray-700"
                    @click="scrollToMenuItem"
                >
                    <Link
                        class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                        :href="route('roles.index')"
                    >
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-orange-500"
                        >
                            <i class="ni ni-badge text-sm leading-normal"></i>
                        </div>
                        <span>Roles</span>
                    </Link>
                </li>

                <li
                    v-if="
                        hasPermission('work_audit.view') ||
                        hasPermission('login_audit_trail.view')
                    "
                    class="w-full mt-4"
                >
                    <h6
                        class="pl-6 ml-2 text-xs font-bold leading-tight uppercase dark:text-white opacity-60"
                    >
                        Log & Audit Trail
                    </h6>
                </li>

                <li
                    v-if="hasPermission('work_audit.view')"
                    :class="[
                        'mt-0.5 w-full',
                        isActiveRoute('workaudit')
                            ? 'bg-blue-500/13 rounded-lg'
                            : '',
                    ]"
                    class="hover:bg-gray-100 dark:hover:bg-gray-700"
                    @click="scrollToMenuItem"
                >
                    <Link
                        class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                        :href="route('workaudit')"
                    >
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-purple-500"
                        >
                            <i
                                class="ni ni-bullet-list-67 text-sm leading-normal"
                            ></i>
                        </div>
                        <span>Work Audit</span>
                    </Link>
                </li>

                <!-- login audit trail  -->
                <li
                    v-if="hasPermission('login_audit_trail.view')"
                    :class="[
                        'mt-0.5 w-full',
                        isActiveRoute('loginaudit')
                            ? 'bg-blue-500/13 rounded-lg'
                            : '',
                    ]"
                    class="hover:bg-gray-100 dark:hover:bg-gray-700"
                    @click="scrollToMenuItem"
                >
                    <Link
                        class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                        :href="route('loginaudit')"
                    >
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-pink-500"
                        >
                            <i class="ni ni-key-25 text-sm leading-normal"></i>
                        </div>
                        <span>Login Audit Trail</span>
                    </Link>
                </li>

                <li class="w-full mt-4">
                    <h6
                        class="pl-6 ml-2 text-xs font-bold leading-tight uppercase dark:text-white opacity-60"
                    >
                        Account pages
                    </h6>
                </li>

                <li
                    :class="[
                        'mt-0.5 w-full',
                        isActiveRoute('profile')
                            ? 'bg-blue-500/13 rounded-lg'
                            : '',
                    ]"
                    class="hover:bg-gray-100 dark:hover:bg-gray-700"
                    @click="scrollToMenuItem"
                >
                    <Link
                        :href="route('profile.edit')"
                        class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                    >
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-slate-700"
                        >
                            <i
                                class="ni ni-single-02 text-sm leading-normal"
                            ></i>
                        </div>
                        <span>Profile</span>
                    </Link>
                </li>

                <!-- logout -->
                <li
                    class="mt-0.5 w-full hover:bg-gray-100 dark:hover:bg-gray-700"
                    @click="scrollToMenuItem"
                >
                    <Link
                        class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                        :href="route('logout')"
                        method="post"
                    >
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-red-500"
                        >
                            <i
                                class="ni ni-button-power text-sm leading-normal"
                            ></i>
                        </div>
                        <span>Logout</span>
                    </Link>
                </li>
            </ul>
        </div>
    </aside>
</template>
