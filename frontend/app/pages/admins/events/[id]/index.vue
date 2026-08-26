<script setup lang="ts">
definePageMeta({
  layout: "admin",
});

const route = useRoute();

/*
|--------------------------------------------------------------------------
| Event ID
|--------------------------------------------------------------------------
*/

const eventId = computed(() => String(route.params.id));

/*
|--------------------------------------------------------------------------
| Event Data
|--------------------------------------------------------------------------
*/

const event = ref({
  id: eventId.value,

  title: "Annual Tech Alumni Mixer 2024",

  date: "Oct 24, 2024",

  time: "5:00 PM - 10:00 PM",

  location: {
    name: "Main Campus, Hall A",
    address: "123 University Rd, Phnom Penh",
    mapImage:
      "https://images.unsplash.com/photo-1526778548025-fa2f459cd5c1?auto=format&fit=crop&w=1200&q=80",
  },

  coverImage:
    "https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=1800&q=80",

  about: `
Join us for the highly anticipated Annual Tech Alumni Mixer,
the premier networking event for graduates of the School of Technology.

This evening is designed to reconnect with classmates, foster new
professional relationships, and celebrate the ongoing achievements
of our alumni community in the tech industry.

Whether you're a seasoned executive, a startup founder, or a recent
graduate looking to make your next career move, this mixer provides
the perfect environment to exchange ideas, share experiences, and
discover potential collaborations.
`,

  registration: {
    registered: 124,
    capacity: 200,
    deadline: "Oct 20, 2024",
  },

  eventType: "Networking Mixer",

  price: "Free (Alumni Only)",

  contact: {
    name: "Linda Houn",
    email: "alumni@university.edu",
  },

  agenda: [
    {
      time: "5:00 PM - 6:00 PM",
      title: "Networking & Drinks",
      description:
        "Check-in at the main foyer. Open bar and light hors d'oeuvres will be served.",
    },
    {
      time: "6:00 PM - 7:00 PM",
      title: "Keynote Speech",
      description: "The AI Revolution presented by Dr. Sarah Jenkins.",
    },
    {
      time: "7:00 PM - 9:00 PM",
      title: "Dinner & Awards",
      description:
        "Dinner followed by the presentation of the Alumni Innovation Awards.",
    },
  ],

  speakers: [
    {
      name: "Dr. Sarah Jenkins",
      role: "VP of AI Research",
      company: "TechNova",
      image:
        "https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=300&q=80",
    },
    {
      name: "Michael Chen",
      role: "Founder & CEO",
      company: "FutureLabs",
      image:
        "https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=300&q=80",
    },
  ],
});

/*
|--------------------------------------------------------------------------
| Registration Percentage
|--------------------------------------------------------------------------
*/

const registrationPercentage = computed(() => {
  const registered = event.value.registration.registered;
  const capacity = event.value.registration.capacity;

  if (capacity <= 0) {
    return 0;
  }

  return Math.min(Math.round((registered / capacity) * 100), 100);
});

/*
|--------------------------------------------------------------------------
| Actions
|--------------------------------------------------------------------------
*/

const editEvent = () => {
  navigateTo(`/admins/events/${eventId.value}/edit`);
};

const deleteEvent = () => {
  if (!import.meta.client) {
    return;
  }

  const confirmed = window.confirm(
    "Are you sure you want to delete this event?",
  );

  if (!confirmed) {
    return;
  }

  console.log("Delete event:", eventId.value);
};

const exportAttendance = () => {
  console.log("Export attendance:", eventId.value);
};

const manageAttendees = () => {
  navigateTo(`/admins/events/${eventId.value}/attendees`);
};

