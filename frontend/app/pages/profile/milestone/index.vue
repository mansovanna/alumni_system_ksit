<script setup lang="ts">
import { ref } from "vue";

definePageMeta({
  layout: "default",
});

const router = useRouter();

// Form State
const form = ref({
  milestoneType: "New Job",
  titleRole: "",
  company: "",
  startDate: "",
  endDate: "",
  isCurrentlyWorking: false,
  location: "",
  description: "",
});

const milestoneOptions = [
  "New Job",
  "Promotion",
  "Career Switch",
  "Certification",
  "Other",
];

const handleCancel = () => {
  router.back();
};

const handleSave = () => {
  console.log("Saving milestone:", form.value);
  alert("រក្សាទុកជោគជ័យ!");
};
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="w-full flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-semibold text-slate-900">
          Add Career Milestone
        </h1>
        <p class="text-slate-400 text-sm mt-0.5">
          Keep your network updated on your latest professional achievements.
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

    <!-- Full Screen Container -->
    <div
      class="w-full bg-white font-Inter flex flex-col justify-between rounded-2xl overflow-clip border border-slate-200 shadow-xs"
    >
      <!-- Form Container -->
      <form @submit.prevent="handleSave" class="w-full">
        <!-- Form Body Fields -->
        <div class="p-6 sm:p-8 space-y-5">
          <!-- Milestone Type -->
          <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-700"
              >Milestone Type</label
            >
            <div class="relative">
              <select
                v-model="form.milestoneType"
                class="w-full appearance-none bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-600/10 cursor-pointer"
              >
                <option
                  v-for="option in milestoneOptions"
                  :key="option"
                  :value="option"
                >
                  {{ option }}
                </option>
              </select>
              <svg
                class="w-4 h-4 absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="m19.5 8.25-7.5 7.5-7.5-7.5"
                />
              </svg>
            </div>
          </div>

          <!-- Title / Role -->
          <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-700"
              >Title / Role</label
            >
            <input
              v-model="form.titleRole"
              type="text"
              placeholder="e.g. Senior Software Engineer"
              class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-600/10"
            />
          </div>

          <!-- Company / Institution -->
          <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-700"
              >Company / Institution</label
            >
            <input
              v-model="form.company"
              type="text"
              placeholder="e.g. Tech Innovations Inc."
              class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-600/10"
            />
          </div>

          <!-- Start Date & End Date -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Start Date -->
            <div class="space-y-1.5">
              <label class="block text-xs font-semibold text-slate-700"
                >Start Date</label
              >
              <div class="relative">
                <input
                  v-model="form.startDate"
                  type="date"
                  class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-600/10"
                />
              </div>
            </div>

            <!-- End Date -->
            <div class="space-y-1.5">
              <label class="block text-xs font-semibold text-slate-700"
                >End Date</label
              >
              <div class="relative">
                <input
                  v-model="form.endDate"
                  type="date"
                  :disabled="form.isCurrentlyWorking"
                  class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-700 focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-600/10 disabled:bg-slate-50 disabled:text-slate-400 disabled:cursor-not-allowed"
                />
              </div>
            </div>
          </div>

          <!-- Checkbox: Currently work here -->
          <div class="flex items-center gap-2 pt-0.5">
            <input
              id="currentlyWork"
              v-model="form.isCurrentlyWorking"
              type="checkbox"
              class="w-4 h-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500 cursor-pointer"
            />
            <label
              for="currentlyWork"
              class="text-xs font-medium text-slate-600 cursor-pointer select-none"
            >
              I currently work here
            </label>
          </div>

          <!-- Location -->
          <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-700"
              >Location</label
            >
            <input
              v-model="form.location"
              type="text"
              placeholder="e.g. San Francisco, CA (or Remote)"
              class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-600/10"
            />
          </div>

          <!-- Description (Optional) -->
          <div class="space-y-1.5">
            <label class="block text-xs font-semibold text-slate-700">
              Description
              <span class="text-slate-400 font-normal">(Optional)</span>
            </label>
            <textarea
              v-model="form.description"
              rows="4"
              placeholder="Briefly describe your achievements or responsibilities..."
              class="w-full bg-white border border-slate-200 rounded-xl p-4 text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-600/10 resize-y"
            ></textarea>
          </div>
        </div>

        <!-- Action Footer Section -->
        <div
          class="p-6 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end gap-3"
        >
          <button
            type="button"
            @click="handleCancel"
            class="px-5 py-2.5 rounded-xl border border-rose-200 text-rose-500 hover:bg-rose-50 active:bg-rose-100 text-sm font-medium transition cursor-pointer"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white text-sm font-semibold shadow-xs transition cursor-pointer"
          >
            Save Milestone
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
