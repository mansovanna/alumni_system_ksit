<script setup lang="ts">
import type { NotificationItem } from "~/types/notification"; // adjust path to your model file

definePageMeta({
  layout: "admin",
});

const route = useRoute();
const router = useRouter();

const messageStore = useMessageStore();
const majorStore = useMajorStore();

const messageId = Number(route.params.id);

/* ------------------- Options ------------------- */
const notificationTypes = [
  "General",
  "Career Fair",
  "Job Posting",
  "Event",
  "Reminder",
];

const years = ["2024", "2023", "2022"];

const employmentStatuses = [
  "self_employed",
  "employed",
  "unemployed",
  "studying",
  "unknown",
];

/* ------------------- Form State ------------------- */
const form = ref({
  title: "",
  message: "",
  type: "General",
  filters: {
    graduationYear: "",
    major: "",
    employmentStatus: "",
  },
});

const isLoading = ref(true);
const loadError = ref("");
const record = ref<NotificationItem | null>(null);

/* ------------------- Parse filters JSON ------------------- */
const parseFilters = (raw: string | null) => {
  if (!raw) return null;

  try {
    return JSON.parse(raw) as {
      graduationYear?: string;
      major?: string;
      employmentStatus?: string;
    };
  } catch {
    return null;
  }
};

/* ------------------- Load Message + Majors ------------------- */
onMounted(async () => {
  isLoading.value = true;
  loadError.value = "";

  try {
    const [msgRes] = await Promise.all([
      messageStore.getMessageById(messageId),
      !majorStore.data?.data?.length
        ? majorStore.getMajorAll?.()
        : Promise.resolve(),
    ]);

    const item: NotificationItem = msgRes?.data?.data ?? msgRes?.data;

    if (!item) {
      loadError.value = "Message not found.";
      return;
    }

    record.value = item;

    const parsed = parseFilters(item.filters);

    form.value = {
      title: item.title || "",
      message: item.message || "",
      type: item.type || "General",
      filters: {
        graduationYear: parsed?.graduationYear || "",
        major: parsed?.major || "",
        employmentStatus: parsed?.employmentStatus || "",
      },
    };
  } catch (err: any) {
    loadError.value =
      err?.data?.message || "Failed to load message. Please try again.";
  } finally {
    isLoading.value = false;
  }
});

/* ------------------- Validation ------------------- */
const TITLE_MAX = 150;
const MESSAGE_MAX = 2000;

const submitted = ref(false);
const isSaving = ref(false);
const serverError = ref("");

const errors = computed(() => {
  const e: Record<string, string> = {};

  const title = form.value.title.trim();
  const message = form.value.message.trim();

  if (!title) {
    e.title = "Message subject is required.";
  } else if (title.length > TITLE_MAX) {
    e.title = `Subject must be ${TITLE_MAX} characters or fewer.`;
  }

  if (!message) {
    e.message = "Message body is required.";
  } else if (message.length > MESSAGE_MAX) {
    e.message = `Message must be ${MESSAGE_MAX} characters or fewer.`;
  }

  if (!form.value.type) {
    e.type = "Please select a notification type.";
  } else if (!notificationTypes.includes(form.value.type)) {
    e.type = "Invalid notification type.";
  }

  if (
    !form.value.filters.graduationYear &&
    !form.value.filters.major &&
    !form.value.filters.employmentStatus
  ) {
    e.filters = "Select at least one audience filter.";
  }

  return e;
});

const isValid = computed(() => Object.keys(errors.value).length === 0);

const touched = ref<Record<string, boolean>>({});
const showError = (field: string) =>
  (touched.value[field] || submitted.value) && !!errors.value[field];

const touch = (field: string) => {
  touched.value[field] = true;
};

/* ------------------- Audience Filter Chips ------------------- */
const activeFilterChips = computed(() => {
  const chips: { key: string; label: string }[] = [];

  if (form.value.filters.graduationYear) {
    chips.push({
      key: "graduationYear",
      label: `Grad Year: ${form.value.filters.graduationYear}`,
    });
  }

  if (form.value.filters.major) {
    chips.push({
      key: "major",
      label: `Major: ${form.value.filters.major}`,
    });
  }

  if (form.value.filters.employmentStatus) {
    chips.push({
      key: "employmentStatus",
      label: `Status: ${form.value.filters.employmentStatus}`,
    });
  }

  return chips;
});

const removeFilter = (key: "graduationYear" | "major" | "employmentStatus") => {
  form.value.filters[key] = "";
};

const clearAllFilters = () => {
  form.value.filters.graduationYear = "";
  form.value.filters.major = "";
  form.value.filters.employmentStatus = "";
};

