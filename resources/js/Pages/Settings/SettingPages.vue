<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import Swal from 'sweetalert2';
import { ref, watch, reactive } from "vue";

defineOptions({
    layout: AuthenticatedLayout,
});

const props = defineProps({
    settingsData: Object, // { disciplines: {...}, notes: {...}, plants: {...}, etc }
    filters: Object,
    errors: Object,
    flash: Object,
    success: String,
});

const page = usePage();

// State untuk mengontrol expand/minimize setiap kategori
const expandedCards = reactive({
    plants: true,
    disciplines: true,
    notes: true,
    categories: true,
    users: true,
});

// Form untuk search dan pagination per kategori
const forms = reactive({
    plants: {
        search: props.filters?.plants?.search || '',
        perPage: props.filters?.plants?.perPage || 10,
        page: props.settingsData?.plants?.current_page || 1,
    },
    disciplines: {
        search: props.filters?.disciplines?.search || '',
        perPage: props.filters?.disciplines?.perPage || 10,
        page: props.settingsData?.disciplines?.current_page || 1,
    },
    notes: {
        search: props.filters?.notes?.search || '',
        perPage: props.filters?.notes?.perPage || 10,
        page: props.settingsData?.notes?.current_page || 1,
    },
    categories: {
        search: props.filters?.categories?.search || '',
        perPage: props.filters?.categories?.perPage || 10,
        page: props.settingsData?.categories?.current_page || 1,
    },
    users: {
        search: props.filters?.users?.search || '',
        perPage: props.filters?.users?.perPage || 10,
        page: props.settingsData?.users?.current_page || 1,
    },
});

// Timeout untuk debounce search
let timeouts = {};

