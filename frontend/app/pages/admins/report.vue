<script setup lang="ts">
import { ref, computed } from "vue";

definePageMeta({
  layout: "admin",
});

// ---- Filters ----
const filters = ref({
  year: "All Years (2018 - 2024)",
  department: "All Departments",
  status: "All Statuses",
});

// ---- Stat cards ----
const stats = ref([
  { label: "Total Graduates", value: "12,450", trend: "+4.2% vs last year", up: true, icon: "i-heroicons-user-group" },
  { label: "Overall Employment", value: "87.3%", trend: "+1.5% vs last year", up: true, icon: "i-heroicons-briefcase" },
  { label: "Continuing Education", value: "8.5%", trend: "-0.8% vs last year", up: false, icon: "i-heroicons-academic-cap" },
  { label: "Unemployed", value: "1,580", trend: "+0.5% vs last year", up: false, icon: "i-heroicons-x-circle", danger: true },
]);

// ---- Bar chart: Employment Trends Over Time ----
const trendChart = ref({
  series: [{ name: "Employment Rate", data: [58, 62, 65, 63, 68, 92, 95] }],
  chartOptions: {
    chart: { type: "bar", toolbar: { show: false } },
    plotOptions: { bar: { columnWidth: "45%", borderRadius: 4, distributed: true } },
    dataLabels: { enabled: false },
    xaxis: { categories: ["2018", "2019", "2020", "2021", "2022", "2023", "2024"] },
    yaxis: { labels: { formatter: (v: number) => `${v}%` } },
    colors: ["#BFE3FF", "#BFE3FF", "#BFE3FF", "#BFE3FF", "#BFE3FF", "#0F172A", "#0F172A"],
    legend: { show: false },
    grid: { borderColor: "#F1F5F9" },
  },
});

// ---- Radial: Status Distribution ----
const statusChart = ref({
  series: [87.3, 8.5, 4.2],
  chartOptions: {
    chart: { type: "donut" },
    labels: ["Employed", "Studying", "Seeking"],
    colors: ["#0F172A", "#60A5FA", "#CBD5E1"],
    legend: { show: false },
    dataLabels: { enabled: false },
    plotOptions: { pie: { donut: { size: "75%" } } },
  },
});
const totalStatus = "2,140";

// ---- Table ----
const rows = ref([
  { dept: "Computer Science", total: 450, employed: 415, cont: 20, unemployed: 15, seeking: 15 },
  { dept: "Business Administration", total: 620, employed: 540, cont: 45, unemployed: 45, seeking: 55 },
  { dept: "Mechanical Engineering", total: 310, employed: 285, cont: 15, unemployed: 15, seeking: 10 },
  { dept: "Psychology", total: 480, employed: 320, cont: 65, unemployed: 60, seeking: 65 },
  { dept: "Biology", total: 280, employed: 150, cont: 100, unemployed: 130, seeking: 50 },
]);

const totals = computed(() => {
  const total = rows.value.reduce((s, r) => s + r.total, 0);
  const employed = rows.value.reduce((s, r) => s + r.employed, 0);
  const cont = rows.value.reduce((s, r) => s + r.cont, 0);
  const unemployed = rows.value.reduce((s, r) => s + r.unemployed, 0);
  const seeking = rows.value.reduce((s, r) => s + r.seeking, 0);
  const rate = ((employed / total) * 100).toFixed(1);
  return { total, employed, cont, unemployed, seeking, rate };
});

const rate = (r: typeof rows.value[0]) => ((r.employed / r.total) * 100).toFixed(1);
</script>

