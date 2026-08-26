<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted, nextTick } from "vue";

import { useDirectoryStore } from "~/stores/alumnis/directory";

definePageMeta({
  middleware: ["auth", "alumni"],
  layout: "default",
});

const directoryStore = useDirectoryStore();
const majors = useMajorStore();
const avatar = useAvatar();

const searchQuery = ref("");
const selectedYear = ref("");
const selectedMajor = ref("");

const getBadgeStyle = (type: string) => {
  switch (type) {
    case "employed":
    case "self_employed":
      return "bg-[#E3F2ED] text-[#2C7A6B]";

    case "studying":
      return "bg-[#E8EEF5] text-[#486581]";

    case "seeking":
    case "unemployed":
      return "bg-[#FBEBEB] text-[#A64141]";

    default:
      return "bg-slate-100 text-slate-600";
  }
};

const clearFilters = () => {
  searchQuery.value = "";
  selectedYear.value = "";
  selectedMajor.value = "";
};

let searchTimeout: ReturnType<typeof setTimeout> | null = null;

const runFilter = () => {
  directoryStore.getDirectory({
    search: searchQuery.value,
    year: selectedYear.value,
    major_id: selectedMajor.value,
  });
};

watch(searchQuery, () => {
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }

  searchTimeout = setTimeout(() => {
    runFilter();
  }, 400);
});

watch([selectedYear, selectedMajor], () => {
  runFilter();
});

/*
|--------------------------------------------------------------------------
| Infinite Scroll
|--------------------------------------------------------------------------
*/

const scrollSentinel = ref<HTMLElement | null>(null);

let observer: IntersectionObserver | null = null;

const createObserver = async () => {
  await nextTick();

  // Remove previous observer
  if (observer) {
    observer.disconnect();
    observer = null;
  }

  // Sentinel does not exist yet
  if (!scrollSentinel.value) {
    return;
  }

  observer = new IntersectionObserver(
    (entries) => {
      const entry = entries[0];

      if (!entry?.isIntersecting) {
        return;
      }

      // Don't load if already loading
      if (directoryStore.isLoadingMore) {
        return;
      }

      // Don't load if there is no more data
      if (!directoryStore.hasMore) {
        return;
      }

      directoryStore.loadMore();
    },
    {
      root: null,
      rootMargin: "300px 0px",
      threshold: 0,
    },
  );

  observer.observe(scrollSentinel.value);
};

/*
|--------------------------------------------------------------------------
| Re-create observer when directory cards are rendered
|--------------------------------------------------------------------------
*/

watch(
  () => directoryStore.alumniList.length,
  async () => {
    await createObserver();
  },
);

watch(
  () => directoryStore.data,
  async (data) => {
    if (data) {
      await createObserver();
    }
  },
);

/*
|--------------------------------------------------------------------------
| Mounted
|--------------------------------------------------------------------------
*/

onMounted(async () => {
  await directoryStore.getDirectory();

  await createObserver();
});

/*
|--------------------------------------------------------------------------
| Cleanup
|--------------------------------------------------------------------------
*/

onUnmounted(() => {
  if (searchTimeout) {
    clearTimeout(searchTimeout);
  }

  if (observer) {
    observer.disconnect();
    observer = null;
  }
});
</script>