const performSearch = (category) => {
    clearTimeout(timeouts[category]);
    timeouts[category] = setTimeout(() => {
        router.get(route('display-settings.index'), {
            ...Object.fromEntries(
                Object.entries(forms).map(([key, value]) => [key, value])
            ),
            [category]: {
                ...forms[category],
                page: 1,
            }
        }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
};

// Watch untuk setiap kategori
Object.keys(forms).forEach(category => {
    watch(() => forms[category].search, () => performSearch(category));
    watch(() => forms[category].perPage, () => {
        router.get(route('display-settings.index'), {
            ...Object.fromEntries(
                Object.entries(forms).map(([key, value]) => [key, value])
            ),
            [category]: {
                ...forms[category],
                page: 1,
            }
        }, {
            preserveState: true,
            replace: true,
        });
    });
});

const changePage = (category, url) => {
    if (!url) return;
    router.visit(url, {
        preserveState: true,
        replace: true,
    });
};

const deleteSetting = (category, settingId, settingName) => {
    Swal.fire({
        title: 'Confirm Deletion',
        text: `Are you sure you want to delete "${settingName}"? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#EF4444',
        cancelButtonColor: '#9CA3AF',
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'rounded-lg shadow-lg',
            title: 'text-lg font-semibold text-gray-800',
            htmlContainer: 'text-sm text-gray-600',
            confirmButton: 'px-4 py-2 rounded-md text-white bg-red-500 hover:bg-red-600',
            cancelButton: 'px-4 py-2 rounded-md text-gray-700 bg-gray-200 hover:bg-gray-300'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route(`${category}.destroy`, settingId), {
                onSuccess: () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: `The setting has been successfully deleted.`,
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'rounded-lg shadow-lg',
                            title: 'text-sm font-semibold text-gray-800',
                        }
                    });
                },
                onError: () => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: `Failed to delete the setting.`,
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true,
                        customClass: {
                            popup: 'rounded-lg shadow-lg',
                            title: 'text-sm font-semibold text-gray-800',
                        }
                    });
                }
            });
        }
    });
};

const toggleCard = (category) => {
    expandedCards[category] = !expandedCards[category];
};

const getCategoryTitle = (category) => {
    const titles = {
        plants: 'Plant Settings',
        disciplines: 'Discipline Settings',
        notes: 'Note Settings',
        categories: 'Category Settings',
        users: 'User Settings',
    };
    return titles[category] || `${category.charAt(0).toUpperCase() + category.slice(1)} Settings`;
};

const getCategoryIcon = (category) => {
    const icons = {
        plants: 'fas fa-seedling',
        disciplines: 'fas fa-graduation-cap',
        notes: 'fas fa-sticky-note',
        categories: 'fas fa-tags',
        users: 'fas fa-users',
    };
    return icons[category] || 'fas fa-cog';
};

const getCategoryColor = (category) => {
    const colors = {
        plants: 'emerald',
        disciplines: 'blue',
        notes: 'amber',
        categories: 'purple',
        users: 'indigo',
    };
    return colors[category] || 'gray';
};

const getAddButtonText = (category) => {
    const texts = {
        plants: 'Add Plant',
        disciplines: 'Add Discipline',
        notes: 'Add Note',
        categories: 'Add Category',
        users: 'Add User',
    };
    return texts[category] || `Add ${category}`;
};

const getSearchPlaceholder = (category) => {
    const placeholders = {
        plants: 'Search plants...',
        disciplines: 'Search disciplines...',
        notes: 'Search notes...',
        categories: 'Search categories...',
        users: 'Search users...',
    };
    return placeholders[category] || `Search ${category}...`;
};
</script>

<template>

    <Head title="Settings Management" />

    <template name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Settings Management
        </h2>
    </template>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            <!-- Loop through each category -->
            <div v-for="(categoryData, category) in settingsData" :key="category"
                class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-all duration-200">

                <!-- Card Header -->
                <div class="p-6 pb-4 border-b border-gray-100">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div
                                :class="`w-10 h-10 rounded-lg bg-${getCategoryColor(category)}-100 flex items-center justify-center mr-3`">
                                <i :class="[getCategoryIcon(category), `text-${getCategoryColor(category)}-600`, 'text-lg']"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ getCategoryTitle(category) }}
                                </h3>
                                <p class="text-sm text-gray-500">
                                    {{ categoryData?.total || 0 }} items
                                </p>
                            </div>
                        </div>
                        <button @click="toggleCard(category)"
                            class="p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                            <i :class="expandedCards[category] ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"
                                class="text-gray-400 text-sm"></i>
                        </button>
                    </div>

                    <!-- Add Button -->
                    <Link :href="route(`${category}.create`)"
                        :class="`w-full flex items-center justify-center px-4 py-2 bg-${getCategoryColor(category)}-500 hover:bg-${getCategoryColor(category)}-600 text-white text-sm font-medium rounded-lg transition-colors duration-200`">
                    <i class="fas fa-plus mr-2 text-xs"></i>
                    {{ getAddButtonText(category) }}
                    </Link>
                </div>

                <!-- Collapsible Content -->
                <div v-show="expandedCards[category]" class="transition-all duration-300">
                    <!-- Search -->
                    <div class="p-6 pt-4 pb-4">
                        <div class="relative">
                            <input v-model="forms[category].search" type="text"
                                :placeholder="getSearchPlaceholder(category)"
                                class="w-full pl-10 pr-10 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" />
                            <i
                                class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></i>
                            <button v-if="forms[category].search" @click="forms[category].search = ''"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="px-6 pb-6">
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between py-3 border-b border-gray-100 text-sm font-medium text-gray-600">
                            <span>Name</span>
                            <span>Actions</span>
                        </div>

                        <!-- Items List -->
                        <div class="space-y-0">
                            <div v-for="setting in categoryData?.data || []" :key="setting.id"
                                class="flex items-center justify-between py-3 border-b border-gray-50 last:border-b-0 hover:bg-gray-25 -mx-2 px-2 rounded">
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ setting.name }}
                                    </div>
                                    <div v-if="setting.description" class="text-xs text-gray-500 mt-1">
                                        {{ setting.description }}
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 ml-4">
                                    <Link :href="route(`${category}.edit`, setting.id)"
                                        class="flex items-center px-2 py-1 text-xs font-medium text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded transition-colors duration-200">
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit
                                    </Link>
                                    <button @click="deleteSetting(category, setting.id, setting.name)"
                                        class="flex items-center px-2 py-1 text-xs font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded transition-colors duration-200">
                                        <i class="fas fa-trash mr-1"></i>
                                        Delete
                                    </button>
                                </div>
                            </div>

                            <!-- Empty State -->
                            <div v-if="!categoryData?.data || categoryData.data.length === 0" class="text-center py-8">
                                <i :class="getCategoryIcon(category)" class="text-3xl text-gray-300 mb-2"></i>
                                <p class="text-sm text-gray-500">No items found</p>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div v-if="categoryData?.links && categoryData.data.length > 0"
                            class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                            <div class="flex items-center text-xs text-gray-500">
                                <select v-model="forms[category].perPage"
                                    class="px-2 py-1 border border-gray-200 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="15">15</option>
                                    <option value="20">20</option>
                                </select>
                                <span class="ml-2">per page</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <template v-for="(link, i) in categoryData.links" :key="i">
                                    <button @click="changePage(category, link.url)" :disabled="!link.url"
                                        class="px-2 py-1 text-xs rounded transition-colors duration-200" :class="{
                                            'bg-blue-500 text-white': link.active,
                                            'text-gray-600 hover:bg-gray-100': !link.active && link.url,
                                            'text-gray-400 cursor-not-allowed': !link.url && link.label === '...',
                                        }">
                                        <span v-html="link.label"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Empty State jika tidak ada kategori sama sekali -->
        <div v-if="!settingsData || Object.keys(settingsData).length === 0" class="text-center py-12">
            <i class="fas fa-cog text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">No Settings Available</h3>
            <p class="text-gray-500">There are no settings categories available at the moment.</p>
        </div>
    </div>
</template>