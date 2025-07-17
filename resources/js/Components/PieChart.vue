<script setup>
import { onMounted, ref, watch, onUnmounted } from 'vue';
import Chart from 'chart.js/auto';

// Define props that the component accepts
const props = defineProps({
  chartData: {
    type: Object,
    required: true
  },
  options: {
    type: Object,
    default: () => ({})
  }
});

const chartContainer = ref(null);
let chartInstance = null;

// Default options for the pie chart
const defaultOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'top',
    },
    tooltip: {
      enabled: true,
    }
  }
};

// Function to render or update the chart
const renderChart = () => {
  // Destroy the old chart instance if it exists
  if (chartInstance) {
    chartInstance.destroy();
  }

  // Render the new chart if the canvas element and data are available
  if (chartContainer.value && props.chartData && props.chartData.labels && props.chartData.labels.length) {
    const ctx = chartContainer.value.getContext('2d');
    chartInstance = new Chart(ctx, {
      type: 'pie',
      data: props.chartData,
      options: { ...defaultOptions, ...props.options }
    });
  }
};

// Render the chart when the component is mounted
onMounted(() => {
  renderChart();
});

// Watch for changes in chartData and re-render the chart
watch(() => props.chartData, (newData) => {
  if (newData) {
    renderChart();
  }
}, { deep: true });

// Clean up the chart instance when the component is unmounted
onUnmounted(() => {
  if (chartInstance) {
    chartInstance.destroy();
  }
});
</script>

<template>
  <div class="chart-container" style="position: relative; height:300px; width:100%;">
    <canvas ref="chartContainer"></canvas>
  </div>
</template>
