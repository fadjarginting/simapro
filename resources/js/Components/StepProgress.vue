<template>
    <div class="flex items-center justify-between mb-8">
        <div v-for="(step, index) in steps" :key="index" class="flex items-center">
            <div class="flex items-center">
                <div :class="getStepClass(index)">
                    {{ index + 1 }}
                </div>
                <div class="ml-3">
                    <div class="font-medium text-gray-900">{{ step.title }}</div>
                </div>
            </div>
            <div v-if="index < steps.length - 1" class="w-16 h-px bg-gray-300 mx-4"></div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    steps: Array,
    currentStep: Number
});

const getStepClass = (index) => {
    const baseClass = 'w-10 h-10 rounded-full flex items-center justify-center font-bold text-white';

    if (props.currentStep > index) {
        return `${baseClass} bg-green-500`;
    } else if (props.currentStep === index) {
        return `${baseClass} bg-blue-500`;
    } else {
        return `${baseClass} bg-gray-400`;
    }
};
</script>