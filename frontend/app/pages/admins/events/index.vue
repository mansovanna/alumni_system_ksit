<script setup>
import AddIcon from "~/components/icons/AddIcon.vue";
import EditIcon from "~/components/icons/EditIcon.vue";
import DeleteIcon from "~/components/icons/DeleteIcon.vue";

definePageMeta({
  layout: "admin",
});

// ---- Tabs ----
const tabs = ["All Events", "Upcoming", "Past", "Drafts"];
const activeTab = ref("All Events");

// ---- Add / Edit event forms ----
const formAddNew = ref(false);
const handleAddNewEvent = () => (formAddNew.value = !formAddNew.value);

const formUpdate = ref(false);
const selectedEvent = ref(null);
const handleUpdate = (event) => {
  selectedEvent.value = event;
  formUpdate.value = true;
  openMenuId.value = null;
};

// ---- "..." dropdown menu ----
const openMenuId = ref(null);
const toggleMenu = (id) =>
  (openMenuId.value = openMenuId.value === id ? null : id);

const handleDelete = (id) => {
  events.value = events.value.filter((e) => e.id !== id);
  openMenuId.value = null;
};

// ---- Mock data (swap for $fetch('/api/events') later) ----
const events = ref([
  {
    id: 1,
    title: "Tech Alumni Mixer 2024",
    description:
      "Annual networking event for engineering and technology graduates. Featuring...",
    dateDay: "24",
    dateMonth: "OCT",
    location: "Main Campus, Hall A",
    locationType: "location",
    attending: 124,
    image:
      "https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=600",
  },
  {
    id: 2,
    title: "Resume Workshop",
    description:
      "Learn how to optimize your resume for ATS systems and stand out to recruiters in...",
    dateDay: "05",
    dateMonth: "NOV",
    location: "Virtual (Zoom)",
    locationType: "virtual",
    attending: 85,
    image:
      "https://images.unsplash.com/photo-1573497491208-6b1acb260507?q=80&w=600",
  },
  {
    id: 3,
    title: "Winter Gala Dinner",
    description:
      "Join us for an elegant evening celebrating the achievements of our outstanding alum...",
    dateDay: "12",
    dateMonth: "DEC",
    location: "Downtown Grand Hotel",
    locationType: "location",
    attending: 210,
    image:
      "https://images.unsplash.com/photo-1519167758481-83f550bb49b3?q=80&w=600",
  },
]);
</script>

