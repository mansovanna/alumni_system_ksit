<script setup lang="ts">
import { ref } from "vue";

const activeFilter = ref("All");
const filters = ["All", "Events", "Announcements", "Messages"];

const notificationsToday = ref([
  {
    id: 1,
    title: "Upcoming Event: Annual Tech Mixer",
    description:
      "Join us downtown for networking and drinks with the Class of '22.",
    time: "2h ago",
    unread: true,
    type: "event",
  },
  {
    id: 2,
    title: "New Connection Request",
    description: "Alex Johnson from your cohort requested to connect.",
    time: "4h ago",
    unread: true,
    type: "connection",
    avatar:
      "https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80",
  },
]);

const notificationsYesterday = ref([
  {
    id: 3,
    title: "Newsletter: Q3 Alumni Highlights",
    description:
      "Read about the recent startup successes from the engineering faculty.",
    time: "1d ago",
    unread: false,
    type: "announcement",
  },
  {
    id: 4,
    title: "Job Alert: Senior Designer",
    description: "A new role matching your profile was posted by TechCorp.",
    time: "1d ago",
    unread: false,
    type: "job",
  },
]);

const markAllAsRead = () => {
  notificationsToday.value.forEach((n) => (n.unread = false));
  notificationsYesterday.value.forEach((n) => (n.unread = false));
};
</script>

<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-bold text-slate-900">Notifications</h1>
      <button
        @click="markAllAsRead"
        class="text-xs font-semibold text-[#006B58] hover:underline cursor-pointer"
      >
        Mark all as read
      </button>
    </div>

    <!-- Filter Pills -->
    <div class="flex items-center gap-2 mb-8 overflow-x-auto pb-2">
      <button
        v-for="filter in filters"
        :key="filter"
        @click="activeFilter = filter"
        :class="[
          'px-4 py-1.5 rounded-full text-xs font-medium transition-all cursor-pointer border whitespace-nowrap',
          activeFilter === filter
            ? 'bg-[#006B58] text-white border-[#006B58]'
            : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50',
        ]"
      >
        {{ filter }}
      </button>
    </div>

    <!-- Today Section -->
    <div class="space-y-4 mb-8">
      <h2 class="text-sm font-semibold text-slate-700">Today</h2>

      <div
        v-for="item in notificationsToday"
        :key="item.id"
        class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 flex items-start gap-4 relative hover:shadow-xs transition"
      >
        <!-- Unread Indicator Dot -->
        <span
          v-if="item.unread"
          class="absolute top-5 right-5 w-2 h-2 bg-[#006B58] rounded-full"
        ></span>

        <!-- Icon / Avatar -->
        <div class="shrink-0">
          <img
            v-if="item.type === 'connection' && item.avatar"
            :src="item.avatar"
            alt="Avatar"
            class="w-10 h-10 rounded-full object-cover"
          />
          <div
            v-else
            class="w-10 h-10 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center"
          >
            <!-- Calendar Icon for Event -->
            <svg
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.8"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
              />
            </svg>
          </div>
        </div>

        <!-- Content -->
        <div class="flex-1 pr-6 space-y-1">
          <h3
            class="font-bold text-slate-900 text-sm sm:text-base leading-snug"
          >
            {{ item.title }}
          </h3>
          <p class="text-xs text-slate-500 leading-relaxed">
            {{ item.description }}
          </p>

          <!-- Action Buttons for Connection Request -->
          <div
            v-if="item.type === 'connection'"
            class="flex items-center gap-2 pt-2"
          >
            <button
              class="bg-[#006B58] hover:bg-[#005546] text-white text-xs font-medium px-4 py-1.5 rounded-lg transition cursor-pointer"
            >
              Accept
            </button>
            <button
              class="border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-medium px-4 py-1.5 rounded-lg transition cursor-pointer"
            >
              Decline
            </button>
          </div>

          <p class="text-[11px] text-slate-400 pt-1">{{ item.time }}</p>
        </div>
      </div>
    </div>

    <!-- Yesterday Section -->
    <div class="space-y-4">
      <h2 class="text-sm font-semibold text-slate-700">Yesterday</h2>

      <div
        v-for="item in notificationsYesterday"
        :key="item.id"
        class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 flex items-start gap-4 relative hover:shadow-xs transition"
      >
        <!-- Icon -->
        <div class="shrink-0">
          <div
            class="w-10 h-10 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center"
          >
            <!-- Megaphone Icon for Announcement -->
            <svg
              v-if="item.type === 'announcement'"
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.8"
                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"
              />
            </svg>
            <!-- Briefcase Icon for Job -->
            <svg
              v-else
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="1.8"
                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
              />
            </svg>
          </div>
        </div>

        <!-- Content -->
        <div class="flex-1 space-y-1">
          <h3
            class="font-bold text-slate-900 text-sm sm:text-base leading-snug"
          >
            {{ item.title }}
          </h3>
          <p class="text-xs text-slate-500 leading-relaxed">
            {{ item.description }}
          </p>
          <p class="text-[11px] text-slate-400 pt-1">{{ item.time }}</p>
        </div>
      </div>
    </div>
  </div>
</template>