<template>
  <div class="w-full flex flex-col gap-4 px-3 sm:px-4 lg:px-0">
    <!-- Title -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <div>
        <h1 class="text-xl sm:text-2xl font-semibold text-slate-800">Employment Reports</h1>
        <p class="text-slate-500 text-xs sm:text-sm">
          Analyze graduate outcomes, trends, and departmental performance.
        </p>
      </div>
      <div class="flex gap-2 w-full sm:w-auto">
        <button
          class="flex-1 sm:flex-none flex items-center justify-center gap-1 px-3 py-2 text-xs sm:text-sm border border-slate-300 rounded-lg hover:bg-slate-50 whitespace-nowrap"
        >
          <Icon name="i-heroicons-arrow-down-tray" /> Export CSV
        </button>
        <button
          class="flex-1 sm:flex-none flex items-center justify-center gap-1 px-3 py-2 text-xs sm:text-sm border border-slate-300 rounded-lg hover:bg-slate-50 whitespace-nowrap"
        >
          <Icon name="i-heroicons-document-text" /> Export PDF
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="w-full bg-white border border-slate-200 p-4 rounded-xl flex flex-col sm:flex-row sm:flex-wrap lg:flex-nowrap sm:items-end gap-3 sm:gap-4">
      <div class="flex flex-col gap-1 w-full sm:w-[calc(50%-0.5rem)] lg:flex-1 lg:w-auto">
        <label class="text-xs text-slate-500">Graduation Year</label>
        <select v-model="filters.year" class="border border-slate-300 rounded-lg px-3 py-2 text-sm w-full">
          <option>All Years (2018 - 2024)</option>
        </select>
      </div>
      <div class="flex flex-col gap-1 w-full sm:w-[calc(50%-0.5rem)] lg:flex-1 lg:w-auto">
        <label class="text-xs text-slate-500">Department / Major</label>
        <select v-model="filters.department" class="border border-slate-300 rounded-lg px-3 py-2 text-sm w-full">
          <option>All Departments</option>
        </select>
      </div>
      <div class="flex flex-col gap-1 w-full sm:w-[calc(50%-0.5rem)] lg:flex-1 lg:w-auto">
        <label class="text-xs text-slate-500">Status</label>
        <select v-model="filters.status" class="border border-slate-300 rounded-lg px-3 py-2 text-sm w-full">
          <option>All Statuses</option>
        </select>
      </div>
      <button
        class="bg-slate-900 text-white text-sm px-5 py-2 rounded-lg hover:bg-slate-800 w-full sm:w-[calc(50%-0.5rem)] lg:w-auto shrink-0"
      >
        Apply Filters
      </button>
    </div>

    <!-- Stat cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
      <div
        v-for="s in stats"
        :key="s.label"
        class="bg-white border border-slate-200 rounded-xl p-3 sm:p-4 flex flex-col gap-1.5 sm:gap-2 min-w-0"
      >
        <div class="flex items-center justify-between gap-2">
          <span class="text-xs sm:text-sm text-slate-500 truncate">{{ s.label }}</span>
          <Icon :name="s.icon" class="text-slate-400 text-base sm:text-lg shrink-0" />
        </div>
        <div class="text-lg sm:text-2xl font-semibold truncate" :class="s.danger ? 'text-red-500' : 'text-slate-800'">
          {{ s.value }}
        </div>
        <div class="text-[11px] sm:text-xs flex items-center gap-1" :class="s.up ? 'text-green-600' : 'text-red-500'">
          <Icon :name="s.up ? 'i-heroicons-arrow-trending-up' : 'i-heroicons-arrow-trending-down'" class="shrink-0" />
          <span class="truncate">{{ s.trend }}</span>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 sm:gap-4">
      <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl p-3 sm:p-4 min-w-0">
        <h2 class="font-medium text-slate-800 mb-2 text-sm sm:text-base">Employment Trends Over Time</h2>
        <ClientOnly>
          <apexchart
            type="bar"
            height="240"
            class="sm:!h-[260px]"
            :options="trendChart.chartOptions"
            :series="trendChart.series"
          />
        </ClientOnly>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-3 sm:p-4 flex flex-col items-center min-w-0">
        <h2 class="font-medium text-slate-800 self-start mb-2 text-sm sm:text-base">Status Distribution (2024)</h2>
        <div class="relative w-full max-w-[220px] sm:max-w-none flex justify-center">
          <ClientOnly>
            <apexchart
              type="donut"
              height="200"
              class="sm:!h-[220px] w-full"
              :options="statusChart.chartOptions"
              :series="statusChart.series"
            />
          </ClientOnly>
          <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
            <span class="text-lg sm:text-xl font-semibold">{{ totalStatus }}</span>
            <span class="text-xs text-slate-400">Total</span>
          </div>
        </div>
        <div class="w-full flex flex-col gap-1 mt-2 text-xs sm:text-sm">
          <div class="flex items-center justify-between">
            <span class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-slate-900 shrink-0"></span>Employed</span>
            <span>87.3%</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-blue-400 shrink-0"></span>Studying</span>
            <span>8.5%</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-slate-300 shrink-0"></span>Seeking</span>
            <span>4.2%</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white border border-slate-200 rounded-xl p-3 sm:p-4">
      <h2 class="font-medium text-slate-800 mb-3 text-sm sm:text-base">Majors by Employment Status</h2>
      <div class="overflow-x-auto -mx-3 sm:mx-0 px-3 sm:px-0">
        <table class="w-full text-xs sm:text-sm min-w-[640px]">
          <thead>
            <tr class="text-left text-slate-400 border-b border-slate-100">
              <th class="py-2 pr-3 font-medium whitespace-nowrap">DEPARTMENT / MAJOR</th>
              <th class="py-2 pr-3 font-medium whitespace-nowrap">TOTAL GRADS</th>
              <th class="py-2 pr-3 font-medium whitespace-nowrap">EMPLOYED</th>
              <th class="py-2 pr-3 font-medium whitespace-nowrap">CONT. EDUCATION</th>
              <th class="py-2 pr-3 font-medium whitespace-nowrap">UNEMPLOYED</th>
              <th class="py-2 pr-3 font-medium whitespace-nowrap">SEEKING</th>
              <th class="py-2 font-medium whitespace-nowrap">EMPLOYMENT RATE</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="r in rows" :key="r.dept" class="border-b border-slate-50">
              <td class="py-2 pr-3 text-orange-500 font-medium whitespace-nowrap">{{ r.dept }}</td>
              <td class="py-2 pr-3">{{ r.total }}</td>
              <td class="py-2 pr-3">{{ r.employed }}</td>
              <td class="py-2 pr-3">{{ r.cont }}</td>
              <td class="py-2 pr-3">{{ r.unemployed }}</td>
              <td class="py-2 pr-3">{{ r.seeking }}</td>
              <td class="py-2 text-blue-600 font-medium whitespace-nowrap">{{ rate(r) }}%</td>
            </tr>
            <tr class="font-semibold">
              <td class="py-2 pr-3 whitespace-nowrap">Total / Averages</td>
              <td class="py-2 pr-3">{{ totals.total }}</td>
              <td class="py-2 pr-3">{{ totals.employed }}</td>
              <td class="py-2 pr-3">{{ totals.cont }}</td>
              <td class="py-2 pr-3">{{ totals.unemployed }}</td>
              <td class="py-2 pr-3">{{ totals.seeking }}</td>
              <td class="py-2 text-blue-600 whitespace-nowrap">{{ totals.rate }}%</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>