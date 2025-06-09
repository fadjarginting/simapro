<script setup>
import { ref, computed, onMounted, reactive } from 'vue';

const props = defineProps({
    work: Object,
});

// EAT data structure
const eatData = ref({
    erf: 'PAD0/99/A2/EM/060/OE/2025',
    title: 'Kajian Penanggulangan Panas Akibat Matahari di Jendela Kaca Sisi Barat Lobby Utama SPH',
    type: 'FEED/DED',
    totalDuration: 90,
    startDate: '2024-11-01',
    endDate: '2025-01-30',
    notes: [
        'Tgl. efektif pelaksanaan pekerjaan adalah sama dengan tgl. approval EAT.',
        'Jika tgl. approval EAT berbeda dengan yang ada dalam skedul ini, maka dalam file monitoring progress, tgl. mulai pekerjaan dan akhir pekerjaan mengacu pada tgl. approval EAT dan durasi pekerjaan.',
        'Durasi pekerjaan sudah termasuk waktu approval engineering document melalui E-DEMS.'
    ],
    activities: [
        {
            no: 1,
            name: 'Scope Disiplin Proses',
            color: 'bg-blue-500',
            tasks: [
                { name: 'Flowsheet', pic: 'SDN', duration: 5, startDate: '2024-11-01', endDate: '2024-11-06', progress: 80 },
                { name: 'Narasi Proses', pic: 'SDN', duration: 5, startDate: '2024-11-07', endDate: '2024-11-12', progress: 60 }
            ]
        },
        {
            no: 2,
            name: 'Scope Disiplin Mekanikal',
            color: 'bg-green-500',
            tasks: [
                { name: 'GA (General Arrangement)', pic: 'KAF, ZAK', duration: 15, startDate: '2024-11-13', endDate: '2024-11-28', progress: 40 },
                { name: 'Drawing', pic: 'KAF, ZAK', duration: 15, startDate: '2024-11-29', endDate: '2024-12-14', progress: 20 },
                { name: 'BOQ', pic: 'KAF, ZAK', duration: 5, startDate: '2024-12-15', endDate: '2024-12-20', progress: 0 },
                { name: 'HPP', pic: 'KAF, ZAK', duration: 5, startDate: '2024-12-21', endDate: '2024-12-26', progress: 0 },
                { name: 'Draft PPK Mekanikal', pic: 'KAF, ZAK', duration: 5, startDate: '2024-12-27', endDate: '2025-01-01', progress: 0 }
            ]
        },
        {
            no: 3,
            name: 'Scope Disiplin Sipil',
            color: 'bg-orange-500',
            tasks: [
                { name: 'Drawing', pic: 'DCH, ZFR', duration: 25, startDate: '2024-11-29', endDate: '2024-12-24', progress: 10 },
                { name: 'BOQ', pic: 'DCH, ZFR', duration: 5, startDate: '2024-12-25', endDate: '2024-12-30', progress: 0 },
                { name: 'HPP', pic: 'DCH, ZFR', duration: 5, startDate: '2024-12-31', endDate: '2025-01-05', progress: 0 },
                { name: 'Draft PPK Sipil', pic: 'DCH, ZFR', duration: 5, startDate: '2025-01-06', endDate: '2025-01-11', progress: 0 }
            ]
        },
        {
            no: 4,
            name: 'Scope Disiplin Elins',
            color: 'bg-purple-500',
            tasks: [
                { name: 'Drawing', pic: 'EV, AS', duration: 25, startDate: '2024-11-29', endDate: '2024-12-24', progress: 10 },
                { name: 'BOQ', pic: 'EV, AS', duration: 5, startDate: '2024-12-25', endDate: '2024-12-30', progress: 0 },
                { name: 'Material List', pic: 'EV, AS', duration: 5, startDate: '2024-12-31', endDate: '2025-01-05', progress: 0 },
                { name: 'HPP', pic: 'EV, AS', duration: 5, startDate: '2025-01-06', endDate: '2025-01-11', progress: 0 },
                { name: 'Draft PPK Elins', pic: 'EV, AS', duration: 5, startDate: '2025-01-12', endDate: '2025-01-17', progress: 0 }
            ]
        },
        {
            no: 5,
            name: 'Scope Multi Disiplin',
            color: 'bg-indigo-500',
            tasks: [
                { name: 'BOQ Multi Disiplin', pic: 'KAF, ZAK', duration: 5, startDate: '2025-01-18', endDate: '2025-01-23', progress: 0 },
                { name: 'HPP Multi Disiplin', pic: 'KAF, ZAK', duration: 5, startDate: '2025-01-18', endDate: '2025-01-23', progress: 0 },
                { name: 'Draft PPK Multi Disiplin', pic: 'KAF, ZAK', duration: 6, startDate: '2025-01-24', endDate: '2025-01-30', progress: 0 }
            ]
        }
    ]
});

