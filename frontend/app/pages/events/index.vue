<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";

import { useEventAluniStore } from "~/stores/alumnis/events";

const alumniEventStore = useEventAluniStore();

// ==================================================
// TABS
// ==================================================

const activeTab = ref<"Upcoming" | "Past" | "All">("Upcoming");

const tabs = ["Upcoming", "Past", "All"] as const;

// ==================================================
// MAP UI TAB -> BACKEND FILTER
// ==================================================

const tabToFilter = {
  Upcoming: "upcoming",
  Past: "past",
  All: "all",
} as const;

// ==================================================
// CHANGE TAB
// ==================================================

const changeTab = async (tab: "Upcoming" | "Past" | "All") => {
  if (activeTab.value === tab) {
    return;
  }

  activeTab.value = tab;

  await alumniEventStore.changeFilter(tabToFilter[tab]);
};

// ==================================================
// FORMAT DATE
// ==================================================

const formatEventDate = (startDate: string, endDate: string) => {
  const start = new Date(startDate);
  const end = new Date(endDate);

  const dateFormatter = new Intl.DateTimeFormat("en-US", {
    month: "short",
    day: "2-digit",
    year: "numeric",
  });

  const timeFormatter = new Intl.DateTimeFormat("en-US", {
    hour: "numeric",
    minute: "2-digit",
  });

  const startDateText = dateFormatter.format(start);

  const startTimeText = timeFormatter.format(start);

  const endTimeText = timeFormatter.format(end);

  return `${startDateText} • ${startTimeText} - ${endTimeText}`;
};

// ==================================================
// CHECK PAST EVENT
// ==================================================

const isPastEvent = (endDate: string) => {
  return new Date(endDate).getTime() < Date.now();
};

// ==================================================
// INFINITE SCROLL
// ==================================================

const handleScroll = async () => {
  const scrollPosition = window.innerHeight + window.scrollY;

  const pageHeight = document.documentElement.scrollHeight;

  if (
    scrollPosition >= pageHeight - 300 &&
    !alumniEventStore.isLoading &&
    !alumniEventStore.isLoadingMore &&
    alumniEventStore.hasMore
  ) {
    await alumniEventStore.loadMore();
  }
};

// ==================================================
// INITIAL LOAD
// ==================================================

onMounted(async () => {
  await alumniEventStore.getEvents("upcoming");

  window.addEventListener("scroll", handleScroll);
});

// ==================================================
// CLEANUP
// ==================================================

onUnmounted(() => {
  window.removeEventListener("scroll", handleScroll);
});
</script>

