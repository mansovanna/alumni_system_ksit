<script setup lang="ts">
import { computed } from "vue";

// 1. ប្រកាស Props ទទួលទិន្នន័យ array [ { year: "2022", count: 2 }, ... ]
const props = defineProps<{
  chartData?: Array<{ year: string | number; count: number }>;
}>();

// 2. ទាញយក Year ធ្វើជា categories លើ X-Axis
const categories = computed(() => {
  if (!props.chartData || !props.chartData.length) return [];
  return props.chartData.map((item) => String(item.year));
});

// 3. ទាញយក Count ធ្វើជា Series សម្រាប់ Bar Chart
const series = computed(() => {
  if (!props.chartData || !props.chartData.length) return [];
  return [
    {
      name: "Employed Alumni",
      data: props.chartData.map((item) => item.count),
    },
  ];
});

// 4. Configuration សម្រាប់ ApexCharts
const chartOptions = computed(() => ({
  chart: {
    type: "bar",
    toolbar: { show: false },
    background: "transparent",
    fontFamily: "Inter, sans-serif",
  },
  theme: { mode: "light" },
  plotOptions: {
    bar: {
      borderRadius: 4,
      columnWidth: "40%",
    },
  },
  colors: ["#10b981"], // ពណ៌បៃតង Emerald ដូចរូប UI design
  dataLabels: {
    enabled: true,
    style: { fontSize: "12px", colors: ["#fff"] },
  },
  xaxis: {
    categories: categories.value,
    labels: {
      style: { fontSize: "13px", colors: "#64748b" },
    },
  },
  grid: {
    show: true,
    borderColor: "#f1f5f9",
    strokeDashArray: 4,
  },
  tooltip: {
    theme: "light",
  },
}));
</script>

<template>
  <div class="w-full">
    <apexchart
      type="bar"
      height="250"
      :options="chartOptions"
      :series="series"
    />
  </div>
</template>