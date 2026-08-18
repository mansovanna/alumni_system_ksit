<script setup lang="ts">
// Data state
const user = ref({
  name: "Sarah",
  avatar:
    "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=200&auto=format&fit=crop",
  classYear: "2021",
  role: "Software Engineer at TechCorp",
  status: "Employed",
  networkStrength: 84,
  connections: 142,
  mentoring: 5,
});
</script>

<template>
  <div
    class="lg:col-span-4 bg-white rounded-2xl p-6 border border-teal-100/80 shadow-xs flex flex-col justify-between text-center"
  >
    <div>
      <span
        class="block text-[11px] font-bold text-slate-400 tracking-wider uppercase mb-1"
      >
        NETWORK STRENGTH
      </span>
      <div class="text-3xl sm:text-4xl font-extrabold text-[#036250]">
        {{ user.networkStrength }}%
      </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 gap-3 mt-6">
      <!-- Connections -->
      <div
        class="bg-sky-50/60 rounded-xl p-3 border border-sky-100/50 flex flex-col items-center justify-center"
      >
        <svg
          class="w-5 h-5 text-teal-700 mb-1"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          viewBox="0 0 24 24"
        >
          <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
          <circle cx="9" cy="7" r="4" />
          <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
          <path d="M16 3.13a4 4 0 0 1 0 7.75" />
        </svg>
        <span class="text-sm font-bold text-slate-800">{{
          user.connections
        }}</span>
        <span class="text-[11px] text-slate-500 font-medium">Connections</span>
      </div>

      <!-- Mentoring -->
      <div
        class="bg-sky-50/60 rounded-xl p-3 border border-sky-100/50 flex flex-col items-center justify-center"
      >
        <svg
          class="w-5 h-5 text-teal-700 mb-1"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          viewBox="0 0 24 24"
        >
          <path
            d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"
          />
        </svg>
        <span class="text-sm font-bold text-slate-800">{{
          user.mentoring
        }}</span>
        <span class="text-[11px] text-slate-500 font-medium">Mentoring</span>
      </div>
    </div>
  </div>
</template>
