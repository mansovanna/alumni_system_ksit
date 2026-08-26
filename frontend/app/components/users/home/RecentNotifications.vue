<script setup lang="ts">
const notifications = ref([
  {
    id: 1,
    user: "Michael Chen",
    action: "viewed your profile.",
    time: "2 hours ago",
    type: "view",
  },
  {
    id: 2,
    user: "Emily Davis",
    action: "started a new role at DesignCo.",
    time: "Yesterday",
    type: "role",
  },
]);
</script>

<template>
  <div
    class="bg-white rounded-2xl p-5 border border-teal-100/80 shadow-xs space-y-4"
  >
    <h3 class="text-xs font-bold text-slate-800">Recent Notifications</h3>
    <hr class="border-slate-100 -mx-5" />

    <div class="space-y-3.5">
      <div
        v-for="item in notifications"
        :key="item.id"
        class="flex items-start gap-3"
      >
        <!-- Notification Icon -->
        <div
          class="w-7 h-7 rounded-full bg-sky-50 text-teal-700 flex items-center justify-center shrink-0 mt-0.5"
        >
          <svg
            class="w-3.5 h-3.5"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
          >
            <path
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
            />
          </svg>
        </div>

        <!-- Text -->
        <div class="text-xs">
          <p class="text-slate-800 font-normal">
            <span class="font-bold text-slate-900">{{ item.user }}</span>
            {{ item.action }}
          </p>
          <span class="text-[11px] text-slate-400">{{ item.time }}</span>
        </div>
      </div>
    </div>
  </div>
</template>
