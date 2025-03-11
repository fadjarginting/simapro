<template>
  <div>
    <canvas ref="barChart"></canvas>
  </div>
</template>

<script>
import { Chart, registerables } from "chart.js";

Chart.register(...registerables);

export default {
  props: {
    chartData: Array,
  },
  mounted() {
    new Chart(this.$refs.barChart, {
      type: "bar",
      data: {
        labels: this.chartData.map((d) => d.name),
        datasets: [
          {
            label: "Total Work by Plant",
            data: this.chartData.map((d) => d.value),
            backgroundColor: "#3498db",
          },
        ],
      },
    });
  },
};
</script>
