<script setup lang="ts">
import VueApexCharts from "vue3-apexcharts";

const dashStore = useDashStore();

const series = computed<number[]>(() => {
  const workData = dashStore.chartWorks?.data.work ?? [];
  const totalUser = dashStore.chartWorks?.data.total_user ?? 0;

  if (totalUser === 0) return workData.map(() => 0);

  return workData.map((e) => {
    const total = e?.total ?? 0;
    return Number(((total / totalUser) * 100).toFixed(2));
  });
});


const labels = computed<string[]>(() => {
  const workData = dashStore.chartWorks?.data.work ?? [];
  return workData.map((e) => e.status ?? "");
});

const chartOptions = computed(() => ({
  labels: labels.value,
  colors: ["#dc2626", "#16a34a", "#f59e0b", "#4338ca"],
  chart: { type: "donut" },
  plotOptions: {
    pie: {
      donut: {
        size: "50%",
        labels: { show: true },
      },
    },
  },
  stroke: { width: 2, colors: ["#fff"] },
  dataLabels: { enabled: true },
  legend: { show: false },
}));
</script>

<template>
  <div class="rounded-2xl bg-white p-6 shadow w-full">
    <h2 class="text-xl font-bold">Alumni All</h2>
    <p class="text-sm text-gray-400">Where your money goes</p>

    <VueApexCharts
      type="donut"
      height="280"
      :options="chartOptions"
      :series="series"
    />

    <div class="mt-4 space-y-2">
      <div
        v-for="(label, i) in chartOptions.labels"
        :key="label"
        class="flex items-center justify-between"
      >
        <div class="flex items-center gap-2">
          <span
            class="h-4 w-4 rounded-full"
            :style="{ background: chartOptions.colors[i] }"
          />
          <span class="text-gray-500 capitalize">{{ label }}</span>
        </div>
        <span class="font-bold">{{ series[i] }}%</span>
      </div>
    </div>
  </div>
</template>
