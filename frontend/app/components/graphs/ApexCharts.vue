<script setup lang="ts">
const dashStore = useDashStore();

const statusLabels: Record<string, string> = {
  employed: "Employed",
  unemployed: "Unemployed",
  seeking: "Seeking",
  continuing_study: "Continuing Study",
};

const statusColors: Record<string, string> = {
  employed: "#1BC5BD",
  unemployed: "#F64E60",
  seeking: "#FFA800",
  continuing_study: "#8950FC",
};

const categories = computed<string[]>(() => {
  return (dashStore.chartYears?.data.years ?? []).map((y) => String(y));
});

const series = computed(() => {
  const raw =
    (dashStore.chartYears?.data.series as Record<
      string,
      Record<string, number>
    >) ?? {};
  const years =
    dashStore.chartYears?.data.years ?? ([] as Array<number | string>);

  return Object.keys(raw).map((status) => ({
    name: statusLabels[status] ?? status,
    data: years.map((y) => raw[status]?.[String(y)] ?? 0),
  }));
});

const chartOptions = computed(() => ({
  chart: {
    type: "bar",
    toolbar: { show: true },
    background: "transparent",
    fontFamily: "Kantumruy Pro, sans-serif",
  },
  theme: { mode: "light" },
  plotOptions: {
    bar: {
      borderRadius: 4,
      columnWidth: "60%",
    },
  },
  colors: Object.values(statusColors),
  dataLabels: {
    enabled: true,
    style: { fontSize: "12px", colors: ["#fff"] },
  },
  xaxis: {
    categories: categories.value,
    labels: {
      style: { fontSize: "13px", colors: "#666" },
    },
  },
  legend: {
    position: "top",
    horizontalAlign: "center",
  },
  grid: {
    show: true,
    borderColor: "#eee",
    strokeDashArray: 4,
    position: "back",
    xaxis: { lines: { show: true } },
    yaxis: { lines: { show: true } },
    row: { colors: undefined, opacity: 0.2 },
    column: { colors: undefined, opacity: 0.2 },
    padding: { top: 0, right: 0, bottom: 0, left: 0 },
  },
  tooltip: {
    theme: "light",
  },
}));

</script>

<template>
  <div class="rounded-xl bg-white p-4 pb-0 shadow w-full">
    <h3 class="mb-4 font-semibold">Alumni By all Year</h3>

    <apexchart
      type="bar"
      height="440"
     
      :options="chartOptions"
      :series="series"
    />
  </div>
</template>
