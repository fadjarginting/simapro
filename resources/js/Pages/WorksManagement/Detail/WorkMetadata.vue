<script setup>
import { computed } from 'vue';

const props = defineProps({
    work: Object,
});

// Utility functions
const formatDate = (date) => {
    if (!date) return null;
    return new Date(date).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const getVerificationClass = (status) => {
    const classes = {
        "Belum Verifikasi": "bg-gray-50 text-gray-600 ring-gray-500/10",
        "Sedang Diverifikasi": "bg-yellow-50 text-yellow-800 ring-yellow-600/20",
        "Sudah Diverifikasi": "bg-green-50 text-green-700 ring-green-600/20",
        Ditolak: "bg-red-50 text-red-700 ring-red-600/20",
    };
    return classes[status] || "bg-gray-50 text-gray-600 ring-gray-500/10";
};

const getStatusClass = (status) => {
    const classes = {
        "Not started": "bg-gray-50 text-gray-600 ring-gray-500/10",
        "In Progress": "bg-yellow-50 text-yellow-800 ring-yellow-600/20",
        Completed: "bg-green-50 text-green-700 ring-green-600/20",
        "On Hold": "bg-orange-50 text-orange-700 ring-orange-600/20",
    };
    return classes[status] || "bg-gray-50 text-gray-600 ring-gray-500/10";
};

const getPhaseClass = (phase) => {
    const classes = {
        "Not started": "bg-gray-50 text-gray-600 ring-gray-500/10",
        Initiating: "bg-blue-50 text-blue-700 ring-blue-600/20",
        Planning: "bg-indigo-50 text-indigo-700 ring-indigo-600/20",
        Executing: "bg-yellow-50 text-yellow-800 ring-yellow-600/20",
        Monitoring: "bg-purple-50 text-purple-700 ring-purple-600/20",
        Closing: "bg-orange-50 text-orange-700 ring-orange-600/20",
        Completed: "bg-green-50 text-green-700 ring-green-600/20",
    };
    return classes[phase] || "bg-gray-50 text-gray-600 ring-gray-500/10";
};
</script>

<template>
    <!-- Metadata -->
    <div class="bg-white shadow-sm border border-gray-200 sm:rounded-lg">
        <div class="px-4 py-4 sm:px-6 sm:py-5">
            <div class="flex items-center justify-between mb-3 lg:mb-4">
                <h3 class="text-base font-semibold leading-6 text-gray-900">
                    Info Tambahan
                </h3>
            </div>
            <dl class="space-y-4">
                <!-- System Information -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Informasi Sistem</h4>
                    <div class="space-y-2">
                        <div>
                            <dt class="text-xs font-medium text-gray-500">
                                Dibuat oleh
                            </dt>
                            <dd class="mt-1 text-xs text-gray-900">
                                {{ work.creator?.name || 'Tidak diketahui' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">
                                Dibuat
                            </dt>
                            <dd class="mt-1 text-xs text-gray-600">
                                {{ formatDate(work.created_at) }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">
                                Diperbarui
                            </dt>
                            <dd class="mt-1 text-xs text-gray-600">
                                {{ formatDate(work.updated_at) }}
                            </dd>
                        </div>
                    </div>
                </div>
            </dl>
        </div>
    </div>
</template>