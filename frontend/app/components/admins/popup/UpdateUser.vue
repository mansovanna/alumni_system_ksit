<script setup lang="ts">
import { useAlumniStore } from "#imports";
import CloseIcon from "~/components/icons/CloseIcon.vue";
import SpannerIcon from "~/components/icons/SpannerIcon.vue";
import type { AlumniModels } from "~/types/alumni.model";

/* =========================================================
   Props & Emits
========================================================= */

const alumni = useAlumniStore();

const props = defineProps<{
  data?: AlumniModels;
}>();

const emit = defineEmits<{
  close: [];
  submitted: [data: any];
}>();

/* =========================================================
   Status Options
========================================================= */

const workStatus = [
  { status: "employed", title: "Employed" },
  { status: "unemployed", title: "Unemployed" },
  { status: "self_employed", title: "Self Employed" },
  { status: "studying", title: "Studying" },
  { status: "unknown", title: "Unknown" },
];

/* =========================================================
   Form State (Initial Data from Props)
========================================================= */

const formData = ref({
  status: props.data?.employment_status || "employed",
  notes: "",
});

const isLoading = ref(false);
const message = ref("");
const errors = ref<{
  status?: string;
  notes?: string;
}>({});

/* =========================================================
   Watch Data Property Change
========================================================= */

watch(
  () => props.data,
  (newData) => {
    if (newData?.employment_status) {
      formData.value.status = newData.employment_status;
    }
  },
  { immediate: true },
);

/* =========================================================
   Alumni Name
========================================================= */

const alumniName = computed(() => {
  return (
    props.data?.user?.name_english || props.data?.user?.name_khmer || "Alumni"
  );
});

/* =========================================================
   Format Date Helper
========================================================= */

const formatDate = (dateStr?: string | null) => {
  if (!dateStr) return "";
  const d = new Date(dateStr);
  return Number.isNaN(d.getTime())
    ? dateStr
    : d.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
      });
};

/* =========================================================
   Validate & Submit
========================================================= */

const validate = () => {
  errors.value = {};
  message.value = "";
  let hasError = false;

  if (!formData.value.status) {
    errors.value.status = "Status is required.";
    hasError = true;
  }

  return hasError;
};

const submit = async () => {
  if (validate()) return;

  const data = new FormData();

  data.append("employment_status", formData.value.status);
  data.append("bio", formData.value.notes);

  isLoading.value = true;

  try {
    const res = await alumni.updateStatusAlumni(Number(props.data?.id), data);

    if (res.data && alumni.data?.data) {
      alumni.data.data = alumni.data.data.map((e) =>
        e.id === props.data?.id ? { ...e, ...res.data } : e,
      );

      closeModal();
    }
  } catch (e: any) {
    // console.log(e);
    message.value = e.response;
  } finally {
    isLoading.value = false;
  }
  // Simulate API Save
};

const closeModal = () => {
  emit("close");
};
</script>

