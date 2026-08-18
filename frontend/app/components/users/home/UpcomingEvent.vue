<script setup lang="ts">
const upcomingEvents = ref([
  {
    id: 1,
    title: "Tech Alumni Mixer 2024",
    location: "San Francisco, CA",
    dateMonth: "OCT",
    dateDay: "24",
    description:
      "Join fellow graduates in the tech industry for an evening of networking, drinks, and...",
    image:
      "https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=600&auto=format&fit=crop",
    type: "location",
  },
  {
    id: 2,
    title: "Resume Workshop: Post-Grad Edition",
    location: "Virtual Event",
    dateMonth: "NOV",
    dateDay: "02",
    description:
      "Learn how to translate your early career experiences into a compelling resume...",
    image:
      "https://images.unsplash.com/photo-1588196749597-9ff075ee6b5b?q=80&w=600&auto=format&fit=crop",
    type: "virtual",
  },
]);
</script>

<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div
      v-for="event in upcomingEvents"
      :key="event.id"
      class="bg-white rounded-2xl border border-teal-100/80 shadow-xs overflow-hidden flex flex-col justify-between"
    >
      <div>
        <!-- Image & Date Badge -->
        <div class="relative h-40 w-full bg-slate-100 overflow-hidden">
          <img
            :src="event.image"
            :alt="event.title"
            class="w-full h-full object-cover"
          />

          <!-- Date Badge Overlay -->
          <div
            class="absolute top-3 right-3 bg-white/95 backdrop-blur-xs rounded-lg px-2.5 py-1 text-center shadow-xs"
          >
            <span
              class="block text-[10px] font-extrabold text-slate-400 leading-tight uppercase"
              >{{ event.dateMonth }}</span
            >
            <span
              class="block text-sm font-black text-slate-800 leading-none"
              >{{ event.dateDay }}</span
            >
          </div>
        </div>

        <!-- Event Info -->
        <div class="p-4 space-y-2">
          <!-- Location / Type -->
          <div
            class="flex items-center gap-1.5 text-xs font-semibold text-teal-700"
          >
            <svg
              v-if="event.type === 'location'"
              class="w-3.5 h-3.5"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <path
                d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"
              />
              <circle cx="12" cy="10" r="3" />
            </svg>
            <svg
              v-else
              class="w-3.5 h-3.5"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <polygon points="23 7 16 12 23 17 23 7" />
              <rect x="1" y="5" width="15" height="14" rx="2" ry="2" />
            </svg>
            <span>{{ event.location }}</span>
          </div>

          <h3 class="text-sm font-bold text-slate-900 line-clamp-1">
            {{ event.title }}
          </h3>

          <p class="text-xs text-slate-500 leading-relaxed line-clamp-2">
            {{ event.description }}
          </p>
        </div>
      </div>

      <!-- Button -->
      <div class="p-4 pt-0">
        <button
          type="button"
          class="w-full py-2 bg-sky-50/80 hover:bg-sky-100 text-[#036250] text-xs font-bold rounded-xl border border-sky-100 transition cursor-pointer"
        >
          Register Now
        </button>
      </div>
    </div>
  </div>
</template>
