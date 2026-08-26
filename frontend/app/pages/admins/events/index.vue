<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import AddIcon from "~/components/icons/AddIcon.vue";
import EditIcon from "~/components/icons/EditIcon.vue";
import DeleteIcon from "~/components/icons/DeleteIcon.vue";

definePageMeta({
  layout: "admin",
});

const eventStore = useEventStore();
const router = useRouter();

/* ------------------- Types (matches migration columns only) ------------------- */
interface EventItem {
  id: number;
  created_by: number | null;
  title: string;
  description: string | null;
  event_type: "job_fair" | "reunion" | "workshop" | "seminar" | "other" | null;
  banner_image: string | null;
  location: string | null;
  start_date: string;
  end_date: string;
  registration_deadline?: string | null;
  max_participants: number;
  status: "draft" | "published" | "ongoing" | "completed" | "cancelled";
  created_at: string;
  updated_at: string;
  registrations_count?: number;
}

/* ------------------- Tabs (based on status column) ------------------- */
const tabs = [
  { label: "All Events", value: "all" as const },
  { label: "Upcoming", value: "published" as const },
  { label: "Past", value: "completed" as const },
  { label: "Drafts", value: "draft" as const },
];

const activeTab = ref<(typeof tabs)[number]["value"]>("all");

const setTab = (value: (typeof tabs)[number]["value"]) => {
  activeTab.value = value;
  (eventStore as unknown as { status: typeof value }).status = value;
  eventStore.getEventAll();
};

/* ------------------- Data Fetching ------------------- */
onMounted(() => {
  eventStore.getEventAll();
});

/* ------------------- "..." dropdown menu ------------------- */
const openMenuId = ref<number | null>(null);

const toggleMenu = (id: number) => {
  openMenuId.value = openMenuId.value === id ? null : id;
};

/* ------------------- Edit ------------------- */
const handleUpdate = (event: any) => {
  openMenuId.value = null;
  router.push({ name: "admins-events-id-edit", params: { id: event.id } });
};

/* ------------------- Delete ------------------- */
const isDeleting = ref<number | null>(null);

const handleDelete = async (id: number) => {
  openMenuId.value = null;

  if (!confirm("Delete this event? This cannot be undone.")) return;

  isDeleting.value = id;

  try {
    await eventStore.deleteEvent(id);
  } catch (err) {
    console.error("Failed to delete event:", err);
    alert("Failed to delete event. Please try again.");
  } finally {
    isDeleting.value = null;
  }
};

/* ------------------- Date badge (derived from start_date only) ------------------- */
const dateBadge = (startDate: string) => {
  const d = new Date(startDate);
  if (isNaN(d.getTime())) return { day: "--", month: "---" };
  return {
    day: String(d.getDate()).padStart(2, "0"),
    month: d.toLocaleDateString("en-US", { month: "short" }).toUpperCase(),
  };
};

/* ------------------- Full date + time label, e.g. "Oct 15, 2024 • 9:00 AM" ------------------- */
const dateTimeLabel = (startDate: string) => {
  const d = new Date(startDate);
  if (isNaN(d.getTime())) return "--";
  const datePart = d.toLocaleDateString("en-US", {
    month: "short",
    day: "numeric",
    year: "numeric",
  });
  const timePart = d.toLocaleTimeString("en-US", {
    hour: "numeric",
    minute: "2-digit",
  });
  return `${datePart} • ${timePart}`;
};

/* ------------------- event_type label + color ------------------- */
const eventTypeLabel = (type: EventItem["event_type"]) => {
  const map: Record<string, string> = {
    job_fair: "Job Fair",
    reunion: "Reunion",
    workshop: "Workshop",
    seminar: "Seminar",
    other: "Other",
  };
  return type ? (map[type] ?? type) : null;
};

const eventTypeClass = (type: EventItem["event_type"]) => {
  const map: Record<string, string> = {
    job_fair: "bg-blue-50 text-blue-600 border-blue-100",
    reunion: "bg-pink-50 text-pink-600 border-pink-100",
    workshop: "bg-purple-50 text-purple-600 border-purple-100",
    seminar: "bg-amber-50 text-amber-600 border-amber-100",
    other: "bg-slate-50 text-slate-500 border-slate-200",
  };
  return type ? (map[type] ?? map.other) : map.other;
};

