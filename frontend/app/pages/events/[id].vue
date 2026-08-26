<script setup lang="ts">
import { computed, onMounted } from "vue";
import { useEventAluniStore } from "~/stores/alumnis/events";

definePageMeta({
  layout: "default",
});

const alumniEventStore = useEventAluniStore();
const route = useRoute();

// =====================================================
// Event Data
// =====================================================

const event = computed(() => {
  return alumniEventStore.data_Detail?.data ?? null;
});

// =====================================================
// Date / Time
// =====================================================

const formatDate = (date: string | null | undefined) => {
  if (!date) return "-";

  return new Intl.DateTimeFormat("en-US", {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
    timeZone: "Asia/Phnom_Penh",
  }).format(new Date(date));
};

const formatTime = (date: string | null | undefined) => {
  if (!date) return "-";

  return new Intl.DateTimeFormat("en-US", {
    hour: "numeric",
    minute: "2-digit",
    hour12: true,
    timeZone: "Asia/Phnom_Penh",
  }).format(new Date(date));
};

const eventDate = computed(() => {
  return formatDate(event.value?.start_date);
});

const eventTime = computed(() => {
  if (!event.value?.start_date || !event.value?.end_date) {
    return "-";
  }

  return `${formatTime(event.value.start_date)} - ${formatTime(
    event.value.end_date,
  )}`;
});

const registrationDeadline = computed(() => {
  return formatDate(event.value?.registration_deadline);
});

// =====================================================
// Status
// =====================================================

const statusClass = computed(() => {
  switch (event.value?.status) {
    case "published":
      return "bg-emerald-50 text-emerald-700 border-emerald-100";

    case "ongoing":
      return "bg-blue-50 text-blue-700 border-blue-100";

    case "completed":
      return "bg-slate-100 text-slate-600 border-slate-200";

    case "cancelled":
      return "bg-red-50 text-red-700 border-red-100";

    case "draft":
      return "bg-amber-50 text-amber-700 border-amber-100";

    default:
      return "bg-slate-100 text-slate-600 border-slate-200";
  }
});

// =====================================================
// Registration
// =====================================================

const isRegistered = computed(() => {
  return event.value?.is_registered ?? false;
});

// =====================================================
// Load Event
// =====================================================

onMounted(async () => {
  const id = Number(route.params.id);

  if (!Number.isNaN(id)) {
    await alumniEventStore.getDetailt(id);
  }
});
</script>

