<script setup lang="ts">
import { ref } from "vue";

const searchQuery = ref("");
const selectedYear = ref("");
const selectedMajor = ref("");
const selectedIndustry = ref("");

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
    role: "Law Student @ Global Law",
    iconType: "education",
  },
]);

const getBadgeStyle = (type: string) => {
  switch (type) {
    case "employed":
      return "bg-[#E3F2ED] text-[#2C7A6B]";
    case "studying":
      return "bg-[#E8EEF5] text-[#486581]";
    case "seeking":
      return "bg-[#FBEBEB] text-[#A64141]";
    default:
      return "bg-gray-100 text-gray-600";
  }
};
</script>

<template>
  <div class="max-w-7xl mx-auto space-y-6">
    <!-- Title & Subtitle -->
    <div class="space-y-1">
      <h1 class="text-3xl font-extrabold text-[#0C322C] tracking-tight">
        Alumni Directory
      </h1>
      <p class="text-slate-500 text-sm sm:text-base">
        Connect with graduates across industries.
      </p>
    </div>

    <!-- Search & Filter Card -->
    <div
      class="bg-white p-3 sm:p-4 rounded-2xl border border-slate-200/80 shadow-[0_2px_8px_rgba(0,0,0,0.02)] flex flex-col md:flex-row items-center gap-3"
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
            d="21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
          />
        </svg>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by name, company, or role..."
          class="w-full pl-9 pr-4 py-2 bg-[#F6F8FA] border border-slate-200/60 rounded-xl text-xs sm:text-sm text-slate-700 placeholder-slate-400 focus:outline-none focus:bg-white focus:border-emerald-500 transition"
        />
      </div>

      <!-- Dropdown Filters -->
      <div class="flex items-center gap-2 w-full md:w-auto justify-end">
        <div class="relative">
          <select
            v-model="selectedYear"
            class="appearance-none bg-white border border-slate-200/80 rounded-full px-4 py-1.5 pr-8 text-xs font-medium text-slate-600 focus:outline-none hover:border-slate-300 transition cursor-pointer"
          >
            <option value="">Class Year</option>
            <option value="2023">2023</option>
            <option value="2022">2022</option>
            <option value="2021">2021</option>
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
              d="19 9l-7 7-7-7"
            />
          </svg>
        </div>

        <div class="relative">
          <select
            v-model="selectedMajor"
            class="appearance-none bg-white border border-slate-200/80 rounded-full px-4 py-1.5 pr-8 text-xs font-medium text-slate-600 focus:outline-none hover:border-slate-300 transition cursor-pointer"
          >
            <option value="">Major</option>
            <option value="cs">Computer Science</option>
            <option value="ba">Business Admin</option>
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
              d="19 9l-7 7-7-7"
            />
          </svg>
        </div>

        <div class="relative">
          <select
            v-model="selectedIndustry"
            class="appearance-none bg-white border border-slate-200/80 rounded-full px-4 py-1.5 pr-8 text-xs font-medium text-slate-600 focus:outline-none hover:border-slate-300 transition cursor-pointer"
          >
            <option value="">Industry</option>
            <option value="tech">Tech</option>
            <option value="finance">Finance</option>
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
              d="19 9l-7 7-7-7"
            />
          </svg>
        </div>
      </div>
    </div>

    <!-- Alumni Directory Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
      <div
        v-for="person in alumniList"
        :key="person.id"
        class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-[0_2px_6px_rgba(0,0,0,0.02)] flex flex-col justify-between space-y-6 hover:shadow-md transition-shadow duration-200"
      >
        <div class="space-y-3">
          <!-- Top Row: Avatar & Status Pill -->
          <div class="flex items-start justify-between">
            <img
              :src="person.avatar"
              :alt="person.name"
              class="w-12 h-12 rounded-full object-cover"
            />
            <span
              :class="[
                'text-[10px] font-bold tracking-wider px-2.5 py-1 rounded-full uppercase',
                getBadgeStyle(person.statusType),
              ]"
            >
              {{ person.status }}
            </span>
          </div>

          <!-- Name & Major Info -->
          <div>
            <h3 class="font-bold text-slate-800 text-base leading-snug">
              {{ person.name }}
            </h3>
            <p class="text-[11px] text-slate-400 font-medium">
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
            class="w-3.5 h-3.5 text-slate-400 shrink-0"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
            />
          </svg>
          <!-- Education Icon -->
          <svg
            v-else-if="person.iconType === 'education'"
            class="w-3.5 h-3.5 text-slate-400 shrink-0"
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
            class="w-3.5 h-3.5 text-slate-400 shrink-0"
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

    <!-- Bottom Action Button -->
    <div class="flex justify-center pt-2">
      <button
        class="flex items-center gap-2 bg-white border border-slate-200 hover:border-emerald-600 text-[#2C7A6B] font-semibold text-xs px-4 py-2 rounded-xl shadow-xs transition"
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
