<script setup lang="ts">
import ApexCharts from "~/components/graphs/ApexCharts.vue";
import LoadingChartBar from "~/widgets/LoadingChartBar.vue";

definePageMeta({
  middleware: ["auth", "admin"],
  layout: "admin",
});

const dashStore = useDashStore();

/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const selectedYear = ref("all");
const selectedMajor = ref("all");

const yearOptions = computed(() => {
  const years = dashStore.data?.data?.available_years ?? [];

  return [
    { label: "All Years", value: "all" },
    ...years.map((year) => ({
      label: String(year),
      value: String(year),
    })),
  ];
});

const majorOptions = computed(() => {
  const majors = dashStore.data?.data?.majors ?? [];

  return [
    { label: "All Majors", value: "all" },
    ...majors.map((major) => ({
      label: major.title,
      value: String(major.id),
    })),
  ];
});

/*
|--------------------------------------------------------------------------
| Load Dashboard
|--------------------------------------------------------------------------
*/

const loadDashboard = async () => {
  try {
    await dashStore.getDash({
      year: selectedYear.value,
      major: selectedMajor.value,
    });
  } catch (error) {
    console.error("Dashboard loading error:", error);
  }
};

watch([selectedYear, selectedMajor], async () => {
  await loadDashboard();
});

onMounted(async () => {
  await loadDashboard();
});

/*
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
*/

const summary = computed(() => {
  return (
    dashStore.data?.data?.summary ?? {
      employed: 0,
      seeking: 0,
      studying: 0,
      unemployed: 0,
      employed_change: null,
      seeking_change: null,
      studying_change: null,
      unemployed_change: null,
    }
  );
});

/*
|--------------------------------------------------------------------------
| Stat Cards
|--------------------------------------------------------------------------
*/

const statCards = computed(() => {
  const s = summary.value;

  return [
    {
      key: "employed",
      title: "Employed",
      value: s.employed ?? 0,
      change: s.employed_change ?? null,
      iconBg: "bg-emerald-50 text-emerald-500",
      icon: "briefcase",
    },
    {
      key: "seeking",
      title: "Job Seeking",
      value: s.seeking ?? 0,
      change: s.seeking_change ?? null,
      iconBg: "bg-amber-50 text-amber-500",
      icon: "search",
    },
    {
      key: "studying",
      title: "Further Study",
      value: s.studying ?? 0,
      change: s.studying_change ?? null,
      iconBg: "bg-sky-50 text-sky-500",
      icon: "book",
    },
    {
      key: "unemployed",
      title: "Unemployed",
      value: s.unemployed ?? 0,
      change: s.unemployed_change ?? null,
      iconBg: "bg-rose-50 text-rose-500",
      icon: "user-x",
    },
  ];
});

/*
|--------------------------------------------------------------------------
| Total Alumni
|--------------------------------------------------------------------------
*/

const totalAlumni = computed(() => {
  return dashStore.data?.data?.total_alumni ?? 0;
});

/*
|--------------------------------------------------------------------------
| Employment Status Donut
|--------------------------------------------------------------------------
*/

const donutSegments = computed(() => {
  const s = summary.value;

  const total =
    (s.employed ?? 0) +
    (s.seeking ?? 0) +
    (s.studying ?? 0) +
    (s.unemployed ?? 0);

  if (!total) {
    return [];
  }

  const parts = [
    { label: "Employed", value: s.employed ?? 0, color: "#10b981" },
    { label: "Studying", value: s.studying ?? 0, color: "#38bdf8" },
    { label: "Job Seeking", value: s.seeking ?? 0, color: "#f59e0b" },
    { label: "Unemployed", value: s.unemployed ?? 0, color: "#f43f5e" },
  ];

  let offset = 0;

  return parts.map((part) => {
    const percentage = (part.value / total) * 100;

    const segment = {
      ...part,
      percentage,
      pct: Math.round(percentage),
      offset,
    };

    offset += percentage;

    return segment;
  });
});

const donutLeadPct = computed(() => donutSegments.value[0]?.pct ?? 0);
const donutLeadLabel = computed(() => donutSegments.value[0]?.label ?? "");

