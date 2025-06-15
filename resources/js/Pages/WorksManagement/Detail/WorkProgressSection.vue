<script setup>
const props = defineProps({
    // Data history yang berisi objek dengan id, pesan, dan timestamp
    history: {
        type: Array,
        default: () => [
            { id: 1, message: 'Work progress updated to 60%', timestamp: '2023-10-07 10:15:00' },
            { id: 2, message: 'Discipline Mechanical changed', timestamp: '2023-10-07 09:30:00' }
        ]
    }
});
</script>

<template>
    <div class="bg-white shadow-sm border border-gray-200 sm:rounded-lg">
        <div class="px-4 py-4 sm:px-6 sm:py-5">
            <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">
                History Perubahan
            </h3>
            <ul class="divide-y divide-gray-200">
                <li v-for="entry in history" :key="entry.id" class="py-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-900 truncate">{{ entry.message }}</span>
                        <span class="text-xs text-gray-500">{{ entry.timestamp }}</span>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>
