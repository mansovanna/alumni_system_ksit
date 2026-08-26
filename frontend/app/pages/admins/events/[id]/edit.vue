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
| Form State
|--------------------------------------------------------------------------
*/

const form = ref({
  coverImage:
    "https://images.unsplash.com/photo-1511632765486-a01980e01a18?auto=format&fit=crop&w=1800&q=80",

  title: "Annual Tech Alumni Mixer 2024",

  eventType: "Networking Mixer",

  location: "Main Campus, Hall A",

  address: "123 University Rd, Phnom Penh",

  date: "2024-10-24",

  startTime: "17:00",

  endTime: "22:00",

  description: `Join us for the highly anticipated Annual Tech Alumni Mixer,
the premier networking event for graduates of the School of Technology.

This evening is designed to reconnect with classmates, foster new
professional relationships, and celebrate the ongoing achievements
of our alumni community in the tech industry.`,

  registrationDeadlineDate: "2024-10-20",

  registrationDeadlineTime: "23:59",

  capacity: 200,

  price: "Free",

  alumniOnly: true,

  contactName: "Linda Houn",

  contactEmail: "alumni@university.edu",

  status: "active",
});

/*
|--------------------------------------------------------------------------
| Event Types
|--------------------------------------------------------------------------
*/

const eventTypes = [
  "Networking Mixer",
  "Workshop",
  "Seminar",
  "Conference",
  "Career Fair",
  "Alumni Reunion",
  "Sports",
  "Other",
];

/*
|--------------------------------------------------------------------------
| Status
|--------------------------------------------------------------------------
*/

const statuses = [
  {
    value: "draft",
    label: "Draft",
  },
  {
    value: "active",
    label: "Active",
  },
  {
    value: "closed",
    label: "Closed",
  },
];

/*
|--------------------------------------------------------------------------
| Agenda
|--------------------------------------------------------------------------
*/

const agenda = ref([
  {
    time: "17:00",
    endTime: "18:00",
    title: "Networking & Drinks",
    description:
      "Check-in at the main foyer. Open bar and light hors d'oeuvres will be served.",
  },

  {
    time: "18:00",
    endTime: "19:00",
    title: "Keynote Speech",
    description:
      "The AI Revolution presented by Dr. Sarah Jenkins.",
  },

  {
    time: "19:00",
    endTime: "21:00",
    title: "Dinner & Awards",
    description:
      "Dinner followed by the presentation of the Alumni Innovation Awards.",
  },
]);

/*
|--------------------------------------------------------------------------
| Speakers
|--------------------------------------------------------------------------
*/

const speakers = ref([
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
]);

/*
|--------------------------------------------------------------------------
| Image
|--------------------------------------------------------------------------
*/

const imageInput = ref<HTMLInputElement | null>(null);

const imagePreview = ref(
  form.value.coverImage
);

const handleImageChange = (
  event: Event
) => {
  const target =
    event.target as HTMLInputElement;

  const file = target.files?.[0];

  if (!file) {
    return;
  }

  imagePreview.value =
    URL.createObjectURL(file);
};

const removeImage = () => {
  imagePreview.value = "";
};

const openImagePicker = () => {
  imageInput.value?.click();
};

/*
|--------------------------------------------------------------------------
| Agenda Actions
|--------------------------------------------------------------------------
*/

const addAgenda = () => {
  agenda.value.push({
    time: "09:00",
    endTime: "10:00",
    title: "",
    description: "",
  });
};

const removeAgenda = (
  index: number
) => {
  agenda.value.splice(index, 1);
};

/*
|--------------------------------------------------------------------------
| Speaker Actions
|--------------------------------------------------------------------------
*/

const addSpeaker = () => {
  speakers.value.push({
    name: "",
    role: "",
    company: "",
    image: "",
  });
};

const removeSpeaker = (
  index: number
) => {
  speakers.value.splice(index, 1);
};

/*
|--------------------------------------------------------------------------
| Save
|--------------------------------------------------------------------------
*/

