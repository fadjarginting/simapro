<template>
  <div>
    <canvas ref="pieChart"></canvas>
  </div>
</template>

<script>
import { Chart, registerables } from "chart.js";

Chart.register(...registerables);

export default {
  props: {
    chartData: Array,
    colors: Array,
  },
  mounted() {
    new Chart(this.$refs.pieChart, {
      type: "pie",
      data: {
        labels: this.chartData.map((d) => d.name),
        datasets: [
          {
            data: this.chartData.map((d) => d.value),
            backgroundColor: this.colors,
          },
        ],
      },
    });
  },
};
</script>
