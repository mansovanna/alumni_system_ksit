<script setup>
import { ref } from "vue";

const series = ref([
  {
    name: "Alumni",
    data: [5, 12, 24, 10, 30, 52, 38, 60, 45, 68, 55, 48, 20, 50, 65, 70, 52],
  },
]);

const chartOptions = ref({
  chart: {
    type: "area",
    toolbar: { show: true },
    zoom: { enabled: false },
  },
  colors: ["#3b82f6"],
  dataLabels: {
    enabled: false,
    text:{
      enabled: false
    },
    style: {
      fontSize: "12px",
      
      colors: ["#fff"],
    },
    background: {
      enabled: true,
      foreColor: "#3b82f6",
      borderRadius: 100,
      padding: 4,
      opacity: 0.9,
      borderWidth: 2,
      borderColor: "#3b82f6",
    },
   
  },
  stroke: {
    curve: "smooth",
    width: 3,
  },
  fill: {
    type: "gradient",
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.8,
      opacityTo: 0.5,
      stops: [1, 100, 100],
    },
  },
  grid: {
    borderColor: "#f1f5f9",
    row: { colors: ["transparent"] },
    column: {
      colors: ["#f8fafc", "transparent"],
    },
  },
  xaxis: {
    categories: [
      "2020",
      "2020",
      "2021",
      "2021",
      "2022",
      "2022",
      "2023",
      "2023",
      "2024",
      "2024",
      "2025",
      "2025",
      "2026",
    ],
    labels: { show: true },
    axisBorder: { show: true },
    axisTicks: { show: true },
  },
  yaxis: {
    min: 0,
    max: 90,
    tickAmount: 9,
  },
  title: {
    text: "Alumni by Department",
    align: "left",
    style: { fontSize: "14px", fontWeight: 600, color: "#334155" },
  },
});
</script>

<template>
  <div class="bg-white rounded-2xl shadow-md p-4">
    <apexchart
      type="area"
      height="280"
      :options="chartOptions"
      :series="series"
    />
  </div>
</template>
