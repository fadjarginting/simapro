<script setup>
import { onMounted, ref, watch, onBeforeUnmount } from "vue";
import * as d3 from "d3";

const props = defineProps({
    data: {
        type: Object,
        required: true,
    },
    height: {
        type: Number,
        default: 300,
    },
});

const chartContainer = ref(null);
const chart = ref(null);

const renderChart = () => {
    if (
        !chartContainer.value ||
        !props.data ||
        !props.data.categories ||
        !props.data.series ||
        props.data.series.length === 0
    )
        return;

    // Hapus chart sebelumnya jika ada
    if (chart.value) {
        d3.select(chartContainer.value).selectAll("*").remove();
    }

    const container = chartContainer.value;
    const width = container.clientWidth;
    const height = props.height;
    const margin = { top: 20, right: 30, bottom: 60, left: 60 };
    const innerWidth = width - margin.left - margin.right;
    const innerHeight = height - margin.top - margin.bottom;

    // Persiapkan data untuk d3
    const categories = props.data.categories;
    const series = props.data.series;

    // Buat elemen SVG
    const svg = d3
        .select(container)
        .append("svg")
        .attr("width", width)
        .attr("height", height);

    const g = svg
        .append("g")
        .attr("transform", `translate(${margin.left},${margin.top})`);

    // Skala untuk sumbu x (kategori)
    const x0Scale = d3
        .scaleBand()
        .domain(categories)
        .range([0, innerWidth])
        .paddingInner(0.1)
        .paddingOuter(0.2);

    // Skala untuk grup di dalam kategori
    const x1Scale = d3
        .scaleBand()
        .domain(series.map((d) => d.name))
        .range([0, x0Scale.bandwidth()])
        .padding(0.05);

    // Cari nilai maksimum untuk skala y
    const maxValue = d3.max(series, (s) => d3.max(s.data, (d) => d));

    // Skala untuk sumbu y (nilai)
    const yScale = d3
        .scaleLinear()
        .domain([0, maxValue * 1.1]) // Memberi ruang sedikit di atas
        .range([innerHeight, 0]);

    // Tambahkan sumbu x
    g.append("g")
        .attr("transform", `translate(0,${innerHeight})`)
        .call(d3.axisBottom(x0Scale))
        .selectAll("text")
        .style("text-anchor", "middle");

    // Tambahkan sumbu y
    g.append("g").call(d3.axisLeft(yScale));

    // Tambahkan grid horizontal
    g.append("g")
        .attr("class", "grid")
        .call(d3.axisLeft(yScale).tickSize(-innerWidth).tickFormat(""))
        .selectAll("line")
        .attr("stroke", "#e0e0e0")
        .attr("stroke-dasharray", "5,5");

    // Buat grup untuk setiap kategori
    const categoryGroups = g
        .selectAll(".category-group")
        .data(categories)
        .enter()
        .append("g")
        .attr("class", "category-group")
        .attr("transform", (d) => `translate(${x0Scale(d)},0)`);

    // Untuk setiap seri data
    series.forEach((s) => {
        categoryGroups
            .append("g")
            .selectAll("rect")
            .data((category, i) => [
                {
                    category: category,
                    name: s.name,
                    value: s.data[i],
                    color: s.color,
                },
            ])
            .enter()
            .append("rect")
            .attr("x", (d) => x1Scale(d.name))
            .attr("y", (d) => yScale(d.value))
            .attr("width", x1Scale.bandwidth())
            .attr("height", (d) => innerHeight - yScale(d.value))
            .attr("fill", (d) => d.color);
    });

    // Tambahkan label nilai di atas bar
    categoryGroups
        .selectAll(".bar-group")
        .data((category) => {
            return series.map((s) => {
                const i = categories.indexOf(category);
                return {
                    name: s.name,
                    value: s.data[i],
                };
            });
        })
        .enter()
        .append("text")
        .attr("class", "value-label")
        .attr("x", (d) => x1Scale(d.name) + x1Scale.bandwidth() / 2)
        .attr("y", (d) => yScale(d.value) - 5)
        .attr("text-anchor", "middle")
        .text((d) => d.value)
        .style("font-size", "10px")
        .style("fill", "#333");

    chart.value = svg;
};

// Render chart saat komponen dimount dan saat data berubah
onMounted(() => {
    renderChart();

    // Resize handler
    const handleResize = () => {
        renderChart();
    };

    window.addEventListener("resize", handleResize);

    // Clean up
    onBeforeUnmount(() => {
        window.removeEventListener("resize", handleResize);
    });
});

watch(() => props.data, renderChart, { deep: true });
watch(() => props.height, renderChart);
</script>

<template>
    <div ref="chartContainer" class="w-full h-full"></div>
</template>
