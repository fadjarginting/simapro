<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
import axios from 'axios';

defineOptions({
    layout: AuthenticatedLayout,
});

const props = defineProps({
    reportData: Object,
});

// State Management
const openAccordion = ref(null);
const isLoading = ref(false);
const searchTerm = ref('');
const selectedFilter = ref('all');
const animatedCards = ref([]);

// Data cache untuk menghindari request berulang
const loadedCardData = ref({});
const loadingCards = ref(new Set());

// Report Cards Configuration
const reportCards = ref([
    {
        name: 'new_works_today',
        title: 'ERF Masuk Hari Ini',
        count: props.reportData.new_works.today,
        apiParams: { cardType: 'new_works', period: 'today' },
        color: 'blue',
        icon: 'fas fa-inbox',
        priority: 'normal',
        columns: [
            { key: 'erf_number', label: 'No. ERF' },
            { key: 'description', label: 'Deskripsi' },
            { key: 'plant.name', label: 'Plant' },
            { key: 'lead_engineer.name', label: 'Lead' },
        ],
    },
    {
        name: 'new_works_this_week',
        title: 'ERF Masuk Minggu Ini',
        count: props.reportData.new_works.this_week,
        apiParams: { cardType: 'new_works', period: 'this_week' },
        color: 'blue',
        icon: 'fas fa-calendar-week',
        priority: 'normal',
        columns: [
            { key: 'erf_number', label: 'No. ERF' },
            { key: 'description', label: 'Deskripsi' },
            { key: 'plant.name', label: 'Plant' },
            { key: 'entry_date', label: 'Tgl. Masuk', isDate: true },
        ],
    },
    {
        name: 'priority_high',
        title: 'Pekerjaan Prioritas HIGH',
        count: props.reportData.priority_high_works,
        apiParams: { cardType: 'priority_high_works' },
        color: 'red',
        icon: 'fas fa-fire',
        priority: 'high',
        columns: [
            { key: 'erf_number', label: 'No. ERF' },
            { key: 'description', label: 'Deskripsi' },
            { key: 'lead_engineer.name', label: 'Lead' },
            { key: 'project_status', label: 'Status' },
        ],
    },
    {
        name: 'finished_today',
        title: 'Pekerjaan Selesai Hari Ini',
        count: props.reportData.finished_works.today,
        apiParams: { cardType: 'finished_works', period: 'today' },
        color: 'green',
        icon: 'fas fa-check-circle',
        priority: 'normal',
        columns: [
            { key: 'erf_number', label: 'No. ERF' },
            { key: 'description', label: 'Deskripsi' },
            { key: 'lead_engineer.name', label: 'Lead' },
            { key: 'executing_actual_date', label: 'Tgl. Selesai', isDate: true },
        ],
    },
    {
        name: 'finished_this_week',
        title: 'Pekerjaan Selesai Minggu Ini',
        count: props.reportData.finished_works.this_week,
        apiParams: { cardType: 'finished_works', period: 'this_week' },
        color: 'green',
        icon: 'fas fa-calendar-check',
        priority: 'normal',
        columns: [
            { key: 'erf_number', label: 'No. ERF' },
            { key: 'description', label: 'Deskripsi' },
            { key: 'lead_engineer.name', label: 'Lead' },
            { key: 'executing_actual_date', label: 'Tgl. Selesai', isDate: true },
        ],
    },
    {
        name: 'needs_assignment',
        title: 'Perlu Penunjukan Lead',
        count: props.reportData.needs_assignment,
        apiParams: { cardType: 'needs_assignment' },
        color: 'purple',
        icon: 'fas fa-user-plus',
        priority: 'medium',
        columns: [
            { key: 'erf_number', label: 'No. ERF' },
            { key: 'description', label: 'Deskripsi' },
            { key: 'plant.name', label: 'Plant' },
            { key: 'entry_date', label: 'Tgl. Masuk', isDate: true },
        ],
    },
    {
        name: 'on_hold',
        title: 'Pekerjaan Status "Hold"',
        count: props.reportData.on_hold_works,
        apiParams: { cardType: 'on_hold_works' },
        color: 'yellow',
        icon: 'fas fa-pause-circle',
        priority: 'medium',
        columns: [
            { key: 'erf_number', label: 'No. ERF' },
            { key: 'description', label: 'Deskripsi' },
            { key: 'lead_engineer.name', label: 'Lead' },
        ],
    },
    {
        name: 'nearing_deadline_exec',
        title: 'Target Selesai <= 2 Minggu',
        count: props.reportData.nearing_deadline_executing,
        apiParams: { cardType: 'nearing_deadline_executing' },
        color: 'orange',
        icon: 'fas fa-hourglass-half',
        priority: 'high',
        columns: [
            { key: 'erf_number', label: 'No. ERF' },
            { key: 'description', label: 'Deskripsi' },
            { key: 'lead_engineer.name', label: 'Lead' },
            { key: 'executing_target_date', label: 'Target', isDate: true },
        ],
    },
    {
        name: 'executing_phase_works',
        title: 'Pekerjaan Fase Eksekusi',
        count: props.reportData.executing_phase_works,
        apiParams: { cardType: 'executing_phase_works' },
        color: 'cyan',
        icon: 'fas fa-cogs',
        priority: 'normal',
        columns: [
            { key: 'erf_number', label: 'No. ERF' },
            { key: 'description', label: 'Deskripsi' },
            { key: 'lead_engineer.name', label: 'Lead' },
            { key: 'executing_start_date', label: 'Tgl. Mulai', isDate: true },
        ],
    },
    {
        name: 'initiating_planning_works',
        title: 'Pekerjaan Fase Inisiasi & Perencanaan',
        count: props.reportData.initiating_planning_works,
        apiParams: { cardType: 'initiating_planning_works' },
        color: 'teal',
        icon: 'fas fa-pencil-ruler',
        priority: 'normal',
        columns: [
            { key: 'erf_number', label: 'No. ERF' },
            { key: 'description', label: 'Deskripsi' },
            { key: 'lead_engineer.name', label: 'Lead' },
            { key: 'current_phase', label: 'Fase' },
        ],
    },
    {
        name: 'nearing_deadline_initiating',
        title: 'Target Inisiasi/Perencanaan <= 2 Minggu',
        count: props.reportData.nearing_deadline_initiating,
        apiParams: { cardType: 'nearing_deadline_initiating' },
        color: 'amber',
        icon: 'fas fa-hourglass-start',
        priority: 'high',
        columns: [
            { key: 'erf_number', label: 'No. ERF' },
            { key: 'description', label: 'Deskripsi' },
            { key: 'lead_engineer.name', label: 'Lead' },
            { key: 'initiating_target_date', label: 'Target', isDate: true },
        ],
    },
    {
        name: 'needs_validation',
        title: 'Perlu Validasi ERF',
        count: props.reportData.needs_validation,
        apiParams: { cardType: 'needs_validation' },
        color: 'indigo',
        icon: 'fas fa-stamp',
        priority: 'medium',
        columns: [
            { key: 'erf_number', label: 'No. ERF' },
            { key: 'description', label: 'Deskripsi' },
            { key: 'lead_engineer.name', label: 'Lead' },
            { key: 'entry_date', label: 'Tgl. Masuk', isDate: true },
        ],
    },
    {
        name: 'needs_eat',
        title: 'Perlu Dibuatkan EAT',
        count: props.reportData.needs_eat,
        apiParams: { cardType: 'needs_eat' },
        color: 'lime',
        icon: 'fas fa-file-signature',
        priority: 'medium',
        columns: [
            { key: 'erf_number', label: 'No. ERF' },
            { key: 'description', label: 'Deskripsi' },
            { key: 'lead_engineer.name', label: 'Lead' },
            { key: 'erf_validated_date', label: 'Tgl. Validasi', isDate: true },
        ],
    },
    {
        name: 'needs_eat_approval',
        title: 'EAT Perlu Approval',
        count: props.reportData.needs_eat_approval,
        apiParams: { cardType: 'needs_eat_approval' },
        color: 'pink',
        icon: 'fas fa-thumbs-up',
        priority: 'medium',
        columns: [
            { key: 'erf_number', label: 'No. ERF' },
            { key: 'description', label: 'Deskripsi' },
            { key: 'lead_engineer.name', label: 'Lead' },
            { key: 'initiating_target_date', label: 'Target Inisiasi', isDate: true },
        ],
    },
]);

