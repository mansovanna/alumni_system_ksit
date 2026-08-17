<script setup lang="ts">
import type { MessageModel } from "~/types/message.response.model";

definePageMeta({
  layout: "admin",
});

const props = defineProps<{
  data?: MessageModel | null;
}>();

const emit = defineEmits(["close", "submit"]);

/* ------------------- Form State ------------------- */
const form = ref({
  subject:
    props.data?.title ||
    "Annual Alumni Gala 2024: Early Bird Registration Closing Soon!",

  body:
    props.data?.body ||
    `Dear [First Name],

We are incredibly excited to invite you back to campus for the Annual Alumni Gala 2024! This year's event promises to be our most spectacular gathering yet, celebrating the remarkable achievements of our community over the past decade.

Early bird registration is closing this Friday. Don't miss your chance to secure tickets at the discounted rate.

Join us on October 15th for an evening of networking, celebration, and nostalgia.

Warmly,
The Alumni Relations Team
University Administration`,

  deliveryOption: "schedule",
  scheduleDate: "2024-10-15",
  scheduleTime: "09:00",
});

/* ------------------- Active Audience Filters ------------------- */
const activeFilters = ref([
  {
    id: 1,
    label: "Grad Year: 2010 - 2023",
  },
  {
    id: 2,
    label: "Major: Engineering, Business Admin",
  },
  {
    id: 3,
    label: "Status: Employed",
  },
]);

/* ------------------- Remove Filter ------------------- */
const removeFilter = (id: number) => {
  activeFilters.value = activeFilters.value.filter(
    (f) => f.id !== id,
  );
};

/* ------------------- Clear Filters ------------------- */
const clearAllFilters = () => {
  activeFilters.value = [];
};

/* ------------------- Save / Update ------------------- */
const handleSave = () => {
  emit("submit", form.value);
};
</script>

