<script setup>
import { computed } from 'vue';

const props = defineProps({
    work: Object,
});

// Utility functions
const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString("id-ID", {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const metadataItems = computed(() => [
    {
        label: 'Dibuat oleh',
        value: props.work.creator?.name || 'Tidak diketahui',
        icon: 'fas fa-user-tie',
        color: 'text-blue-600 bg-blue-100'
    },
    {
        label: 'Dibuat pada',
        value: formatDate(props.work.created_at),
        icon: 'fas fa-calendar-plus',
        color: 'text-green-600 bg-green-100'
    },
    {
        label: 'Terakhir diperbarui pada',
        value: formatDate(props.work.updated_at),
        icon: 'fas fa-calendar-check',
        color: 'text-purple-600 bg-purple-100'
    }
]);
</script>

<template>
    <!-- Metadata -->
    <div class="bg-gradient-to-br from-blue-50 via-white to-purple-50 border rounded-2xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="border-b p-4 bg-gradient-to-r from-blue-100 via-white to-purple-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow">
                    <i class="fas fa-info-circle text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 tracking-tight">
                        Info Tambahan
                    </h2>
                    <p class="text-xs text-gray-500">Informasi sistem terkait pekerjaan ini.</p>
                </div>
            </div>
        </div>

        <!-- Content Body -->
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div v-for="item in metadataItems" :key="item.label"
                    class="bg-white/60 p-3 rounded-lg shadow-sm flex items-center gap-3 hover:shadow-md transition-shadow duration-200">
                    <div :class="item.color" class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0">
                        <i :class="item.icon" class="text-base"></i>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500">{{ item.label }}</p>
                        <p class="text-sm font-semibold text-gray-800 truncate" :title="item.value">{{ item.value }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