// Load data ketika accordion dibuka
const toggleAccordion = async (cardName) => {
    const isOpening = openAccordion.value !== cardName;
    openAccordion.value = isOpening ? cardName : null;

    // Jika membuka accordion dan data belum di-load
    if (isOpening && !loadedCardData.value[cardName]) {
        await loadCardData(cardName);
    }
};

// Fungsi untuk load data dari API
const loadCardData = async (cardName) => {
    const card = reportCards.value.find(c => c.name === cardName);
    if (!card || loadingCards.value.has(cardName)) return;

    loadingCards.value.add(cardName);

    try {
        const params = new URLSearchParams(card.apiParams);
        const response = await axios.get(`/morning-report/load/${card.apiParams.cardType}?${params}`);

        // Simpan data ke cache
        loadedCardData.value[cardName] = response.data;
    } catch (error) {
        console.error(`Error loading data for ${cardName}:`, error);
        loadedCardData.value[cardName] = [];
    } finally {
        loadingCards.value.delete(cardName);
    }
};

// Get data untuk card tertentu
const getCardData = (cardName) => {
    return loadedCardData.value[cardName] || [];
};

// Check apakah card sedang loading
const isCardLoading = (cardName) => {
    return loadingCards.value.has(cardName);
};

// Filtered cards
const filteredCards = computed(() => {
    let filtered = reportCards.value;

    if (selectedFilter.value !== 'all') {
        filtered = filtered.filter(card => card.priority === selectedFilter.value);
    }

    if (searchTerm.value) {
        filtered = filtered.filter(card =>
            card.title.toLowerCase().includes(searchTerm.value.toLowerCase())
        );
    }

    return filtered.filter(card => card.count > 0);
});