<template>
  <div class="max-w-7xl mx-auto space-y-6">
    <!-- Title & Subtitle -->
    <div class="space-y-1">
      <h1
        class="text-2xl sm:text-3xl font-extrabold text-[#0C322C] tracking-tight"
      >
        Alumni Directory
      </h1>
      <p class="text-slate-500 text-xs sm:text-sm">
        Connect with graduates across industries.
      </p>
    </div>

    <!-- Initial loading skeleton: shown only before any data has arrived -->
    <ClientOnly v-if="directoryStore.isLoading && !directoryStore.data">
      <div>
        <div
          class="w-full bg-white rounded-2xl flex justify-between max-lg:flex-col items-center gap-6 p-4 border border-slate-200"
        >
          <div
            class="w-full md:flex-1 p-4 bg-slate-200 rounded-md animate-pulse"
          ></div>

          <div
            class="flex justify-end items-center gap-4 max-lg:w-full animate-pulse"
          >
            <div class="w-20 max-lg:w-full rounded-full bg-slate-200 p-4"></div>
            <div class="w-20 max-lg:w-full rounded-full bg-slate-200 p-4"></div>
          </div>
        </div>
        <div
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5 mt-6"
        >
          <div
            v-for="index in 6"
            :key="index"
            class="w-full bg-white rounded-3xl border border-slate-200 p-5"
          >
            <div
              class="w-full flex flex-col justify-between space-y-2 animate-pulse"
            >
              <div class="w-full flex justify-between items-start">
                <div class="size-15 bg-slate-200 rounded-full"></div>
                <div class="w-20 p-3 rounded-full bg-slate-200"></div>
              </div>
              <div class="w-30 p-2.5 bg-slate-200 rounded-full"></div>
              <div class="flex justify-start items-center gap-1">
                <div class="w-15 p-1 bg-slate-200 rounded-full"></div>
                <div class="w-15 p-1 bg-slate-200 rounded-full"></div>
                <div class="w-15 p-1 bg-slate-200 rounded-full"></div>
              </div>
              <hr class="w-full text-slate-200 my-2" />
              <div class="flex justify-start items-center gap-1">
                <div class="size-6 p-1 bg-slate-200 rounded-full"></div>
                <div class="w-20 p-1.5 bg-slate-200 rounded-full"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </ClientOnly>

    <!-- Main content: once data has loaded at least once. Dimmed (not hidden) during a filter refetch. -->
    <div
      v-else-if="directoryStore.data"
      class="w-full space-y-6 transition-opacity"
      :class="{ 'opacity-50 pointer-events-none': directoryStore.isLoading }"
    >
      <!-- Search & Filter Bar -->
      <div
        class="bg-white p-3 sm:p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row items-center gap-3"
      >
        <!-- Search Input Wrapper -->
        <div class="relative w-full md:flex-1">
          <svg
            class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
            />
          </svg>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search by name, company, or role..."
            class="w-full pl-10 pr-4 py-2 bg-[#F6F8FA] border border-slate-200/60 rounded-xl text-xs sm:text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-[#2C7A6B]/20 focus:border-[#2C7A6B] transition"
          />
        </div>

        <!-- Dropdown Filters -->
        <div
          class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full md:w-auto"
        >
          <!-- Class Year Dropdown -->
          <div class="relative flex-1 sm:flex-initial">
            <select
              v-model="selectedYear"
              class="w-full sm:w-auto appearance-none bg-white border border-slate-200/80 rounded-full px-3.5 py-2 pr-8 text-xs font-medium text-slate-600 focus:outline-none focus:border-[#2C7A6B] hover:border-slate-300 transition cursor-pointer"
            >
              <option value="">Class Year</option>
              <option
                v-for="item in directoryStore.data.data.graduation_years"
                :key="item"
                :value="item"
              >
                {{ item }}
              </option>
            </select>
            <svg
              class="w-3 h-3 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
              />
            </svg>
          </div>

          <!-- Major Dropdown -->
          <div class="relative flex-1 sm:flex-initial">
            <select
              v-model="selectedMajor"
              class="w-full sm:w-auto appearance-none bg-white border border-slate-200/80 rounded-full px-3.5 py-2 pr-8 text-xs font-medium text-slate-600 focus:outline-none focus:border-[#2C7A6B] hover:border-slate-300 transition cursor-pointer"
            >
              <option value="">Major</option>
              <option
                v-for="(item, index) in majors.data?.data"
                :key="index"
                :value="item.id"
              >
                {{ item.name }}
              </option>
            </select>
            <svg
              class="w-3 h-3 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M19 9l-7 7-7-7"
              />
            </svg>
          </div>
        </div>
      </div>

      <!-- Alumni Directory Cards Grid -->
      <div
        v-if="directoryStore.alumniList.length > 0"
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5"
      >
        <div
          v-for="person in directoryStore.alumniList"
          :key="person.id"
          class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between space-y-5 group"
        >
          <div class="space-y-3">
            <!-- Top Row: Avatar & Status Pill -->
            <div class="flex items-start justify-between">
              <img
                :src="
                  person.user.avatar
                    ? (person.user.profile_url ??
                      avatar.textToImage(person.user.name_english))
                    : avatar.textToImage(person.user.name_english)
                "
                :alt="person.user.name_english"
                class="w-12 h-12 rounded-full object-cover ring-2 ring-slate-100 group-hover:ring-[#2C7A6B]/20 transition-all"
              />
              <span
                :class="[
                  'text-[10px] font-bold tracking-wider px-2.5 py-1 rounded-full uppercase shrink-0',
                  getBadgeStyle(person.employment_status),
                ]"
              >
                {{ person.employment_status }}
              </span>
            </div>

            <!-- Name & Major Info -->
            <div>
              <h3
                class="font-bold text-slate-800 text-base leading-snug group-hover:text-[#2C7A6B] transition-colors"
              >
                {{ person.user.name_english }}
              </h3>
              <p class="text-[11px] text-slate-400 font-medium mt-0.5">
                Class of {{ person.graduation_year }} • {{ person.major.name }}
              </p>
            </div>
          </div>

          <!-- Role & Icon Footer -->
          <div
            class="pt-3 border-t border-slate-100 flex items-center gap-2 text-xs text-slate-600 font-medium"
          >
            <!-- Job Icon -->
            <svg
              v-if="
                person.employment_status === 'employed' ||
                person.employment_status === 'self_employed'
              "
              class="w-4 h-4 text-slate-400 shrink-0"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
              />
            </svg>
            <!-- Education Icon -->
            <svg
              v-else-if="person.employment_status === 'studying'"
              class="w-4 h-4 text-slate-400 shrink-0"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 14l9-5-9-5-9 5 9 5z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"
              />
            </svg>
            <!-- Location Icon -->
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
              {{ person.employment?.job_title?? 'N/A' }}-
              {{ person.employment?.company?? 'N/A' }}
            </span>
          </div>
        </div>

        <!-- Scroll sentinel spans the full grid width -->
        <div
          ref="scrollSentinel"
          class="col-span-full flex justify-center py-6"
        >
          <div
            v-if="directoryStore.isLoadingMore"
            class="flex items-center gap-2 text-xs text-slate-400"
          >
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
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
                d="M4 12a8 8 0 018-8v8H4z"
              />
            </svg>
            <span>Loading more...</span>
          </div>
          <div
            v-else-if="!directoryStore.hasMore"
            class="text-xs text-slate-400"
          >
            That's everyone — end of the list.
          </div>
        </div>
      </div>

      <!-- Empty State (When no match is found) -->
      <div
        v-else
        class="text-center py-12 px-4 bg-white rounded-2xl border border-slate-200/80 space-y-3"
      >
        <div
          class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-400"
        >
          <svg
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"
            />
          </svg>
        </div>
        <h3 class="text-base font-semibold text-slate-800">No alumni found</h3>
        <p class="text-xs text-slate-500 max-w-sm mx-auto">
          We couldn't find any alumni matching your current filter criteria.
        </p>
        <button
          @click="clearFilters"
          class="inline-flex items-center text-xs font-semibold text-[#2C7A6B] hover:underline pt-1 cursor-pointer"
        >
          Reset Filters
        </button>
      </div>
    </div>

    <!-- Fallback: not loading, and no data at all (e.g. initial request failed) -->
    <div
      v-else
      class="text-center py-12 px-4 bg-white rounded-2xl border border-slate-200/80 space-y-3"
    >
      <h3 class="text-base font-semibold text-slate-800">
        Couldn't load the alumni directory
      </h3>
      <p class="text-xs text-slate-500 max-w-sm mx-auto">
        Something went wrong while fetching the data. Please try again.
      </p>
      <button
        @click="directoryStore.getDirectory()"
        class="inline-flex items-center text-xs font-semibold text-[#2C7A6B] hover:underline pt-1 cursor-pointer"
      >
        Retry
      </button>
    </div>
  </div>
</template>
