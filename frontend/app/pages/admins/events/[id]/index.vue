<script setup lang="ts">
import type { EventItem } from "~/types/event";

definePageMeta({
  layout: "admin",
});

const route = useRoute();
const eventStore = useEventStore();

/*
|--------------------------------------------------------------------------
| Event ID
|--------------------------------------------------------------------------
*/

const eventId = computed(() => String(route.params.id));

/*
|--------------------------------------------------------------------------
| Fetch Event Data
|--------------------------------------------------------------------------
*/

const isLoading = ref(false);
const data = ref<EventItem | null>(null);

const handleDataAPI = async (id: number) => {
  if (!id) return;

  isLoading.value = true;

  try {
    const res = await eventStore.getEventById(id);
    data.value = res.data;
  } catch (e: any) {
    console.log(e.response);
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  handleDataAPI(Number(route.params?.id));
});

/*
|--------------------------------------------------------------------------
| Formatting Helpers
|--------------------------------------------------------------------------
*/

const formatDate = (dateStr?: string) => {
  if (!dateStr) return "—";
  return new Date(dateStr).toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  });
};

const formatTime = (dateStr?: string) => {
  if (!dateStr) return "—";
  return new Date(dateStr).toLocaleTimeString("en-US", {
    hour: "numeric",
    minute: "2-digit",
    hour12: true,
  });
};

const timeRange = computed(() => {
  if (!data.value) return "";
  return `${formatTime(data.value.start_date)} - ${formatTime(data.value.end_date)}`;
});

/*
|--------------------------------------------------------------------------
| Status Badge
|--------------------------------------------------------------------------
*/

const statusStyles: Record<string, string> = {
  draft: "bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-600/20",
  published: "bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20",
  ongoing: "bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-600/20",
  completed: "bg-gray-100 text-gray-600 ring-1 ring-inset ring-gray-400/20",
  cancelled: "bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20",
};

const statusLabel: Record<string, string> = {
  draft: "Draft",
  published: "Published",
  ongoing: "Ongoing",
  completed: "Completed",
  cancelled: "Cancelled",
};

const statusClass = computed(
  () => statusStyles[data.value?.status ?? "draft"] ?? statusStyles.draft,
);

const statusText = computed(
  () =>
    statusLabel[data.value?.status ?? "draft"] ?? data.value?.status ?? "Draft",
);

/*
|--------------------------------------------------------------------------
| Registration Percentage
|--------------------------------------------------------------------------
*/

const registrationPercentage = computed(() => {
  const registered = data.value?.registrations_count ?? 0;
  const capacity = data.value?.max_participants ?? 0;

  if (capacity <= 0) return 0;

  return Math.min(Math.round((registered / capacity) * 100), 100);
});

/*
|--------------------------------------------------------------------------
| Speaker Initials (no photo field in API)
|--------------------------------------------------------------------------
*/

const initials = (name: string) => {
  if (!name) return "?";
  const parts = name.trim().split(/\s+/);
  return parts
    .slice(0, 2)
    .map((p) => p[0])
    .join("")
    .toUpperCase();
};

/*
|--------------------------------------------------------------------------
| Actions
|--------------------------------------------------------------------------
*/

const editEvent = () => {
  navigateTo(`/admins/events/${eventId.value}/edit`);
};


const manageAttendees = () => {
  navigateTo(`/admins/events/${eventId.value}/attendees`);
};

const viewMap = () => {
  if (!import.meta.client || !data.value?.location) return;

  const address = encodeURIComponent(data.value.location);

  window.open(
    `https://www.google.com/maps/search/?api=1&query=${address}`,
    "_blank",
    "noopener,noreferrer",
  );
};
</script>