const viewMap = () => {
  if (!import.meta.client) {
    return;
  }

  const address = encodeURIComponent(event.value.location.address);

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
    <!-- TOP HEADER -->
    <!-- ===================================================== -->

    <header class="border-b border-gray-200 bg-white">
      <div class="w-full px-4 py-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->

        <div class="mb-4 flex items-center gap-2 text-xs text-gray-500">
          <NuxtLink to="/admins/events" class="transition hover:text-gray-900">
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
                {{ event.title }}
              </h1>

              <span
                class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-medium text-green-700 ring-1 ring-inset ring-green-600/20"
              >
                Active
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

                <span>
                  {{ event.date }}
                </span>
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

                <span>
                  {{ event.time }}
                </span>
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

                <span>
                  {{ event.location.name }}
                </span>
              </div>
            </div>
          </div>

          <!-- Actions -->

          <div class="flex flex-wrap items-center gap-2">
            <!-- Delete -->

            <button
              type="button"
              class="inline-flex h-9 items-center gap-2 rounded-md border border-gray-300 bg-white px-3 text-xs font-medium text-gray-700 transition hover:bg-gray-50"
              @click="deleteEvent"
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
                  d="M4 7h16M9 7V4h6v3M7 7l.8 13h8.4L17 7M10 11v5M14 11v5"
                />
              </svg>

              <span> Delete </span>
            </button>

            <!-- Export -->

            <button
              type="button"
              class="inline-flex h-9 items-center gap-2 rounded-md border border-gray-300 bg-white px-3 text-xs font-medium text-gray-700 transition hover:bg-gray-50"
              @click="exportAttendance"
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
                  d="M12 3v12M8 11l4 4 4-4M5 20h14"
                />
              </svg>

              <span> Export Attendance </span>
            </button>

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
        class="relative h-[240px] w-full overflow-hidden rounded-xl border border-gray-200 bg-gray-200 shadow-sm sm:h-[320px] lg:h-[400px]"
      >
        <img
          :src="event.coverImage"
          :alt="event.title"
          class="h-full w-full object-cover"
        />

        <!-- Overlay -->

        <div
          class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"
        ></div>

        <!-- Featured -->

        <div
          class="absolute left-4 top-4 inline-flex items-center gap-2 rounded-full bg-black/50 px-3 py-1.5 text-[10px] font-medium text-white backdrop-blur-md"
        >
          <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor">
            <path
              d="m12 3 2.78 5.63 6.22.9-4.5 4.39 1.06 6.2L12 17.2l-5.56 2.92 1.06-6.2L3 9.53l6.22-.9L12 3Z"
            />
          </svg>

          Featured Event
        </div>

        <!-- Cover bottom info -->

        <div class="absolute bottom-0 left-0 right-0 p-5 sm:p-7">
          <div class="max-w-3xl">
            <p
              class="mb-2 text-xs font-medium uppercase tracking-wider text-white/70"
            >
              {{ event.eventType }}
            </p>

            <h2 class="text-xl font-bold text-white sm:text-3xl">
              {{ event.title }}
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

          <section class="rounded-xl border border-gray-200 bg-white shadow-sm">
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
                {{ event.about }}
              </p>
            </div>
          </section>

          <!-- AGENDA -->

          <section class="rounded-xl border border-gray-200 bg-white shadow-sm">
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
                  class="absolute bottom-4 left-[7px] top-4 w-px bg-gray-200"
                ></div>

                <div class="space-y-7">
                  <div
                    v-for="(item, index) in event.agenda"
                    :key="`${item.time}-${index}`"
                    class="relative flex gap-5"
                  >
                    <!-- Dot -->

                    <div
                      class="relative z-10 mt-1.5 h-[15px] w-[15px] shrink-0 rounded-full border-2 border-white bg-gray-800 ring-1 ring-gray-300"
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

                      <p class="mt-1.5 text-xs leading-6 text-gray-500">
                        {{ item.description }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- SPEAKERS -->

          <section class="rounded-xl border border-gray-200 bg-white shadow-sm">
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
                v-for="speaker in event.speakers"
                :key="speaker.name"
                class="flex items-center gap-4 rounded-lg border border-gray-200 p-4 transition hover:border-gray-300 hover:shadow-sm"
              >
                <img
                  :src="speaker.image"
                  :alt="speaker.name"
                  class="h-14 w-14 shrink-0 rounded-full object-cover ring-2 ring-gray-100"
                />

                <div class="min-w-0 flex-1">
                  <h3 class="truncate text-sm font-semibold text-gray-900">
                    {{ speaker.name }}
                  </h3>

                  <p class="mt-1 truncate text-xs text-gray-500">
                    {{ speaker.role }}
                  </p>

                  <p class="mt-0.5 truncate text-[10px] text-gray-400">
                    {{ speaker.company }}
                  </p>

                  <span
                    class="mt-2 inline-flex rounded-full bg-gray-100 px-2 py-1 text-[9px] font-medium text-gray-500"
                  >
                    Class of '24
                  </span>
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

          <section
            class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"
          >
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
                class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-[9px] font-medium text-green-700"
              >
                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>

                Open
              </span>
            </div>

            <!-- Stats -->

            <div class="mt-5 grid grid-cols-2 gap-3">
              <div class="rounded-lg bg-gray-50 p-3">
                <p class="text-[10px] text-gray-400">Attending</p>

                <p class="mt-1 text-xl font-bold text-gray-900">
                  {{ event.registration.registered }}
                </p>
              </div>

              <div class="rounded-lg bg-gray-50 p-3">
                <p class="text-[10px] text-gray-400">Capacity</p>

                <p class="mt-1 text-xl font-bold text-gray-900">
                  {{ event.registration.capacity }}
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
                {{ event.registration.deadline }}
              </span>
            </div>

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

          <section
            class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"
          >
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

                  <p class="mt-1 text-xs font-medium text-gray-700">
                    {{ event.eventType }}
                  </p>
                </div>
              </div>

              <!-- Price -->

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
                    <circle cx="12" cy="12" r="8.5" />
                    <path
                      stroke-linecap="round"
                      d="M14.5 9.5c-.5-.7-1.3-1-2.5-1-1.4 0-2.5.7-2.5 1.8 0 2.8 5.2 1.1 5.2 3.9 0 1.2-1.1 2-2.7 2-1.2 0-2.1-.3-2.8-1.1"
                    />
                    <path stroke-linecap="round" d="M12 7v10" />
                  </svg>
                </div>

                <div>
                  <p class="text-[9px] text-gray-400">Price</p>

                  <p class="mt-1 text-xs font-medium text-gray-700">
                    {{ event.price }}
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
                  <p class="text-[9px] text-gray-400">Contact Person</p>

                  <p class="mt-1 text-xs font-medium text-gray-700">
                    {{ event.contact.name }}
                  </p>

                  <p class="mt-0.5 truncate text-[10px] text-gray-400">
                    {{ event.contact.email }}
                  </p>
                </div>
              </div>
            </div>
          </section>

          <!-- LOCATION -->

          <section
            class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
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
                  <h2 class="text-sm font-semibold text-gray-900">Location</h2>

                  <p class="mt-0.5 text-[10px] text-gray-400">Event venue</p>
                </div>
              </div>
            </div>

            <!-- Map -->

            <div class="relative h-44 bg-gray-100">
              <img
                :src="event.location.mapImage"
                alt="Event location"
                class="h-full w-full object-cover"
              />

              <div class="absolute inset-0 bg-black/10"></div>

              <!-- Map Pin -->

              <div
                class="absolute left-1/2 top-1/2 flex h-10 w-10 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-white shadow-lg"
              >
                <svg
                  class="h-5 w-5 text-gray-900"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M20 10.5c0 5.5-8 10-8 10s-8-4.5-8-10a8 8 0 1 1 16 0Z"
                  />
                  <circle cx="12" cy="10.5" r="2.5" />
                </svg>
              </div>
            </div>

            <!-- Address -->

            <div class="p-5">
              <h3 class="text-sm font-semibold text-gray-800">
                {{ event.location.name }}
              </h3>

              <p class="mt-1.5 text-xs leading-5 text-gray-500">
                {{ event.location.address }}
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
  </div>
</template>