// Track expanded sections
const expandedSections = reactive({});

// Format date to display in Indonesian format
const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};

// Check if EAT is configured
const hasEAT = computed(() => {
    return eatData.value !== null;
});

// Functions for EAT setup
const initializeEAT = () => { console.log('Initialize EAT setup'); };
const importEAT = () => { console.log('Import EAT file'); };

// Calculate overall progress
const calculateOverallProgress = () => {
    const allTasks = eatData.value.activities.flatMap(activity => activity.tasks);
    const totalProgress = allTasks.reduce((sum, task) => sum + task.progress, 0);
    return Math.round(totalProgress / allTasks.length);
};

// Get status color based on progress
const getStatusColor = (progress) => {
    if (progress === 0) return 'bg-gray-200 text-gray-600';
    if (progress < 50) return 'bg-red-100 text-red-700';
    if (progress < 80) return 'bg-yellow-100 text-yellow-700';
    return 'bg-green-100 text-green-700';
};

// Get status text based on progress
const getStatusText = (progress) => {
    if (progress === 0) return 'Belum Mulai';
    if (progress < 50) return 'Terlambat';
    if (progress < 80) return 'Dalam Progress';
    return 'Hampir Selesai';
};

// Toggle section expansion
const toggleSection = (sectionId) => {
    expandedSections[sectionId] = !expandedSections[sectionId];
};

// Calculate activity average progress
const activityProgress = (activity) => {
    return Math.round(activity.tasks.reduce((sum, task) => sum + task.progress, 0) / activity.tasks.length);
};

