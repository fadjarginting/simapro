<script setup>
import { computed } from 'vue';
const props = defineProps({
    work: Object,
    disciplines: {
        type: Array,
        default: () => [
            { name: 'Civil', progress: 75, color: 'bg-blue-600' },
            { name: 'Mechanical', progress: 60, color: 'bg-green-600' },
            { name: 'Electrical', progress: 85, color: 'bg-yellow-600' },
            { name: 'Instrumentation', progress: 45, color: 'bg-purple-600' },
            { name: 'Piping', progress: 90, color: 'bg-red-600' },
            { name: 'HSE', progress: 70, color: 'bg-orange-600' }
        ]
    }
});

// Calculate overall progress from disciplines
const overallProgress = computed(() => {
    if (!props.disciplines.length) return 0;
    const total = props.disciplines.reduce((sum, discipline) => sum + discipline.progress, 0);
    return Math.round(total / props.disciplines.length);
});
</script>

<template>
    <!-- Progress per Disiplin -->
    <div class="bg-white shadow-sm border border-gray-200 sm:rounded-lg">
        <div class="px-4 py-4 sm:px-6 sm:py-5">
            <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">
                Progress per Disiplin
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div v-for="discipline in disciplines" :key="discipline.name" class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                    <!-- Circular Progress -->
                    <div class="relative w-12 h-12 flex-shrink-0">
                        <svg class="w-12 h-12 transform -rotate-90" viewBox="0 0 36 36">
                            <!-- Background circle -->
                            <path
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                fill="none"
                                stroke="#e5e7eb"
                                stroke-width="2"
                            />
                            <!-- Progress circle -->
                            <path
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                fill="none"
                                :stroke="discipline.color.replace('bg-', '#').replace('-600', '')"
                                stroke-width="2"
                                :stroke-dasharray="`${discipline.progress}, 100`"
                                stroke-linecap="round"
                            />
                        </svg>
                        <!-- Percentage text -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-xs font-medium text-gray-700">{{ discipline.progress }}%</span>
                        </div>
                    </div>
                    <!-- Discipline name -->
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-medium text-gray-700 truncate">{{ discipline.name }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Overall Progress -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex items-center justify-center space-x-3">
                    <div class="relative w-16 h-16 flex-shrink-0">
                        <svg class="w-16 h-16 transform -rotate-90" viewBox="0 0 36 36">
                            <!-- Background circle -->
                            <path
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                fill="none"
                                stroke="#e5e7eb"
                                stroke-width="3"
                            />
                            <!-- Progress circle with gradient -->
                            <path
                                d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                fill="none"
                                stroke="#3b82f6"
                                stroke-width="3"
                                :stroke-dasharray="`${overallProgress}, 100`"
                                stroke-linecap="round"
                            />
                        </svg>
                        <!-- Percentage text -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-sm font-bold text-gray-900">{{ overallProgress }}%</span>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <span class="text-sm font-semibold text-gray-900">Progress Keseluruhan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>