<script setup>
const props = defineProps({
    category: Object,
    depth: Number,
    form: Object
});
</script>

<template>
    <option v-if="category" :value="category.id" >
        {{ '&nbsp;'.repeat(depth * 4) + category.name }}
    </option>
    <template v-if="category && category.children && category.children.length">
        <FilterSelect v-for="child in category.children" :key="child.id" :category="child" :depth="depth + 1"
            :form="form" />
    </template>
</template>