/*
|--------------------------------------------------------------------------
| Employment By Year
|--------------------------------------------------------------------------
*/

const employmentByYear = computed(() => {
  return dashStore.data?.data.employment_by_year ?? [];
});

/*
|--------------------------------------------------------------------------
| Employment Rate Trend
|--------------------------------------------------------------------------
*/

const employmentRateTrend = computed(() => {
  return dashStore.data?.data?.employment_rate_trend ?? [];
});

/*
|--------------------------------------------------------------------------
| Recent Updates
|--------------------------------------------------------------------------
*/

const recentUpdates = computed(() => {
  return dashStore.data?.data?.recent_updates ?? [];
});

/*
|--------------------------------------------------------------------------
| Status Badge
|--------------------------------------------------------------------------
*/

const statusBadgeClass = (status: string | null | undefined) => {
  const value = (status ?? "").toLowerCase();

  if (value.includes("employ")) return "bg-emerald-50 text-emerald-600";
  if (value.includes("seek")) return "bg-amber-50 text-amber-600";
  if (value.includes("stud")) return "bg-sky-50 text-sky-600";

  return "bg-slate-100 text-slate-500";
};

/*
|--------------------------------------------------------------------------
| Initials
|--------------------------------------------------------------------------
*/

const initials = (name: string | null | undefined) => {
  return (name ?? "?")
    .split(" ")
    .map((part) => part[0])
    .slice(0, 2)
    .join("")
    .toUpperCase();
};
</script>

