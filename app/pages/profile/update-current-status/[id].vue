<script setup lang="ts">
import { ref, computed } from "vue";

// Step State
const currentStep = ref(2); // 1: Graduated, 2: Career, 3: Milestone

const steps = [
  { id: 1, label: "Graduated", icon: "graduation" },
  { id: 2, label: "Career", icon: "rocket" },
  { id: 3, label: "Milestone", icon: "sparkles" },
];

// Form Data State
const form = ref({
  employmentStatus: "Employed Full-Time",
  role: "",
  company: "",
  industry: "",
  location: "",
  openToMentoring: false,
});

// Dropdown Options
const employmentStatusOptions = [
  "Employed Full-Time",
  "Employed Part-Time",
  "Self-Employed / Freelance",
  "Seeking Opportunities",
  "Continuing Education",
];

const industryOptions = [
  "Technology & Software",
  "Finance & Banking",
  "Healthcare & Medicine",
  "Education & Research",
  "Engineering & Manufacturing",
  "Creative & Design",
];

// Simple completion meter so the form gives quiet feedback as it fills in
const requiredFields = ["role", "company", "industry", "location"] as const;
const completion = computed(() => {
  const filled = requiredFields.filter((f) => form.value[f]?.trim()).length;
  return Math.round((filled / requiredFields.length) * 100);
});

// Actions
const handleSave = () => {
  console.log("Saving career status:", form.value);
  alert("Career status updated successfully!");
};

const router = useRouter();
const handleCancel = () => {
  // console.log("Cancelled");
  router.back();
};
</script>

