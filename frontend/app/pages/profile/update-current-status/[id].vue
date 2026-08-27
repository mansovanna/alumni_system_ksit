<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import LoadingIcon from "~/components/icons/LoadingIcon.vue";
import { useAlumniProfileStore } from "~/stores/alumnis/alumni";
import { useProfileAlumni } from "~/stores/alumnis/profile";

definePageMeta({
  middleware: ["auth", "alumni"],
  layout: "default",
});

const router = useRouter();

const profileStore = useAlumniProfileStore();
const alumniProfule = useProfileAlumni();

const currentStep = ref(2);

const steps = [
  {
    id: 1,
    label: "Graduated",
    icon: "graduation",
  },
  {
    id: 2,
    label: "Career",
    icon: "rocket",
  },
  {
    id: 3,
    label: "Milestone",
    icon: "sparkles",
  },
];

// =====================================================
// Form Data
// =====================================================

const form = ref({
  employment_type: "full_time",
  job_title: "",
  company_name: "",
  industry: "",
  location: "",
  salary_range: "",
  start_date: "",
  end_date: "",
  is_current: true,
});

// =====================================================
// Helpers
// =====================================================

const formatDateForInput = (date: string | null | undefined) => {
  if (!date) return "";

  // 2026-08-27T00:00:00.000000Z
  //       ↓
  // 2026-08-27
  return date.substring(0, 10);
};

// =====================================================
// Load Employment Data
// =====================================================

const fillForm = () => {
  const employment = alumniProfule.data?.employment;

  if (!employment) return;

  form.value = {
    employment_type: employment.employment_type || "full_time",

    job_title: employment.job_title || "",

    company_name: employment.company_name || "",

    industry: employment.industry || "",

    location: employment.location || "",

    salary_range: employment.salary_range || "",

    start_date: formatDateForInput(employment.start_date),

    end_date: formatDateForInput(employment.end_date),

    is_current: Boolean(employment.is_current),
  };

  console.log("Employment loaded:", employment);
  console.log("Form:", form.value);
};

// =====================================================
// Employment Type Options
// =====================================================

const employmentTypeOptions = [
  {
    value: "full_time",
    label: "Full-Time",
  },
  {
    value: "part_time",
    label: "Part-Time",
  },
  {
    value: "contract",
    label: "Contract",
  },
  {
    value: "internship",
    label: "Internship",
  },
  {
    value: "self_employed",
    label: "Self-Employed / Freelance",
  },
];

// =====================================================
// Completion
// =====================================================

const requiredFields = [
  "job_title",
  "company_name",
  "industry",
  "location",
] as const;

const completion = computed(() => {
  const filled = requiredFields.filter((field) => {
    return form.value[field]?.trim().length > 0;
  }).length;

  return Math.round((filled / requiredFields.length) * 100);
});

// =====================================================
// Validation
// =====================================================

const errorMessage = ref("");

const validateForm = () => {
  errorMessage.value = "";

  if (!form.value.job_title.trim()) {
    errorMessage.value = "Please enter your current role / title.";
    return false;
  }

  if (!form.value.company_name.trim()) {
    errorMessage.value = "Please enter your company / organization.";
    return false;
  }

  if (!form.value.industry.trim()) {
    errorMessage.value = "Please enter your industry.";
    return false;
  }

  if (!form.value.location.trim()) {
    errorMessage.value = "Please enter your location.";
    return false;
  }

  if (!form.value.start_date) {
    errorMessage.value = "Please select your start date.";
    return false;
  }

  if (!form.value.is_current && !form.value.end_date) {
    errorMessage.value = "Please select your end date.";
    return false;
  }

  if (
    !form.value.is_current &&
    form.value.start_date &&
    form.value.end_date &&
    form.value.end_date < form.value.start_date
  ) {
    errorMessage.value = "End date cannot be before start date.";
    return false;
  }

  return true;
};

// =====================================================
// Cancel
// =====================================================

const handleCancel = () => {
  router.back();
};