/* ------------------- Date Formatter ------------------- */
const formatDate = (dateString?: string) => {
  if (!dateString) return "—";

  const date = new Date(dateString);

  if (isNaN(date.getTime())) return "—";

  return (
    date.toLocaleDateString("en-US", {
      month: "short",
      day: "2-digit",
      year: "numeric",
    }) +
    " · " +
    date.toLocaleTimeString("en-US", {
      hour: "2-digit",
      minute: "2-digit",
      hour12: true,
    })
  );
};

/* ------------------- Save / Update ------------------- */
const handleSave = async () => {
  submitted.value = true;
  serverError.value = "";

  if (!isValid.value) {
    return;
  }

  const data = new FormData();

  data.append("title", form.value.title.trim());
  data.append("message", form.value.message.trim());
  data.append("type", form.value.type);
  data.append("filters", JSON.stringify(form.value.filters));

  isSaving.value = true;

  try {
    const res = await messageStore.updateMessage(messageId, data);

    const updated = res?.data?.data ?? res?.data;

    if (updated && messageStore.data?.data.data) {
      const idx = messageStore.data.data.data.findIndex(
        (m) => m.id === messageId,
      );

      if (idx !== -1) {
        messageStore.data.data.data[idx] = updated;
      }
    }

    router.push({ name: "admins-messages" });
  } catch (err: any) {
    serverError.value =
      err?.data?.message || "Failed to update message. Please try again.";
  } finally {
    isSaving.value = false;
  }
};

const handleDiscard = () => {
  router.push({ name: "admins-messages" });
};
</script>

<template>
  <div
    class="min-h-screen bg-[#F8FAFC] text-slate-800 p-6 font-Inter rounded-xl overflow-clip"
  >
    <!-- =========================================================
         TOP BAR
    ========================================================== -->
    <!-- =========================================================
     TOP BAR
