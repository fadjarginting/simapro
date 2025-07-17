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
  },
  // New prop to accept custom plugins
  plugins: {
    type: Array,
    default: () => []
  }
});

const chartContainer = ref(null);
let chartInstance = null;

// Default options for the bar chart
const defaultOptions = {
  responsive: true,
  maintainAspectRatio: false,
  scales: {
    x: {
      stacked: true,
    },
    y: {
      stacked: true,
      beginAtZero: true,
      ticks: {
        precision: 0
      }
    }
  },
  plugins: {
    legend: {
      position: 'top',
    },
    tooltip: {
      enabled: true,
    }
  },
  interaction: {
    mode: 'index',
    intersect: false,
  },
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
      type: 'bar',
      data: props.chartData,
      // Merge default options with custom options passed via props
      options: { ...defaultOptions, ...props.options },
      // Register any custom plugins passed via props
      plugins: props.plugins
    });
  }
};

// Render the chart when the component is mounted
onMounted(() => {
  renderChart();
});

// Watch for changes in chartData and re-render the chart
watch(() => [props.chartData, props.options, props.plugins], () => {
  renderChart();
}, { deep: true });

// Clean up the chart instance when the component is unmounted
onUnmounted(() => {
  if (chartInstance) {
    chartInstance.destroy();
  }
});
</script>

<template>
  <div class="chart-container" style="position: relative; height:400px; width:100%;">
    <canvas ref="chartContainer"></canvas>
  </div>
</template>