<template>
  <div class="flex flex-col gap-4">
    <!-- Block header: Tabs + Create button -->
    <div
      class="w-full flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3"
    >
      <div
        class="flex items-center gap-1 bg-white border border-slate-200 rounded-lg p-1 w-fit overflow-x-auto"
      >
        <button
          v-for="tab in tabs"
          :key="tab"
          @click="activeTab = tab"
          class="px-3 sm:px-4 py-1.5 text-xs sm:text-sm rounded-md whitespace-nowrap transition cursor-pointer"
          :class="
            activeTab === tab
              ? 'bg-slate-100 text-slate-800 font-medium'
              : 'text-slate-500 hover:text-slate-700'
          "
        >
          {{ tab }}
        </button>
      </div>

      <RouterLink
        :to="{ name: 'admins-events-create' }"
        class="flex justify-center items-center gap-1 text-nowrap px-4 py-2 bg-primary text-white rounded-md text-sm cursor-pointer hover:bg-primary/80 w-fit"
      >
        <AddIcon />
        <span>Create Event</span>
      </RouterLink>
    </div>
    <!-- End header -->

    <!-- Block: Event cards (flex-wrap ជំនួស grid-cols-3) -->
    <div class="grid grid-cols-4 gap-4 max-xl:grid-cols-3">
      <NuxtLink
        :to="{ name: 'admins-events-id', params: { id: 1 } }"
        v-for="event in events"
        :key="event.id"
      >
        <div
          class="bg-white border group hover:-translate-y-1 cursor-pointer ease-in-out hover:shadow-lg duration-300 border-slate-200 rounded-xl overflow-hidden shadow-sm flex flex-col w-full"
        >
          <!-- Image + date badge -->
          <div class="relative h-[200px] w-full">
            <img
              :src="event.image"
              :alt="event.title"
              class="w-full h-full object-cover group-hover:scale-105 duration-300 ease-in-out"
            />
            <div
              class="absolute top-2 left-2 bg-white rounded-md px-2.5 py-1 text-center leading-tight shadow"
            >
              <div
                class="text-[10px] font-semibold text-red-500 uppercase tracking-wide"
              >
                {{ event.dateMonth }}
              </div>
              <div class="text-sm font-bold text-slate-800">
                {{ event.dateDay }}
              </div>
            </div>
          </div>

          <!-- Body -->
          <div class="p-4 flex flex-col gap-2 flex-1 relative">
            <div class="flex items-start justify-between gap-2">
              <h3
                class="font-semibold text-slate-800 text-[15px] leading-snug line-clamp-1"
              >
                {{ event.title }}
              </h3>

              <!-- "..." menu -->
              <div class="relative shrink-0">
                <button
                  @click="toggleMenu(event.id)"
                  class="text-slate-400 hover:text-slate-600 cursor-pointer leading-none px-1"
                >
                  &#8942;
                </button>

                <div
                  v-if="openMenuId === event.id"
                  class="absolute right-0 top-6 z-10 bg-white border border-slate-200 rounded-lg shadow-md w-32 py-1 flex flex-col"
                >
                  <button
                    @click="handleUpdate(event)"
                    class="flex items-center gap-2 px-3 py-2 text-xs text-slate-600 hover:bg-slate-50"
                  >
                    <EditIcon class="size-3.5" /> Edit
                  </button>
                  <button
                    @click="handleDelete(event.id)"
                    class="flex items-center gap-2 px-3 py-2 text-xs text-red-500 hover:bg-slate-50"
                  >
                    <DeleteIcon class="size-3.5" /> Delete
                  </button>
                </div>
              </div>
            </div>

            <p class="text-[13px] text-blue-500 leading-snug line-clamp-2">
              {{ event.description }}
            </p>

            <div
              class="border-t border-slate-100 mt-2 pt-2 flex flex-col gap-1.5 text-xs text-slate-500"
            >
              <div class="flex items-center gap-1.5">
                <svg
                  v-if="event.locationType === 'virtual'"
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  class="size-3.5 shrink-0"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15.75 10.5l4.72-2.36a.75.75 0 011.03.67v6.38a.75.75 0 01-1.03.67l-4.72-2.36M4.5 6.75h9a1.5 1.5 0 011.5 1.5v7.5a1.5 1.5 0 01-1.5 1.5h-9a1.5 1.5 0 01-1.5-1.5v-7.5a1.5 1.5 0 011.5-1.5z"
                  />
                </svg>
                <svg
                  v-else
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  class="size-3.5 shrink-0"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"
                  />
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"
                  />
                </svg>
                <span class="truncate">{{ event.location }}</span>
              </div>
              <div class="flex items-center gap-1.5">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  class="size-3.5 shrink-0"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"
                  />
                </svg>
                <span>{{ event.attending }} attending</span>
              </div>
            </div>
          </div>
        </div>
      </NuxtLink>

      <!-- Plan a New Event (placeholder card) -->
      <button
        @click="$router.push({ name: 'admins-events-create' })"
        class="bg-slate-100/70 border-2 border-dashed border-slate-200 rounded-xl flex flex-col items-center justify-center gap-3 py-10 px-6 text-center hover:border-primary/50 hover:bg-slate-100 transition cursor-pointer w-full"
        style="min-height: 296px"
      >
        <div
          class="size-5 rounded-full bg-white border border-slate-200 flex items-center justify-center shadow-sm"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            class="size-5 text-slate-700"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M9 12h3.75M9 15h3.75M9 18h3.75m3-15H6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 006 21h12a2.25 2.25 0 002.25-2.25V6.108c0-.464-.184-.909-.51-1.238l-3.61-3.61a1.75 1.75 0 00-1.238-.51z"
            />
          </svg>
        </div>
        <div class="text-[15px] font-semibold text-slate-800">
          Plan a New Event
        </div>
        <p class="text-[13px] text-slate-500 leading-snug">
          Create an <span class="text-blue-500">upcoming</span> webinar, mixer,
          or workshop for alumni.
        </p>
      </button>
    </div>
    <!-- End Event cards -->
  </div>
</template>
