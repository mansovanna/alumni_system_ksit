<script setup lang="ts">
import { useAlumnisStore } from "~/stores/alumnis";
import { useEventAluniStore } from "~/stores/alumnis/events";

const alumniStores = useAlumnisStore();
const alumniEventStore = useEventAluniStore();
const formatMonth = (dateStr: string) =>
  new Date(dateStr)
    .toLocaleDateString("en-US", { month: "short" })
    .toUpperCase();

const formatDay = (dateStr: string) =>
  new Date(dateStr).getDate().toString().padStart(2, "0");
</script>

<template>
  <!-- Loading skeleton -->
  <div
    v-if="alumniStores.isLoading"
    class="grid grid-cols-1 sm:grid-cols-2 gap-4 animate-pulse"
  >
    <div
      v-for="n in 2"
      :key="n"
      class="bg-white rounded-2xl border border-teal-100/80 shadow-xs overflow-hidden"
    >
      <div class="h-40 w-full bg-slate-100"></div>
      <div class="p-4 space-y-2">
        <div class="h-3 bg-slate-100 rounded w-1/3"></div>
        <div class="h-4 bg-slate-100 rounded w-2/3"></div>
        <div class="h-3 bg-slate-100 rounded w-full"></div>
        <div class="h-3 bg-slate-100 rounded w-3/4"></div>
      </div>
      <div class="p-4 pt-0">
        <div class="h-9 bg-slate-100 rounded-xl"></div>
      </div>
    </div>
  </div>

  <!-- Empty state -->
  <div
    v-else-if="!alumniStores.data?.data.events?.length"
    class="bg-white rounded-2xl border border-teal-100/80 shadow-xs py-10 text-center"
  >
    <p class="text-sm text-slate-400">No upcoming events right now.</p>
  </div>

  <!-- Loaded content -->
  <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div
      v-for="event in alumniStores.data?.data.events"
      :key="event.id"
      @click="$router.push({ name: 'events-id', params: { id: event.id } })"
      class="bg-white rounded-2xl border border-teal-100/80 shadow-xs overflow-hidden flex flex-col justify-between group duration-200 cursor-pointer hover:-translate-y-1"
    >
      <div>
        <div class="relative h-40 w-full bg-slate-100 overflow-hidden">
          <img
            :src="event.banner_image_url"
            :alt="event.title"
            class="w-full h-full object-cover group-hover:scale-105 duration-300 ease-in-out"
          />

          <div
            class="absolute top-3 right-3 bg-white/95 backdrop-blur-xs rounded-lg px-2.5 py-1 text-center shadow-xs"
          >
            <span
              class="block text-[10px] font-extrabold text-slate-400 leading-tight uppercase"
            >
              {{ formatMonth(event.start_date) }}
            </span>
            <span class="block text-sm font-black text-slate-800 leading-none">
              {{ formatDay(event.start_date) }}
            </span>
          </div>
        </div>

        <div class="p-4 space-y-2">
          <div
            class="flex items-center gap-1.5 text-xs font-semibold text-teal-700"
          >
            <svg
              v-if="event.event_type === 'location'"
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

      <div class="p-4 pt-0" @click.stop>
        <div>
          <!-- Already Registered -->
          <div
            v-if="event.is_registered"
            class="w-full flex items-center justify-center gap-2 py-3 rounded-xl bg-teal-50 border border-teal-100 text-teal-700 text-sm font-semibold"
          >
            <svg
              class="w-5 h-5"
              fill="none"
              stroke="currentColor"
              stroke-width="2.5"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M5 13l4 4L19 7"
              />
            </svg>

            You are already registered
          </div>

          <!-- Register -->
          <button @click="alumniEventStore.registration(event.id)"
            v-else
            type="button"
            class="w-full py-3 px-4 rounded-xl bg-[#009688] hover:bg-[#00897b] active:bg-[#00796b] text-white text-sm font-semibold transition shadow-sm cursor-pointer"
          >
            Register Now
          </button>

          <!-- External Link -->
          <a
            v-if="event.external_link"
            :href="event.external_link"
            target="_blank"
            rel="noopener noreferrer"
            class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl border border-slate-200 text-slate-700 text-sm font-semibold hover:bg-slate-50 transition"
          >
            <svg
              class="w-4 h-4"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M14 3h7v7m0-7L10 14"
              />

              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M21 14v5a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h5"
              />
            </svg>

            Registration Form
          </a>
        </div>
      </div>
    </div>
  </div>
</template>