// Total statistics
const totalStats = computed(() => {
    const allCards = reportCards.value;
    const highPriorityCards = allCards.filter(card => card.priority === 'high');
    const mediumPriorityCards = allCards.filter(card => card.priority === 'medium');
    const normalPriorityCards = allCards.filter(card => card.priority === 'normal');

    return {
        total: allCards.reduce((sum, card) => sum + card.count, 0),
        high: highPriorityCards.reduce((sum, card) => sum + card.count, 0),
        medium: mediumPriorityCards.reduce((sum, card) => sum + card.count, 0),
        normal: normalPriorityCards.reduce((sum, card) => sum + card.count, 0),
    };
});

// Helper functions
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(new Date(dateString));
};

const getNestedValue = (obj, path) => {
    if (!path) return obj;
    return path.split('.').reduce((value, key) => value && value[key], obj);
};

const getPriorityColor = (priority) => {
    switch (priority) {
        case 'high': return 'bg-red-100 text-red-800 border-red-200';
        case 'medium': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
        default: return 'bg-blue-100 text-blue-800 border-blue-200';
    }
};

const formatPriority = (priority) => {
    switch (priority) {
        case 'high': return 'Tinggi';
        case 'medium': return 'Sedang';
        default: return 'Normal';
    }
};

const getTextColorClass = (color) => {
    const colorMap = {
        blue: 'text-blue-600', red: 'text-red-600', green: 'text-green-600',
        purple: 'text-purple-600', yellow: 'text-yellow-600', orange: 'text-orange-600',
        cyan: 'text-cyan-600', teal: 'text-teal-600', amber: 'text-amber-600',
        indigo: 'text-indigo-600', lime: 'text-lime-600', pink: 'text-pink-600',
    };
    return colorMap[color] || 'text-gray-600';
};