<template>
  <div
    class="min-h-screen bg-[#F8FAFC] text-slate-800 p-6 font-Inter rounded-xl overflow-clip"
  >
    <!-- =========================================================
         TOP BAR
    ========================================================== -->
    <div
      class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6"
    >
      <!-- Page Title -->
      <div>
        <button
          @click="$router.back()"
          type="button"
          class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-800 font-medium transition cursor-pointer mb-1"
        >
          <svg
            class="w-3.5 h-3.5"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
          >
            <path d="M19 12H5M12 19l-7-7 7-7" />
          </svg>

          Back to Messages
        </button>

        <h1
          class="text-2xl font-bold text-slate-900 tracking-tight"
        >
          Edit Message
        </h1>
      </div>

      <!-- Actions -->
      <div class="flex items-center gap-3">
        <!-- Discard -->
        <button
          @click="$router.back()"
          type="button"
          class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-lg transition cursor-pointer"
        >
          Discard Changes
        </button>

        <!-- Update -->
        <button
          @click="handleSave"
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-white bg-slate-950 hover:bg-slate-800 rounded-lg transition shadow-xs cursor-pointer"
        >
          <svg
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

          Update Message
        </button>
      </div>
    </div>

    <!-- =========================================================
         MAIN CONTENT
    ========================================================== -->
    <div
      class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start"
    >
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
          <!-- Header -->
          <div
            class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50"
          >
            <h2
              class="text-sm font-bold text-slate-800"
            >
              Message Content
            </h2>
          </div>

          <!-- Content -->
          <div class="p-5 space-y-5">
            <!-- Subject -->
            <div>
              <div
                class="flex items-center justify-between mb-1.5"
              >
                <label
                  class="block text-xs font-bold text-slate-700"
                >
                  Subject Line
                </label>

                <span
                  class="text-[11px] text-slate-400 font-medium"
                >
                  {{ form.subject.length }}/100
                </span>
              </div>

              <input
                v-model="form.subject"
                type="text"
                maxlength="100"
                class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-800 font-medium text-slate-800 placeholder:text-slate-400 transition"
              />
            </div>

            <!-- Email Body -->
            <div>
              <label
                class="block text-xs font-bold text-slate-700 mb-1.5"
              >
                Email Body
              </label>

              <div
                class="border border-slate-200 rounded-lg overflow-hidden focus-within:border-slate-800 focus-within:ring-2 focus-within:ring-slate-900/10 transition"
              >
                <!-- Toolbar -->
                <div
                  class="flex items-center gap-4 px-3.5 py-2 bg-slate-50 border-b border-slate-200 text-slate-600 text-xs"
                >
                  <!-- Text Style -->
                  <div
                    class="flex items-center gap-1 cursor-pointer hover:text-slate-900 font-medium"
                  >
                    <span>Normal Text</span>

                    <svg
                      class="w-3 h-3"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      viewBox="0 0 24 24"
                    >
                      <path d="m6 9 6 6 6-6" />
                    </svg>
                  </div>

                  <div
                    class="h-3 w-px bg-slate-200"
                  ></div>

                  <!-- Text Formatting -->
                  <div
                    class="flex items-center gap-3"
                  >
                    <button
                      type="button"
                      class="font-bold hover:text-slate-900 cursor-pointer"
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

                  <div
                    class="h-3 w-px bg-slate-200"
                  ></div>

                  <!-- Alignment -->
                  <div
                    class="flex items-center gap-3"
                  >
                    <!-- Align Left -->
                    <button
                      type="button"
                      class="hover:text-slate-900 cursor-pointer"
                    >
                      <svg
                        class="w-3.5 h-3.5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                      >
                        <line
                          x1="8"
                          y1="6"
                          x2="21"
                          y2="6"
                        />

                        <line
                          x1="8"
                          y1="12"
                          x2="21"
                          y2="12"
                        />

                        <line
                          x1="8"
                          y1="18"
                          x2="21"
                          y2="18"
                        />

                        <line
                          x1="3"
                          y1="6"
                          x2="3.01"
                          y2="6"
                        />

                        <line
                          x1="3"
                          y1="12"
                          x2="3.01"
                          y2="12"
                        />

                        <line
                          x1="3"
                          y1="18"
                          x2="3.01"
                          y2="18"
                        />
                      </svg>
                    </button>

                    <!-- Ordered List -->
                    <button
                      type="button"
                      class="hover:text-slate-900 cursor-pointer"
                    >
                      <svg
                        class="w-3.5 h-3.5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                      >
                        <line
                          x1="10"
                          y1="6"
                          x2="21"
                          y2="6"
                        />

                        <line
                          x1="10"
                          y1="12"
                          x2="21"
                          y2="12"
                        />

                        <line
                          x1="10"
                          y1="18"
                          x2="21"
                          y2="18"
                        />

                        <path d="M4 6h1v4" />
                        <path d="M4 10h2" />
                      </svg>
                    </button>
                  </div>

                  <div
                    class="h-3 w-px bg-slate-200"
                  ></div>

                  <!-- Link / Image -->
                  <div
                    class="flex items-center gap-3"
                  >
                    <!-- Link -->
                    <button
                      type="button"
                      class="hover:text-slate-900 cursor-pointer"
                    >
                      <svg
                        class="w-3.5 h-3.5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                      >
                        <path
                          d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"
                        />

                        <path
                          d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"
                        />
                      </svg>
                    </button>

                    <!-- Image -->
                    <button
                      type="button"
                      class="hover:text-slate-900 cursor-pointer"
                    >
                      <svg
                        class="w-3.5 h-3.5"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        viewBox="0 0 24 24"
                      >
                        <rect
                          x="3"
                          y="3"
                          width="18"
                          height="18"
                          rx="2"
                          ry="2"
                        />

                        <circle
                          cx="8.5"
                          cy="8.5"
                          r="1.5"
                        />

                        <polyline
                          points="21 15 16 10 5 21"
                        />
                      </svg>
                    </button>
                  </div>
                </div>

                <!-- Textarea -->
                <textarea
                  v-model="form.body"
                  rows="12"
                  class="w-full p-4 text-xs sm:text-sm text-slate-800 leading-relaxed focus:outline-none resize-y font-Inter"
                ></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- =====================================================
             AUDIENCE TARGETING
        ====================================================== -->
        <div
          class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden"
        >
          <!-- Header -->
          <div
            class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between"
          >
            <h2
              class="text-sm font-bold text-slate-800"
            >
              Audience Targeting
            </h2>

            <button
              @click="clearAllFilters"
              type="button"
              class="text-xs font-semibold text-slate-600 hover:text-slate-900 cursor-pointer transition"
            >
              Clear Filters
            </button>
          </div>

          <!-- Content -->
          <div class="p-5 space-y-4">
            <div>
              <span
                class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2"
              >
                Active Segments
              </span>

              <!-- Filter Badges -->
              <div
                class="flex flex-wrap items-center gap-2"
              >
                <span
                  v-for="filter in activeFilters"
                  :key="filter.id"
                  class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-700 text-xs font-medium rounded-md border border-blue-100"
                >
                  {{ filter.label }}

                  <button
                    @click="removeFilter(filter.id)"
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
                      <path
                        d="M18 6 6 18M6 6l12 12"
                      />
                    </svg>
                  </button>
                </span>

                <!-- Add Filter -->
                <button
                  type="button"
                  class="inline-flex items-center gap-1 px-3 py-1 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-medium rounded-md border border-slate-200 transition cursor-pointer"
                >
                  <span
                    class="text-base leading-none"
                  >
                    +
                  </span>

                  Add Filter
                </button>
              </div>
            </div>

            <!-- Recipients Match Banner -->
            <div
              class="p-4 bg-slate-50/80 border border-slate-200/80 rounded-xl flex items-center justify-between gap-4"
            >
              <div
                class="flex items-center gap-3"
              >
                <!-- Icon -->
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
                    <path
                      d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"
                    />

                    <circle
                      cx="9"
                      cy="7"
                      r="4"
                    />

                    <path
                      d="M23 21v-2a4 4 0 0 0-3-3.87"
                    />

                    <path
                      d="M16 3.13a4 4 0 0 1 0 7.75"
                    />
                  </svg>
                </div>

                <div>
                  <div
                    class="text-sm font-bold text-slate-900"
                  >
                    14,250

                    <span
                      class="text-xs font-normal text-slate-500"
                    >
                      Recipients Match Filters
                    </span>
                  </div>

                  <p
                    class="text-[11px] text-blue-600 font-medium mt-0.5 flex items-center gap-1"
                  >
                    <span
                      class="w-1.5 h-1.5 rounded-full bg-blue-600 inline-block"
                    ></span>

                    Live estimate updated moments ago
                  </p>
                </div>
              </div>

              <!-- Preview -->
              <button
                type="button"
                class="inline-flex items-center gap-1 text-xs font-semibold text-slate-700 hover:text-slate-900 transition cursor-pointer"
              >
                Preview List

                <svg
                  class="w-3.5 h-3.5"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                >
                  <path
                    d="M7 17L17 7M17 7H7M17 7V17"
                  />
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- =======================================================
           RIGHT COLUMN
      ======================================================== -->
      <div class="lg:col-span-4 space-y-6">
        <!-- =====================================================
             MESSAGE DETAILS
        ====================================================== -->
        <div
          class="bg-white rounded-xl border border-slate-200/80 shadow-xs p-5 space-y-5"
        >
          <!-- Header -->
          <div
            class="flex items-center gap-2 text-slate-900 font-bold text-sm"
          >
            <svg
              class="w-4 h-4 text-slate-500"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <circle
                cx="12"
                cy="12"
                r="10"
              />

              <line
                x1="12"
                y1="16"
                x2="12"
                y2="12"
              />

              <line
                x1="12"
                y1="8"
                x2="12.01"
                y2="8"
              />
            </svg>

            <span>Message Details</span>
          </div>

          <!-- Current Status -->
          <div>
            <span
              class="block text-[11px] font-semibold text-slate-500 mb-1.5"
            >
              Current Status
            </span>

            <span
              class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-semibold"
            >
              <span
                class="w-1.5 h-1.5 rounded-full bg-slate-500"
              ></span>

              Draft
            </span>
          </div>

          <!-- Last Modified -->
          <div>
            <span
              class="block text-[11px] font-semibold text-slate-500"
            >
              Last Modified
            </span>

            <p
              class="text-xs font-bold text-slate-800 mt-0.5"
            >
              Oct 12, 2024 · 10:45 AM
            </p>

            <div
              class="flex items-center gap-1.5 mt-1"
            >
              <div
                class="w-4 h-4 rounded-full bg-slate-300 text-[9px] font-bold text-slate-700 flex items-center justify-center"
              >
                SJ
              </div>

              <span
                class="text-xs text-slate-500"
              >
                by Sarah Jenkins
              </span>
            </div>
          </div>

          <!-- Created On -->
          <div>
            <span
              class="block text-[11px] font-semibold text-slate-500"
            >
              Created On
            </span>

            <p
              class="text-xs font-bold text-slate-800 mt-0.5"
            >
              Oct 10, 2024 · 09:00 AM
            </p>
          </div>

          <hr
            class="border-slate-100"
          />

          <!-- Testing & Preview -->
          <div class="space-y-2.5">
            <span
              class="block text-[11px] font-semibold text-slate-500"
            >
              Testing & Preview
            </span>

            <!-- Preview Browser -->
            <button
              type="button"
              class="w-full py-2 px-3 bg-white border border-slate-300 hover:bg-slate-50 text-slate-800 text-xs font-semibold rounded-lg transition flex items-center justify-center gap-2 cursor-pointer shadow-2xs"
            >
              <svg
                class="w-3.5 h-3.5"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
              >
                <path
                  d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"
                />

                <circle
                  cx="12"
                  cy="12"
                  r="3"
                />
              </svg>

              Preview in Browser
            </button>

            <!-- Send Test Email -->
            <button
              type="button"
              class="w-full py-2 px-3 bg-slate-50 border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg transition flex items-center justify-center gap-2 cursor-pointer"
            >
              <svg
                class="w-3.5 h-3.5"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
              >
                <path
                  d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
                />

                <polyline
                  points="22,6 12,13 2,6"
                />
              </svg>

              Send Test Email
            </button>
          </div>
        </div>

        <!-- =====================================================
             DELIVERY OPTIONS
        ====================================================== -->
        <div
          class="bg-white rounded-xl border border-slate-200/80 shadow-xs p-5 space-y-4"
        >
          <!-- Header -->
          <div
            class="flex items-center gap-2 text-slate-900 font-bold text-sm"
          >
            <svg
              class="w-4 h-4 text-slate-500"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <circle
                cx="12"
                cy="12"
                r="10"
              />

              <polyline
                points="12 6 12 12 16 14"
              />
            </svg>

            <span>Delivery Options</span>
          </div>

          <div
            class="space-y-3 pt-1"
          >
            <!-- Send Immediately -->
            <label
              class="flex items-start gap-3 cursor-pointer group"
            >
              <input
                type="radio"
                name="delivery"
                value="immediately"
                v-model="form.deliveryOption"
                class="mt-0.5 accent-slate-900"
              />

              <div>
                <span
                  class="block text-xs font-bold text-slate-800"
                >
                  Send Immediately
                </span>

                <span
                  class="block text-[11px] text-slate-400 mt-0.5"
                >
                  Message will dispatch upon saving.
                </span>
              </div>
            </label>

            <!-- Schedule for Later -->
            <label
              class="flex items-start gap-3 cursor-pointer group"
            >
              <input
                type="radio"
                name="delivery"
                value="schedule"
                v-model="form.deliveryOption"
                class="mt-0.5 accent-slate-900"
              />

              <div>
                <span
                  class="block text-xs font-bold text-slate-800"
                >
                  Schedule for Later
                </span>
              </div>
            </label>

            <!-- Date & Time -->
            <div
              v-if="form.deliveryOption === 'schedule'"
              class="grid grid-cols-2 gap-2 pt-1 pl-6"
            >
              <div class="relative">
                <input
                  type="date"
                  v-model="form.scheduleDate"
                  class="w-full px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-slate-800 text-slate-800 font-medium font-Inter"
                />
              </div>

              <div class="relative">
                <input
                  type="time"
                  v-model="form.scheduleTime"
                  class="w-full px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg focus:outline-none focus:border-slate-800 text-slate-800 font-medium font-Inter"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>