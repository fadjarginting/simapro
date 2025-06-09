<script setup>
import { ref, defineProps, computed, onMounted } from "vue";
import { Link, usePage } from '@inertiajs/vue3';
// import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import SideBar from "@/Components/SideBar.vue";
// import NotificationItem from "@/Components/NotificationItem.vue";
import axios from 'axios';

const isSidebarOpen = ref(false);
const isNotificationDropdownOpen = ref(false);
const notifications = ref([]);
const unreadCount = ref(0);
const isLoading = ref(false);

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const closeSidebar = () => {
    isSidebarOpen.value = false;
};
const user = usePage().props.auth.user.data;

</script>

<template>
    <!-- Navbar -->
    <nav class="relative flex flex-wrap items-center justify-between px-0 py-2 mx-6 transition-all ease-in shadow-none duration-250 rounded-2xl lg:flex-nowrap lg:justify-start"
        navbar-main navbar-scroll="false">
        <div class="flex items-center justify-between w-full px-4 py-1 mx-auto flex-wrap-inherit">
            <nav>
                <!-- breadcrumb -->
                <!-- <ol class="flex flex-wrap pt-1 mr-12 bg-transparent rounded-lg sm:mr-16">
                    <li
                        class="inline-flex items-center text-sm leading-normal text-white hover:text-gray-300 transition-colors duration-200">
                        <Link href="/dashboard" class="hover:opacity-100 transition-opacity duration-200">
                        <i class="fas fa-home"></i>
                        </Link>
                        <span class="mx-2 text-white">/</span>
                    </li>
                    <Breadcrumbs :items="$page.props.breadcrumbs" />
                </ol> -->
                <h6 class="mb-0 font-bold text-white capitalize">
                    <!-- breadcrumb name -->
                    <!-- {{ $page.props.breadcrumbs[$page.props.breadcrumbs.length - 1].name }} -->
                </h6>
            </nav>

            <div class="flex items-center mt-2 grow sm:mt-0 sm:mr-6 md:mr-0 lg:flex lg:basis-auto">
                <div class="flex items-center md:ml-auto md:pr-4">          
                    <ul class="flex flex-row justify-end pl-0 mb-0 list-none md-max:w-full">
                        <li class="flex items-center">
                            <a class="block px-0 py-2 text-sm font-semibold text-white transition-all ease-nav-brand">
                                <i class="fa fa-user sm:mr-1"></i>
                                <span class="hidden sm:inline">Welcome, {{ user.name }}</span>
                            </a>
                        </li>
                        <!-- humberger menu -->
                        <li class="flex items-center pl-4 xl:hidden p-2">
                            <!-- Hamburger Menu -->
                            <button @click="toggleSidebar"
                                class="inline-flex items-center justify-center rounded-md p-2 text-white transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{
                                        hidden: isSidebarOpen,
                                        'inline-flex': !isSidebarOpen,
                                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{
                                        hidden: !isSidebarOpen,
                                        'inline-flex': isSidebarOpen,
                                    }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </li>
                        <!-- fixed-plugin -->
                        <li class="flex items-center px-4">
                            <a href="javascript:;" class="p-0 text-sm text-white transition-all ease-nav-brand">
                                <i fixed-plugin-button-nav class="cursor-pointer fa fa-cog"></i>
                                <!-- fixed-plugin-button-nav  -->
                            </a>
                        </li>
    
                        <!-- notifications -->
                        <li class="relative flex items-center pr-4 notification-dropdown">
                            <p class="hidden transform-dropdown-show"></p>
                            <a href="javascript:;"
                                class="block p-0 text-sm text-white transition-all ease-nav-brand relative"
                                @click.stop="toggleNotificationDropdown">
                                <i class="cursor-pointer fa fa-bell"></i>
                                <!-- Notification badge -->
                                <span v-if="unreadCount > 0"
                                    class="absolute -top-1 -right-1 px-1.5 py-0.5 bg-red-500 text-white text-xs rounded-full">
                                    {{ unreadCount > 9 ? '9+' : unreadCount }}
                                </span>
                            </a>
    
                            <!-- Notification dropdown menu -->
                            <ul v-if="isNotificationDropdownOpen"
                                class="text-sm transform-dropdown before:font-awesome before:leading-default before:duration-350 before:ease lg:shadow-3xl duration-250 min-w-44 before:sm:right-8 before:text-5.5 absolute right-0 top-0 z-50 origin-top list-none rounded-lg border-0 border-solid border-transparent dark:shadow-dark-xl dark:bg-slate-850 bg-white bg-clip-padding px-2 py-4 text-left text-slate-500 opacity-100 transition-all before:absolute before:right-2 before:left-auto before:top-0 before:z-50 before:inline-block before:font-normal before:text-white before:antialiased before:transition-all before:content-['\f0d8'] sm:-mr-6 lg:absolute lg:right-0 lg:left-auto lg:mt-2 lg:block lg:cursor-pointer">
                                <!-- Loading state -->
                                <li v-if="isLoading" class="text-center py-2">
                                    <span class="text-sm text-gray-500">Loading...</span>
                                </li>
    
                                <!-- Empty state -->
                                <li v-else-if="notifications.length === 0" class="text-center py-2">
                                    <span class="text-sm text-gray-500">No new notifications</span>
                                </li>
    
                                <!-- Notification items -->
                                <template v-else>
                                    <li v-for="notification in notifications" :key="notification.id" class="relative mb-2">
                                        <NotificationItem :notification="notification" />
                                    </li>
    
                                    <!-- Mark all as read button -->
                                    <li class="text-center mt-2">
                                        <button @click="markAllAsRead"
                                            class="text-sm text-blue-500 hover:text-blue-700 font-medium">
                                            Mark all as read
                                        </button>
                                    </li>
                                </template>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Overlay: Ditampilkan saat sidebar terbuka -->
    <div v-if="isSidebarOpen" @click="closeSidebar" class="fixed inset-0 bg-black bg-opacity-50 z-30"></div>

    <!-- Sidebar Component -->
    <SideBar :isOpen="isSidebarOpen" @close="closeSidebar" />
    <!-- Sidebar -->

    <!-- end Navbar -->
</template>