<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import LoadingIcon from "~/components/icons/LoadingIcon.vue";

definePageMeta({
  layout: "admin",
});

const reportStore = useReportStore();

// ============================================================
// Current Year
// ============================================================

const currentYear = new Date().getFullYear();
const startYear = currentYear - 2;

// ============================================================
// Filters
// ============================================================

const filters = ref({
  year_start: String(startYear),
  year_end: String(currentYear),
  department: "all",
  status: "all",
});

// ============================================================
// Apply Filters
// ============================================================

const applyFilters = async () => {
  await reportStore.getReport({
    year_start: filters.value.year_start,
    year_end: filters.value.year_end,
    major_id: filters.value.department,
    status: filters.value.status,
  });
};

// ============================================================
// Stats
// ============================================================

const stats = computed(() => {
  const report = reportStore.data?.data;

  if (!report) {
    return [];
  }

  return [
    {
      label: "Total Graduates",
      value: report.stats.total_graduates.value,
      trend: report.stats.total_graduates.trend,
      up: report.stats.total_graduates.up,
      icon: "i-heroicons-user-group",
    },
    {
      label: "Overall Employment",
      value: report.stats.overall_employment.value,
      trend: report.stats.overall_employment.trend,
      up: report.stats.overall_employment.up,
      icon: "i-heroicons-briefcase",
    },
    {
      label: "Continuing Education",
      value: report.stats.continuing_education.value,
      trend: report.stats.continuing_education.trend,
      up: report.stats.continuing_education.up,
      icon: "i-heroicons-academic-cap",
    },
    {
      label: "Unemployed",
      value: report.stats.unemployed.value,
      trend: report.stats.unemployed.trend,
      up: report.stats.unemployed.up,
      icon: "i-heroicons-x-circle",
      danger: true,
    },
  ];
});

// ============================================================
// Employment Trend Chart
// ============================================================

const trendChart = computed(() => {
  const report = reportStore.data?.data;

  return {
    series: [
      {
        name: "Employment Rate",
        data: report?.trend_chart?.rates ?? [],
      },
    ],

    chartOptions: {
      chart: {
        type: "bar",
        toolbar: {
          show: false,
        },
      },

      plotOptions: {
        bar: {
          columnWidth: "45%",
          borderRadius: 4,
          distributed: true,
        },
      },

      dataLabels: {
        enabled: false,
      },

      xaxis: {
        categories: report?.trend_chart?.categories ?? [],
      },

      yaxis: {
        min: 0,
        max: 100,
        labels: {
          formatter: (value: number) => `${value}%`,
        },
      },

      colors: [
        "#BFE3FF",
        "#BFE3FF",
        "#BFE3FF",
        "#BFE3FF",
        "#BFE3FF",
        "#0F172A",
        "#0F172A",
      ],

      legend: {
        show: false,
      },

      grid: {
        borderColor: "#F1F5F9",
      },

      tooltip: {
        y: {
          formatter: (value: number) => `${value}%`,
        },
      },
    },
  };
});

// ============================================================
// Status Distribution
// ============================================================

const statusChart = computed(() => {
  const report = reportStore.data?.data;

  return {
    series: report?.status_distribution?.series ?? [],

    chartOptions: {
      chart: {
        type: "donut",
      },

      labels: report?.status_distribution?.labels ?? [],

      colors: ["#0F172A", "#60A5FA", "#CBD5E1"],

      legend: {
        show: false,
      },

      dataLabels: {
        enabled: false,
      },

      plotOptions: {
        pie: {
          donut: {
            size: "75%",
          },
        },
      },

      tooltip: {
        y: {
          formatter: (value: number) => `${value}%`,
        },
      },
    },
  };
});

// ============================================================
// Total Status
// ============================================================

const totalStatus = computed(() => {
  return reportStore.data?.data?.status_distribution?.total ?? "0";
});

// ============================================================
// Table Rows
// ============================================================

const rows = computed(() => {
  return reportStore.data?.data?.rows ?? [];
});

// ============================================================
// Table Totals
// ============================================================