========================================================== -->
    <div
      class="sticky top-0 z-10 -mx-6 -mt-6 mb-6 px-6 py-4 bg-[#F8FAFC]/95 backdrop-blur-sm border-b border-slate-200/70"
    >
      <div
        class="flex flex-col sm:flex-row sm:items-center justify-between gap-4"
      >
        <!-- Title + Back -->
        <div class="flex items-center gap-3">
          <button
            @click="router.back()"
            type="button"
            title="Back to Messages"
            class="shrink-0 inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 bg-white text-slate-500 hover:text-slate-900 hover:bg-slate-50 hover:border-slate-300 transition cursor-pointer shadow-2xs"
          >
            <svg
              class="w-4 h-4"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <path d="M19 12H5M12 19l-7-7 7-7" />
            </svg>
          </button>

          <div>
            <h1
              class="text-xl font-bold text-slate-900 tracking-tight leading-tight"
            >
              Edit Message
            </h1>

            <p class="text-xs text-slate-500 mt-0.5">
              Message #{{ messageId }}
            </p>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2.5 shrink-0">
          <button
            @click="handleDiscard"
            type="button"
            :disabled="isSaving"
            class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-transparent"
          >
            Discard Changes
          </button>

          <button
            @click="handleSave"
            type="button"
            :disabled="isSaving || isLoading || (submitted && !isValid)"
            class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-slate-950 hover:bg-slate-800 rounded-lg transition shadow-xs cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <svg
              v-if="!isSaving"
              class="w-3.5 h-3.5"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <path
                d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"
              />
              <polyline points="17 21 17 13 7 13 7 21" />
              <polyline points="7 3 7 8 15 8" />
            </svg>

            <svg
              v-else
              class="w-3.5 h-3.5 animate-spin"
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
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
              />
            </svg>

            {{ isSaving ? "Updating..." : "Update Message" }}
          </button>
        </div>
      </div>
    </div>

    <!-- Loading -->
    <div
      v-if="isLoading"
      class="bg-white rounded-xl border border-slate-200/80 shadow-xs p-8 text-center text-sm text-slate-400 font-medium"
    >
      Loading message...
    </div>

    <!-- Load error -->
    <div
      v-else-if="loadError"
      class="bg-white rounded-xl border border-red-200 shadow-xs p-8 text-center text-sm text-red-600 font-medium"
    >
      {{ loadError }}
    </div>

    <!-- =========================================================
         MAIN CONTENT
    ========================================================== -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
      <!-- =======================================================
           LEFT COLUMN
      ======================================================== -->
      <div class="lg:col-span-8 space-y-6">
        <!-- =====================================================
             MESSAGE CONTENT
        ====================================================== -->
        <div
          class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden"
        >
          <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-sm font-bold text-slate-800">Message Content</h2>
          </div>

          <div class="p-5 space-y-5">
            <!-- Subject -->
            <div>
              <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-bold text-slate-700">
                  Subject Line <span class="text-red-500">*</span>
                </label>

                <span class="text-[11px] text-slate-400 font-medium">
                  {{ form.title.length }}/{{ TITLE_MAX }}
                </span>
              </div>

              <input
                v-model="form.title"
                type="text"
                :maxlength="TITLE_MAX"
                @blur="touch('title')"
                :class="[
                  'w-full px-3.5 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 font-medium text-slate-800 placeholder:text-slate-400 transition',
                  showError('title')
                    ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500'
                    : 'border-slate-200 focus:ring-slate-900/10 focus:border-slate-800',
                ]"
              />

              <p
                v-if="showError('title')"
                class="mt-1 text-[11px] text-red-600"
              >
                {{ errors.title }}
              </p>
            </div>

            <!-- Notification Type -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Notification Type <span class="text-red-500">*</span>
              </label>

              <select
                v-model="form.type"
                @blur="touch('type')"
                :class="[
                  'w-full px-3.5 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 bg-white font-medium text-slate-800 transition',
                  showError('type')
                    ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500'
                    : 'border-slate-200 focus:ring-slate-900/10 focus:border-slate-800',
                ]"
              >
                <option v-for="t in notificationTypes" :key="t" :value="t">
                  {{ t }}
                </option>
              </select>

              <p v-if="showError('type')" class="mt-1 text-[11px] text-red-600">
                {{ errors.type }}
              </p>
            </div>

            <!-- Message Body -->
            <div>
              <label class="block text-xs font-bold text-slate-700 mb-1.5">
                Message Body <span class="text-red-500">*</span>
              </label>

              <div
                :class="[
                  'border rounded-lg overflow-hidden focus-within:ring-2 transition',
                  showError('message')
                    ? 'border-red-300 focus-within:ring-red-500/20 focus-within:border-red-500'
                    : 'border-slate-200 focus-within:ring-slate-900/10 focus-within:border-slate-800',
                ]"
              >
                <!-- Toolbar (visual only — no rich text persisted) -->
                <div
                  class="flex items-center gap-3 px-3.5 py-2 bg-slate-50 border-b border-slate-200 text-slate-600 text-xs font-semibold"
                >
                  <button
                    type="button"
                    class="hover:text-slate-900 cursor-pointer"
                  >
                    B
                  </button>

                  <button
                    type="button"
                    class="italic hover:text-slate-900 cursor-pointer"
                  >
                    I
                  </button>

                  <button
                    type="button"
                    class="underline hover:text-slate-900 cursor-pointer"
                  >
                    U
                  </button>
                </div>

                <!-- Textarea -->
                <textarea
                  v-model="form.message"
                  rows="12"
                  :maxlength="MESSAGE_MAX"
                  @blur="touch('message')"
                  class="w-full p-4 text-xs sm:text-sm text-slate-800 leading-relaxed focus:outline-none resize-y font-Inter"
                ></textarea>
              </div>

              <div class="flex items-center justify-between mt-1">
                <p v-if="showError('message')" class="text-[11px] text-red-600">
                  {{ errors.message }}
                </p>

                <span class="text-[11px] text-slate-400 font-medium ml-auto">
                  {{ form.message.length }}/{{ MESSAGE_MAX }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- =====================================================
             AUDIENCE TARGETING (backend: filters JSON column)
        ====================================================== -->
        <div
          class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden"
        >
          <div
            class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between"
          >
            <h2 class="text-sm font-bold text-slate-800">Audience Targeting</h2>

            <button
              @click="clearAllFilters"
              type="button"
              class="text-xs font-semibold text-slate-600 hover:text-slate-900 cursor-pointer transition"
            >
              Clear Filters
            </button>
          </div>

          <div class="p-5 space-y-4">
            <div>
              <div class="flex items-center justify-between mb-2">
                <span
                  class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider"
                >
                  Active Segments
                </span>

                <p v-if="showError('filters')" class="text-[11px] text-red-600">
                  {{ errors.filters }}
                </p>
              </div>

              <!-- Filter Chips -->
              <div class="flex flex-wrap items-center gap-2 mb-4">
                <template v-if="activeFilterChips.length">
                  <span
                    v-for="chip in activeFilterChips"
                    :key="chip.key"
                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-md border border-blue-100"
                  >
                    {{ chip.label }}

                    <button
                      @click="removeFilter(chip.key as any)"
                      type="button"
                      class="hover:text-blue-900 cursor-pointer"
                    >
                      <svg
                        class="w-3 h-3"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                      >
                        <path d="M18 6 6 18M6 6l12 12" />
                      </svg>
                    </button>
                  </span>
                </template>

                <span v-else class="text-xs text-slate-400">
                  No filters selected — will target all alumni.
                </span>
              </div>

              <!-- Graduation Year -->
              <div class="mb-3">
                <span
                  class="block text-[11px] font-medium text-slate-500 mb-1.5"
                >
                  Graduation Year
                </span>

                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="year in years"
                    :key="year"
                    type="button"
                    @click="form.filters.graduationYear = year"
                    :class="[
                      'px-3 py-1 text-xs rounded-full border transition cursor-pointer',
                      form.filters.graduationYear === year
                        ? 'bg-blue-600 border-blue-600 text-white font-medium'
                        : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50',
                    ]"
                  >
                    {{ year }}
                  </button>
                </div>
              </div>

              <!-- Major -->
              <div class="mb-3">
                <span
                  class="block text-[11px] font-medium text-slate-500 mb-1.5"
                >
                  Major
                </span>

                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="item in majorStore.data?.data"
                    :key="Number(item.id)"
                    type="button"
                    @click="form.filters.major = String(item.name)"
                    :class="[
                      'px-3 py-1 text-xs rounded-full border transition cursor-pointer',
                      form.filters.major === item.name
                        ? 'bg-blue-600 border-blue-600 text-white font-medium'
                        : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50',
                    ]"
                  >
                    {{ item.name ?? "N/A" }}
                  </button>
                </div>
              </div>

              <!-- Employment Status -->
              <div>
                <span
                  class="block text-[11px] font-medium text-slate-500 mb-1.5"
                >
                  Employment Status
                </span>

                <div class="flex flex-wrap gap-2">
                  <button
                    v-for="st in employmentStatuses"
                    :key="st"
                    type="button"
                    @click="form.filters.employmentStatus = st"
                    :class="[
                      'px-3 py-1 text-xs capitalize rounded-full border transition cursor-pointer',
                      form.filters.employmentStatus === st
                        ? 'bg-blue-600 border-blue-600 text-white font-medium'
                        : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50',
                    ]"
                  >
                    {{ st }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Reach Estimate -->
            <div
              class="p-4 bg-slate-50/80 border border-slate-200/80 rounded-xl flex items-center gap-3"
            >
              <div
                class="w-10 h-10 rounded-lg bg-slate-900 flex items-center justify-center text-white shrink-0"
              >
                <svg
                  class="w-5 h-5"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                >
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                  <circle cx="9" cy="7" r="4" />
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                  <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
              </div>

              <div>
                <div class="text-sm font-bold text-slate-900">
                  {{ messageStore.data?.totalAlumni ?? 0 }}

                  <span class="text-xs font-normal text-slate-500">
                    Total Alumni (aggregate estimate)
                  </span>
                </div>

                <p class="text-[11px] text-slate-400 mt-0.5">
                  Per-filter recipient counts aren't available yet from the API.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- =======================================================
           RIGHT COLUMN
      ======================================================== -->
      <div class="lg:col-span-4 space-y-6">
        <div
          class="bg-white rounded-xl border border-slate-200/80 shadow-xs p-5 space-y-5"
        >
          <div class="flex items-center gap-2 text-slate-900 font-bold text-sm">
            <svg
              class="w-4 h-4 text-slate-500"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <circle cx="12" cy="12" r="10" />
              <line x1="12" y1="16" x2="12" y2="12" />
              <line x1="12" y1="8" x2="12.01" y2="8" />
            </svg>

            <span>Message Details</span>
          </div>

          <!-- Type -->
          <div>
            <span class="block text-[11px] font-semibold text-slate-500 mb-1.5">
              Notification Type
            </span>

            <span
              class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-semibold"
            >
              {{ form.type }}
            </span>
          </div>

          <!-- Created On (real data) -->
          <div>
            <span class="block text-[11px] font-semibold text-slate-500">
              Created On
            </span>

            <p class="text-xs font-bold text-slate-800 mt-0.5">
              {{ formatDate(record?.created_at) }}
            </p>
          </div>

          <!-- Message ID -->
          <div>
            <span class="block text-[11px] font-semibold text-slate-500">
              Message ID
            </span>

            <p class="text-xs font-bold text-slate-800 mt-0.5">
              #{{ record?.id }}
            </p>
          </div>
        </div>

        <!-- Server-side error -->
        <p v-if="serverError" class="text-xs text-red-600 px-1">
          {{ serverError }}
        </p>
      </div>
    </div>
  </div>
</template>
