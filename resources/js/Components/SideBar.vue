<script setup>
import { defineProps, ref, onMounted, onBeforeUnmount } from "vue";
import { Head, Link } from "@inertiajs/vue3";
const props = defineProps({
    isOpen: Boolean,
});

// State untuk toggle submenu
const showSubCategories = ref(false);

// Fungsi toggle submenu
function toggleSubCategories(e) {
    e.stopPropagation();
    showSubCategories.value = !showSubCategories.value;
}

const contentRef = ref(null);
let ps = null;

onMounted(() => {
    // Inisialisasi Perfect Scrollbar pada konten sidebar (bukan seluruh <aside>)
    ps = new PerfectScrollbar(contentRef.value, {
        wheelPropagation: true,
    });
});

onBeforeUnmount(() => {
    if (ps) {
        ps.destroy();
        ps = null;
    }
});
</script>

<template>
    <aside
        :class="[
            isOpen ? 'translate-x-0' : '-translate-x-full xl:translate-x-0',
            'fixed inset-y-0 flex flex-wrap flex-col bg-white dark:bg-slate-850 border-0 shadow-xl dark:shadow-none max-w-64 ease-nav-brand my-4 z-990 xl:ml-6 rounded-2xl xl:left-0 xl:translate-x-0 transition-transform duration-200',
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
        <div ref="contentRef" class="py-2 relative flex-1 overflow-auto ps">
            <ul class="flex flex-col pl-0 mb-0">
                <!-- Dashboard -->
                <li class="mt-0.5 w-full bg-blue-500/13 rounded-lg">
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
                <li class="mt-0.5 w-full">
                    <div
                        class="flex items-center justify-between px-6 py-2.5 cursor-pointer"
                    >
                        <!-- Link ke halaman semua dokumen -->
                        <Link
                            :href="route('erfs')"
                            class="flex items-center text-sm dark:text-white dark:opacity-80"
                        >
                            <div
                                class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-emerald-500"
                            >
                                <i
                                    class="ni ni-credit-card text-sm leading-normal"
                                ></i>
                            </div>
                            <span>ERF Management</span>
                        </Link>
                    </div>
                </li>

                <!-- Progress Report -->
                <li class="mt-0.5 w-full">
                    <a
                        class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                        href=""
                    >
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-yellow-500"
                        >
                            <i
                                class="ni ni-folder-17 text-sm leading-normal"
                            ></i>
                        </div>
                        <span>Progress Report</span>
                    </a>
                </li>

                <!-- Morning Report -->
                <li class="mt-0.5 w-full">
                    <Link
                        class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                        :href="route('morning')"
                    >
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-green-500"
                        >
                            <i
                                class="ni ni-cloud-download-95 text-sm leading-normal"
                            ></i>
                        </div>
                        <span>Morning Report</span>
                    </Link>

                    <!-- Key Performance Indicator -->
                </li>

                <li class="mt-0.5 w-full">
                    <a
                        class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                        href=""
                    >
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-red-500"
                        >
                            <i
                                class="ni ni-single-copy-04 text-sm leading-normal"
                            ></i>
                        </div>
                        <span>Key Performance Indicator</span>
                    </a>
                </li>

                <!-- Page of Users -->
                <li class="w-full mt-4">
                    <h6
                        class="pl-6 ml-2 text-xs font-bold leading-tight uppercase dark:text-white opacity-60"
                    >
                        User Management
                    </h6>
                </li>

                <!-- User Management -->
                <li class="mt-0.5 w-full">
                    <Link
                        class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                        :href="route('users.index')"
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
                <li class="mt-0.5 w-full">
                    <Link
                        class="flex items-center text-sm dark:text-white dark:opacity-80 transition-colors px-6 py-2.5 whitespace-nowrap"
                        :href="route('roles.index')"
                    >
                        <div
                            class="mr-2 flex h-8 w-8 items-center justify-center rounded-lg bg-center text-orange-500"
                        >
                            <i class="ni ni-badge text-sm leading-normal"></i>
                        </div>
                        <span>Role</span>
                    </Link>
                </li>

                <li class="w-full mt-4">
                    <h6
                        class="pl-6 ml-2 text-xs font-bold leading-tight uppercase dark:text-white opacity-60"
                    >
                        Log & Audit Trail
                    </h6>
                </li>

                <li class="mt-0.5 w-full">
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
                        <span>Work Audit Trail</span>
                    </Link>
                </li>

                <!-- login audit trail  -->
                <li class="mt-0.5 w-full">
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

                <li class="mt-0.5 w-full">
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
                <li class="mt-0.5 w-full">
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
                    <!-- Tambahkan item menu lain sesuai kebutuhan -->
                </li>
            </ul>
        </div>
    </aside>
</template>
