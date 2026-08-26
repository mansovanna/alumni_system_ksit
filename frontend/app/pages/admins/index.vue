<script setup>
import ApexCharts from "~/components/graphs/ApexCharts.vue";
import LoadingChartBar from "~/widgets/LoadingChartBar.vue";

definePageMeta({
  middleware: ["auth"],
  layout: "admin",
});

const dashStore = useDashStore();

// ── Filters ──────────────────────────────────────────────────────────────
const selectedYear = ref("all");
const selectedMajor = ref("all");

const yearOptions = computed(() => {
  const years = dashStore.data?.available_years ?? [];
  return [{ label: "All Years", value: "all" }, ...years.map((y) => ({ label: String(y), value: y }))];
});

const majorOptions = computed(() => {
  const majors = dashStore.data?.majors ?? [];
  return [
    { label: "All Majors", value: "all" },
    ...majors.map((m) => ({ label: m.title, value: m.id ?? m.title })),
  ];
});

async function loadDashboard() {
  const filters = { year: selectedYear.value, major: selectedMajor.value };
  try {
    await dashStore.getDash(filters);
    await dashStore.chartByWorkStatus(filters);
    await dashStore.chartByYears(filters);
  } catch (error) {
    console.error("Dashboard loading error:", error);
  }
}
watch([selectedYear, selectedMajor], loadDashboard);
onMounted(loadDashboard);

// ── Stat cards (Employed / Job Seeking / Further Study / Unemployed) ──────
// ⚠️ Assumes dashStore.chartWorks = { employed, seeking, studying, unemployed }
// with optional *_change fields for % vs last year. Rename these fields
// to match your actual store shape.
const statCards = computed(() => {
  const w = dashStore.chartWorks ?? {};
  return [
    {
      key: "employed",
      title: "Employed",
      value: w.employed ?? 0,
      change: w.employed_change ?? null,
      iconBg: "bg-emerald-50 text-emerald-500",
      icon: "briefcase",
    },
    {
      key: "seeking",
      title: "Job Seeking",
      value: w.seeking ?? 0,
      change: w.seeking_change ?? null,
      iconBg: "bg-amber-50 text-amber-500",
      icon: "search",
    },
    {
      key: "studying",
      title: "Further Study",
      value: w.studying ?? 0,
      change: w.studying_change ?? null,
      iconBg: "bg-sky-50 text-sky-500",
      icon: "book",
    },
    {
      key: "unemployed",
      title: "Unemployed/Other",
      value: w.unemployed ?? 0,
      change: w.unemployed_change ?? null,
      iconBg: "bg-rose-50 text-rose-500",
      icon: "user-x",
    },
  ];
});

// ── Status breakdown donut (Employed vs Studying vs Seeking) ─────────────
const donutSegments = computed(() => {
  const w = dashStore.chartWorks ?? {};
  const total = (w.employed ?? 0) + (w.seeking ?? 0) + (w.studying ?? 0) + (w.unemployed ?? 0);
  if (!total) return [];
  const parts = [
    { label: "Employed", value: w.employed ?? 0, color: "#1e293b" },
    { label: "Studying", value: w.studying ?? 0, color: "#38bdf8" },
    { label: "Seeking", value: w.seeking ?? 0, color: "#fbbf24" },
  ];
  let offset = 0;
  return parts.map((p) => {
    const pct = (p.value / total) * 100;
    const seg = { ...p, pct: Math.round(pct), offset };
    offset += pct;
    return seg;
  });
});
const donutLeadPct = computed(() => donutSegments.value[0]?.pct ?? 0);
const donutLeadLabel = computed(() => donutSegments.value[0]?.label ?? "");

// ── Recent activity feed ───────────────────────────────────────────────
// ⚠️ Assumes dashStore.data.recent_updates = [{ name, degree, status }].
// If the store doesn't return this field yet, add the endpoint/query on the backend.
const recentUpdates = computed(() => dashStore.data?.recent_updates ?? []);