// =====================================================
// Save
// =====================================================

const isSaving = ref(false);

const handleSave = async () => {
  if (isSaving.value) return;

  if (!validateForm()) {
    return;
  }

  isSaving.value = true;
  errorMessage.value = "";

  const payload = {
    employment_type: form.value.employment_type,

    job_title: form.value.job_title.trim(),

    company_name: form.value.company_name.trim(),

    industry: form.value.industry.trim(),

    location: form.value.location.trim(),

    salary_range: form.value.salary_range.trim() || null,

    start_date: form.value.start_date || null,

    end_date: form.value.is_current
      ? null
      : form.value.end_date || null,

    is_current: form.value.is_current,
  };

  console.log("Career payload:", payload);

  try {
    await profileStore.updateCareer(payload);

    // Reload profile so UI/store has latest data
    await alumniProfule.getProfile();

    // Update form with latest API data
    fillForm();

    alert("Career status updated successfully!");
  } catch (error: any) {
    console.error("Failed to update career:", error);

    if (error?.response?.data?.message) {
      errorMessage.value = error.response.data.message;
    } else {
      errorMessage.value =
        "Failed to update career status. Please try again.";
    }
  } finally {
    isSaving.value = false;
  }
};

// =====================================================
// Load Profile
// =====================================================

onMounted(async () => {
  try {
    await alumniProfule.getProfile();

    fillForm();
  } catch (error) {
    console.error("Failed to load alumni profile:", error);
  }
});
</script>

