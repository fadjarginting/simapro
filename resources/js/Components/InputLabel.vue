<script setup>
defineProps({
    value: {
        type: String,
    },
});
</script>

<template>
    <label class="mb-2 ml-1 font-bold text-xs text-slate-700">
        <span v-if="value">{{ value }}</span>
        <span v-if="$attrs.required" class="text-red-500 ml-1">*</span>

        <span v-else><slot /></span>
    </label>
</template>