<template>
  <!-- Overlay -->
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
    @click="closeModal"
  >
    <!-- Modal Container -->
    <div
      class="w-full max-w-md overflow-hidden rounded-lg bg-white shadow-xl border border-gray-200"
      @click.stop
    >
      <!-- Header -->
      <div class="border-b border-gray-100 px-6 py-4">
        <div class="flex items-start justify-between">
          <div>
            <h2 class="text-base font-bold text-gray-900">Update Status</h2>
            <p class="mt-0.5 text-xs text-gray-500">
              For <span class="font-bold text-gray-900">{{ alumniName }}</span>
            </p>
          </div>

          <button
            type="button"
            class="text-gray-400 hover:text-gray-600 transition p-1"
            :disabled="isLoading"
            @click="closeModal"
          >
            <CloseIcon class="w-4 h-4" />
          </button>
        </div>
      </div>

      <!-- Form Body -->
      <form @submit.prevent="submit" class="px-6 py-5 space-y-4">
        <!-- Error Message Banner -->
        <div
          v-if="message"
          class="rounded-md border border-red-200 bg-red-50 p-2.5 text-xs text-red-600"
        >
          {{ message }}
        </div>

        <!-- Current Status Selection -->
        <div>
          <label class="mb-1.5 block text-xs font-semibold text-gray-700">
            Current Status
          </label>

          <select
            v-model="formData.status"
            :disabled="isLoading"
            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-800 outline-none transition focus:border-black focus:ring-1 focus:ring-black disabled:bg-gray-50"
            :class="{ 'border-red-500': errors.status }"
          >
            <option
              v-for="item in workStatus"
              :key="item.status"
              :value="item.status"
            >
              {{ item.title }}
            </option>
          </select>

          <p v-if="errors.status" class="mt-1 text-xs text-red-500">
            {{ errors.status }}
          </p>
        </div>

        <!-- Current Employment Info ( dynamic data ) -->
        <div
          v-if="props.data?.employment"
          class="rounded-md border border-gray-100 bg-gray-50 p-3 space-y-1 text-xs text-gray-600"
        >
          <div class="font-semibold text-gray-800">
            {{ props.data.employment.job_title }}
          </div>
          <div class="flex items-center justify-between text-[11px]">
            <span>Location: {{ props.data.employment.location }}</span>
            <span
              class="capitalize bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded text-[10px]"
            >
              {{ props.data.employment.employment_type?.replace("_", " ") }}
            </span>
          </div>
        </div>

        <!-- Notes / Comments -->
        <div>
          <label class="mb-1.5 block text-xs font-semibold text-gray-700">
            Notes / Comments
          </label>

          <textarea
            v-model="formData.notes"
            :disabled="isLoading"
            rows="3"
            placeholder="Add any relevant details about this status change..."
            class="w-full resize-none rounded-md border border-gray-200 px-3 py-2 text-xs text-gray-700 outline-none placeholder:text-gray-400 focus:border-black focus:ring-1 focus:ring-black disabled:bg-gray-50"
            :class="{ 'border-red-500': errors.notes }"
          />

          <p v-if="errors.notes" class="mt-1 text-xs text-red-500">
            {{ errors.notes }}
          </p>
        </div>

        <!-- Recent History Section -->
        <div>
          <div
            class="mb-1.5 text-[10px] font-bold uppercase tracking-wider text-gray-400"
          >
            RECENT HISTORY
          </div>

          <!-- Active History Display -->
          <div
            v-if="props.data?.created_at"
            class="flex items-center gap-2.5 rounded-md border border-gray-200 bg-gray-50/50 p-3"
          >
            <div
              class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-3.5 w-3.5"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M12 8v4l2.5 2.5"
                />
                <circle cx="12" cy="12" r="9" />
              </svg>
            </div>

            <div class="min-w-0 flex-1 text-xs">
              <p class="text-gray-800">
                Created with status:
                <span class="font-bold text-gray-900 capitalize">
                  {{ props.data.employment_status }}
                </span>
              </p>
              <p class="mt-0.5 text-[10px] text-gray-400 font-mono">
                {{ formatDate(props.data.created_at) }}
              </p>
            </div>
          </div>

          <div
            v-else
            class="rounded-md border border-gray-200 bg-gray-50/50 p-3 text-center text-xs text-gray-400"
          >
            No recent status history.
          </div>
        </div>
      </form>

      <!-- Footer Actions -->
      <div
        class="flex items-center justify-end gap-3 border-t border-gray-100 bg-white px-6 py-3.5"
      >
        <button
          type="button"
          :disabled="isLoading"
          class="px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:text-slate-900 disabled:opacity-50"
          @click="closeModal"
        >
          Cancel
        </button>

        <button
          type="button"
          :disabled="isLoading"
          class="flex items-center gap-1.5 rounded-md bg-black px-3.5 py-1.5 text-xs font-medium text-white transition hover:bg-slate-800 disabled:bg-gray-400"
          @click="submit"
        >
          <template v-if="!isLoading">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-3.5 w-3.5"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 002-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"
              />
            </svg>
            Save Changes
          </template>

          <template v-else>
            <SpannerIcon class="w-3.5 h-3.5 animate-spin" />
            Saving...
          </template>
        </button>
      </div>
    </div>
  </div>
</template>