const totals = computed(() => {
  const items = rows.value;

  const total = items.reduce((sum, row) => sum + row.total, 0);

  const employed = items.reduce((sum, row) => sum + row.employed, 0);

  const cont = items.reduce((sum, row) => sum + row.cont, 0);

  const unemployed = items.reduce((sum, row) => sum + row.unemployed, 0);

  const seeking = items.reduce((sum, row) => sum + row.seeking, 0);

  const rate = total > 0 ? ((employed / total) * 100).toFixed(1) : "0.0";

  return {
    total,
    employed,
    cont,
    unemployed,
    seeking,
    rate,
  };
});

// ============================================================
// Filter Options
// ============================================================

const years = computed(() => {
  return reportStore.data?.data?.filters?.years ?? [];
});

const majors = computed(() => {
  return reportStore.data?.data?.filters?.majors ?? [];
});

// ============================================================
// Graduation Year Display
// ============================================================

const yearRangeLabel = computed(() => {
  return `${filters.value.year_start} - ${filters.value.year_end}`;
});

// ============================================================
// Status Options
// ============================================================

const statusOptions = [
  {
    label: "All Statuses",
    value: "all",
  },
  {
    label: "Employed",
    value: "employed",
  },
  {
    label: "Studying",
    value: "studying",
  },
  {
    label: "Unemployed",
    value: "unemployed",
  },
  {
    label: "Seeking",
    value: "unknown",
  },
];

// ============================================================
// Load Data
// ============================================================

onMounted(async () => {
  await reportStore.getReport({
    year_start: String(startYear),
    year_end: String(currentYear),
    major_id: "all",
    status: "all",
  });
});
</script>

