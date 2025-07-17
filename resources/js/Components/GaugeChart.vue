<script setup>
import { ref, onMounted, watch } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
    percentage: { type: Number, required: true, default: 0 },
    target: { type: Number, default: 85 },
});

const chartCanvas = ref(null);
let chartInstance = null;

const renderChart = () => {
    if (chartInstance) {
        chartInstance.destroy();
    }
    if (!chartCanvas.value) return;

    const percentage = props.percentage;
    const target = props.target;

    // Plugin to draw the percentage text in the center
    const centerTextPlugin = {
        id: 'gaugeCenterText',
        afterDraw: (chart) => {
            const { ctx, chartArea: { width, height } } = chart;
            ctx.save();
            ctx.font = `bold ${height / 4}px Arial`;
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = '#92d050';
            const text = `${percentage.toFixed(2)}%`;
            ctx.fillText(text, width / 2, height * 0.9);
            ctx.restore();
        }
    };

    // Plugin to draw the target line and text
    const targetLinePlugin = {
        id: 'gaugeTargetLine',
        afterDatasetsDraw: (chart) => {
            const { ctx, chartArea } = chart;
            const meta = chart.getDatasetMeta(0);
            const r = (meta.data[0].outerRadius + meta.data[0].innerRadius) / 2;
            const angle = Math.PI + (target / 100) * Math.PI;

            const x = chartArea.left + chartArea.width / 2;
            const y = chartArea.top + chartArea.height;

            const xLine = x + r * Math.cos(angle);
            const yLine = y + r * Math.sin(angle);

            // Draw line
            ctx.save();
            ctx.beginPath();
            ctx.lineWidth = 3;
            ctx.strokeStyle = '#000000';
            ctx.moveTo(x, y);
            ctx.lineTo(xLine, yLine);
            ctx.stroke();
            ctx.restore();

            // Draw text
            ctx.save();
            ctx.font = 'bold 12px Arial';
            ctx.fillStyle = '#0070c0';
            ctx.textAlign = xLine > x ? 'left' : 'right';
            ctx.textBaseline = 'middle';
            ctx.fillText(`Target ${target}%`, xLine + (xLine > x ? 5 : -5), yLine);
            ctx.restore();
        }
    }

    chartInstance = new Chart(chartCanvas.value, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [percentage, 100 - percentage],
                backgroundColor: ['#92d050', '#d9d9d9'],
                borderWidth: 0,
                circumference: 180,
                rotation: -90,
                cutout: '75%',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: { enabled: false }
            }
        },
        plugins: [centerTextPlugin, targetLinePlugin]
    });
};

onMounted(renderChart);
watch(() => props.percentage, renderChart);

</script>

<template>
    <div class="relative w-full h-48">
        <canvas ref="chartCanvas"></canvas>
    </div>
</template>
