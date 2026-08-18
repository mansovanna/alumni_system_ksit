<script setup lang="ts">
import { ref, computed } from "vue";

// Filter States
const searchQuery = ref("");
const selectedYear = ref("");
const selectedMajor = ref("");
const selectedIndustry = ref("");

// Alumni Data
const alumniList = ref([
  {
    id: 1,
    name: "Sarah Jenkins",
    avatar:
      "https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150",
    status: "EMPLOYED",
    statusType: "employed",
    classYear: "2021",
    major: "Computer Science",
    majorCode: "cs",
    role: "Product Designer @ TechFlow",
    iconType: "job",
  },
  {
    id: 2,
    name: "David Chen",
    avatar:
      "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150",
    status: "EMPLOYED",
    statusType: "employed",
    classYear: "2022",
    major: "Business Admin",
    majorCode: "ba",
    role: "Financial Analyst @ Nexus Bank",
    iconType: "job",
  },
  {
    id: 3,
    name: "Elena Rodriguez",
    avatar:
      "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150",
    status: "STUDYING",
    statusType: "studying",
    classYear: "2023",
    major: "Biology",
    majorCode: "bio",
    role: "PhD Candidate @ State Univ",
    iconType: "education",
  },
  {
    id: 4,
    name: "Michael Chang",
    avatar:
      "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150",
    status: "SEEKING",
    statusType: "seeking",
    classYear: "2023",
    major: "Graphic Design",
    majorCode: "gd",
    role: "Open to Relocation",
    iconType: "location",
  },
  {
    id: 5,
    name: "Dr. Emily Stanton",
    avatar:
      "https://images.unsplash.com/photo-1580489944761-15a19d654956?w=150",
    status: "EMPLOYED",
    statusType: "employed",
    classYear: "2015",
    major: "Engineering",
    majorCode: "eng",
    role: "VP Engineering @ BuildCo",
    iconType: "job",
  },
  {
    id: 6,
    name: "Omar Al-Fayed",
    avatar:
      "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150",
    status: "STUDYING",
    statusType: "studying",
    classYear: "2022",
    major: "History",
    majorCode: "his",
    role: "Law Student @ Global Law",
    iconType: "education",
  },
]);

// Badge Styling Helper
const getBadgeStyle = (type: string) => {
  switch (type) {
    case "employed":
      return "bg-[#E3F2ED] text-[#2C7A6B]";
    case "studying":
      return "bg-[#E8EEF5] text-[#486581]";
    case "seeking":
      return "bg-[#FBEBEB] text-[#A64141]";
    default:
      return "bg-slate-100 text-slate-600";
  }
};

// Filter Logic
const filteredAlumni = computed(() => {
  return alumniList.value.filter((person) => {
    // Search Query (Name, Major, or Role)
    const matchesSearch =
      searchQuery.value === "" ||
      person.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      person.role.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      person.major.toLowerCase().includes(searchQuery.value.toLowerCase());

    // Year Filter
    const matchesYear =
      selectedYear.value === "" || person.classYear === selectedYear.value;

    // Major Filter
    const matchesMajor =
      selectedMajor.value === "" || person.majorCode === selectedMajor.value;

    return matchesSearch && matchesYear && matchesMajor;
  });
});

// Clear Filters Helper
const clearFilters = () => {
  searchQuery.value = "";
  selectedYear.value = "";
  selectedMajor.value = "";
  selectedIndustry.value = "";
};
</script>

<template>
  <div class="max-w-7xl mx-auto  space-y-6">
    <!-- Title & Subtitle -->
    <div class="space-y-1">
      <h1 class="text-2xl sm:text-3xl font-extrabold text-[#0C322C] tracking-tight">
        Alumni Directory
      </h1>
      <p class="text-slate-500 text-xs sm:text-sm">
        Connect with graduates across industries.
      </p>
    </div>

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
      <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 w-full md:w-auto">
        <!-- Class Year Dropdown -->
        <div class="relative flex-1 sm:flex-initial">
          <select
            v-model="selectedYear"
            class="w-full sm:w-auto appearance-none bg-white border border-slate-200/80 rounded-full px-3.5 py-2 pr-8 text-xs font-medium text-slate-600 focus:outline-none focus:border-[#2C7A6B] hover:border-slate-300 transition cursor-pointer"
          >
            <option value="">Class Year</option>
            <option value="2023">2023</option>
            <option value="2022">2022</option>
            <option value="2021">2021</option>
            <option value="2015">2015</option>
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
            <option value="cs">Computer Science</option>
            <option value="ba">Business Admin</option>
            <option value="bio">Biology</option>
            <option value="gd">Graphic Design</option>
            <option value="eng">Engineering</option>
            <option value="his">History</option>
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

        <!-- Industry Dropdown -->
        <div class="relative flex-1 sm:flex-initial">
          <select
            v-model="selectedIndustry"
            class="w-full sm:w-auto appearance-none bg-white border border-slate-200/80 rounded-full px-3.5 py-2 pr-8 text-xs font-medium text-slate-600 focus:outline-none focus:border-[#2C7A6B] hover:border-slate-300 transition cursor-pointer"
          >
            <option value="">Industry</option>
            <option value="tech">Tech</option>
            <option value="finance">Finance</option>
            <option value="education">Education</option>
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
      v-if="filteredAlumni.length > 0"
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5"
    >
      <div
        v-for="person in filteredAlumni"
        :key="person.id"
        class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between space-y-5 group"
      >
        <div class="space-y-3">
          <!-- Top Row: Avatar & Status Pill -->
          <div class="flex items-start justify-between">
            <img
              :src="person.avatar"
              :alt="person.name"
              class="w-12 h-12 rounded-full object-cover ring-2 ring-slate-100 group-hover:ring-[#2C7A6B]/20 transition-all"
            />
            <span
              :class="[
                'text-[10px] font-bold tracking-wider px-2.5 py-1 rounded-full uppercase shrink-0',
                getBadgeStyle(person.statusType),
              ]"
            >
              {{ person.status }}
            </span>
          </div>

          <!-- Name & Major Info -->
          <div>
            <h3 class="font-bold text-slate-800 text-base leading-snug group-hover:text-[#2C7A6B] transition-colors">
              {{ person.name }}
            </h3>
            <p class="text-[11px] text-slate-400 font-medium mt-0.5">
              Class of {{ person.classYear }} • {{ person.major }}
            </p>
          </div>
        </div>

        <!-- Role & Icon Footer -->
        <div
          class="pt-3 border-t border-slate-100 flex items-center gap-2 text-xs text-slate-600 font-medium"
        >
          <!-- Job Icon -->
          <svg
            v-if="person.iconType === 'job'"
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
            v-else-if="person.iconType === 'education'"
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

          <span class="truncate">{{ person.role }}</span>
        </div>
      </div>
    </div>

    <!-- Empty State (When no match is found) -->
    <div
      v-else
      class="text-center py-12 px-4 bg-white rounded-2xl border border-slate-200/80 space-y-3"
    >
      <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto text-slate-400">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
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

    <!-- Bottom Action Button -->
    <div v-if="filteredAlumni.length > 0" class="flex justify-center pt-2">
      <button
        class="flex items-center gap-2 bg-white border border-slate-200 hover:border-[#2C7A6B] text-[#2C7A6B] font-semibold text-xs px-5 py-2.5 rounded-xl shadow-xs hover:shadow-sm transition cursor-pointer"
      >
        <span>Load More Alumni</span>
        <svg
          class="w-3.5 h-3.5"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
          />
        </svg>
      </button>
    </div>
  </div>
</template>