<template>
  <div class="flex flex-col gap-5 font-Inter">
    <!-- ================================================================
         Header + Filters
    ================================================================= -->

    <div class="w-full flex items-center justify-between flex-wrap gap-3">
      <div>
        <h1 class="text-xl font-semibold text-slate-800">Overview Dashboard</h1>
        <p class="text-sm text-slate-400 mt-1">
          Alumni statistics and employment overview
        </p>
      </div>

      <div class="flex items-center gap-2">
        <!-- Year -->
        <div class="relative">
          <select
            v-model="selectedYear"
            :disabled="dashStore.isLoading"
            class="appearance-none text-sm font-medium border border-slate-200 rounded-lg pl-3 pr-8 py-2 bg-white text-slate-600 shadow-sm hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-primary/10 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <option
              v-for="option in yearOptions"
              :key="option.value"
              :value="option.value"
            >
              {{ option.label }}
            </option>
          </select>

          <svg
            class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 size-3.5 text-slate-400"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
          >
            <path
              d="M6 9l6 6 6-6"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>
        </div>

        <!-- Major -->
        <div class="relative">
          <select
            v-model="selectedMajor"
            :disabled="dashStore.isLoading"
            class="appearance-none text-sm font-medium border border-slate-200 rounded-lg pl-3 pr-8 py-2 bg-white text-slate-600 shadow-sm hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-primary/10 cursor-pointer min-w-[180px] disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <option
              v-for="option in majorOptions"
              :key="option.value"
              :value="option.value"
            >
              {{ option.label }}
            </option>
          </select>

          <svg
            class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 size-3.5 text-slate-400"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
          >
            <path
              d="M6 9l6 6 6-6"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>
        </div>
      </div>
    </div>

    <!-- ================================================================
         Error Banner
    ================================================================= -->

    <div
      v-if="dashStore.error && !dashStore.isLoading"
      class="bg-rose-50 border border-rose-100 rounded-xl px-4 py-3 flex items-center justify-between gap-3"
    >
      <div class="flex items-center gap-2.5">
        <svg
          class="size-4 text-rose-500 shrink-0"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <circle cx="12" cy="12" r="10" />
          <path d="M12 8v5" stroke-linecap="round" />
          <path d="M12 16h.01" stroke-linecap="round" />
        </svg>

        <p class="text-sm text-rose-600">
          Couldn't load dashboard data. Please try again.
        </p>
      </div>

      <button
        type="button"
        @click="loadDashboard"
        class="text-xs font-semibold text-rose-600 hover:text-rose-700 shrink-0"
      >
        Retry
      </button>
    </div>

    <!-- ================================================================
         Total Alumni
    ================================================================= -->

    <div
      v-if="dashStore.isLoading"
      class="rounded-xl p-5 bg-slate-200/60 animate-pulse h-[92px]"
    ></div>

    <div
      v-else
      class="bg-primary rounded-xl p-5 text-white flex items-center justify-between shadow-sm"
    >
      <div>
        <p class="text-sm text-white/70">Total Alumni</p>
        <p class="text-3xl font-bold mt-1">
          {{ totalAlumni.toLocaleString() }}
        </p>
        <p class="text-xs text-white/60 mt-1">
          Registered alumni in the system
        </p>
      </div>

      <div
        class="size-12 rounded-xl bg-white/10 flex items-center justify-center"
      >
        <svg
          class="size-6"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="1.8"
        >
          <circle cx="9" cy="7" r="4" />
          <path d="M2 21v-2a4 4 0 0 1 4-4h6a4 4 0 0 1 4 4v2" />
          <path d="M16 3.5a4 4 0 0 1 0 7.8" />
          <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
        </svg>
      </div>
    </div>

    <!-- ================================================================
         Stat Cards
    ================================================================= -->

    <div class="grid grid-cols-4 gap-4 max-xl:grid-cols-2 max-md:grid-cols-1">
      <template v-if="dashStore.isLoading">
        <div
          v-for="n in 4"
          :key="n"
          class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 flex flex-col gap-3 animate-pulse"
        >
          <div class="flex items-center justify-between">
            <div class="h-3.5 w-20 bg-slate-200 rounded"></div>
            <div class="size-8 rounded-lg bg-slate-200"></div>
          </div>
          <div class="h-7 w-16 bg-slate-200 rounded"></div>
          <div class="h-3 w-24 bg-slate-100 rounded"></div>
        </div>
      </template>

      <div
        v-else
        v-for="card in statCards"
        :key="card.key"
        class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 flex flex-col gap-3"
      >
        <div class="flex items-center justify-between">
          <span class="text-sm text-slate-500">{{ card.title }}</span>

          <span
            :class="[
              'size-8 rounded-lg flex items-center justify-center',
              card.iconBg,
            ]"
          >
            <!-- Briefcase -->
            <svg
              v-if="card.icon === 'briefcase'"
              class="size-4"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <rect x="3" y="7" width="18" height="13" rx="2" />
              <path
                d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"
                stroke-linecap="round"
              />
            </svg>

            <!-- Search -->
            <svg
              v-else-if="card.icon === 'search'"
              class="size-4"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <circle cx="11" cy="11" r="7" />
              <path d="M21 21l-4.3-4.3" stroke-linecap="round" />
            </svg>

            <!-- Book -->
            <svg
              v-else-if="card.icon === 'book'"
              class="size-4"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path
                d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v17H6.5A2.5 2.5 0 0 0 4 21.5v-17z"
              />
              <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
            </svg>

            <!-- Unemployed -->
            <svg
              v-else
              class="size-4"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <circle cx="9" cy="7" r="4" />
              <path
                d="M2 21v-2a4 4 0 0 1 4-4h2a4 4 0 0 1 3 1.4"
                stroke-linecap="round"
              />
              <path d="M17 8l5 5M22 8l-5 5" stroke-linecap="round" />
            </svg>
          </span>
        </div>

        <div class="text-2xl font-bold text-slate-800">
          {{ card.value.toLocaleString() }}
        </div>

        <div
          v-if="card.change !== null"
          :class="[
            'text-xs font-medium',
            card.change >= 0 ? 'text-emerald-500' : 'text-rose-500',
          ]"
        >
          {{ card.change >= 0 ? "+" : "" }}{{ card.change }}% vs last year
        </div>
      </div>
    </div>

    <!-- ================================================================
         Status Breakdown + Employment By Year
    ================================================================= -->

    <div class="grid grid-cols-3 gap-4 max-lg:grid-cols-1">
      <!-- Status -->
      <div
        class="bg-white rounded-xl border border-slate-100 shadow-sm p-5 flex flex-col gap-4"
      >
        <div class="flex items-center justify-between">
          <h2 class="text-sm font-semibold text-slate-700">Status Breakdown</h2>
          <span class="text-xs text-slate-400">
            {{ dashStore.isLoading ? "..." : `${totalAlumni} alumni` }}
          </span>
        </div>

        <!-- Loading -->
        <div
          v-if="dashStore.isLoading"
          class="flex flex-col items-center gap-4 animate-pulse"
        >
          <div
            class="size-40 rounded-full bg-slate-100 border-8 border-slate-200"
          ></div>
          <div class="w-full flex flex-col gap-2">
            <div v-for="n in 4" :key="n" class="h-4 bg-slate-100 rounded"></div>
          </div>
        </div>

        <!-- Data -->
        <div
          v-else-if="donutSegments.length"
          class="flex flex-col items-center gap-4"
        >
          <div class="relative size-40">
            <svg viewBox="0 0 36 36" class="size-40 -rotate-90">
              <circle
                cx="18"
                cy="18"
                r="15.9"
                fill="none"
                stroke="#f1f5f9"
                stroke-width="4"
              />

              <circle
                v-for="segment in donutSegments"
                :key="segment.label"
                cx="18"
                cy="18"
                r="15.9"
                fill="none"
                :stroke="segment.color"
                stroke-width="4"
                :stroke-dasharray="`${segment.pct} ${100 - segment.pct}`"
                :stroke-dashoffset="-segment.offset"
                stroke-linecap="round"
              />
            </svg>

            <div
              class="absolute inset-0 flex flex-col items-center justify-center"
            >
              <span class="text-2xl font-bold text-slate-800"
                >{{ donutLeadPct }}%</span
              >
              <span class="text-[11px] text-slate-400">{{
                donutLeadLabel
              }}</span>
            </div>
          </div>

          <div class="w-full flex flex-col gap-2">
            <div
              v-for="segment in donutSegments"
              :key="segment.label"
              class="flex items-center justify-between text-sm"
            >
              <span class="flex items-center gap-2 text-slate-500">
                <span
                  class="size-2.5 rounded-full"
                  :style="{ background: segment.color }"
                ></span>
                {{ segment.label }}
              </span>

              <div class="flex items-center gap-2">
                <span class="font-medium text-slate-700">{{
                  segment.value
                }}</span>
                <span class="text-xs text-slate-400">{{ segment.pct }}%</span>
              </div>
            </div>
          </div>
        </div>

        <div
          v-else
          class="h-40 flex items-center justify-center text-sm text-slate-400 text-center"
        >
          No employment status data
        </div>
      </div>

      <!-- Employment By Year -->
      <div
        class="col-span-2 max-lg:col-span-1 bg-white rounded-xl border border-slate-100 shadow-sm p-5 flex flex-col gap-4"
      >
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-sm font-semibold text-slate-700">
              Employment by Graduation Year
            </h2>
            <p class="text-xs text-slate-400 mt-1">
              Number of employed alumni by graduation year
            </p>
          </div>

          <span class="text-xs text-slate-400">
            {{
              dashStore.isLoading ? "..." : `${employmentByYear.length} years`
            }}
          </span>
        </div>

        <div v-if="dashStore.isLoading" class="h-65 overflow-clip">
          <LoadingChartBar />
        </div>

        <div v-else-if="employmentByYear.length">
          <ApexCharts :chart-data="employmentByYear" />
        </div>

        <div
          v-else
          class="h-56 flex flex-col items-center justify-center text-center"
        >
          <svg
            class="size-8 text-slate-300 mb-2"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
          >
            <path
              d="M4 20V10M10 20V4M16 20v-7M22 20H2"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>
          <p class="text-slate-400 text-sm">No employment data available</p>
        </div>
      </div>
    </div>

    <!-- ================================================================
         Employment Rate Trend + Recent Updates
    ================================================================= -->

    <div class="grid grid-cols-3 gap-4 max-lg:grid-cols-1">
      <!-- Employment Rate Trend -->
      <div
        class="col-span-2 max-lg:col-span-1 bg-white rounded-xl border border-slate-100 shadow-sm p-5 flex flex-col gap-4"
      >
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-sm font-semibold text-slate-700">
              Employment Rate Trend
            </h2>
            <p class="text-xs text-slate-400 mt-1">
              Employment rate by graduation year
            </p>
          </div>

          <span class="text-xs text-slate-400">
            {{
              dashStore.isLoading
                ? "..."
                : `Last ${employmentRateTrend.length} years`
            }}
          </span>
        </div>

        <!-- Loading -->
        <div
          v-if="dashStore.isLoading"
          class="flex flex-col gap-3 animate-pulse"
        >
          <div v-for="n in 5" :key="n" class="flex items-center gap-3">
            <div class="w-10 h-3 bg-slate-200 rounded"></div>
            <div class="flex-1 h-3 bg-slate-100 rounded-full"></div>
            <div class="w-12 h-3 bg-slate-200 rounded"></div>
          </div>
        </div>

        <!-- Data -->
        <div v-else-if="employmentRateTrend.length" class="flex flex-col gap-3">
          <div
            v-for="item in employmentRateTrend"
            :key="item.year"
            class="flex items-center gap-3"
          >
            <span class="w-10 text-xs font-medium text-slate-500">{{
              item.year
            }}</span>

            <div class="flex-1 h-3 bg-slate-100 rounded-full overflow-hidden">
              <div
                class="h-full bg-primary rounded-full transition-all duration-500"
                :style="{ width: `${Math.min(Math.max(item.rate, 0), 100)}%` }"
              ></div>
            </div>

            <span class="w-12 text-right text-xs font-semibold text-slate-700"
              >{{ item.rate }}%</span
            >
          </div>
        </div>

        <div
          v-else
          class="h-32 flex items-center justify-center text-sm text-slate-400"
        >
          No employment trend data
        </div>
      </div>

      <!-- Recent Updates -->
      <div
        class="bg-white rounded-xl border border-slate-100 shadow-sm p-5 flex flex-col gap-4"
      >
        <div class="flex items-center justify-between">
          <h2 class="text-sm font-semibold text-slate-700">
            Recent Status Updates
          </h2>
          <span class="text-xs text-slate-400">Latest 5</span>
        </div>

        <!-- Loading -->
        <div
          v-if="dashStore.isLoading"
          class="flex flex-col gap-3 animate-pulse"
        >
          <div v-for="n in 5" :key="n" class="flex items-center gap-3">
            <div class="size-8 rounded-full bg-slate-200 shrink-0"></div>
            <div class="min-w-0 flex-1 flex flex-col gap-1.5">
              <div class="h-3.5 w-2/3 bg-slate-200 rounded"></div>
              <div class="h-3 w-1/2 bg-slate-100 rounded"></div>
            </div>
            <div class="h-5 w-14 bg-slate-100 rounded-full shrink-0"></div>
          </div>
        </div>

        <!-- Data -->
        <div v-else-if="recentUpdates.length" class="flex flex-col gap-3">
          <div
            v-for="(update, index) in recentUpdates"
            :key="index"
            class="flex items-center gap-3"
          >
            <span
              class="size-8 rounded-full bg-slate-100 text-slate-500 text-xs font-semibold flex items-center justify-center shrink-0"
            >
              {{ initials(update.name) }}
            </span>

            <div class="min-w-0 flex-1">
              <p class="text-sm font-medium text-slate-700 truncate">
                {{ update.name }}
              </p>
              <p class="text-xs text-slate-400 truncate">{{ update.degree }}</p>
            </div>

            <span
              :class="[
                'text-xs font-medium px-2 py-0.5 rounded-full shrink-0',
                statusBadgeClass(update.status),
              ]"
            >
              {{ update.status }}
            </span>
          </div>
        </div>

        <div v-else class="text-sm text-slate-400 text-center py-6">
          No recent updates yet
        </div>
      </div>
    </div>
  </div>
</template>