// Calculate task statistics
const taskStats = computed(() => {
    const tasks = eatData.value.activities.flatMap(a => a.tasks);
    return {
        started: tasks.filter(t => t.progress > 0).length,
        inProgress: tasks.filter(t => t.progress > 0 && t.progress < 100).length,
        waiting: tasks.filter(t => t.progress === 0).length
    };
});
</script>
<template>
    <div class="bg-white rounded-xl shadow p-4 md:p-6">
        <!-- Empty State - No EAT Configured -->
        <div v-if="!hasEAT" class="py-12 flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-calendar-alt text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-base font-semibold text-gray-900 mb-1">
                EAT Belum Dikonfigurasi
            </h3>
            <p class="text-gray-500 mb-4 text-center max-w-xs">
                EAT belum diatur untuk pekerjaan ini.
                Silakan buat EAT baru atau impor dari file yang sudah ada.
            </p>
            <div class="flex gap-2">
                <button @click="initializeEAT" :disabled="!props.work.lead_engineer"
                    :title="!props.work.lead_engineer ? 'Harap tetapkan Lead Engineer terlebih dahulu' : ''"
                    :class="`inline-flex items-center px-3 py-1.5 text-xs font-medium ${props.work.lead_engineer ? 'text-white bg-blue-600 hover:bg-blue-700' : 'text-gray-500 bg-gray-200 cursor-not-allowed'}`">
                    <i class="fas fa-plus mr-1"></i>
                    Buat EAT Baru
                </button>
            </div>
        </div>

        <!-- EAT Content (when hasEAT is true) -->
        <div v-else>
            <!-- Header -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-blue-500 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 leading-tight">Estimated Actual Time - Project
                            Planning & Approval System</h2>
                        <p class="text-xs text-gray-500">{{ eatData.erf }}</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button
                        class="flex items-center gap-1 px-3 py-1.5 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                        <i class="fas fa-download"></i>
                        Export
                    </button>
                    <button
                        class="flex items-center gap-1 px-3 py-1.5 bg-green-500 text-white text-xs rounded hover:bg-green-600">
                        <i class="fas fa-edit"></i>
                        Edit
                    </button>
                </div>
            </div>

            <div class="mb-2">
                <div class="text-base font-semibold text-gray-800">{{ eatData.title }}</div>
            </div>

            <!-- Project Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                <div class="bg-blue-50 rounded-lg p-3 text-blue-700 flex flex-col items-start">
                    <div class="flex items-center gap-1 mb-1">
                        <i class="fas fa-calendar text-blue-400"></i>
                        <span class="text-xs font-medium">Durasi Total</span>
                    </div>
                    <div class="text-lg font-bold">{{ eatData.totalDuration }}</div>
                    <div class="text-xs text-blue-400">hari kalender</div>
                </div>
                <div class="bg-green-50 rounded-lg p-3 text-green-700 flex flex-col items-start">
                    <div class="flex items-center gap-1 mb-1">
                        <i class="fas fa-clock text-green-400"></i>
                        <span class="text-xs font-medium">Progress</span>
                    </div>
                    <div class="text-lg font-bold">{{ calculateOverallProgress() }}%</div>
                    <div class="text-xs text-green-400">keseluruhan</div>
                </div>
                <div class="bg-white border rounded-lg p-3">
                    <div class="text-xs text-gray-500 mb-1">Mulai</div>
                    <div class="text-base font-bold text-gray-800">{{ formatDate(eatData.startDate) }}</div>
                </div>
                <div class="bg-white border rounded-lg p-3">
                    <div class="text-xs text-gray-500 mb-1">Selesai</div>
                    <div class="text-base font-bold text-gray-800">{{ formatDate(eatData.endDate) }}</div>
                </div>
            </div>

            <!-- Activities -->
            <div class="space-y-2">
                <div v-for="activity in eatData.activities" :key="activity.no"
                    class="bg-gray-50 rounded-lg overflow-hidden border">
                    <!-- Activity Header -->
                    <div class="p-3 cursor-pointer hover:bg-gray-100 transition" @click="toggleSection(activity.no)">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div
                                    :class="`w-7 h-7 ${activity.color} rounded flex items-center justify-center text-white font-bold text-xs`">
                                    {{ activity.no }}
                                </div>
                                <span class="font-medium text-gray-800">{{ activity.name }}</span>
                                <span class="text-xs text-gray-400">({{ activity.tasks.length }} tasks)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500">
                                    {{ activityProgress(activity) }}% selesai
                                </span>
                                <i v-if="expandedSections[activity.no]" class="fas fa-chevron-down text-gray-400"></i>
                                <i v-else class="fas fa-chevron-right text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks -->
                    <div v-if="expandedSections[activity.no]" class="border-t bg-white">
                        <div v-for="(task, index) in activity.tasks" :key="index"
                            class="p-3 border-b last:border-b-0 hover:bg-gray-50 transition">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-medium text-gray-800">{{ task.name }}</span>
                                        <span
                                            :class="`px-2 py-0.5 rounded-full text-xs font-medium ${getStatusColor(task.progress)}`">
                                            {{ getStatusText(task.progress) }}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500">
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-users"></i>
                                            <span>{{ task.pic }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-calendar"></i>
                                            <span>{{ formatDate(task.startDate) }} - {{ formatDate(task.endDate)
                                                }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <i class="fas fa-clock"></i>
                                            <span>{{ task.duration }} hari</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 min-w-[90px]">
                                    <div class="text-right">
                                        <div class="text-base font-bold text-gray-800">{{ task.progress }}%</div>
                                        <div class="w-20 h-2 bg-gray-200 rounded-full overflow-hidden">
                                            <div :class="`h-full ${activity.color} transition-all duration-500`"
                                                :style="{ width: `${task.progress}%` }"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="mt-4 bg-gray-50 rounded-lg p-4">
                <h3 class="text-base font-semibold text-gray-800 mb-2">Ringkasan Progress</h3>
                <div class="grid grid-cols-3 gap-2">
                    <div class="text-center p-2 bg-green-100 rounded">
                        <div class="text-lg font-bold text-green-700">
                            {{ taskStats.started }}
                        </div>
                        <div class="text-xs text-green-700 font-medium">Tasks Dimulai</div>
                    </div>
                    <div class="text-center p-2 bg-blue-100 rounded">
                        <div class="text-lg font-bold text-blue-700">
                            {{ taskStats.inProgress }}
                        </div>
                        <div class="text-xs text-blue-700 font-medium">Tasks Berjalan</div>
                    </div>
                    <div class="text-center p-2 bg-gray-100 rounded">
                        <div class="text-lg font-bold text-gray-600">
                            {{ taskStats.waiting }}
                        </div>
                        <div class="text-xs text-gray-700 font-medium">Tasks Menunggu</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
