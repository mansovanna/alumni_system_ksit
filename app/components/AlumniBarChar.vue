<script setup>
import { ref } from "vue";

const series = ref([
  {
    name: "Working",
    data: [85, 70, 90, 55],
  },
  {
    name: "Not Working",
    data: [10, 18, 8, 20],
  },
  {
    name: "Finding Work",
    data: [15, 12, 10, 18],
  },
  {
    name: "Continuing Study",
    data: [20, 25, 15, 30],
  },
]);

const chartOptions = ref({
  chart: {
    type: "bar",
    height: 400,
    stacked: true,
    toolbar: {
      show: false,
    },
  },

  plotOptions: {
    bar: {
      horizontal: false,
      borderRadius: 6,
      columnWidth: "50%",
    },
  },

  dataLabels: {
    enabled: false,
  },

  xaxis: {
    categories: [
      "IT",
      "Business",
      "Engineering",
      "Design",
    ],
    title: {
      text: "Department",
    },
  },

  yaxis: {
    title: {
      text: "Number of Alumni",
    },
  },

  legend: {
    position: "top",
  },

  fill: {
    opacity: 1,
  },

  tooltip: {
    y: {
      formatter: (value) => `${value} Alumni`,
    },
  },

  title: {
    text: "Alumni Employment Status by Department",
    align: "left",
    style: {
      fontSize: "16px",
      fontWeight: 600,
    },
  },
});
</script>


<template>
  <div class="bg-white rounded-2xl shadow-md p-5">
    <apexchart
      type="bar"
      height="400"
      :options="chartOptions"
      :series="series"
    />
  </div>
</template>