<template>
  <div>
    <!-- ================================================= -->
    <!-- HEADER -->
    <!-- ================================================= -->

    <div
      class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-7"
    >
      <div>
        <div class="flex items-center gap-2 mb-1">
          <span class="w-1.5 h-6 rounded-full bg-[#009688]"></span>

          <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">
            Event Details
          </h1>
        </div>

        <p class="text-sm text-slate-500">
          View event information, schedule and registration details.
        </p>
      </div>

      <button
        type="button"
        @click="$router.back()"
        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl text-sm font-medium hover:bg-slate-50 hover:border-slate-300 transition shadow-sm"
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
            d="M15 19l-7-7 7-7"
          />
        </svg>

        Back
      </button>
    </div>

    <!-- ================================================= -->
    <!-- LOADING -->
    <!-- ================================================= -->

    <div
      v-if="alumniEventStore.isLoading"
      class="bg-white rounded-2xl border border-slate-200 p-12"
    >
      <div class="flex flex-col items-center justify-center">
        <div
          class="w-10 h-10 border-4 border-slate-200 border-t-[#009688] rounded-full animate-spin"
        ></div>

        <p class="mt-4 text-sm text-slate-500">Loading event details...</p>
      </div>
    </div>

    <!-- ================================================= -->
    <!-- ERROR -->
    <!-- ================================================= -->

    <div
      v-else-if="alumniEventStore.messageError"
      class="rounded-2xl border border-red-200 bg-red-50 p-6"
    >
      <div class="flex items-start gap-3">
        <div
          class="w-10 h-10 shrink-0 rounded-xl bg-red-100 text-red-600 flex items-center justify-center"
        >
          <svg
            class="w-5 h-5"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M12 9v4m0 4h.01M10.3 3.8L2.9 17a2 2 0 001.7 3h14.8a2 2 0 001.7-3L13.7 3.8a2 2 0 00-3.4 0z"
            />
          </svg>
        </div>

        <div>
          <h3 class="font-semibold text-red-800">Unable to load event</h3>

          <p class="text-sm text-red-600 mt-1">
            {{ alumniEventStore.messageError }}
          </p>
        </div>
      </div>
    </div>

    <!-- ================================================= -->
    <!-- EVENT -->
    <!-- ================================================= -->

    <div
      v-else-if="event"
      class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start"
    >
      <!-- ================================================= -->
      <!-- LEFT CONTENT -->
      <!-- ================================================= -->

      <div class="lg:col-span-8 space-y-6">
        <!-- ================================================= -->
        <!-- BANNER -->
        <!-- ================================================= -->

        <div
          class="relative overflow-hidden rounded-2xl bg-slate-200 border border-slate-200 "
        >
          <img
            v-if="event.banner_image"
            :src="event.banner_image"
            :alt="event.title"
            class="w-full h-[260px] sm:h-[360px] lg:h-[420px] object-cover"
          />

          <div
            v-else
            class="w-full h-[360px] flex items-center justify-center text-slate-400"
          >
            <div class="text-center">
              <svg
                class="w-12 h-12 mx-auto mb-3"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M3 16l5-5 4 4 3-3 6 6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                />
              </svg>

              <p class="text-sm">No banner image</p>
            </div>
          </div>

          <!-- Image Overlay -->
          <div
            class="absolute inset-x-0 bottom-0 p-5 sm:p-7 bg-gradient-to-t from-black/70 via-black/30 to-transparent"
          >
            <div class="flex flex-wrap gap-2">
              <span
                :class="statusClass"
                class="inline-flex items-center px-3 py-1.5 rounded-full border text-xs font-semibold capitalize backdrop-blur-sm"
              >
                {{ event.status }}
              </span>

              <span
                class="inline-flex items-center px-3 py-1.5 rounded-full bg-white/90 text-slate-700 text-xs font-semibold capitalize"
              >
                {{ event.event_type }}
              </span>
            </div>
          </div>
        </div>

        <!-- ================================================= -->
        <!-- ABOUT EVENT -->
        <!-- ================================================= -->

        <section
          class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8"
        >
          <div class="flex items-center gap-3 mb-5">
            <div
              class="w-10 h-10 rounded-xl bg-teal-50 text-[#009688] flex items-center justify-center"
            >
              <svg
                class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M13 16h-1v-4h-1m1-4h.01M12 21a9 9 0 100-18 9 9 0 000 18z"
                />
              </svg>
            </div>

            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">
              About the Event
            </h2>
          </div>

          <div class="text-[15px] text-slate-600 leading-7 whitespace-pre-line">
            {{ event.description }}
          </div>
        </section>

        <!-- ================================================= -->
        <!-- AGENDA -->
        <!-- ================================================= -->

        <section
          v-if="event.agendas?.length"
          class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8"
        >
          <div class="flex items-center gap-3 mb-6">
            <div
              class="w-10 h-10 rounded-xl bg-teal-50 text-[#009688] flex items-center justify-center"
            >
              <svg
                class="w-5 h-5"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"
                />
              </svg>
            </div>

            <div>
              <h2 class="text-xl sm:text-2xl font-bold text-slate-900">
                Event Agenda
              </h2>

              <p class="text-sm text-slate-400">Program schedule</p>
            </div>
          </div>

          <div class="relative">
            <!-- Timeline Line -->
            <div
              class="absolute left-[20px] top-5 bottom-5 w-px bg-slate-200 hidden sm:block"
            ></div>

            <div class="space-y-5">
              <div
                v-for="(agenda, index) in event.agendas"
                :key="agenda.id"
                class="relative flex flex-col sm:flex-row gap-4"
              >
                <!-- Number -->
                <div
                  class="relative z-10 w-10 h-10 shrink-0 rounded-full bg-[#009688] text-white flex items-center justify-center text-sm font-bold"
                >
                  {{ index + 1 }}
                </div>

                <!-- Content -->
                <div
                  class="flex-1 rounded-xl border border-slate-100 bg-slate-50 p-4 hover:border-teal-100 hover:bg-teal-50/30 transition"
                >
                  <div
                    class="flex flex-col sm:flex-row sm:items-center gap-2 mb-1"
                  >
                    <span class="text-sm font-bold text-[#009688]">
                      {{ agenda.time }}
                    </span>

                    <span class="hidden sm:block text-slate-300"> • </span>

                    <h3 class="text-sm font-semibold text-slate-800">
                      {{ agenda.title }}
                    </h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>

      <!-- ================================================= -->
      <!-- RIGHT SIDEBAR -->
      <!-- ================================================= -->

      <aside class="lg:col-span-4">
        <div
          class="lg:sticky lg:top-6 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden"
        >
          <!-- Sidebar Header -->
          <div class="p-6 sm:p-7">
            <div class="flex items-center justify-between gap-3 mb-4">
              <span
                :class="statusClass"
                class="inline-flex items-center px-3 py-1.5 rounded-full border text-xs font-semibold capitalize"
              >
                {{ event.status }}
              </span>

              <span
                v-if="isRegistered"
                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-teal-50 text-teal-700 text-xs font-semibold"
              >
                <svg
                  class="w-3.5 h-3.5"
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

                Registered
              </span>
            </div>

            <h2
              class="text-xl sm:text-2xl font-bold text-slate-900 leading-snug"
            >
              {{ event.title }} 
            </h2>
          </div>

          <div class="border-t border-slate-100"></div>

          <!-- ================================================= -->
          <!-- DETAILS -->
          <!-- ================================================= -->

          <div class="p-6 sm:p-7 space-y-5">
            <!-- Date -->
            <div class="flex items-start gap-3.5">
              <div
                class="w-10 h-10 shrink-0 rounded-xl bg-teal-50 text-[#009688] flex items-center justify-center"
              >
                <svg
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                  />
                </svg>
              </div>

              <div class="min-w-0">
                <p class="text-xs text-slate-400 font-medium mb-1">Date</p>

                <p class="text-sm font-semibold text-slate-800">
                  {{ eventDate }}
                </p>
              </div>
            </div>

            <!-- Time -->
            <div class="flex items-start gap-3.5">
              <div
                class="w-10 h-10 shrink-0 rounded-xl bg-teal-50 text-[#009688] flex items-center justify-center"
              >
                <svg
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>

              <div>
                <p class="text-xs text-slate-400 font-medium mb-1">Time</p>

                <p class="text-sm font-semibold text-slate-800">
                  {{ eventTime }}
                </p>

                <p class="text-xs text-slate-400 mt-1">Cambodia Time</p>
              </div>
            </div>

            <!-- Location -->
            <div class="flex items-start gap-3.5">
              <div
                class="w-10 h-10 shrink-0 rounded-xl bg-teal-50 text-[#009688] flex items-center justify-center"
              >
                <svg
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                  />

                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                </svg>
              </div>

              <div class="min-w-0">
                <p class="text-xs text-slate-400 font-medium mb-1">Location</p>

                <p class="text-sm font-semibold text-slate-800 leading-6">
                  {{ event.location || "Online Event" }}
                </p>
              </div>
            </div>

            <!-- Participants -->
            <div v-if="event.max_participants" class="flex items-start gap-3.5">
              <div
                class="w-10 h-10 shrink-0 rounded-xl bg-teal-50 text-[#009688] flex items-center justify-center"
              >
                <svg
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-8a4 4 0 110 8 4 4 0 010-8z"
                  />
                </svg>
              </div>

              <div>
                <p class="text-xs text-slate-400 font-medium mb-1">
                  Maximum Participants
                </p>

                <p class="text-sm font-semibold text-slate-800">
                  {{ event.max_participants }} people
                </p>
              </div>
            </div>

            <!-- Deadline -->
            <div
              v-if="event.registration_deadline"
              class="flex items-start gap-3.5"
            >
              <div
                class="w-10 h-10 shrink-0 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center"
              >
                <svg
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>

              <div>
                <p class="text-xs text-slate-400 font-medium mb-1">
                  Registration Deadline
                </p>

                <p class="text-sm font-semibold text-slate-800">
                  {{ registrationDeadline }}
                </p>
              </div>
            </div>
          </div>

          <div class="border-t border-slate-100"></div>

          <!-- ================================================= -->
          <!-- ACTIONS -->
          <!-- ================================================= -->

          <div class="p-6 sm:p-7 space-y-3">
            <!-- Already Registered -->
            <div
              v-if="isRegistered"
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
            <button
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

          <div class="border-t border-slate-100"></div>

          <!-- ================================================= -->
          <!-- ORGANIZER -->
          <!-- ================================================= -->

          <div v-if="event.creator" class="p-6 sm:p-7">
            <p class="text-xs text-slate-400 font-medium mb-3">Organized by</p>

            <div class="flex items-center gap-3">
              <!-- Avatar -->
              <div
                class="w-11 h-11 shrink-0 rounded-full overflow-hidden bg-teal-50 text-[#009688] flex items-center justify-center"
              >
                <img
                  v-if="event.creator.avatar"
                  :src="event.creator.avatar"
                  :alt="event.creator.name_english"
                  class="w-full h-full object-cover"
                />

                <span v-else class="text-sm font-bold">
                  {{ event.creator.name_english?.charAt(0) }}
                </span>
              </div>

              <div class="min-w-0">
                <p class="text-sm font-bold text-slate-900 truncate">
                  {{ event.creator.name_english }}
                </p>

                <p class="text-xs text-slate-400 truncate">
                  {{ event.creator.name_khmer }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </aside>
    </div>

    <!-- ================================================= -->
    <!-- NOT FOUND -->
    <!-- ================================================= -->

    <div
      v-else
      class="bg-white rounded-2xl border border-slate-200 p-12 text-center"
    >
      <div
        class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mb-4"
      >
        <svg
          class="w-7 h-7"
          fill="none"
          stroke="currentColor"
          stroke-width="1.5"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M9 13h6m-3-3v6m9-4a9 9 0 11-18 0 9 9 0 0118 0z"
          />
        </svg>
      </div>

      <h2 class="text-lg font-semibold text-slate-800">Event Not Found</h2>

      <p class="text-sm text-slate-400 mt-1">
        The event you are looking for does not exist.
      </p>
    </div>
  </div>
</template>
