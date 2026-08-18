<script setup>
import { ref } from "vue";

const series = ref([
  45,
  20,
  15,
  20
]);

const chartOptions = ref({
  chart: {
    type: "pie",
    height: 450,
  },

  labels: [
    "Working",
    "Not Working",
    "Finding Work",
    "Continuing Study"
  ],

  colors: [
    "#312e81",
    "#6366f1",
    "#38bdf8",
    "#a78bfa"
  ],

  legend: {
    show: false
  },

  dataLabels: {
    enabled: true,
    formatter: function (val) {
      return val.toFixed(1) + "%";
    },

    style: {
      fontSize: "16px",
      fontWeight: 600,
      colors: ["#ffffff"]
    },

    dropShadow: {
      enabled: true
    }
  },

  plotOptions: {
    pie: {
      expandOnClick: true,

      donut: {
        size: "0%"
      }
    }
  },

  stroke: {
    width: 2,
    colors: ["#fff"]
  },

  tooltip: {
    y: {
      formatter: (value) => `${value} Alumni`
    }
  },

  title: {
    text: "Alumni Employment Status",
    align: "center",
    style: {
      fontSize: "22px",
      fontWeight: 600,
      color: "#3730a3"
    }
  }
});
</script>


<template>
  <div class="bg-white rounded-2xl shadow-md p-5">
    <apexchart
      type="pie"
      height="450"
      :options="chartOptions"
      :series="series"
    />
  </div>
</template>