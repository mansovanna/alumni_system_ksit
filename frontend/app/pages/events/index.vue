<script setup lang="ts">
import { ref } from "vue";

const activeTab = ref("Upcoming");
const tabs = ["Upcoming", "Past", "All"];

const events = ref([
  {
    id: 1,
    title: "Annual Tech Industry Mixer",
    date: "Oct 24, 2024 • 6:00 PM",
    location: "Innovation Hub, Downtown Campus",
    type: "Networking",
    isOnline: false,
    status: "register",
    image:
      "https://images.unsplash.com/photo-1511578314322-379afb476865?w=600&auto=format&fit=crop&q=80",
  },
  {
    id: 2,
    title: "Leadership in the Digital Age",
    date: "Nov 05, 2024 • 10:00 AM",
    location: "Virtual Event (Zoom)",
    type: "Workshop",
    isOnline: true,
    status: "registered",
    image:
      "https://images.unsplash.com/photo-1524178232363-1fb2b075b655?w=600&auto=format&fit=crop&q=80",
  },
  {
    id: 3,
    title: "Winter Alumni Gala & Awards",
    date: "Dec 12, 2024 • 7:30 PM",
    location: "The Grand Hotel, City Center",
    type: "Gala",
    isOnline: false,
    status: "register",
    image:
      "https://images.unsplash.com/photo-1519671482749-fd09be7ccebf?w=600&auto=format&fit=crop&q=80",
  },
  {
    id: 4,
    title: "Spring Career & Mentorship Fair",
    date: "Jan 15, 2025 • 9:00 AM",
    location: "Main University Arena",
    type: "Career",
    isOnline: false,
    status: "register",
    image:
      "https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600&auto=format&fit=crop&q=80",
  },
]);
</script>

<template>
  <div class=" space-y-4 ">
    <div>
    <h1 class="text-2xl font-bold text-slate-900">Events</h1>
  </div>

  <!-- Text Tabs Navigation -->
  <div class="flex items-center gap-6 border-b border-slate-200/80 pb-2">
    <button
      v-for="tab in tabs"
      :key="tab"
      @click="activeTab = tab"
      :class="[
        'text-sm font-medium transition-colors relative pb-2 -mb-2',
        activeTab === tab
          ? 'text-[#006B58] font-semibold after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-[#006B58]'
          : 'text-slate-500 hover:text-slate-800',
      ]"
    >
      {{ tab }}
    </button>
  </div>

  <!-- Events Cards Grid -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-2">
    <div
      v-for="event in events"
      :key="event.id"
      class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between hover:shadow-sm transition"
    >
      <div>
        <!-- Card Image & Badge -->
        <div class="relative h-48 w-full overflow-hidden bg-slate-100">
          <img
            :src="event.image"
            :alt="event.title"
            class="w-full h-full object-cover"
          />
          <span
            class="absolute top-3 right-3 bg-white/90 backdrop-blur-xs text-slate-700 text-[11px] font-semibold px-2.5 py-1 rounded-md shadow-xs border border-slate-200/50"
          >
            {{ event.type }}
          </span>
        </div>

        <!-- Card Body -->
        <div class="p-5 space-y-2">
          <!-- Date & Time -->
          <p class="text-xs font-semibold text-[#006B58]">
            {{ event.date }}
          </p>

          <!-- Event Title -->
          <h3
            class="font-bold text-slate-900 text-base leading-snug line-clamp-2"
          >
            {{ event.title }}
          </h3>

          <!-- Location / Link -->
          <div class="flex items-center gap-1.5 text-xs text-slate-500 pt-1">
            <svg
              v-if="event.isOnline"
              class="w-4 h-4 text-slate-400 shrink-0"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"
              />
            </svg>
            <svg
              v-else
              class="w-4 h-4 text-slate-400 shrink-0"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
              />
            </svg>
            <span class="truncate">{{ event.location }}</span>
          </div>
        </div>
      </div>

      <!-- Action Button -->
      <div class="px-5 pb-5 pt-1">
        <button
          v-if="event.status === 'registered'"
          class="w-full bg-[#006B58] text-white font-medium text-xs py-2.5 rounded-lg flex items-center justify-center gap-1.5 cursor-default"
        >
          <svg
            class="w-3.5 h-3.5"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2.5"
              d="M5 13l4 4L19 7"
            />
          </svg>
          <span>Registered</span>
        </button>

        <button
          v-else-if="event.status === 'waitlist'"
          class="w-full border border-slate-300 text-slate-700 font-medium text-xs py-2.5 rounded-lg hover:bg-slate-50 transition cursor-pointer"
        >
          Join Waitlist
        </button>

        <button
          v-else
          class="w-full bg-[#006B58] hover:bg-[#005546] text-white font-medium text-xs py-2.5 rounded-lg transition cursor-pointer"
        >
          Register
        </button>
      </div>
    </div>
  </div>
  </div>
</template>
