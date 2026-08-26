<script setup lang="ts">
import { useAlumnisStore } from "~/stores/alumnis";

const alumniStores = useAlumnisStore();

const timeAgo = (dateStr: string) => {
  const diffMs = Date.now() - new Date(dateStr).getTime();
  const mins = Math.floor(diffMs / 60000);
  if (mins < 60) return `${mins} min ago`;
  const hours = Math.floor(mins / 60);
  if (hours < 24) return `${hours} hour${hours > 1 ? "s" : ""} ago`;
  const days = Math.floor(hours / 24);
  return `${days} day${days > 1 ? "s" : ""} ago`;
};
</script>

<template>
  <div
    class="lg:col-span-4 bg-white rounded-2xl p-6 border border-teal-100/80 shadow-xs"
  >
    <div
      class=" space-y-4 leading-1.5"
    >
      <h3 class="text-xs font-bold text-slate-800">Recent Notifications</h3>
      <hr class="border-slate-100 -mx-5" />

      <!-- Loading skeleton -->
      <div v-if="alumniStores.isLoading" class="space-y-3.5 animate-pulse">
        <div v-for="n in 2" :key="n" class="flex items-start gap-3">
          <div class="w-7 h-7 rounded-full bg-slate-100 shrink-0"></div>
          <div class="flex-1 space-y-1.5">
            <div class="h-3 bg-slate-100 rounded w-3/4"></div>
            <div class="h-2.5 bg-slate-100 rounded w-full"></div>
            <div class="h-2 bg-slate-100 rounded w-1/4"></div>
          </div>
        </div>
      </div>

      <!-- Loaded content -->
      <div v-else class="space-y-3.5">
        <div
          v-for="item in alumniStores.data?.data.notifications"
          :key="item.id"
          class="flex items-start gap-3"
        >
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

          <div class="text-xs">
            <p class="text-slate-800 font-normal">
              <span class="font-bold text-slate-900 leading-5">{{
                item.title
              }}</span>
            </p>
            <p class="text-slate-600 mt-0.5 line-clamp-2 leading-5">
              {{ item.message }}
            </p>
            <span class="text-[11px] text-slate-400">{{
              timeAgo(item.created_at)
            }}</span>
          </div>
        </div>

        <p
          v-if="!alumniStores.data?.data.notifications?.length"
          class="text-xs text-slate-400 text-center py-2"
        >
          No notifications yet.
        </p>
      </div>
    </div>
  </div>
</template>