function statusBadgeClass(status) {
  const s = (status || "").toLowerCase();
  if (s.includes("employ")) return "bg-emerald-50 text-emerald-600";
  if (s.includes("seek")) return "bg-amber-50 text-amber-600";
  if (s.includes("stud")) return "bg-sky-50 text-sky-600";
  return "bg-slate-100 text-slate-500";
}

function initials(name) {
  return (name || "?")
    .split(" ")
    .map((p) => p[0])
    .slice(0, 2)
    .join("")
    .toUpperCase();
}
</script>

<template>
  <div class="flex flex-col gap-5 font-Inter">
    <!-- Page header + filters -->
    <div class="w-full flex items-center justify-between flex-wrap gap-3">
      <h1 class="text-xl font-semibold text-slate-800">Overview Dashboard</h1>

      <div class="flex items-center gap-2">
        <div class="relative">
          <select
            v-model="selectedYear"
            class="appearance-none text-sm font-medium border border-slate-200 rounded-lg pl-3 pr-8 py-2 bg-white text-slate-600 shadow-sm hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-100 cursor-pointer"
          >
            <option v-for="opt in yearOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
          </select>
          <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 size-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M6 9l6 6 6-6" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </div>
        <div class="relative">
          <select
            v-model="selectedMajor"
            class="appearance-none text-sm font-medium border border-slate-200 rounded-lg pl-3 pr-8 py-2 bg-white text-slate-600 shadow-sm hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-100 cursor-pointer min-w-[160px]"
          >
            <option v-for="opt in majorOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
          </select>
          <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 size-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M6 9l6 6 6-6" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
        </div>
      </div>
    </div>

    <!-- Stat cards -->
    <div class="grid grid-cols-4 gap-4 max-xl:grid-cols-2 max-md:grid-cols-1">
      <div
        v-for="card in statCards"
        :key="card.key"
        class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 flex flex-col gap-3"
      >
        <div class="flex items-center justify-between">
          <span class="text-sm text-slate-500">{{ card.title }}</span>
          <span :class="['size-8 rounded-lg flex items-center justify-center', card.iconBg]">
            <svg v-if="card.icon === 'briefcase'" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" stroke-linecap="round"/></svg>
            <svg v-else-if="card.icon === 'search'" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3" stroke-linecap="round"/></svg>
            <svg v-else-if="card.icon === 'book'" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4.5A2.5 2.5 0 0 1 6.5 2H20v17H6.5A2.5 2.5 0 0 0 4 21.5v-17z"/><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/></svg>
            <svg v-else class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="7" r="4"/><path d="M2 21v-2a4 4 0 0 1 4-4h2a4 4 0 0 1 3 1.4" stroke-linecap="round"/><path d="M17 8l5 5M22 8l-5 5" stroke-linecap="round"/></svg>
          </span>
        </div>
        <div class="text-2xl font-bold text-slate-800">{{ card.value.toLocaleString?.() ?? card.value }}</div>
        <div v-if="card.change !== null" :class="['text-xs font-medium', card.change >= 0 ? 'text-emerald-500' : 'text-rose-500']">
          {{ card.change >= 0 ? '+' : '' }}{{ card.change }}% vs last year
        </div>
      </div>
    </div>

    <!-- Status breakdown + Employment by year -->
    <div class="grid grid-cols-3 gap-4 max-lg:grid-cols-1">
      <!-- Donut with center label -->
      <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5 flex flex-col gap-4">
        <h2 class="text-sm font-semibold text-slate-700">Status Breakdown</h2>

        <div v-if="donutSegments.length" class="flex flex-col items-center gap-4">
          <div class="relative size-40">
            <svg viewBox="0 0 36 36" class="size-40 -rotate-90">
              <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f1f5f9" stroke-width="4" />
              <circle
                v-for="seg in donutSegments"
                :key="seg.label"
                cx="18" cy="18" r="15.9" fill="none"
                :stroke="seg.color" stroke-width="4"
                :stroke-dasharray="`${seg.pct} ${100 - seg.pct}`"
                :stroke-dashoffset="-seg.offset"
                stroke-linecap="round"
              />
            </svg>
            <div class="absolute inset-0 flex flex-col items-center justify-center">
              <span class="text-2xl font-bold text-slate-800">{{ donutLeadPct }}%</span>
              <span class="text-[11px] text-slate-400">{{ donutLeadLabel }}</span>
            </div>
          </div>

          <div class="w-full flex flex-col gap-2">
            <div v-for="seg in donutSegments" :key="seg.label" class="flex items-center justify-between text-sm">
              <span class="flex items-center gap-2 text-slate-500">
                <span class="size-2.5 rounded-full" :style="{ background: seg.color }"></span>
                {{ seg.label }}
              </span>
              <span class="font-medium text-slate-700">{{ seg.pct }}%</span>
            </div>
          </div>
        </div>

        <div v-else class="h-40 flex items-center justify-center text-sm text-slate-400 text-center px-4">
          Not enough employment status data yet
        </div>
      </div>

      <!-- Employment by graduation year -->
      <div class="col-span-2 max-lg:col-span-1 bg-white rounded-xl border border-slate-100 shadow-sm p-5 flex flex-col gap-4">
        <h2 class="text-sm font-semibold text-slate-700">Employment by Graduation Year</h2>

        <div v-if="dashStore.chartYears && dashStore.chartYears.length">
          <ApexCharts />
        </div>
        <div v-else-if="dashStore.chartYears && !dashStore.chartYears.length" class="h-56 flex flex-col items-center justify-center gap-2 text-center">
          <svg class="size-8 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M4 20V10M10 20V4M16 20v-7M22 20H2" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          <p class="text-slate-400 text-sm max-w-[240px]">Not enough graduate data to display this chart yet</p>
        </div>
        <div v-else-if="dashStore.isLoadingByYear">
          <LoadingChartBar />
        </div>
      </div>
    </div>

    <!-- Trend + Recent updates -->
    <div class="grid grid-cols-3 gap-4 max-lg:grid-cols-1">
      <!-- Trend sparkline -->
      <div class="col-span-2 max-lg:col-span-1 bg-white rounded-xl border border-slate-100 shadow-sm p-5 flex flex-col gap-4">
        <div class="flex items-center justify-between">
          <h2 class="text-sm font-semibold text-slate-700">Employment Rate Trend</h2>
          <span class="text-xs text-slate-400">Last 5 years</span>
        </div>

        <!-- Lightweight inline sparkline placeholder.
             ⚠️ Wire this up to dashStore.employmentTrend (array of {year, rate})
             to show real data. This is a visual placeholder for now. -->
        <div class="h-32 flex items-center justify-center text-sm text-slate-400 text-center px-4 bg-slate-50 rounded-lg">
          Needs "employment rate by year" data from the store to render this trend chart
        </div>
      </div>

      <!-- Recent updates -->
      <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5 flex flex-col gap-4">
        <h2 class="text-sm font-semibold text-slate-700">Recent Status Updates</h2>

        <div v-if="recentUpdates.length" class="flex flex-col gap-3">
          <div v-for="(u, i) in recentUpdates" :key="i" class="flex items-center gap-3">
            <span class="size-8 rounded-full bg-slate-100 text-slate-500 text-xs font-semibold flex items-center justify-center shrink-0">
              {{ initials(u.name) }}
            </span>
            <div class="min-w-0 flex-1">
              <p class="text-sm font-medium text-slate-700 truncate">{{ u.name }}</p>
              <p class="text-xs text-slate-400 truncate">{{ u.degree }}</p>
            </div>
            <span :class="['text-xs font-medium px-2 py-0.5 rounded-full shrink-0', statusBadgeClass(u.status)]">
              {{ u.status }}
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