const getIconBgClass = (color) => {
    const colorMap = {
        blue: 'bg-blue-100 text-blue-600', red: 'bg-red-100 text-red-600',
        green: 'bg-green-100 text-green-600', purple: 'bg-purple-100 text-purple-600',
        yellow: 'bg-yellow-100 text-yellow-600', orange: 'bg-orange-100 text-orange-600',
        cyan: 'bg-cyan-100 text-cyan-600', teal: 'bg-teal-100 text-teal-600',
        amber: 'bg-amber-100 text-amber-600', indigo: 'bg-indigo-100 text-indigo-600',
        lime: 'bg-lime-100 text-lime-600', pink: 'bg-pink-100 text-pink-600',
    };
    return colorMap[color] || 'bg-gray-100 text-gray-600';
};

// Animation on mount
onMounted(() => {
    reportCards.value.forEach((card, index) => {
        setTimeout(() => {
            animatedCards.value.push(card.name);
        }, index * 50);
    });
});
</script>

<template>

    <Head title="Laporan Pagi" />

    <div class="min-h-screen bg-gray-50">
        <div class="py-6">
            <div class="mx-auto sm:px-6 lg:px-8 max-w-7xl">
                <!-- Header dengan Statistik -->
                <div class="mb-8 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b bg-gray-50">
                        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center shadow-md">
                                    <i class="fas fa-building-columns text-white text-xl"></i>
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-800">Laporan Pagi Engineering</h1>
                                    <p class="text-gray-500 text-sm">{{ new Date().toLocaleDateString('id-ID', {
                                        weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                                    }) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-gray-200">
                        <div class="bg-white p-4 text-center">
                            <div class="text-2xl font-bold text-gray-800">{{ totalStats.total }}</div>
                            <div class="text-sm text-gray-500">Total Pekerjaan</div>
                        </div>
                        <div class="bg-white p-4 text-center">
                            <div class="text-2xl font-bold text-red-600">{{ totalStats.high }}</div>
                            <div class="text-sm text-gray-500">Prioritas Tinggi</div>
                        </div>
                        <div class="bg-white p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ totalStats.medium }}</div>
                            <div class="text-sm text-gray-500">Prioritas Sedang</div>
                        </div>
                        <div class="bg-white p-4 text-center">
                            <div class="text-2xl font-bold text-green-600">{{ totalStats.normal }}</div>
                            <div class="text-sm text-gray-500">Normal</div>
                        </div>
                    </div>
                </div>

                <!-- Filter dan Search -->
                <div class="mb-6 bg-white border border-gray-200 rounded-2xl shadow-sm p-4">
                    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
                        <div class="relative flex-1 w-full max-w-md">
                            <input v-model="searchTerm" type="text" placeholder="Cari laporan..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>

                        <div class="flex gap-2 flex-wrap justify-center">
                            <button @click="selectedFilter = 'all'"
                                :class="selectedFilter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all">
                                Semua
                            </button>
                            <button @click="selectedFilter = 'high'"
                                :class="selectedFilter === 'high' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all">
                                Tinggi
                            </button>
                            <button @click="selectedFilter = 'medium'"
                                :class="selectedFilter === 'medium' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all">
                                Sedang
                            </button>
                            <button @click="selectedFilter = 'normal'"
                                :class="selectedFilter === 'normal' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                class="px-4 py-1.5 rounded-lg text-sm font-medium transition-all">
                                Normal
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Konten Utama -->
                <div class="space-y-4">
                    <div v-for="(card, index) in filteredCards" :key="card.name" class="transition-all duration-500"
                        :class="animatedCards.includes(card.name) ? 'translate-y-0 opacity-100' : 'translate-y-8 opacity-0'"
                        :style="{ transitionDelay: `${index * 50}ms` }">
                        <div
                            class="bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md overflow-hidden transition-all duration-300">

                            <!-- Header Accordion -->
                            <div @click="toggleAccordion(card.name)"
                                class="p-4 cursor-pointer transition-all duration-300 hover:bg-gray-50">
                                <div class="flex justify-between items-center">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                            :class="getIconBgClass(card.color)">
                                            <i :class="[card.icon, 'text-lg']"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-md font-bold text-gray-800">{{ card.title }}</h3>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full border"
                                                    :class="getPriorityColor(card.priority)">
                                                    {{ formatPriority(card.priority) }}
                                                </span>
                                                <span v-if="!loadedCardData[card.name] && openAccordion === card.name"
                                                    class="text-xs text-gray-500 italic">
                                                    Klik untuk memuat data
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="text-center">
                                            <div class="text-2xl font-bold" :class="getTextColorClass(card.color)">
                                                {{ card.count }}
                                            </div>
                                            <div class="text-xs text-gray-500">Item</div>
                                        </div>
                                        <div
                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 group-hover:bg-gray-200 transition-colors">
                                            <i class="fas fa-chevron-down text-gray-500 transition-transform duration-300"
                                                :class="{ 'rotate-180': openAccordion === card.name }"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Konten Accordion (Tabel) -->
                            <div v-show="openAccordion === card.name" class="bg-gray-50/50">
                                <!-- Loading State -->
                                <div v-if="isCardLoading(card.name)" class="text-center py-12">
                                    <div
                                        class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto">
                                    </div>
                                    <p class="mt-4 text-gray-600">Memuat data...</p>
                                </div>

                                <!-- Data Table -->
                                <div v-else-if="getCardData(card.name).length > 0" class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead class="bg-gray-100">
                                            <tr class="border-b border-gray-200">
                                                <th v-for="col in card.columns" :key="col.key"
                                                    class="px-4 py-2 text-left font-semibold text-gray-600">
                                                    {{ col.label }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            <tr v-for="(item, itemIndex) in getCardData(card.name)" :key="item.id"
                                                class="hover:bg-white transition-colors duration-200"
                                                :style="{ animationDelay: `${itemIndex * 50}ms` }">
                                                <td v-for="col in card.columns" :key="col.key"
                                                    class="px-4 py-3 text-gray-700">
                                                    <div v-if="col.isDate" class="flex items-center gap-2">
                                                        <i class="fas fa-calendar-alt text-gray-400"></i>
                                                        <span class="font-medium">{{ formatDate(getNestedValue(item,
                                                            col.key)) }}</span>
                                                    </div>
                                                    <div v-else-if="col.key === 'erf_number'"
                                                        class="font-mono font-bold text-blue-600">
                                                        {{ getNestedValue(item, col.key) || 'TBD' }}
                                                    </div>
                                                    <div v-else-if="col.key === 'project_status' || col.key === 'current_phase'"
                                                        class="inline-flex items-center">
                                                        <span
                                                            class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                                            {{ getNestedValue(item, col.key) || 'TBD' }}
                                                        </span>
                                                    </div>
                                                    <div v-else class="truncate max-w-xs"
                                                        :title="getNestedValue(item, col.key)">
                                                        {{ getNestedValue(item, col.key) || 'TBD' }}
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Empty State -->
                                <div v-else class="text-center py-12">
                                    <div
                                        class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-box-open text-gray-400 text-3xl"></i>
                                    </div>
                                    <p class="text-gray-500">Tidak ada data untuk ditampilkan</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500">
                        Data diperbarui pada: {{ new Date().toLocaleTimeString('id-ID') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Smooth scrolling for accordion */
[v-show] {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

/* Custom scrollbar for tables */
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
/* Animation for table rows */
tbody tr {
    opacity: 0;
    transform: translateY(10px);
    animation: fadeInUp 0.3s forwards;
}
@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
