<template>
  <div>
    <canvas ref="lineChart"></canvas>
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
    new Chart(this.$refs.lineChart, {
      type: "line",
      data: {
        labels: this.chartData.map((d) => d.name),
        datasets: [
          {
            label: "Total Work",
            data: this.chartData.map((d) => d.value),
            borderColor: "#4CAF50",
            fill: false,
          },
        ],
      },
    });
  },
};
</script>