<template>
  <div
    v-if="!alumniProfule.isLoading && alumniProfule.data?.employment"
    class="space-y-6 font-Inter"
  >
    <!-- =====================================================
         Header
    ====================================================== -->
    <div
      class="w-full flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
    >
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
      >
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

        <span class="text-sm font-medium"> Back </span>
      </button>
    </div>

    <!-- =====================================================
         Stepper
    ====================================================== -->
    <div
      class="bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-xs"
    >
      <div class="flex items-center justify-between relative px-1 sm:px-3">
        <template v-for="(step, idx) in steps" :key="step.id">
          <!-- Step -->
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
              <!-- Completed -->
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

              <template v-else>
                <!-- Graduation -->
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

                <!-- Rocket -->
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

                <!-- Sparkles -->
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

          <!-- Connector -->
          <div
            v-if="idx < steps.length - 1"
            class="flex-1 h-0.5 mx-1.5 sm:mx-3 bg-slate-200 relative -top-3 sm:-top-3.5 overflow-hidden rounded-full"
          >
            <div
              class="h-full bg-successDark transition-all duration-500 ease-out"
              :style="{
                width: currentStep > step.id ? '100%' : '0%',
              }"
            ></div>
          </div>
        </template>
      </div>
    </div>

    <!-- =====================================================
         Main Card
    ====================================================== -->
    <div
      class="w-full bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden"
    >
      <!-- Progress Header -->
      <div class="px-5 sm:px-6 pt-5 pb-1 flex items-center justify-between">
        <p class="text-xs font-semibold text-slate-500">Career details</p>

        <p class="text-xs font-semibold text-successDark">
          {{ completion }}% complete
        </p>
      </div>

      <!-- Progress -->
      <div class="px-5 sm:px-6 pb-4">
        <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
          <div
            class="h-full bg-successDark transition-all duration-500 ease-out rounded-full"
            :style="{
              width: completion + '%',
            }"
          ></div>
        </div>
      </div>

      <!-- Form Content -->
      <div class="px-4 sm:px-6 pb-6 space-y-5 sm:space-y-6">
        <!-- =================================================
             Error Message
        ================================================== -->
        <div
          v-if="errorMessage"
          class="flex items-start gap-3 p-3.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-700"
        >
          <svg
            class="w-5 h-5 shrink-0 mt-0.5"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            viewBox="0 0 24 24"
          >
            <circle cx="12" cy="12" r="10" />
            <line x1="12" x2="12" y1="8" y2="12" />
            <line x1="12" x2="12.01" y1="16" y2="16" />
          </svg>

          <p class="text-xs sm:text-sm font-medium">
            {{ errorMessage }}
          </p>
        </div>

        <!-- =================================================
             Form
        ================================================== -->
        <div
          class="bg-slate-50/60 border border-slate-200/80 rounded-2xl p-3.5 sm:p-5 space-y-3.5 sm:space-y-4"
        >
          <!-- =================================================
               Employment Type
          ================================================== -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-800">
              Employment Type
            </label>

            <div class="relative">
              <!-- Layers Icon -->
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
                v-model="form.employment_type"
                class="w-full appearance-none bg-white border border-slate-200 rounded-xl pl-9 pr-9 py-2.5 text-xs sm:text-sm text-slate-700 focus:outline-none focus:border-successDark focus:ring-2 focus:ring-successDark/15 transition cursor-pointer hover:border-slate-300"
              >
                <option
                  v-for="type in employmentTypeOptions"
                  :key="type.value"
                  :value="type.value"
                >
                  {{ type.label }}
                </option>
              </select>

              <!-- Chevron -->
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

          <!-- =================================================
               Current Role
          ================================================== -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-800">
              Current Role / Title
            </label>

            <div class="relative">
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
                v-model="form.job_title"
                type="text"
                placeholder="e.g. Senior Software Engineer"
                class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-sm text-slate-700 placeholder-slate-300 focus:outline-none focus:border-successDark focus:ring-2 focus:ring-successDark/15 transition hover:border-slate-300"
              />
            </div>
          </div>

          <!-- =================================================
               Company
          ================================================== -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-800">
              Company / Organization
            </label>

            <div class="relative">
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
                v-model="form.company_name"
                type="text"
                placeholder="e.g. Tech Innovators Inc."
                class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-sm text-slate-700 placeholder-slate-300 focus:outline-none focus:border-successDark focus:ring-2 focus:ring-successDark/15 transition hover:border-slate-300"
              />
            </div>
          </div>

          <!-- =================================================
               Industry
               INPUT - NOT SELECT
          ================================================== -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-800">
              Industry
            </label>

            <div class="relative">
              <!-- Building Icon -->
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

              <input
                v-model="form.industry"
                type="text"
                placeholder="e.g. Technology & Software"
                class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-sm text-slate-700 placeholder-slate-300 focus:outline-none focus:border-successDark focus:ring-2 focus:ring-successDark/15 transition hover:border-slate-300"
              />
            </div>

            <p class="text-[10px] text-slate-400">
              Enter your industry, such as Technology, Finance, Education,
              Healthcare, Agriculture, etc.
            </p>
          </div>

          <!-- =================================================
               Location
          ================================================== -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-800">
              Location
            </label>

            <div class="relative">
              <svg
                class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                viewBox="0 0 24 24"
              >
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 1 1 16 0Z" />

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

          <!-- =================================================
               Salary Range
          ================================================== -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-800">
              Salary Range

              <span class="font-normal text-slate-400"> (Optional) </span>
            </label>

            <div class="relative">
              <svg
                class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                viewBox="0 0 24 24"
              >
                <line x1="12" x2="12" y1="2" y2="22" />

                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
              </svg>

              <input
                v-model="form.salary_range"
                type="text"
                placeholder="e.g. $500 - $1,000 / month"
                class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-sm text-slate-700 placeholder-slate-300 focus:outline-none focus:border-successDark focus:ring-2 focus:ring-successDark/15 transition hover:border-slate-300"
              />
            </div>
          </div>

          <!-- =================================================
               Start Date
          ================================================== -->
          <div class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-800">
              Start Date
            </label>

            <div class="relative">
              <svg
                class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                viewBox="0 0 24 24"
              >
                <rect width="18" height="18" x="3" y="4" rx="2" />

                <line x1="16" x2="16" y1="2" y2="6" />

                <line x1="8" x2="8" y1="2" y2="6" />

                <line x1="3" x2="21" y1="10" y2="10" />
              </svg>

              <input
                v-model="form.start_date"
                type="date"
                class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-sm text-slate-700 focus:outline-none focus:border-successDark focus:ring-2 focus:ring-successDark/15 transition hover:border-slate-300 cursor-pointer"
              />
            </div>
          </div>

          <!-- =================================================
               Current Employment
          ================================================== -->
          <div
            class="bg-white border border-slate-200 rounded-xl p-3 sm:p-3.5 flex items-center justify-between gap-3 transition"
            :class="
              form.is_current
                ? 'ring-1 ring-successDark/25 border-successDark/30'
                : ''
            "
          >
            <div>
              <p class="text-xs font-bold text-slate-800">
                I currently work here
              </p>

              <p class="text-[11px] text-slate-500 mt-0.5">
                Turn this off if this employment has ended.
              </p>
            </div>

            <!-- Switch -->
            <button
              type="button"
              role="switch"
              :aria-checked="form.is_current"
              @click="form.is_current = !form.is_current"
              :class="[
                'relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none',

                form.is_current ? 'bg-successDark' : 'bg-slate-300',
              ]"
            >
              <span
                :class="[
                  'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-xs ring-0 transition duration-200 ease-in-out',

                  form.is_current ? 'translate-x-5' : 'translate-x-0',
                ]"
              ></span>
            </button>
          </div>

          <!-- =================================================
               End Date
          ================================================== -->
          <div v-if="!form.is_current" class="space-y-1.5">
            <label class="block text-xs font-bold text-slate-800">
              End Date
            </label>

            <div class="relative">
              <svg
                class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                viewBox="0 0 24 24"
              >
                <rect width="18" height="18" x="3" y="4" rx="2" />

                <line x1="16" x2="16" y1="2" y2="6" />

                <line x1="8" x2="8" y1="2" y2="6" />

                <line x1="3" x2="21" y1="10" y2="10" />
              </svg>

              <input
                v-model="form.end_date"
                type="date"
                :min="form.start_date || undefined"
                class="w-full bg-white border border-slate-200 rounded-xl pl-9 pr-3.5 py-2.5 text-xs sm:text-sm text-slate-700 focus:outline-none focus:border-successDark focus:ring-2 focus:ring-successDark/15 transition hover:border-slate-300 cursor-pointer"
              />
            </div>
          </div>
        </div>

        <!-- =================================================
             Action Buttons
        ================================================== -->
        <div
          class="pt-1 w-full flex flex-col sm:flex-row gap-3 justify-end items-stretch sm:items-center"
        >
          <!-- Cancel -->
          <button
            @click="handleCancel"
            type="button"
            :disabled="isSaving"
            class="text-nowrap px-6 flex items-center justify-center gap-2 bg-white hover:bg-slate-50 active:bg-slate-100 disabled:opacity-60 disabled:cursor-not-allowed border border-rose-300 text-rose-700 font-semibold text-xs sm:text-sm py-2.5 sm:py-3 rounded-xl transition-colors cursor-pointer"
          >
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

          <!-- Save -->
          <button
            @click="handleSave"
            type="button"
            :disabled="isSaving"
            class="text-nowrap px-6 flex items-center justify-center gap-2 bg-successDark hover:bg-[#005546] active:bg-[#004538] disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold text-xs sm:text-sm py-2.5 sm:py-3 rounded-xl transition-colors cursor-pointer shadow-xs"
          >
            <!-- Save Icon -->
            <svg
              v-if="!isSaving"
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

            <!-- Loading -->
            <svg
              v-else
              class="w-4 h-4 animate-spin"
              fill="none"
              viewBox="0 0 24 24"
            >
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
                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
              />
            </svg>

            {{ isSaving ? "Saving..." : "Save Status" }}
          </button>
        </div>
      </div>
    </div>
  </div>
  <div v-else class="w-full p-6 flex justify-center items-center">
    <LoadingIcon class="size-10 text-primary" />
  </div>
</template>