<template>
  <div class="space-y-6 font-Inter">
    <!-- Header -->
    <div class="w-full flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-semibold text-slate-900">
          Update Career Status
        </h1>
        <p class="text-slate-400 text-sm mt-0.5">
          Keep your alumni network informed about your professional journey.
        </p>
      </div>

      <button
        @click="handleCancel"
        type="button"
        class="p-1.5 px-4 flex justify-center items-center gap-1.5 bg-slate-900 text-white hover:bg-slate-800 rounded-lg active:bg-slate-700 transition cursor-pointer shadow-xs"
        aria-label="Go back"
      >
        <!-- ArrowLeft Icon SVG -->
        <svg
          class="w-4 h-4"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
          viewBox="0 0 24 24"
        >
          <path d="m12 19-7-7 7-7" />
          <path d="M19 12H5" />
        </svg>
        <span class="text-sm font-medium">Back</span>
      </button>
    </div>

    <!-- Stepper Component -->
    <div
      class="bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-xs"
    >
      <div class="flex items-center justify-between relative px-1 sm:px-3">
        <template v-for="(step, idx) in steps" :key="step.id">
          <div class="flex flex-col items-center gap-1.5 z-10">
            <div
              :class="[
                'w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition-all duration-300',
                currentStep > step.id
                  ? 'bg-successDark text-white ring-4 ring-successDark/10'
                  : currentStep === step.id
                    ? 'bg-successDark text-white ring-4 ring-successDark/15 scale-105'
                    : 'bg-slate-100 text-slate-400',
              ]"
            >
              <!-- Check Icon SVG -->
              <svg
                v-if="currentStep > step.id"
                class="w-4 h-4"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                viewBox="0 0 24 24"
              >
                <path d="M20 6 9 17l-5-5" />
              </svg>

              <!-- Step Icons SVG -->
              <template v-else>
                <!-- GraduationCap Icon SVG -->
                <svg
                  v-if="step.icon === 'graduation'"
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  viewBox="0 0 24 24"
                >
                  <path
                    d="M21.42 10.922a1 1 0 0 0-.019-1.838L12.83 5.18a2 2 0 0 0-1.66 0L2.6 9.08a1 1 0 0 0 0 1.832l8.57 3.908a2 2 0 0 0 1.66 0z"
                  />
                  <path d="M22 10v6" />
                  <path d="M6 12.5V16a6 3 0 0 0 12 0v-3.5" />
                </svg>

                <!-- Rocket Icon SVG -->
                <svg
                  v-else-if="step.icon === 'rocket'"
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  viewBox="0 0 24 24"
                >
                  <path
                    d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.71 1.1-1.66 1.12-2.67.03-.96-.34-1.92-1.05-2.63a3.75 3.75 0 0 0-2.63-1.05A3.72 3.72 0 0 0 2.27 14.3c.02 1.01.41 1.96 1.12 2.67z"
                  />
                  <path d="M12 15l-3-3" />
                  <path d="M15 9l-3-3" />
                  <path d="M9 18l3 3" />
                  <path
                    d="M14 2c2.5 0 7 2 7 7 0 3.5-2.5 7.5-6.5 11.5L12 18l-3-3 2.5-2.5C15.5 8.5 19.5 6 19.5 3.5 19.5 2.67 18 2 14 2z"
                  />
                </svg>

                <!-- Sparkles Icon SVG -->
                <svg
                  v-else-if="step.icon === 'sparkles'"
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  viewBox="0 0 24 24"
                >
                  <path
                    d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"
                  />
                  <path d="M20 3v4" />
                  <path d="M22 5h-4" />
                  <path d="M4 17v2" />
                  <path d="M5 18H3" />
                </svg>
              </template>
            </div>
            <span
              :class="[
                'text-[10px] sm:text-xs transition-colors',
                currentStep >= step.id
                  ? 'text-successDark font-semibold'
                  : 'text-slate-400 font-medium',
              ]"
            >
              {{ step.label }}
            </span>
          </div>

          <div
            v-if="idx < steps.length - 1"
            class="flex-1 h-0.5 mx-1.5 sm:mx-3 bg-slate-200 relative -top-3 sm:-top-3.5 overflow-hidden rounded-full"
          >
            <div
              class="h-full bg-successDark transition-all duration-500 ease-out"
              :style="{ width: currentStep > step.id ? '100%' : '0%' }"
            ></div>
          </div>
        </template>
      </div>
    </div>

    <!-- Block Content -->
    <div
      class="w-full bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden"
    >
      <!-- Progress ribbon -->
      <div class="px-5 sm:px-6 pt-5 pb-1 flex items-center justify-between">
        <p class="text-xs font-semibold text-slate-500">Career details</p>
        <p class="text-xs font-semibold text-successDark">
          {{ completion }}% complete
        </p>
      </div>
      <div class="px-5 sm:px-6 pb-4">
        <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
          <div
            class="h-full bg-successDark transition-all duration-500 ease-out rounded-full"
            :style="{ width: completion + '%' }"
          ></div>
        </div>
      </div>

      <div class="px-4 sm:px-6 pb-6 space-y-5 sm:space-y-6">
        <!-- Main Form Container -->
        <div
          class="bg-slate-50/60 border border-slate-200/80 rounded-2xl p-3.5 sm:p-5 space-y-3.5 sm:space-y-4"
        >
          <!-- Field 1: Employment Status -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-800">
              Employment Status
            </label>
            <div class="relative">
              <!-- Layers Icon SVG -->
              <svg
                class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                viewBox="0 0 24 24"
              >
                <path
                  d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.57 3.91a2 2 0 0 0 1.66 0l8.57-3.91a1 1 0 0 0 0-1.83z"
                />
                <path d="m22 12.5-8.57 3.91a2 2 0 0 1-1.66 0L3.2 12.5" />
                <path d="m22 17.5-8.57 3.91a2 2 0 0 1-1.66 0L3.2 17.5" />
              </svg>
              <select
                v-model="form.employmentStatus"
                class="w-full appearance-none bg-white border border-slate-200 rounded-xl pl-9 pr-9 py-2.5 text-xs sm:text-sm text-slate-700 focus:outline-none focus:border-successDark focus:ring-2 focus:ring-successDark/15 transition cursor-pointer hover:border-slate-300"
              >
                <option
                  v-for="status in employmentStatusOptions"
                  :key="status"
                  :value="status"
                >
                  {{ status }}
                </option>
              </select>
              <!-- Dropdown Chevron Icon SVG -->
              <svg
                class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                viewBox="0 0 24 24"
              >
                <path d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </div>

          <!-- Field 2: Current Role / Title -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-800">
              Current Role / Title
            </label>
            <div class="relative">
              <!-- Briefcase Icon SVG -->
              <svg
                class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                viewBox="0 0 24 24"
              >
                <path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                <rect width="20" height="14" x="2" y="6" rx="2" />
              </svg>
              <input
                v-model="form.role"
                type="text"
                placeholder="e.g. Senior Software Engineer"
                class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-sm text-slate-700 placeholder-slate-300 focus:outline-none focus:border-successDark focus:ring-2 focus:ring-successDark/15 transition hover:border-slate-300"
              />
            </div>
          </div>

          <!-- Field 3: Company / Organization -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-800">
              Company / Organization
            </label>
            <div class="relative">
              <!-- Building2 Icon SVG -->
              <svg
                class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                viewBox="0 0 24 24"
              >
                <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z" />
                <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2" />
                <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2" />
                <path d="M10 6h4" />
                <path d="M10 10h4" />
                <path d="M10 14h4" />
                <path d="M10 18h4" />
              </svg>
              <input
                v-model="form.company"
                type="text"
                placeholder="e.g. Tech Innovators Inc."
                class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-sm text-slate-700 placeholder-slate-300 focus:outline-none focus:border-successDark focus:ring-2 focus:ring-successDark/15 transition hover:border-slate-300"
              />
            </div>
          </div>

          <!-- Field 4: Industry -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-800">
              Industry
            </label>
            <div class="relative">
              <!-- Factory/Building Icon SVG -->
              <svg
                class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                viewBox="0 0 24 24"
              >
                <path
                  d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0H5m14 0h2M5 21H3m9-14h.01M12 11h.01M12 15h.01M9 15h.01M9 11h.01M15 15h.01M15 11h.01"
                />
              </svg>
              <select
                v-model="form.industry"
                class="w-full appearance-none bg-white border border-slate-200 rounded-xl pl-9 pr-9 py-2.5 text-xs sm:text-sm text-slate-700 focus:outline-none focus:border-successDark focus:ring-2 focus:ring-successDark/15 transition cursor-pointer hover:border-slate-300"
              >
                <option value="" disabled selected>Select an Industry</option>
                <option v-for="ind in industryOptions" :key="ind" :value="ind">
                  {{ ind }}
                </option>
              </select>
              <!-- Dropdown Chevron Icon SVG -->
              <svg
                class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                viewBox="0 0 24 24"
              >
                <path d="M19 9l-7 7-7-7" />
              </svg>
            </div>
          </div>

          <!-- Field 5: Location -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-800">
              Location
            </label>
            <div class="relative">
              <!-- MapPin Icon SVG -->
              <svg
                class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                viewBox="0 0 24 24"
              >
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                <circle cx="12" cy="10" r="3" />
              </svg>
              <input
                v-model="form.location"
                type="text"
                placeholder="City, State or Country"
                class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-sm text-slate-700 placeholder-slate-300 focus:outline-none focus:border-successDark focus:ring-2 focus:ring-successDark/15 transition hover:border-slate-300"
              />
            </div>
          </div>

          <!-- Toggle Box: Open to Mentoring -->
          <div
            class="bg-white border border-slate-200 rounded-xl p-3 sm:p-3.5 flex items-center justify-between gap-3 transition"
            :class="
              form.openToMentoring
                ? 'ring-1 ring-successDark/25 border-successDark/30'
                : ''
            "
          >
            <div class="flex items-start gap-2.5">
              <div
                class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 transition-colors"
                :class="
                  form.openToMentoring
                    ? 'bg-successDark/10 text-successDark'
                    : 'bg-slate-100 text-slate-400'
                "
              >
                <!-- Users Icon SVG -->
                <svg
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  viewBox="0 0 24 24"
                >
                  <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                  <circle cx="9" cy="7" r="4" />
                  <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                  <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
              </div>
              <div class="space-y-0.5">
                <p class="text-xs font-bold text-slate-800">
                  Open to Mentoring
                </p>
                <p class="text-[11px] text-slate-500 leading-tight">
                  Allow students to contact you for guidance.
                </p>
              </div>
            </div>

            <!-- Switch Toggle -->
            <button
              type="button"
              @click="form.openToMentoring = !form.openToMentoring"
              :class="[
                'relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',
                form.openToMentoring ? 'bg-successDark' : 'bg-slate-300',
              ]"
            >
              <span
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-xs ring-0 transition duration-200 ease-in-out',
                  form.openToMentoring ? 'translate-x-5' : 'translate-x-0',
                ]"
              ></span>
            </button>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="pt-1 w-full flex gap-3 justify-end items-center">
          <button
            @click="handleCancel"
            type="button"
            class="min:w-full text-nowrap px-6 flex items-center gap-2 bg-white hover:bg-slate-50 active:bg-slate-100 border border-rose-300 text-rose-700 font-semibold text-xs sm:text-sm py-2.5 sm:py-3 rounded-xl transition-colors cursor-pointer"
          >
            <!-- X Icon SVG -->
            <svg
              class="w-4 h-4"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              viewBox="0 0 24 24"
            >
              <path d="M18 6 6 18" />
              <path d="m6 6 12 12" />
            </svg>
            Cancel
          </button>

          <button
            @click="handleSave"
            type="button"
            class="min:w-full text-nowrap px-6 flex items-center gap-2 bg-successDark hover:bg-[#005546] active:bg-[#004538] text-white font-semibold text-xs sm:text-sm py-2.5 sm:py-3 rounded-xl transition-colors cursor-pointer shadow-xs"
          >
            <!-- Save Icon SVG -->
            <svg
              class="w-4 h-4"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              viewBox="0 0 24 24"
            >
              <path
                d="M15.2 3a1 1 0 0 1 .7.3l4.8 4.8a1 1 0 0 1 .3.7V20a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"
              />
              <path d="M17 21v-8H7v8" />
              <path d="M7 3v5h8" />
            </svg>
            Save Status
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