/* ------------------- status badge (matches enum values exactly) ------------------- */
const statusBadgeClass = (status: EventItem["status"]) => {
  switch (status) {
    case "draft":
      return "bg-white text-slate-500 border border-slate-300";
    case "published":
      return "bg-emerald-500 text-white";
    case "ongoing":
      return "bg-blue-500 text-white";
    case "completed":
      return "bg-slate-500 text-white";
    case "cancelled":
      return "bg-red-500 text-white";
    default:
      return "bg-white text-slate-500 border border-slate-300";
  }
};



/* =========================================
   Add Event
========================================= */
const isOpenAddForm = ref(false);

const handdleForm = () => {
  isOpenAddForm.value = !isOpenAddForm.value;
};
</script>

<template>
  <AdminsEventAdd v-if="isOpenAddForm" @close="handdleForm" @submitted="handdleForm" />

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
          :key="tab.value"
          @click="setTab(tab.value)"
          class="px-3 sm:px-4 py-1.5 text-xs sm:text-sm rounded-md whitespace-nowrap transition cursor-pointer"
          :class="
            activeTab === tab.value
              ? 'bg-slate-100 text-slate-800 font-medium'
              : 'text-slate-500 hover:text-slate-700'
          "
        >
          {{ tab.label }}
        </button>
      </div>

      <button
        @click="handdleForm"
        class="flex justify-center items-center gap-1 text-nowrap px-4 py-2 bg-primary text-white rounded-md text-sm cursor-pointer hover:bg-primary/80 w-fit"
      >
        <AddIcon />
        <span>Create Event</span>
      </button>
    </div>

    <!-- Loading -->
    <div
      v-if="eventStore.isLoading && !eventStore.data?.data"
      class="grid grid-cols-4 gap-4 max-xl:grid-cols-3"
    >
      <div
        v-for="n in 3"
        :key="n"
        class="bg-white border border-slate-200 rounded-xl overflow-hidden shadow-sm animate-pulse"
        style="min-height: 296px"
      >
        <div class="h-50 bg-slate-200"></div>
        <div class="p-4 space-y-2">
          <div class="h-4 bg-slate-200 rounded w-3/4"></div>
          <div class="h-3 bg-slate-200 rounded w-full"></div>
          <div class="h-3 bg-slate-200 rounded w-1/2"></div>
        </div>
      </div>
    </div>

    <!-- Event cards -->
    <div v-else class="grid grid-cols-4 gap-4 max-xl:grid-cols-3">
      <NuxtLink
        :to="{ name: 'admins-events-id', params: { id: event.id } }"
        v-for="event in (eventStore.data?.data.data) || []"
        :key="event.id"
      >
        <div
          class="bg-white border group hover:-translate-y-1 cursor-pointer ease-in-out hover:shadow-lg duration-300 border-slate-200 rounded-xl overflow-hidden shadow-sm flex flex-col w-full h-full"
        >
          <!-- Image + date badge + status -->
          <div class="relative h-50 w-full bg-slate-100 shrink-0">
            <img
              v-if="event.banner_image"
              :src="event.banner_image_url!"
              :alt="event.title"
              class="w-full h-full object-cover group-hover:scale-105 duration-300 ease-in-out"
            />
            <div
              v-else
              class="w-full h-full flex items-center justify-center bg-linear-to-br from-slate-50 to-slate-100 text-slate-300"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                class="size-10"
              >
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                <circle cx="8.5" cy="8.5" r="1.5" />
                <polyline points="21 15 16 10 5 21" />
              </svg>
            </div>

            <div
              class="absolute top-2 left-2 bg-white rounded-md px-2.5 py-1 text-center leading-tight shadow"
            >
              <div class="text-[10px] font-semibold text-red-500 uppercase tracking-wide">
                {{ dateBadge(event.start_date).month }}
              </div>
              <div class="text-sm font-bold text-slate-800">
                {{ dateBadge(event.start_date).day }}
              </div>
            </div>

            <span
              class="absolute top-2 right-2 px-2.5 py-1 rounded-full text-[10px] font-semibold capitalize shadow-sm"
              :class="statusBadgeClass(event.status)"
            >
              {{ event.status }}
            </span>
          </div>

          <!-- Body -->
          <div class="p-4 flex flex-col gap-2 flex-1 relative">
            <div class="flex items-start justify-between gap-2">
              <h3 class="font-semibold text-slate-800 text-[15px] leading-snug line-clamp-1">
                {{ event.title }}
              </h3>

              <div class="relative shrink-0" @click.prevent.stop>
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
                    class="flex items-center gap-2 px-3 py-2 text-xs text-slate-600 hover:bg-slate-50 cursor-pointer"
                  >
                    <EditIcon class="size-3.5" /> Edit
                  </button>
                  <button
                    @click="handleDelete(event.id)"
                    :disabled="isDeleting === event.id"
                    class="flex items-center gap-2 px-3 py-2 text-xs text-red-500 hover:bg-slate-50 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    <DeleteIcon class="size-3.5" />
                    {{ isDeleting === event.id ? "Deleting..." : "Delete" }}
                  </button>
                </div>
              </div>
            </div>

            <span
              v-if="eventTypeLabel(event.event_type)"
              class="text-[11px] font-medium w-fit px-2 py-0.5 border rounded"
              :class="eventTypeClass(event.event_type)"
            >
              {{ eventTypeLabel(event.event_type) }}
            </span>

            <p
              v-if="event.description"
              class="text-[13px] text-slate-500 leading-snug line-clamp-2"
            >
              {{ event.description }}
            </p>

            <div class="border-t border-slate-100 mt-2 pt-2 flex flex-col gap-1.5 text-xs text-slate-500">
              <!-- date + time -->
              <div class="flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5 shrink-0">
                  <rect x="3" y="4.5" width="18" height="16.5" rx="2" />
                  <path stroke-linecap="round" d="M3 9h18M8 3v3m8-3v3" />
                </svg>
                <span class="truncate">{{ dateTimeLabel(event.start_date) }}</span>
              </div>

              <!-- location -->
              <div v-if="event.location" class="flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5 shrink-0">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                </svg>
                <span class="truncate">{{ event.location }}</span>
              </div>

              <!-- attendees -->
              <div class="flex items-center justify-between pt-1">
                <div class="flex items-center gap-1.5">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-3.5 shrink-0">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                  </svg>
                  <span class="font-medium text-slate-700">
                    {{ event.registrations_count ?? 0 }}
                  </span>
                  <span>/ {{ event.max_participants }} attending</span>
                </div>

                <span
                  class="text-[11px] font-medium px-2 py-0.5 rounded-full"
                  :class="
                    (event.registrations_count ?? 0) >= (event.max_participants ?? 0)
                      ? 'bg-red-50 text-red-500'
                      : 'bg-emerald-50 text-emerald-600'
                  "
                >
                  {{
                    (event.registrations_count ?? 0) >= (event.max_participants ?? 0)
                      ? "Full"
                      : "Open"
                  }}
                </span>
              </div>
            </div>
          </div>
        </div>
      </NuxtLink>

      <!-- Empty state -->
      <div
        v-if="!eventStore.isLoading && !eventStore.data?.data?.data?.length"
        class="col-span-full text-center py-12 text-slate-400 font-medium"
      >
        No events found in this view.
      </div>

      <!-- Plan a New Event (placeholder card) -->
      <button
        @click="handdleForm"
        class="bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl flex flex-col items-center justify-center gap-3 py-10 px-6 text-center hover:border-primary/50 hover:bg-slate-100 transition cursor-pointer w-full"
        style="min-height: 296px"
      >
        <div class="size-10 rounded-full bg-white border border-slate-200 flex items-center justify-center shadow-sm">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="size-5 text-slate-700">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3-15H6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 006 21h12a2.25 2.25 0 002.25-2.25V6.108c0-.464-.184-.909-.51-1.238l-3.61-3.61a1.75 1.75 0 00-1.238-.51z" />
          </svg>
        </div>
        <div class="text-[15px] font-semibold text-slate-800">Plan a New Event</div>
        <p class="text-[13px] text-slate-500 leading-snug">
          Create an <span class="text-primary">upcoming</span> webinar, mixer, or workshop for alumni.
        </p>
      </button>
    </div>
  </div>
</template>