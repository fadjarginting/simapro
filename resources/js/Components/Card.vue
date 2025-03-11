<template>
  <div class="bg-white p-4 shadow-md rounded-lg text-center">
    <h2 class="text-lg font-bold">{{ title }}</h2>
    <p class="text-2xl font-semibold">{{ value }}</p>
    <slot></slot>
  </div>
</template>

<script>
export default {
  props: {
    title: String,
    value: Number,
  },
};
</script>