<template>
  <div class="space-y-4">
    <!-- ==================================================
         HEADER
    ================================================== -->

    <div>
      <h1 class="text-2xl font-bold text-slate-900">Events</h1>
    </div>

    <!-- ==================================================
         TABS
    ================================================== -->

    <div class="flex items-center gap-6 border-b border-slate-200/80 pb-2">
      <button
        v-for="tab in tabs"
        :key="tab"
        @click="changeTab(tab)"
        :disabled="alumniEventStore.isLoading"
        :class="[
          'text-sm font-medium transition-colors relative pb-2 -mb-2 cursor-pointer disabled:cursor-not-allowed',

          activeTab === tab
            ? 'text-[#006B58] font-semibold after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-[#006B58]'
            : 'text-slate-500 hover:text-slate-800',
        ]"
      >
        {{ tab }}
      </button>
    </div>

    <!-- ==================================================
         LOADING
    ================================================== -->

    <div
      v-if="alumniEventStore.isLoading"
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-2"
    >
      <div
        v-for="i in 6"
        :key="i"
        class="bg-white rounded-xl border border-slate-200 overflow-hidden animate-pulse"
      >
        <div class="h-48 bg-slate-200"></div>

        <div class="p-5 space-y-3">
          <div class="h-3 bg-slate-200 rounded w-32"></div>

          <div class="h-5 bg-slate-200 rounded w-full"></div>

          <div class="h-3 bg-slate-200 rounded w-40"></div>
        </div>
      </div>
    </div>

    <!-- ==================================================
         ERROR
    ================================================== -->

    <div
      v-else-if="alumniEventStore.messageError"
      class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-600"
    >
      {{ alumniEventStore.messageError }}
    </div>

    <!-- ==================================================
         EMPTY
    ================================================== -->

    <div
      v-else-if="alumniEventStore.eventList.length === 0"
      class="flex flex-col items-center justify-center py-16 text-center"
    >
      <div
        class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center mb-4"
      >
        <svg
          class="w-7 h-7 text-slate-400"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
          />
        </svg>
      </div>

      <h3 class="font-semibold text-slate-800">No Events Found</h3>

      <p class="text-sm text-slate-500 mt-1">
        There are no
        {{ activeTab.toLowerCase() }}
        events.
      </p>
    </div>

    <!-- ==================================================
         EVENTS
    ================================================== -->

    <div
      v-else
      class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 pt-2"
    >
      <div
        v-for="event in alumniEventStore.eventList"
        :key="event.id"
        @click="
          $router.push({
            name: 'events-id',
            params: {
              id: event.id,
            },
          })
        "
        class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between hover:shadow-sm transition cursor-pointer"
      >
        <!-- ==================================================
             EVENT CONTENT
        ================================================== -->

        <div>
          <!-- Image -->

          <div class="relative h-48 w-full overflow-hidden bg-slate-100">
            <img
              :src="
                event.banner_image_url ||
                event.banner_image ||
                '/images/event-placeholder.jpg'
              "
              :alt="event.title"
              class="w-full h-full object-cover"
            />

            <!-- Event Type -->

            <span
              class="absolute top-3 right-3 bg-white/90 backdrop-blur-xs text-slate-700 text-[11px] font-semibold px-2.5 py-1 rounded-md shadow-xs border border-slate-200/50"
            >
              {{ event.event_type }}
            </span>
          </div>

          <!-- Card Body -->

          <div class="p-5 space-y-2">
            <!-- Date -->

            <p class="text-xs font-semibold text-[#006B58]">
              {{ formatEventDate(event.start_date, event.end_date) }}
            </p>

            <!-- Title -->

            <h3
              class="font-bold text-slate-900 text-base leading-snug line-clamp-2"
            >
              {{ event.title }}
            </h3>

            <!-- Location -->

            <div class="flex items-center gap-1.5 text-xs text-slate-500 pt-1">
              <!-- Online -->

              <svg
                v-if="event.external_link"
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

              <!-- Physical -->

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

              <span class="truncate">
                {{ event.location || "Online Event" }}
              </span>
            </div>
          </div>
        </div>

        <!-- ==================================================
             ACTION
        ================================================== -->

        <div class="px-5 pb-5 pt-1 w-full" @click.stop>
          <!-- REGISTERED -->

          <button
            v-if="event.is_registered"
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

            <span> Registered </span>
          </button>

          <!-- PAST -->

          <button
            v-else-if="isPastEvent(event.end_date)"
            disabled
            class="w-full bg-slate-100 text-slate-400 font-medium text-xs py-2.5 rounded-lg cursor-not-allowed"
          >
            Event Ended
          </button>

          <!-- REGISTER -->

          <button
            v-else
            @click="alumniEventStore.registration(event.id)"
            :disabled="
              alumniEventStore.isLoadingRegister.value &&
              alumniEventStore.isLoadingRegister.id === event.id
            "
            class="w-full bg-[#006B58] hover:bg-[#005546] disabled:bg-[#006B58]/60 text-white font-medium text-xs py-2.5 rounded-lg transition cursor-pointer disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            <!-- Loading -->

            <svg
              v-if="
                alumniEventStore.isLoadingRegister.value &&
                alumniEventStore.isLoadingRegister.id === event.id
              "
              class="w-4 h-4 animate-spin"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              />

              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
              />
            </svg>

            <span>
              {{
                alumniEventStore.isLoadingRegister.value &&
                alumniEventStore.isLoadingRegister.id === event.id
                  ? "Registering..."
                  : "Register"
              }}
            </span>
          </button>
        </div>
      </div>
    </div>

    <!-- ==================================================
         LOADING MORE
    ================================================== -->

    <div v-if="alumniEventStore.isLoadingMore" class="flex justify-center py-6">
      <div
        class="w-6 h-6 border-2 border-[#006B58] border-t-transparent rounded-full animate-spin"
      ></div>
    </div>

    <!-- ==================================================
         NO MORE
    ================================================== -->

    <div
      v-else-if="
        alumniEventStore.eventList.length > 0 && !alumniEventStore.hasMore
      "
      class="text-center py-6 text-xs text-slate-400"
    >
      No more events
    </div>
  </div>
</template>
