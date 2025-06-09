<!-- components/WorkDetail/WorkActionButtons.vue -->
<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    work: {
        type: Object,
        required: true
    }
});

// Action button configurations
const actionButtons = [
    {
        label: "Lihat Schedule EAT",
        icon: "fa fa-calendar-days",
        href: null, // Will be computed based on work.id
        color: "blue",
        permission: "view_schedule"
    },
    {
        label: "Update Progress",
        icon: "fa fa-chart-bar",
        href: null, // Will be computed based on work.id
        color: "green",
        permission: "update_progress"
    },
    {
        label: "Update Data Pekerjaan",
        icon: "fa fa-pencil-square",
        href: null, // Will use route('works.edit', work.id)
        color: "yellow",
        permission: "edit_work"
    },
    {
        label: "Laporan Progress",
        icon: "fa fa-clipboard-list",
        href: null, // Will be computed based on work.id
        color: "purple",
        permission: "view_reports"
    }
];


// Color classes mapping
const getColorClasses = (color) => {
    const colorMap = {
        blue: "bg-blue-600 hover:bg-blue-500 focus:ring-blue-500",
        green: "bg-green-600 hover:bg-green-500 focus:ring-green-500",
        yellow: "bg-yellow-600 hover:bg-yellow-500 focus:ring-yellow-500",
        purple: "bg-purple-600 hover:bg-purple-500 focus:ring-purple-500",
        gray: "bg-gray-600 hover:bg-gray-500 focus:ring-gray-500",
        indigo: "bg-indigo-600 hover:bg-indigo-500 focus:ring-indigo-500",
        teal: "bg-teal-600 hover:bg-teal-500 focus:ring-teal-500",
        slate: "bg-slate-600 hover:bg-slate-500 focus:ring-slate-500"
    };
    return colorMap[color] || "bg-gray-600 hover:bg-gray-500 focus:ring-gray-500";
};

// Generate href for buttons based on work data
const getButtonHref = (button) => {
    // switch (button.permission) {
    //     case 'view_schedule':
    //         return route('works.schedule', props.work.id);
    //     case 'update_progress':
    //         return route('works.progress.edit', props.work.id);
    //     case 'edit_work':
    //         return route('works.edit', props.work.id);
    //     case 'view_reports':
    //         return route('works.reports', props.work.id);
    //     case 'export_data':
    //         return route('works.export', { work: props.work.id, format: 'pdf' });
    //     case 'preview_work':
    //         return route('works.preview', props.work.id);
    //     case 'share_work':
    //         return route('works.share', props.work.id);
    //     default:
    //         return '#';
    // }
};

// Check if user has permission (placeholder - implement based on your auth system)
const hasPermission = (permission) => {
    // This should be replaced with your actual permission checking logic
    // For now, returning true for all permissions
    return true;
};

// Handle button click
const handleButtonClick = (button, event) => {
    if (button.onClick) {
        event.preventDefault();
        button.onClick();
    }
};
</script>

<template>
    <div class="mb-6">
        <!-- Primary Action Buttons -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
            <template v-for="button in actionButtons" :key="button.permission">
                <Link
                    v-if="hasPermission(button.permission)"
                    :href="getButtonHref(button)"
                    :class="getColorClasses(button.color)"
                    class="inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                    @click="handleButtonClick(button, $event)"
                >
                    <i :class="button.icon" class="mr-2 text-lg"></i>
                    {{ button.label }}
                </Link>
            </template>
        </div>
    </div>
</template>