<template>
  <div class="w-full flex flex-col gap-4 px-3 sm:px-4 lg:px-0">
    <!-- ===================================================== -->
    <!-- Header -->
    <!-- ===================================================== -->

    <div
      class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
    >
      <div>
        <h1 class="text-xl sm:text-2xl font-semibold text-slate-800">
          Employment Reports
        </h1>

        <p class="text-slate-500 text-xs sm:text-sm">
          Analyze graduate outcomes, trends, and departmental performance.
        </p>
      </div>

      <div class="flex gap-2 w-full sm:w-auto">
        <button
          hidden
          class="flex-1 sm:flex-none flex items-center justify-center gap-1 px-3 py-2 text-xs sm:text-sm border border-slate-300 rounded-lg hover:bg-slate-50 whitespace-nowrap"
        >
          <Icon name="i-heroicons-arrow-down-tray" />
          Export CSV
        </button>

        <button
          hidden
          class="flex-1 sm:flex-none flex items-center justify-center gap-1 px-3 py-2 text-xs sm:text-sm border border-slate-300 rounded-lg hover:bg-slate-50 whitespace-nowrap"
        >
          <Icon name="i-heroicons-document-text" />
          Export PDF
        </button>
      </div>
    </div>

    <!-- ===================================================== -->
    <!-- Loading -->
    <!-- ===================================================== -->

    <div
      v-if="reportStore.isLoading && !reportStore.data?.data"
      class="w-full flex flex-col gap-4"
    >
      <!-- Loading Header -->
      <div class="bg-white border border-slate-200 rounded-xl p-4 sm:p-5">
        <div class="flex items-center justify-between gap-4">
          <div class="space-y-2">
            <div class="h-5 w-40 bg-slate-200 rounded-md animate-pulse"></div>

            <div class="h-3 w-64 bg-slate-100 rounded-md animate-pulse"></div>
          </div>

          <div
            class="hidden sm:block h-9 w-28 bg-slate-100 rounded-lg animate-pulse"
          ></div>
        </div>
      </div>

      <!-- Loading Filters -->
      <div class="bg-white border border-slate-200 rounded-xl p-4">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
          <div v-for="i in 4" :key="`filter-${i}`" class="space-y-2">
            <div class="h-3 w-24 bg-slate-100 rounded animate-pulse"></div>

            <div
              class="h-10 w-full bg-slate-200 rounded-lg animate-pulse"
            ></div>
          </div>
        </div>
      </div>

      <!-- Loading Stat Cards -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div
          v-for="i in 4"
          :key="`stat-${i}`"
          class="bg-white border border-slate-200 rounded-xl p-4"
        >
          <div class="flex items-center justify-between mb-4">
            <div class="h-3 w-24 bg-slate-100 rounded animate-pulse"></div>

            <div class="w-8 h-8 bg-slate-100 rounded-lg animate-pulse"></div>
          </div>

          <div
            class="h-7 w-20 bg-slate-200 rounded-md animate-pulse mb-3"
          ></div>

          <div class="h-3 w-28 bg-slate-100 rounded animate-pulse"></div>
        </div>
      </div>

      <!-- Loading Charts -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 sm:gap-4">
        <!-- Chart Skeleton -->
        <div
          class="lg:col-span-2 bg-white border border-slate-200 rounded-xl p-4"
        >
          <div class="flex items-center justify-between mb-5">
            <div class="h-4 w-48 bg-slate-200 rounded animate-pulse"></div>

            <div class="h-3 w-20 bg-slate-100 rounded animate-pulse"></div>
          </div>

          <!-- Fake Bars -->
          <div class="h-[220px] flex items-end justify-around gap-3 px-4">
            <div
              v-for="height in [35, 55, 45, 70, 60, 85, 75]"
              :key="height"
              class="w-full max-w-[45px] bg-slate-200 rounded-t-md animate-pulse"
              :style="{ height: `${height}%` }"
            ></div>
          </div>
        </div>

        <!-- Donut Skeleton -->
        <div class="bg-white border border-slate-200 rounded-xl p-4">
          <div class="h-4 w-40 bg-slate-200 rounded animate-pulse mb-5"></div>

          <div class="flex justify-center">
            <div
              class="w-[170px] h-[170px] rounded-full border-[28px] border-slate-200 animate-pulse"
            ></div>
          </div>

          <div class="mt-5 space-y-3">
            <div
              v-for="i in 3"
              :key="`legend-${i}`"
              class="flex justify-between"
            >
              <div class="h-3 w-24 bg-slate-100 rounded animate-pulse"></div>

              <div class="h-3 w-12 bg-slate-100 rounded animate-pulse"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading Table -->
      <div class="bg-white border border-slate-200 rounded-xl p-4">
        <div class="flex justify-between items-center mb-5">
          <div class="h-4 w-52 bg-slate-200 rounded animate-pulse"></div>

          <div class="h-3 w-20 bg-slate-100 rounded animate-pulse"></div>
        </div>

        <div class="space-y-4">
          <!-- Table Header -->
          <div class="grid grid-cols-7 gap-4">
            <div
              v-for="i in 7"
              :key="`th-${i}`"
              class="h-3 bg-slate-100 rounded animate-pulse"
            ></div>
          </div>

          <!-- Table Rows -->
          <div
            v-for="row in 5"
            :key="`row-${row}`"
            class="grid grid-cols-7 gap-4 py-2 border-t border-slate-50"
          >
            <div
              v-for="col in 7"
              :key="`col-${col}`"
              class="h-4 bg-slate-100 rounded animate-pulse"
            ></div>
          </div>
        </div>
      </div>
    </div>

    <!-- ===================================================== -->
    <!-- Content -->
    <!-- ===================================================== -->

    <template v-else>
      <!-- =================================================== -->
      <!-- Filters -->
      <!-- =================================================== -->

      <div
        class="w-full bg-white border border-slate-200 p-4 rounded-xl flex flex-col sm:flex-row sm:flex-wrap lg:flex-nowrap sm:items-end gap-3 sm:gap-4"
      >
        <!-- Graduation Year -->
        <div
          class="flex flex-col gap-1 w-full sm:w-[calc(50%-0.5rem)] lg:flex-[1.5]"
        >
          <label class="text-xs text-slate-500"> Graduation Year </label>

          <div class="flex items-center gap-2">
            <!-- Start Year -->
            <select
              v-model="filters.year_start"
              class="border border-slate-300 rounded-lg px-3 py-2 text-sm w-full"
            >
              <option
                v-for="year in years"
                :key="`start-${year}`"
                :value="String(year)"
              >
                {{ year }}
              </option>
            </select>

            <span class="text-slate-400 shrink-0"> - </span>

            <!-- End Year -->
            <select
              v-model="filters.year_end"
              class="border border-slate-300 rounded-lg px-3 py-2 text-sm w-full"
            >
              <option
                v-for="year in years"
                :key="`end-${year}`"
                :value="String(year)"
                :disabled="Number(year) < Number(filters.year_start)"
              >
                {{ year }}
              </option>
            </select>
          </div>
        </div>

        <!-- Department / Major -->

        <div
          class="flex flex-col gap-1 w-full sm:w-[calc(50%-0.5rem)] lg:flex-1 lg:w-auto"
        >
          <label class="text-xs text-slate-500"> Department / Major </label>

          <select
            v-model="filters.department"
            class="border border-slate-300 rounded-lg px-3 py-2 text-sm w-full"
          >
            <option value="all">All Departments</option>

            <option v-for="major in majors" :key="major.id" :value="major.id">
              {{ major.name }}
            </option>
          </select>
        </div>

        <!-- Status -->

        <div
          class="flex flex-col gap-1 w-full sm:w-[calc(50%-0.5rem)] lg:flex-1 lg:w-auto"
        >
          <label class="text-xs text-slate-500"> Status </label>

          <select
            v-model="filters.status"
            class="border border-slate-300 rounded-lg px-3 py-2 text-sm w-full"
          >
            <option
              v-for="status in statusOptions"
              :key="status.value"
              :value="status.value"
            >
              {{ status.label }}
            </option>
          </select>
        </div>

        <!-- Apply -->

        <button
          @click="applyFilters"
          class="bg-slate-900 text-white text-sm flex justify-center items-center gap-2 px-5 py-2 rounded-lg hover:bg-slate-800 w-full sm:w-[calc(50%-0.5rem)] lg:w-auto shrink-0"
        >
          <LoadingIcon
            v-if="reportStore.isLoading && reportStore.data?.data"
            class="text-white"
          />
          <span v-else>Apply </span>
          Filters
        </button>
      </div>

      <!-- =================================================== -->
      <!-- Stat Cards -->
      <!-- =================================================== -->

      <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div
          v-for="s in stats"
          :key="s.label"
          class="bg-white border border-slate-200 rounded-xl p-3 sm:p-4 flex flex-col gap-1.5 sm:gap-2 min-w-0"
        >
          <div class="flex items-center justify-between gap-2">
            <span class="text-xs sm:text-sm text-slate-500 truncate">
              {{ s.label }}
            </span>

            <Icon
              :name="s.icon"
              class="text-slate-400 text-base sm:text-lg shrink-0"
            />
          </div>

          <div
            class="text-lg sm:text-2xl font-semibold truncate"
            :class="s.danger ? 'text-red-500' : 'text-slate-800'"
          >
            {{ s.value }}
          </div>

          <div
            class="text-[11px] sm:text-xs flex items-center gap-1"
            :class="s.up ? 'text-green-600' : 'text-red-500'"
          >
            <Icon
              :name="
                s.up
                  ? 'i-heroicons-arrow-trending-up'
                  : 'i-heroicons-arrow-trending-down'
              "
              class="shrink-0"
            />

            <span class="truncate">
              {{ s.trend }}
            </span>
          </div>
        </div>
      </div>

      <!-- =================================================== -->
      <!-- Charts -->
      <!-- =================================================== -->

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 sm:gap-4">
        <!-- Employment Trend -->

        <div
          class="lg:col-span-2 bg-white border border-slate-200 rounded-xl p-3 sm:p-4 min-w-0"
        >
          <div class="flex items-center justify-between mb-2">
            <h2 class="font-medium text-slate-800 text-sm sm:text-base">
              Employment Trends Over Time
            </h2>

            <span class="text-xs text-slate-400">
              {{ yearRangeLabel }}
            </span>
          </div>

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

        <!-- Status Distribution -->

        <div
          class="bg-white border border-slate-200 rounded-xl p-3 sm:p-4 flex flex-col items-center min-w-0"
        >
          <h2
            class="font-medium text-slate-800 self-start mb-2 text-sm sm:text-base"
          >
            Status Distribution
          </h2>

          <div
            class="relative w-full max-w-[220px] sm:max-w-none flex justify-center"
          >
            <ClientOnly>
              <apexchart
                type="donut"
                height="200"
                class="sm:!h-[220px] w-full"
                :options="statusChart.chartOptions"
                :series="statusChart.series"
              />
            </ClientOnly>

            <div
              class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none"
            >
              <span class="text-lg sm:text-xl font-semibold">
                {{ totalStatus }}
              </span>

              <span class="text-xs text-slate-400"> Total </span>
            </div>
          </div>

          <!-- Legend -->

          <div class="w-full flex flex-col gap-1 mt-2 text-xs sm:text-sm">
            <div
              v-for="(label, index) in statusChart.chartOptions.labels"
              :key="label"
              class="flex items-center justify-between"
            >
              <span class="flex items-center gap-2">
                <span
                  class="w-2 h-2 rounded-full shrink-0"
                  :class="
                    index === 0
                      ? 'bg-slate-900'
                      : index === 1
                        ? 'bg-blue-400'
                        : 'bg-slate-300'
                  "
                />

                {{ label }}
              </span>

              <span> {{ statusChart.series[index] ?? 0 }}% </span>
            </div>
          </div>
        </div>
      </div>

      <!-- =================================================== -->
      <!-- Table -->
      <!-- =================================================== -->

      <div class="bg-white border border-slate-200 rounded-xl p-3 sm:p-4">
        <div class="flex items-center justify-between mb-3">
          <h2 class="font-medium text-slate-800 text-sm sm:text-base">
            Majors by Employment Status
          </h2>

          <span class="text-xs text-slate-400">
            {{ yearRangeLabel }}
          </span>
        </div>

        <div class="overflow-x-auto -mx-3 sm:mx-0 px-3 sm:px-0">
          <table class="w-full text-xs sm:text-sm min-w-[640px]">
            <thead>
              <tr class="text-left text-slate-400 border-b border-slate-100">
                <th class="py-2 pr-3 font-medium whitespace-nowrap">
                  DEPARTMENT / MAJOR
                </th>

                <th class="py-2 pr-3 font-medium whitespace-nowrap">
                  TOTAL GRADS
                </th>

                <th class="py-2 pr-3 font-medium whitespace-nowrap">
                  EMPLOYED
                </th>

                <th class="py-2 pr-3 font-medium whitespace-nowrap">
                  CONT. EDUCATION
                </th>

                <th class="py-2 pr-3 font-medium whitespace-nowrap">
                  UNEMPLOYED
                </th>

                <th class="py-2 pr-3 font-medium whitespace-nowrap">SEEKING</th>

                <th class="py-2 font-medium whitespace-nowrap">
                  EMPLOYMENT RATE
                </th>
              </tr>
            </thead>

            <tbody>
              <tr
                v-for="r in rows"
                :key="r.dept"
                class="border-b border-slate-50"
              >
                <td
                  class="py-2 pr-3 text-orange-500 font-medium whitespace-nowrap"
                >
                  {{ r.dept }}
                </td>

                <td class="py-2 pr-3">
                  {{ r.total }}
                </td>

                <td class="py-2 pr-3">
                  {{ r.employed }}
                </td>

                <td class="py-2 pr-3">
                  {{ r.cont }}
                </td>

                <td class="py-2 pr-3">
                  {{ r.unemployed }}
                </td>

                <td class="py-2 pr-3">
                  {{ r.seeking }}
                </td>

                <td class="py-2 text-blue-600 font-medium whitespace-nowrap">
                  {{ r.rate }}%
                </td>
              </tr>

              <!-- Total -->

              <tr class="font-semibold">
                <td class="py-2 pr-3 whitespace-nowrap">Total / Averages</td>

                <td class="py-2 pr-3">
                  {{ totals.total }}
                </td>

                <td class="py-2 pr-3">
                  {{ totals.employed }}
                </td>

                <td class="py-2 pr-3">
                  {{ totals.cont }}
                </td>

                <td class="py-2 pr-3">
                  {{ totals.unemployed }}
                </td>

                <td class="py-2 pr-3">
                  {{ totals.seeking }}
                </td>

                <td class="py-2 text-blue-600 whitespace-nowrap">
                  {{ totals.rate }}%
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>

    <!--  -->
    <div
      v-if="reportStore.isLoading && reportStore.data?.data"
      class="w-full flex justify-center items-center"
    >
      <LoadingIcon class="size-10 text-primary" />
    </div>
  </div>
</template>