const isSaving = ref(false);

const saveEvent = async () => {
  isSaving.value = true;

  try {
    /*
    |--------------------------------------------------------------------------
    | API Example
    |--------------------------------------------------------------------------
    |
    | await $fetch(`/api/admin/events/${eventId.value}`, {
    |   method: "PUT",
    |   body: {
    |     ...form.value,
    |     agenda: agenda.value,
    |     speakers: speakers.value,
    |   },
    | });
    |
    */

    console.log("Update Event", {
      id: eventId.value,
      ...form.value,
      agenda: agenda.value,
      speakers: speakers.value,
    });

    await new Promise(
      (resolve) =>
        setTimeout(resolve, 700)
    );

    await navigateTo(
      `/admins/events/${eventId.value}`
    );
  } finally {
    isSaving.value = false;
  }
};

/*
|--------------------------------------------------------------------------
| Cancel
|--------------------------------------------------------------------------
*/

const cancelEdit = () => {
  navigateTo(
    `/admins/events/${eventId.value}`
  );
};
</script>

<template>
  <div
    class="w-full bg-gray-50 rounded-2xl overflow-clip"
  >

    <!-- ===================================================== -->
    <!-- HEADER -->
    <!-- ===================================================== -->

    <header
      class="border-b border-gray-200 bg-white"
    >
      <div
        class="w-full px-4 py-4 sm:px-6 lg:px-8 "
      >

        <!-- Breadcrumb -->

        <div
          class="mb-4 flex items-center gap-2 text-xs text-gray-500 mt-2"
        >
          <NuxtLink
            to="/admins/events"
            class="hover:text-gray-900"
          >
            Events
          </NuxtLink>

          <svg
            class="h-3.5 w-3.5 text-gray-400"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.24 4.24a.75.75 0 0 1 0 1.06l-4.24 4.24a.75.75 0 0 1-1.08 0Z"
              clip-rule="evenodd"
            />
          </svg>

          <NuxtLink
            :to="`/admins/events/${eventId}`"
            class="hover:text-gray-900"
          >
            Event Detail
          </NuxtLink>

          <svg
            class="h-3.5 w-3.5 text-gray-400"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.24 4.24a.75.75 0 0 1-1.08 0Z"
              clip-rule="evenodd"
            />
          </svg>

          <span
            class="font-medium text-gray-700"
          >
            Edit Event
          </span>
        </div>

        <!-- Title -->

        <div
          class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
          <div>
            <h1
              class="text-xl font-bold text-gray-900 sm:text-2xl"
            >
              Edit Event
            </h1>

            <p
              class="mt-1 text-xs text-gray-500"
            >
              Update event information, schedule,
              speakers and registration settings.
            </p>
          </div>

          <!-- Header actions -->

          <div
            class="flex items-center gap-2"
          >
            <button
              type="button"
              class="h-9 rounded-md border border-gray-300 bg-white px-4 text-xs font-medium text-gray-700 transition hover:bg-gray-50"
              @click="cancelEdit"
            >
              Cancel
            </button>

            <button
              type="button"
              class="inline-flex h-9 items-center gap-2 rounded-md bg-gray-900 px-4 text-xs font-medium text-white transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-60"
              :disabled="isSaving"
              @click="saveEvent"
            >

              <svg
                v-if="!isSaving"
                class="h-4 w-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M5 4h11l3 3v13H5V4Z"
                />
                <path
                  stroke-linecap="round"
                  d="M8 4v6h8V4M8 20v-6h8v6"
                />
              </svg>

              <svg
                v-else
                class="h-4 w-4 animate-spin"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <circle
                  cx="12"
                  cy="12"
                  r="9"
                  class="opacity-25"
                />

                <path
                  d="M21 12a9 9 0 0 1-9 9"
                  class="opacity-90"
                />
              </svg>

              {{ isSaving ? "Saving..." : "Save Changes" }}
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- ===================================================== -->
    <!-- CONTENT -->
    <!-- ===================================================== -->

    <main
      class="w-full px-4 py-6 sm:px-6 lg:px-8"
    >

      <div
        class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_360px]"
      >

        <!-- ================================================= -->
        <!-- LEFT COLUMN -->
        <!-- ================================================= -->

        <div
          class="min-w-0 space-y-6"
        >

          <!-- ================================================= -->
          <!-- BASIC INFORMATION -->
          <!-- ================================================= -->

          <section
            class="rounded-xl border border-gray-200 bg-white shadow-sm"
          >

            <div
              class="border-b border-gray-100 px-5 py-4"
            >
              <div
                class="flex items-center gap-3"
              >

                <div
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100"
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
                      d="M4 6h16M4 12h16M4 18h10"
                    />
                  </svg>
                </div>

                <div>
                  <h2
                    class="text-sm font-semibold text-gray-900"
                  >
                    Basic Information
                  </h2>

                  <p
                    class="mt-0.5 text-[10px] text-gray-400"
                  >
                    General information about the event
                  </p>
                </div>
              </div>
            </div>

            <div
              class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2"
            >

              <!-- Title -->

              <div
                class="md:col-span-2"
              >
                <label
                  class="mb-1.5 block text-xs font-medium text-gray-700"
                >
                  Event Title
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model="form.title"
                  type="text"
                  placeholder="Enter event title"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- Event Type -->

              <div>
                <label
                  class="mb-1.5 block text-xs font-medium text-gray-700"
                >
                  Event Type
                  <span class="text-red-500">*</span>
                </label>

                <select
                  v-model="form.eventType"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                >
                  <option
                    v-for="type in eventTypes"
                    :key="type"
                    :value="type"
                  >
                    {{ type }}
                  </option>
                </select>
              </div>

              <!-- Status -->

              <div>
                <label
                  class="mb-1.5 block text-xs font-medium text-gray-700"
                >
                  Status
                </label>

                <select
                  v-model="form.status"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                >
                  <option
                    v-for="status in statuses"
                    :key="status.value"
                    :value="status.value"
                  >
                    {{ status.label }}
                  </option>
                </select>
              </div>

              <!-- Description -->

              <div
                class="md:col-span-2"
              >
                <label
                  class="mb-1.5 block text-xs font-medium text-gray-700"
                >
                  Description
                  <span class="text-red-500">*</span>
                </label>

                <textarea
                  v-model="form.description"
                  rows="7"
                  placeholder="Describe the event..."
                  class="w-full resize-y rounded-md border border-gray-300 bg-white px-3 py-2.5 text-xs leading-6 text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                ></textarea>

                <p
                  class="mt-1 text-[10px] text-gray-400"
                >
                  Provide a clear description of the event.
                </p>
              </div>
            </div>
          </section>

          <!-- ================================================= -->
          <!-- DATE & LOCATION -->
          <!-- ================================================= -->

          <section
            class="rounded-xl border border-gray-200 bg-white shadow-sm"
          >

            <div
              class="border-b border-gray-100 px-5 py-4"
            >
              <div
                class="flex items-center gap-3"
              >

                <div
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100"
                >
                  <svg
                    class="h-4 w-4 text-gray-700"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                  >
                    <rect
                      x="4"
                      y="5"
                      width="16"
                      height="15"
                      rx="2"
                    />

                    <path
                      d="M8 3v4M16 3v4M4 9h16"
                      stroke-linecap="round"
                    />
                  </svg>
                </div>

                <div>
                  <h2
                    class="text-sm font-semibold text-gray-900"
                  >
                    Date, Time & Location
                  </h2>

                  <p
                    class="mt-0.5 text-[10px] text-gray-400"
                  >
                    When and where the event will take place
                  </p>
                </div>
              </div>
            </div>

            <div
              class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2"
            >

              <!-- Date -->

              <div>
                <label
                  class="mb-1.5 block text-xs font-medium text-gray-700"
                >
                  Event Date
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model="form.date"
                  type="date"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- Location -->

              <div>
                <label
                  class="mb-1.5 block text-xs font-medium text-gray-700"
                >
                  Venue
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model="form.location"
                  type="text"
                  placeholder="Event venue"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- Start -->

              <div>
                <label
                  class="mb-1.5 block text-xs font-medium text-gray-700"
                >
                  Start Time
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model="form.startTime"
                  type="time"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- End -->

              <div>
                <label
                  class="mb-1.5 block text-xs font-medium text-gray-700"
                >
                  End Time
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model="form.endTime"
                  type="time"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- Address -->

              <div
                class="md:col-span-2"
              >
                <label
                  class="mb-1.5 block text-xs font-medium text-gray-700"
                >
                  Full Address
                </label>

                <input
                  v-model="form.address"
                  type="text"
                  placeholder="Full event address"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>
            </div>
          </section>

          <!-- ================================================= -->
          <!-- REGISTRATION -->
          <!-- ================================================= -->

          <section
            class="rounded-xl border border-gray-200 bg-white shadow-sm"
          >

            <div
              class="border-b border-gray-100 px-5 py-4"
            >
              <div
                class="flex items-center gap-3"
              >

                <div
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100"
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
                      d="M16 11a3 3 0 1 0 0-6"
                    />
                    <path
                      stroke-linecap="round"
                      d="M16 14c2.3.4 3.8 2 4.2 5"
                    />
                  </svg>
                </div>

                <div>
                  <h2
                    class="text-sm font-semibold text-gray-900"
                  >
                    Registration Settings
                  </h2>

                  <p
                    class="mt-0.5 text-[10px] text-gray-400"
                  >
                    Configure registration and capacity
                  </p>
                </div>
              </div>
            </div>

            <div
              class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2"
            >

              <!-- Deadline Date -->

              <div>
                <label
                  class="mb-1.5 block text-xs font-medium text-gray-700"
                >
                  Registration Deadline
                </label>

                <input
                  v-model="form.registrationDeadlineDate"
                  type="date"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- Deadline Time -->

              <div>
                <label
                  class="mb-1.5 block text-xs font-medium text-gray-700"
                >
                  Deadline Time
                </label>

                <input
                  v-model="form.registrationDeadlineTime"
                  type="time"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- Capacity -->

              <div>
                <label
                  class="mb-1.5 block text-xs font-medium text-gray-700"
                >
                  Maximum Capacity
                </label>

                <input
                  v-model.number="form.capacity"
                  type="number"
                  min="1"
                  placeholder="200"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- Price -->

              <div>
                <label
                  class="mb-1.5 block text-xs font-medium text-gray-700"
                >
                  Price
                </label>

                <input
                  v-model="form.price"
                  type="text"
                  placeholder="Free"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- Alumni Only -->

              <div
                class="flex items-center justify-between rounded-lg border border-gray-200 p-4 md:col-span-2"
              >
                <div>
                  <p
                    class="text-xs font-medium text-gray-800"
                  >
                    Alumni Only
                  </p>

                  <p
                    class="mt-1 text-[10px] text-gray-400"
                  >
                    Only registered alumni can attend this event.
                  </p>
                </div>

                <button
                  type="button"
                  class="relative h-6 w-11 rounded-full transition"
                  :class="
                    form.alumniOnly
                      ? 'bg-gray-900'
                      : 'bg-gray-300'
                  "
                  @click="
                    form.alumniOnly =
                      !form.alumniOnly
                  "
                >
                  <span
                    class="absolute top-1 h-4 w-4 rounded-full bg-white shadow transition"
                    :class="
                      form.alumniOnly
                        ? 'left-6'
                        : 'left-1'
                    "
                  ></span>
                </button>
              </div>
            </div>
          </section>

          <!-- ================================================= -->
          <!-- AGENDA -->
          <!-- ================================================= -->

          <section
            class="rounded-xl border border-gray-200 bg-white shadow-sm"
          >

            <div
              class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
            >

              <div
                class="flex items-center gap-3"
              >
                <div
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100"
                >
                  <svg
                    class="h-4 w-4 text-gray-700"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                  >
                    <circle cx="12" cy="12" r="9" />
                    <path
                      stroke-linecap="round"
                      d="M12 7v5l3 2"
                    />
                  </svg>
                </div>

                <div>
                  <h2
                    class="text-sm font-semibold text-gray-900"
                  >
                    Event Agenda
                  </h2>

                  <p
                    class="mt-0.5 text-[10px] text-gray-400"
                  >
                    Add and manage event activities
                  </p>
                </div>
              </div>

              <button
                type="button"
                class="inline-flex h-8 items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-3 text-[10px] font-medium text-gray-700 hover:bg-gray-50"
                @click="addAgenda"
              >
                <svg
                  class="h-3.5 w-3.5"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    stroke-linecap="round"
                    d="M12 5v14M5 12h14"
                  />
                </svg>

                Add Agenda
              </button>
            </div>

            <div
              class="space-y-4 p-5"
            >

              <div
                v-for="(item, index) in agenda"
                :key="index"
                class="rounded-lg border border-gray-200 bg-gray-50 p-4"
              >

                <div
                  class="mb-4 flex items-center justify-between"
                >
                  <span
                    class="text-[10px] font-semibold uppercase tracking-wider text-gray-400"
                  >
                    Agenda #{{ index + 1 }}
                  </span>

                  <button
                    type="button"
                    class="inline-flex h-7 w-7 items-center justify-center rounded-md text-gray-400 hover:bg-red-50 hover:text-red-600"
                    @click="removeAgenda(index)"
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
                        d="M5 7h14M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"
                      />
                    </svg>
                  </button>
                </div>

                <div
                  class="grid grid-cols-1 gap-4 md:grid-cols-2"
                >

                  <div>
                    <label
                      class="mb-1.5 block text-[10px] font-medium text-gray-600"
                    >
                      Start Time
                    </label>

                    <input
                      v-model="item.time"
                      type="time"
                      class="h-9 w-full rounded-md border border-gray-300 bg-white px-3 text-xs outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                    />
                  </div>

                  <div>
                    <label
                      class="mb-1.5 block text-[10px] font-medium text-gray-600"
                    >
                      End Time
                    </label>

                    <input
                      v-model="item.endTime"
                      type="time"
                      class="h-9 w-full rounded-md border border-gray-300 bg-white px-3 text-xs outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                    />
                  </div>

                  <div
                    class="md:col-span-2"
                  >
                    <label
                      class="mb-1.5 block text-[10px] font-medium text-gray-600"
                    >
                      Activity Title
                    </label>

                    <input
                      v-model="item.title"
                      type="text"
                      placeholder="Activity title"
                      class="h-9 w-full rounded-md border border-gray-300 bg-white px-3 text-xs outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                    />
                  </div>

                  <div
                    class="md:col-span-2"
                  >
                    <label
                      class="mb-1.5 block text-[10px] font-medium text-gray-600"
                    >
                      Description
                    </label>

                    <textarea
                      v-model="item.description"
                      rows="3"
                      placeholder="Activity description"
                      class="w-full resize-y rounded-md border border-gray-300 bg-white px-3 py-2 text-xs leading-5 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                    ></textarea>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- ================================================= -->
          <!-- SPEAKERS -->
          <!-- ================================================= -->

          <section
            class="rounded-xl border border-gray-200 bg-white shadow-sm"
          >

            <div
              class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
            >

              <div
                class="flex items-center gap-3"
              >
                <div
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100"
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
                      d="M16 11a3 3 0 1 0 0-6"
                    />
                  </svg>
                </div>

                <div>
                  <h2
                    class="text-sm font-semibold text-gray-900"
                  >
                    Featured Speakers
                  </h2>

                  <p
                    class="mt-0.5 text-[10px] text-gray-400"
                  >
                    Manage event speakers
                  </p>
                </div>
              </div>

              <button
                type="button"
                class="inline-flex h-8 items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-3 text-[10px] font-medium text-gray-700 hover:bg-gray-50"
                @click="addSpeaker"
              >
                <svg
                  class="h-3.5 w-3.5"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    stroke-linecap="round"
                    d="M12 5v14M5 12h14"
                  />
                </svg>

                Add Speaker
              </button>
            </div>

            <div
              class="space-y-4 p-5"
            >

              <div
                v-for="(speaker, index) in speakers"
                :key="index"
                class="rounded-lg border border-gray-200 p-4"
              >

                <div
                  class="mb-4 flex items-center justify-between"
                >
                  <span
                    class="text-[10px] font-semibold uppercase tracking-wider text-gray-400"
                  >
                    Speaker #{{ index + 1 }}
                  </span>

                  <button
                    type="button"
                    class="inline-flex h-7 w-7 items-center justify-center rounded-md text-gray-400 hover:bg-red-50 hover:text-red-600"
                    @click="removeSpeaker(index)"
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
                        d="M5 7h14M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"
                      />
                    </svg>
                  </button>
                </div>

                <div
                  class="grid grid-cols-1 gap-4 md:grid-cols-3"
                >

                  <div>
                    <label
                      class="mb-1.5 block text-[10px] font-medium text-gray-600"
                    >
                      Name
                    </label>

                    <input
                      v-model="speaker.name"
                      type="text"
                      placeholder="Speaker name"
                      class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                    />
                  </div>

                  <div>
                    <label
                      class="mb-1.5 block text-[10px] font-medium text-gray-600"
                    >
                      Role
                    </label>

                    <input
                      v-model="speaker.role"
                      type="text"
                      placeholder="Speaker role"
                      class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                    />
                  </div>

                  <div>
                    <label
                      class="mb-1.5 block text-[10px] font-medium text-gray-600"
                    >
                      Company
                    </label>

                    <input
                      v-model="speaker.company"
                      type="text"
                      placeholder="Company"
                      class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                    />
                  </div>

                  <div
                    class="md:col-span-3"
                  >
                    <label
                      class="mb-1.5 block text-[10px] font-medium text-gray-600"
                    >
                      Profile Image URL
                    </label>

                    <input
                      v-model="speaker.image"
                      type="text"
                      placeholder="https://..."
                      class="h-9 w-full rounded-md border border-gray-300 px-3 text-xs outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                    />
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>

        <!-- ================================================= -->
        <!-- RIGHT COLUMN -->
        <!-- ================================================= -->

        <aside
          class="min-w-0 space-y-6"
        >

          <!-- ================================================= -->
          <!-- COVER IMAGE -->
          <!-- ================================================= -->

          <section
            class="rounded-xl border border-gray-200 bg-white shadow-sm"
          >

            <div
              class="border-b border-gray-100 px-5 py-4"
            >
              <h2
                class="text-sm font-semibold text-gray-900"
              >
                Cover Image
              </h2>

              <p
                class="mt-0.5 text-[10px] text-gray-400"
              >
                Event cover image
              </p>
            </div>

            <div
              class="p-5"
            >

              <div
                class="group relative overflow-hidden rounded-lg border border-gray-200 bg-gray-100"
              >

                <img
                  v-if="imagePreview"
                  :src="imagePreview"
                  :alt="form.title"
                  class="h-52 w-full object-cover"
                />

                <div
                  v-else
                  class="flex h-52 items-center justify-center"
                >
                  <svg
                    class="h-8 w-8 text-gray-300"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                  >
                    <rect
                      x="3"
                      y="4"
                      width="18"
                      height="16"
                      rx="2"
                    />
                    <circle
                      cx="8.5"
                      cy="9"
                      r="1.5"
                    />
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="m3 17 5-5 4 4 2-2 7 6"
                    />
                  </svg>
                </div>

                <!-- Image overlay -->

                <div
                  v-if="imagePreview"
                  class="absolute inset-0 flex items-center justify-center gap-2 bg-black/50 opacity-0 transition group-hover:opacity-100"
                >

                  <button
                    type="button"
                    class="inline-flex h-9 items-center gap-2 rounded-md bg-white px-3 text-xs font-medium text-gray-800"
                    @click="openImagePicker"
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
                        d="M12 5v14M5 12h14"
                      />
                    </svg>

                    Change
                  </button>

                  <button
                    type="button"
                    class="inline-flex h-9 items-center gap-2 rounded-md bg-white px-3 text-xs font-medium text-red-600"
                    @click="removeImage"
                  >
                    Remove
                  </button>
                </div>
              </div>

              <input
                ref="imageInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="handleImageChange"
              />

              <button
                v-if="!imagePreview"
                type="button"
                class="mt-3 flex h-10 w-full items-center justify-center gap-2 rounded-md border border-dashed border-gray-300 text-xs font-medium text-gray-600 hover:border-gray-500 hover:bg-gray-50"
                @click="openImagePicker"
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
                    d="M12 5v14M5 12h14"
                  />
                </svg>

                Upload Cover Image
              </button>

              <p
                class="mt-2 text-center text-[9px] text-gray-400"
              >
                Recommended: 1600 × 900px · JPG, PNG, WebP
              </p>
            </div>
          </section>

          <!-- ================================================= -->
          <!-- CONTACT -->
          <!-- ================================================= -->

          <section
            class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"
          >

            <div
              class="mb-5"
            >
              <h2
                class="text-sm font-semibold text-gray-900"
              >
                Contact Information
              </h2>

              <p
                class="mt-0.5 text-[10px] text-gray-400"
              >
                Event contact person
              </p>
            </div>

            <div
              class="space-y-4"
            >

              <div>
                <label
                  class="mb-1.5 block text-xs font-medium text-gray-700"
                >
                  Contact Name
                </label>

                <input
                  v-model="form.contactName"
                  type="text"
                  class="h-10 w-full rounded-md border border-gray-300 px-3 text-xs outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <div>
                <label
                  class="mb-1.5 block text-xs font-medium text-gray-700"
                >
                  Email Address
                </label>

                <input
                  v-model="form.contactEmail"
                  type="email"
                  class="h-10 w-full rounded-md border border-gray-300 px-3 text-xs outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>
            </div>
          </section>

          <!-- ================================================= -->
          <!-- SAVE CARD -->
          <!-- ================================================= -->

          <section
            class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm"
          >

            <div
              class="flex items-start gap-3"
            >

              <div
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-100"
              >
                <svg
                  class="h-4 w-4 text-gray-600"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.8"
                >
                  <path
                    stroke-linecap="round"
                    d="M12 3v12M8 11l4 4 4-4"
                  />
                  <path
                    stroke-linecap="round"
                    d="M5 20h14"
                  />
                </svg>
              </div>

              <div>
                <h3
                  class="text-xs font-semibold text-gray-800"
                >
                  Save your changes
                </h3>

                <p
                  class="mt-1 text-[10px] leading-5 text-gray-400"
                >
                  Make sure all event information is correct
                  before saving.
                </p>
              </div>
            </div>

            <div
              class="mt-4 grid grid-cols-2 gap-2"
            >

              <button
                type="button"
                class="h-9 rounded-md border border-gray-300 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50"
                @click="cancelEdit"
              >
                Cancel
              </button>

              <button
                type="button"
                class="h-9 rounded-md bg-gray-900 text-xs font-medium text-white hover:bg-gray-800 disabled:opacity-50"
                :disabled="isSaving"
                @click="saveEvent"
              >
                {{ isSaving ? "Saving..." : "Save Changes" }}
              </button>
            </div>
          </section>

        </aside>
      </div>
    </main>
  </div>
</template>