<template>
  <div class="w-full bg-gray-50 rounded-2xl overflow-clip">
    <!-- ===================================================== -->
    <!-- LOADING STATE -->
    <!-- ===================================================== -->

    <div v-if="isLoading" class="flex h-96 w-full items-center justify-center">
      <div
        class="h-8 w-8 animate-spin rounded-full border-2 border-gray-300 border-t-gray-900"
      ></div>
    </div>

    <!-- ===================================================== -->
    <!-- EMPTY / NOT FOUND -->
    <!-- ===================================================== -->

    <div
      v-else-if="!data"
      class="flex h-96 w-full flex-col items-center justify-center text-gray-400"
    >
      <p class="text-sm">Event not found.</p>
    </div>

    <template v-else>
      <!-- ===================================================== -->
      <!-- TOP HEADER -->
      <!-- ===================================================== -->

      <header class="border-b border-gray-200 bg-white">
        <div class="w-full px-4 py-4 sm:px-6 lg:px-8">
          <!-- Breadcrumb -->

          <div class="mb-4 flex items-center gap-2 text-xs text-gray-500">
            <NuxtLink
              to="/admins/events"
              class="transition hover:text-gray-900"
            >
              Events
            </NuxtLink>

            <svg
              class="h-3.5 w-3.5 text-gray-400"
              viewBox="0 0 20 20"
              fill="currentColor"
              aria-hidden="true"
            >
              <path
                fill-rule="evenodd"
                d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.24 4.24a.75.75 0 0 1 0 1.06l-4.24 4.24a.75.75 0 0 1-1.08 0Z"
                clip-rule="evenodd"
              />
            </svg>

            <span class="font-medium text-gray-700"> Event Detail </span>
          </div>

          <!-- Main Header -->

          <div
            class="flex flex-col gap-5 xl:flex-row xl:items-center xl:justify-between"
          >
            <!-- Event Info -->

            <div class="min-w-0">
              <div class="flex flex-wrap items-center gap-2">
                <h1
                  class="text-xl font-bold tracking-tight text-gray-900 sm:text-2xl"
                >
                  {{ data.title }}
                </h1>

                <span
                  :class="statusClass"
                  class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-medium"
                >
                  {{ statusText }}
                </span>
              </div>

              <div
                class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-gray-500"
              >
                <!-- Date -->

                <div class="flex items-center gap-2">
                  <svg
                    class="h-4 w-4 text-gray-400"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M8 2v4M16 2v4M3.5 9.5h17M5 4.5h14A1.5 1.5 0 0 1 20.5 6v13A1.5 1.5 0 0 1 19 20.5H5A1.5 1.5 0 0 1 3.5 19V6A1.5 1.5 0 0 1 5 4.5Z"
                    />
                  </svg>

                  <span> {{ formatDate(data.start_date) }} </span>
                </div>

                <!-- Time -->

                <div class="flex items-center gap-2">
                  <svg
                    class="h-4 w-4 text-gray-400"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                  >
                    <circle cx="12" cy="12" r="8.5" />
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M12 7.5v5l3.25 2"
                    />
                  </svg>

                  <span> {{ timeRange }} </span>
                </div>

                <!-- Location -->

                <div class="flex items-center gap-2">
                  <svg
                    class="h-4 w-4 text-gray-400"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                    aria-hidden="true"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M20 10.5c0 5.5-8 10-8 10s-8-4.5-8-10a8 8 0 1 1 16 0Z"
                    />
                    <circle cx="12" cy="10.5" r="2.5" />
                  </svg>

                  <span class="truncate max-w-55">
                    {{ data.location }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Actions -->

            <div class="flex flex-wrap items-center gap-2">
              <!-- Export -->

             
              <!-- Edit -->

              <button
                type="button"
                class="inline-flex h-9 items-center gap-2 rounded-md bg-gray-900 px-3 text-xs font-medium text-white transition hover:bg-gray-800"
                @click="editEvent"
              >
                <svg
                  class="h-4 w-4"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.8"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M4 20h4l10.5-10.5a2.12 2.12 0 0 0-3-3L5 17v3Z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m13.5 7.5 3 3"
                  />
                </svg>

                <span> Edit Event </span>
              </button>
            </div>
          </div>
        </div>
      </header>

      <!-- ===================================================== -->
      <!-- PAGE CONTENT -->
      <!-- ===================================================== -->

      <main class="w-full px-4 py-5 sm:px-6 lg:px-8">
        <!-- =================================================== -->
        <!-- COVER -->
        <!-- =================================================== -->

        <div
          class="relative h-60 w-full overflow-hidden rounded-xl border border-gray-200 bg-gray-200 shadow-sm sm:h-80 lg:h-100"
        >
          <img
            v-if="data.banner_image_url"
            :src="data.banner_image_url"
            :alt="data.title"
            class="h-full w-full object-cover"
          />

          <div
            v-else
            class="flex h-full w-full items-center justify-center bg-gray-200 text-xs text-gray-400"
          >
            No banner image
          </div>

          <!-- Overlay -->

          <div
            class="absolute inset-0 bg-linear-to-t from-black/70 via-black/10 to-transparent"
          ></div>

          <!-- Event Type badge -->

          <div
            class="absolute left-4 top-4 inline-flex items-center gap-2 rounded-full bg-black/50 px-3 py-1.5 text-[10px] font-medium capitalize text-white backdrop-blur-md"
          >
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
              <path
                d="m12 3 2.78 5.63 6.22.9-4.5 4.39 1.06 6.2L12 17.2l-5.56 2.92 1.06-6.2L3 9.53l6.22-.9L12 3Z"
              />
            </svg>

            {{ data.event_type }}
          </div>

          <!-- Cover bottom info -->

          <div class="absolute bottom-0 left-0 right-0 p-5 sm:p-7">
            <div class="max-w-3xl">
              <p
                class="mb-2 text-xs font-medium uppercase tracking-wider text-white/70"
              >
                {{ data.event_type }}
              </p>

              <h2 class="text-xl font-bold text-white sm:text-3xl">
                {{ data.title }}
              </h2>
            </div>
          </div>
        </div>

        <!-- =================================================== -->
        <!-- MAIN GRID -->
        <!-- =================================================== -->

        <div
          class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_360px]"
        >
          <!-- ================================================= -->
          <!-- LEFT -->
          <!-- ================================================= -->

          <div class="min-w-0 space-y-6">
            <!-- ABOUT -->

            <section class="rounded-xl border border-gray-200 bg-white">
              <div class="border-b border-gray-100 px-5 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100"
                  >
                    <svg
                      class="h-4 w-4 text-gray-700"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.8"
                    >
                      <circle cx="12" cy="12" r="9" />
                      <path stroke-linecap="round" d="M12 10.5v6" />
                      <path stroke-linecap="round" d="M12 7.5h.01" />
                    </svg>
                  </div>

                  <div>
                    <h2 class="text-sm font-semibold text-gray-900">
                      About This Event
                    </h2>

                    <p class="mt-0.5 text-[10px] text-gray-400">
                      Event description and information
                    </p>
                  </div>
                </div>
              </div>

              <div class="px-5 py-5">
                <p class="whitespace-pre-line text-sm leading-7 text-gray-600">
                  {{ data.description }}
                </p>
              </div>
            </section>

            <!-- AGENDA -->

            <section
              v-if="data.agendas?.length"
              class="rounded-xl border border-gray-200 bg-white"
            >
              <div class="border-b border-gray-100 px-5 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100"
                  >
                    <svg
                      class="h-4 w-4 text-gray-700"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.8"
                    >
                      <circle cx="12" cy="12" r="9" />
                      <path stroke-linecap="round" d="M12 7v5l3 2" />
                    </svg>
                  </div>

                  <div>
                    <h2 class="text-sm font-semibold text-gray-900">
                      Event Agenda
                    </h2>

                    <p class="mt-0.5 text-[10px] text-gray-400">
                      Schedule and activities
                    </p>
                  </div>
                </div>
              </div>

              <div class="px-5 py-6">
                <div class="relative">
                  <!-- Timeline -->

                  <div
                    class="absolute bottom-4 left-1.75 top-4 w-px bg-gray-200"
                  ></div>

                  <div class="space-y-7">
                    <div
                      v-for="(item, index) in data.agendas"
                      :key="index"
                      class="relative flex gap-5"
                    >
                      <!-- Dot -->

                      <div
                        class="relative z-10 mt-1.5 h-3.75 w-3.75 shrink-0 rounded-full border-2 border-white bg-gray-800 ring-1 ring-gray-300"
                      ></div>

                      <!-- Content -->

                      <div class="min-w-0 flex-1">
                        <div
                          class="mb-1 inline-flex rounded-md bg-gray-100 px-2 py-1 text-[10px] font-medium text-gray-500"
                        >
                          {{ item.time }}
                        </div>

                        <h3 class="mt-1 text-sm font-semibold text-gray-900">
                          {{ item.title }}
                        </h3>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>

            <!-- SPEAKERS -->

            <section
              v-if="data.speakers?.length"
              class="rounded-xl border border-gray-200 bg-white"
            >
              <div class="border-b border-gray-100 px-5 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100"
                  >
                    <svg
                      class="h-4 w-4 text-gray-700"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.8"
                    >
                      <circle cx="9" cy="8" r="3" />
                      <path
                        stroke-linecap="round"
                        d="M3.5 19c.5-3.2 2.3-5 5.5-5s5 1.8 5.5 5"
                      />
                      <path
                        stroke-linecap="round"
                        d="M15.5 5.5a3 3 0 0 1 0 5.8M17 14c2 .6 3.2 2.1 3.5 5"
                      />
                    </svg>
                  </div>

                  <div>
                    <h2 class="text-sm font-semibold text-gray-900">
                      Featured Speakers
                    </h2>

                    <p class="mt-0.5 text-[10px] text-gray-400">
                      Speakers and presenters
                    </p>
                  </div>
                </div>
              </div>

              <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2">
                <div
                  v-for="(speaker, index) in data.speakers"
                  :key="index"
                  class="flex items-center gap-4 rounded-lg border border-gray-200 p-4 transition hover:border-gray-300 hover:shadow-sm"
                >
                  <div
                    class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-gray-900 text-sm font-semibold text-white ring-2 ring-gray-100"
                  >
                    {{ initials(speaker.name ?? "") }}
                  </div>

                  <div class="min-w-0 flex-1">
                    <h3 class="truncate text-sm font-semibold text-gray-900">
                      {{ speaker.name }}
                    </h3>

                    <p class="mt-1 truncate text-xs text-gray-500">
                      {{ speaker.professional_title }}
                    </p>
                  </div>
                </div>
              </div>
            </section>
          </div>

          <!-- ================================================= -->
          <!-- RIGHT SIDEBAR -->
          <!-- ================================================= -->

          <aside class="min-w-0 space-y-6">
            <!-- REGISTRATION -->

            <section class="rounded-xl border border-gray-200 bg-white p-5">
              <div class="flex items-center justify-between">
                <div>
                  <h2 class="text-sm font-semibold text-gray-900">
                    Registration
                  </h2>

                  <p class="mt-0.5 text-[10px] text-gray-400">
                    Current attendance
                  </p>
                </div>

                <span
                  :class="statusClass"
                  class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[9px] font-medium"
                >
                  {{ statusText }}
                </span>
              </div>

              <!-- Stats -->

              <div class="mt-5 grid grid-cols-2 gap-3">
                <div class="rounded-lg bg-gray-50 p-3">
                  <p class="text-[10px] text-gray-400">Attending</p>

                  <p class="mt-1 text-xl font-bold text-gray-900">
                    {{ data.registrations_count }}
                  </p>
                </div>

                <div class="rounded-lg bg-gray-50 p-3">
                  <p class="text-[10px] text-gray-400">Capacity</p>

                  <p class="mt-1 text-xl font-bold text-gray-900">
                    {{ data.max_participants }}
                  </p>
                </div>
              </div>

              <!-- Progress -->

              <div class="mt-5">
                <div class="mb-2 flex items-center justify-between text-[10px]">
                  <span class="font-medium text-gray-500">
                    Registration Progress
                  </span>

                  <span class="font-semibold text-gray-900">
                    {{ registrationPercentage }}%
                  </span>
                </div>

                <div class="h-2 overflow-hidden rounded-full bg-gray-100">
                  <div
                    class="h-full rounded-full bg-gray-900 transition-all duration-500"
                    :style="{
                      width: `${registrationPercentage}%`,
                    }"
                  ></div>
                </div>
              </div>

              <!-- Deadline -->

              <div
                class="mt-5 flex items-center justify-between border-t border-gray-100 pt-4"
              >
                <span class="text-[10px] text-gray-400">
                  Registration Closes
                </span>

                <span class="text-xs font-medium text-gray-700">
                  {{ formatDate(data.registration_deadline ?? "") }}
                </span>
              </div>

              <!-- External link -->

              <a
                v-if="data.external_link"
                :href="data.external_link"
                target="_blank"
                rel="noopener noreferrer"
                class="mt-3 block truncate text-[10px] text-gray-400 underline decoration-dotted hover:text-gray-700"
              >
                {{ data.external_link }}
              </a>

              <!-- Attendees -->

              <button
                type="button"
                class="mt-5 flex h-10 w-full items-center justify-center gap-2 rounded-md bg-gray-900 px-4 text-xs font-medium text-white transition hover:bg-gray-800"
                @click="manageAttendees"
              >
                <svg
                  class="h-4 w-4"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.8"
                >
                  <circle cx="9" cy="8" r="3" />
                  <path
                    stroke-linecap="round"
                    d="M3.5 19c.5-3.2 2.3-5 5.5-5s5 1.8 5.5 5"
                  />
                  <path stroke-linecap="round" d="M16 11a3 3 0 1 0 0-6" />
                  <path stroke-linecap="round" d="M16 14c2.3.4 3.8 2 4.2 4" />
                </svg>

                Manage Attendee List
              </button>
            </section>

            <!-- QUICK INFO -->

            <section class="rounded-xl border border-gray-200 bg-white p-5">
              <h2 class="text-sm font-semibold text-gray-900">Quick Info</h2>

              <p class="mt-0.5 text-[10px] text-gray-400">Event information</p>

              <div class="mt-5 divide-y divide-gray-100">
                <!-- Event Type -->

                <div class="flex gap-3 py-3 first:pt-0">
                  <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100"
                  >
                    <svg
                      class="h-4 w-4 text-gray-600"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.8"
                    >
                      <rect x="4" y="4" width="16" height="16" rx="2" />
                      <path d="M8 8h8M8 12h8M8 16h5" stroke-linecap="round" />
                    </svg>
                  </div>

                  <div>
                    <p class="text-[9px] text-gray-400">Event Type</p>

                    <p
                      class="mt-1 text-xs font-medium capitalize text-gray-700"
                    >
                      {{ data.event_type }}
                    </p>
                  </div>
                </div>

                <!-- Max Participants -->

                <div class="flex gap-3 py-3">
                  <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100"
                  >
                    <svg
                      class="h-4 w-4 text-gray-600"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.8"
                    >
                      <circle cx="9" cy="8" r="3" />
                      <path
                        stroke-linecap="round"
                        d="M3.5 19c.5-3.2 2.3-5 5.5-5s5 1.8 5.5 5"
                      />
                    </svg>
                  </div>

                  <div>
                    <p class="text-[9px] text-gray-400">Max Participants</p>

                    <p class="mt-1 text-xs font-medium text-gray-700">
                      {{ data.max_participants }}
                    </p>
                  </div>
                </div>

                <!-- Contact -->

                <div class="flex gap-3 py-3 last:pb-0">
                  <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100"
                  >
                    <svg
                      class="h-4 w-4 text-gray-600"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.8"
                    >
                      <circle cx="12" cy="8" r="3" />
                      <path
                        stroke-linecap="round"
                        d="M5 20c.7-3.5 3-5.5 7-5.5s6.3 2 7 5.5"
                      />
                    </svg>
                  </div>

                  <div class="min-w-0">
                    <p class="text-[9px] text-gray-400">Organized by</p>

                    <p class="mt-1 text-xs font-medium text-gray-700">
                      {{
                        data.creator?.name_english || data.creator?.name_khmer
                      }}
                    </p>

                    <p class="mt-0.5 truncate text-[10px] text-gray-400">
                      {{ data.creator?.email }}
                    </p>
                  </div>
                </div>
              </div>
            </section>

            <!-- LOCATION -->

            <section
              class="overflow-hidden rounded-xl border border-gray-200 bg-white"
            >
              <div class="border-b border-gray-100 px-5 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100"
                  >
                    <svg
                      class="h-4 w-4 text-gray-700"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.8"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M20 10.5c0 5.5-8 10-8 10s-8-4.5-8-10a8 8 0 1 1 16 0Z"
                      />
                      <circle cx="12" cy="10.5" r="2.5" />
                    </svg>
                  </div>

                  <div>
                    <h2 class="text-sm font-semibold text-gray-900">
                      Location
                    </h2>

                    <p class="mt-0.5 text-[10px] text-gray-400">Event venue</p>
                  </div>
                </div>
              </div>

              <!-- Address -->

              <div class="p-5">
                <p class="text-xs leading-6 text-gray-600">
                  {{ data.location }}
                </p>

                <button
                  type="button"
                  class="mt-4 inline-flex items-center gap-2 text-xs font-medium text-gray-700 transition hover:text-black"
                  @click="viewMap"
                >
                  <svg
                    class="h-4 w-4"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M14 5h5v5M19 5l-8 8"
                    />
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M18 13v5a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5"
                    />
                  </svg>

                  View on Map
                </button>
              </div>
            </section>
          </aside>
        </div>
      </main>
    </template>
  </div>
</template>
