<script setup lang="ts">
import SearchIcon from "~/components/icons/SearchIcon.vue";
import type { MessageModel } from "~/types/message.response.model";

definePageMeta({
  layout: "admin",
});

/* ------------------- Store & States ------------------- */
const messageStore = useMessageStore();

/* ------------------- Form States ------------------- */
const newMessage = ref({
  subject: "",
  body: "",
  graduationYear: "2024",
  major: "Engineering",
  employmentStatus: "Actively Job Seeking",
});

/* ------------------- Selection Options ------------------- */
const years = ["2024", "2023", "2022"];

const majors = ["Engineering", "Business", "Science"];

const employmentStatuses = [
  "Actively Job Seeking",
  "Employed",
  "Continuing Education",
];

/* ------------------- Update / View States ------------------- */
const formUpdate = ref(false);
const dataUpdate = ref<MessageModel>({} as MessageModel);

const handleUpdate = (data: any) => {
  if (data) {
    formUpdate.value = true;
    dataUpdate.value = data;
  }
};

const handleUpdateNew = (data: any) => {
  console.log(data);
};

/* ------------------- Data Fetching ------------------- */
onMounted(() => {
  messageStore.getMessageAll();
});

/* ------------------- Pagination ------------------- */
const pageDirect = (page: number) => {
  if (page < 1) return;

  const lastPage = messageStore.data?.data?.last_page || 1;

  if (page > lastPage) return;

  messageStore.page = page;
  messageStore.getMessageAll();
};

/* ------------------- Date Formatter ------------------- */
const formatDate = (dateString?: string) => {
  if (!dateString) return "—";

  const date = new Date(dateString);

  if (isNaN(date.getTime())) return "—";

  const month = date.toLocaleDateString("en-US", {
    month: "short",
  });

  const day = String(date.getDate()).padStart(2, "0");

  const time = date.toLocaleTimeString("en-US", {
    hour: "2-digit",
    minute: "2-digit",
    hour12: true,
  });

  return `${month} ${day}, ${time}`;
};

/* ------------------- Search Debounce ------------------- */
let timeout: ReturnType<typeof setTimeout>;

watch(
  () => messageStore.search,
  () => {
    clearTimeout(timeout);

    timeout = setTimeout(() => {
      messageStore.page = 1;
      messageStore.getMessageAll();
    }, 400);
  },
);

/* ------------------- Schedule Modal ------------------- */
const showModal = ref(false);

const handleConfirm = (data: any) => {
  console.log("Scheduled for:", data.date, data.time);

  showModal.value = false;
};
</script>

<template>
  <AdminsMessageSchedule
    :isOpen="showModal"
    :recipientCount="342"
    @close="showModal = false"
    @confirm="handleConfirm"
  />

  <div
    class="p-6 bg-slate-50 text-slate-800 font-Inter rounded-xl overflow-clip"
  >
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-slate-900">Messages</h1>

      <p class="text-sm text-slate-500 mt-1">
        Manage and distribute messages to alumni segments.
      </p>
    </div>

    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
      <!-- ===================================================
           LEFT COLUMN
      ==================================================== -->
      <div
        class="lg:col-span-5 bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs space-y-5"
      >
        <!-- Section Header -->
        <div
          class="flex items-center gap-2 text-slate-900 font-bold text-base pb-2 border-b border-slate-100"
        >
          <svg
            class="w-4 h-4"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
          >
            <path
              d="M12 20h9M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"
            />
          </svg>

          <span>New Message</span>
        </div>

        <!-- Message Subject -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            Message Subject
          </label>

          <input
            v-model="newMessage.subject"
            type="text"
            placeholder="e.g., Spring Career Fair 2024"
            class="w-full px-3 py-2 text-sm border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 placeholder:text-slate-400"
          />
        </div>

        <!-- Message Body -->
        <div>
          <label class="block text-xs font-semibold text-slate-700 mb-1.5">
            Message Body
          </label>

          <div
            class="border border-slate-200 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-500"
          >
            <!-- Toolbar -->
            <div
              class="flex items-center gap-3 px-3 py-1.5 bg-slate-50 border-b border-slate-200 text-slate-600 text-xs font-semibold"
            >
              <button type="button" class="hover:text-slate-900 cursor-pointer">
                B
              </button>

              <button
                type="button"
                class="italic hover:text-slate-900 cursor-pointer"
              >
                I
              </button>

              <button type="button" class="hover:text-slate-900 cursor-pointer">
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

              <button type="button" class="hover:text-slate-900 cursor-pointer">
                <svg
                  class="w-3.5 h-3.5"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                >
                  <line x1="8" y1="6" x2="21" y2="6" />
                  <line x1="8" y1="12" x2="21" y2="12" />
                  <line x1="8" y1="18" x2="21" y2="18" />

                  <line x1="3" y1="6" x2="3.01" y2="6" />
                  <line x1="3" y1="12" x2="3.01" y2="12" />
                  <line x1="3" y1="18" x2="3.01" y2="18" />
                </svg>
              </button>
            </div>

            <!-- Textarea -->
            <textarea
              v-model="newMessage.body"
              rows="4"
              placeholder="Write your message here..."
              class="w-full p-3 text-sm focus:outline-none placeholder:text-slate-400 resize-y"
            ></textarea>
          </div>
        </div>

        <!-- =================================================
             AUDIENCE FILTERS
        ================================================== -->
        <div class="space-y-3">
          <p class="text-xs font-bold text-slate-800">Audience Filters</p>

          <!-- Graduation Year -->
          <div>
            <span class="block text-[11px] font-medium text-slate-500 mb-1.5">
              Graduation Year
            </span>

            <div class="flex flex-wrap gap-2">
              <button
                v-for="year in years"
                :key="year"
                type="button"
                @click="newMessage.graduationYear = year"
                :class="[
                  'px-3 py-1 text-xs rounded-full border transition cursor-pointer',
                  newMessage.graduationYear === year
                    ? 'bg-blue-600 border-blue-600 text-white font-medium'
                    : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50',
                ]"
              >
                {{ year }}
              </button>
            </div>
          </div>

          <!-- Major -->
          <div>
            <span class="block text-[11px] font-medium text-slate-500 mb-1.5">
              Major
            </span>

            <div class="flex flex-wrap gap-2">
              <button
                v-for="mj in majors"
                :key="mj"
                type="button"
                @click="newMessage.major = mj"
                :class="[
                  'px-3 py-1 text-xs rounded-full border transition cursor-pointer',
                  newMessage.major === mj
                    ? 'bg-blue-600 border-blue-600 text-white font-medium'
                    : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50',
                ]"
              >
                {{ mj }}
              </button>
            </div>
          </div>

          <!-- Employment Status -->
          <div>
            <span class="block text-[11px] font-medium text-slate-500 mb-1.5">
              Employment Status
            </span>

            <div class="flex flex-wrap gap-2">
              <button
                v-for="st in employmentStatuses"
                :key="st"
                type="button"
                @click="newMessage.employmentStatus = st"
                :class="[
                  'px-3 py-1 text-xs rounded-full border transition cursor-pointer',
                  newMessage.employmentStatus === st
                    ? 'bg-blue-600 border-blue-600 text-white font-medium'
                    : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50',
                ]"
              >
                {{ st }}
              </button>
            </div>
          </div>
        </div>

        <!-- Reaching Alert -->
        <div
          class="flex items-center gap-2 p-3 bg-amber-50 border border-amber-200 rounded-lg text-amber-800 text-xs"
        >
          <svg
            class="w-4 h-4 shrink-0 text-amber-600"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            viewBox="0 0 24 24"
          >
            <path
              d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"
            />

            <line x1="12" y1="9" x2="12" y2="13" />
            <line x1="12" y1="17" x2="12.01" y2="17" />
          </svg>

          <span>
            This will reach approximately
            <strong>4,250 alumni</strong>
          </span>
        </div>

        <!-- =================================================
             ACTION BUTTONS
        ================================================== -->
        <div class="flex items-center gap-3 pt-2">
          <!-- Schedule -->
          <button
            type="button"
            @click="showModal = true"
            class="flex-1 inline-flex justify-center items-center gap-1.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold py-2.5 rounded-lg transition cursor-pointer"
          >
            <svg
              class="w-3.5 h-3.5"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <circle cx="12" cy="12" r="10" />
              <polyline points="12 6 12 12 16 14" />
            </svg>

            Schedule
          </button>

          <!-- Send -->
          <button
            type="button"
            class="flex-1 inline-flex justify-center items-center gap-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-2.5 rounded-lg transition shadow-xs cursor-pointer"
          >
            <svg
              class="w-3.5 h-3.5"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <line x1="22" y1="2" x2="11" y2="13" />
              <polygon points="22 2 15 22 11 13 2 9 22 2" />
            </svg>

            Send Message
          </button>
        </div>
      </div>

      <!-- ===================================================
           RIGHT COLUMN
      ==================================================== -->
      <div
        class="lg:col-span-7 bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden flex flex-col justify-between"
      >
        <div>
          <!-- Table Header -->
          <div
            class="p-4 flex flex-wrap items-center justify-between gap-3 border-b border-slate-100"
          >
            <div
              class="flex items-center gap-2 text-slate-900 font-bold text-base"
            >
              <svg
                class="w-4 h-4 text-slate-600"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
              >
                <path d="M3 12a9 9 0 1 0 3-6.7" />
                <path d="M3 4v6h6" />
                <path d="M12 7v5l3 2" />
              </svg>

              <span>Message History</span>
            </div>

            <!-- Search -->
            <div class="relative w-full sm:w-64">
              <input
                v-model="messageStore.search"
                type="text"
                placeholder="Search by title or recipient..."
                class="w-full bg-slate-50 pl-8 pr-3 py-1.5 border border-slate-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 placeholder:text-slate-400"
              />

              <div
                class="absolute left-2.5 top-0 bottom-0 flex items-center text-slate-400 pointer-events-none"
              >
                <SearchIcon class="w-3.5 h-3.5" />
              </div>
            </div>
          </div>

          <!-- =================================================
               TABLE
          ================================================== -->
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
              <thead
                class="bg-slate-50/70 border-b border-slate-100 text-slate-500 font-semibold"
              >
                <tr>
                  <th class="px-4 py-3">Message Subject</th>

                  <th class="px-3 py-3">Date Sent</th>

                  <th class="px-3 py-3 text-center">Status</th>

                  <th class="px-3 py-3 text-right">Recipients</th>

                  <th class="px-4 py-3 text-right">Actions</th>
                </tr>
              </thead>

              <tbody class="divide-y divide-slate-100">
                <!-- Loading -->
                <tr
                  v-if="
                    messageStore.isLoading && !messageStore.data?.data?.data
                  "
                  v-for="n in 4"
                  :key="n"
                  class="animate-pulse"
                >
                  <td class="px-4 py-4">
                    <div class="h-3.5 bg-slate-200 rounded w-3/4"></div>
                  </td>

                  <td class="px-3 py-4">
                    <div class="h-3.5 bg-slate-200 rounded w-16"></div>
                  </td>

                  <td class="px-3 py-4 text-center">
                    <div
                      class="h-4 bg-slate-200 rounded-full w-12 mx-auto"
                    ></div>
                  </td>

                  <td class="px-3 py-4 text-right">
                    <div class="h-3.5 bg-slate-200 rounded w-10 ml-auto"></div>
                  </td>

                  <td class="px-4 py-4 text-right">
                    <div class="h-6 bg-slate-200 rounded w-16 ml-auto"></div>
                  </td>
                </tr>

                <!-- Empty -->
                <tr
                  v-else-if="
                    !messageStore.isLoading &&
                    (!messageStore.data?.data?.data ||
                      messageStore.data?.data?.data.length === 0)
                  "
                >
                  <td
                    colspan="5"
                    class="px-4 py-8 text-center text-slate-400 font-medium"
                  >
                    No messages found.
                  </td>
                </tr>

                <!-- Data -->
                <tr
                  v-else
                  v-for="item in messageStore.data?.data?.data || []"
                  :key="item.id"
                  class="hover:bg-slate-50/60 transition"
                >
                  <!-- Subject -->
                  <td
                    class="px-4 py-3.5 font-semibold text-slate-800 max-w-[180px] truncate"
                  >
                    {{ item.title ?? "Untitled Message" }}
                  </td>

                  <!-- Date -->
                  <td class="px-3 py-3.5 text-slate-500 whitespace-nowrap">
                    {{ formatDate(item.created_at) }}
                  </td>

                  <!-- Status -->
                  <td class="px-3 py-3.5 text-center whitespace-nowrap">
                    <span
                      class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-semibold"
                      :class="
                        item.body
                          ? 'bg-emerald-100/80 text-emerald-700'
                          : 'bg-slate-100 text-slate-600'
                      "
                    >
                      {{ item.body ? "Sent" : "Draft" }}
                    </span>
                  </td>

                  <!-- Recipients -->
                  <td
                    class="px-3 py-3.5 text-right font-mono text-slate-700 whitespace-nowrap"
                  >
                    {{ (item.id * 1024).toLocaleString() }}
                  </td>

                  <!-- Actions -->
                  <td class="px-4 py-3.5 text-right whitespace-nowrap">
                    <div class="inline-flex items-center justify-end gap-2">
                      <!-- View -->
                      <button
                        type="button"
                        title="View Details"
                        class="p-1.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition border border-slate-200/80 cursor-pointer"
                      >
                        <svg
                          class="w-4 h-4"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="2"
                          viewBox="0 0 24 24"
                        >
                          <path
                            d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"
                          />

                          <circle cx="12" cy="12" r="3" />
                        </svg>
                      </button>

                      <!-- Edit -->
                      <button
                        type="button"
                        title="Edit Message"
                        @click="
                          $router.push({
                            name: 'admins-messages-id',
                            params: {
                              id: item.id,
                            },
                          })
                        "
                        class="p-1.5 text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition border border-blue-200/80 cursor-pointer"
                      >
                        <svg
                          class="w-4 h-4"
                          fill="none"
                          stroke="currentColor"
                          stroke-width="2"
                          viewBox="0 0 24 24"
                        >
                          <path d="M12 20h9" />

                          <path
                            d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"
                          />
                        </svg>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- =================================================
             PAGINATION
        ================================================== -->
        <div
          class="px-4 py-3 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between text-xs text-slate-500"
        >
          <span>
            Showing

            <strong class="text-slate-700">
              {{ messageStore.data?.data?.data?.length || 0 }}
            </strong>

            of

            <strong class="text-slate-700">
              {{ messageStore.data?.data?.total || 0 }}
            </strong>
          </span>

          <div class="inline-flex items-center gap-1">
            <!-- Previous -->
            <button
              @click="
                pageDirect((messageStore.data?.data?.current_page || 1) - 1)
              "
              :disabled="(messageStore.data?.data?.current_page || 1) <= 1"
              class="p-1 rounded border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
            >
              <svg
                class="w-3.5 h-3.5"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
              >
                <polyline points="15 18 9 12 15 6" />
              </svg>
            </button>

            <!-- Next -->
            <button
              @click="
                pageDirect((messageStore.data?.data?.current_page || 1) + 1)
              "
              :disabled="
                (messageStore.data?.data?.current_page || 1) >=
                (messageStore.data?.data?.last_page || 1)
              "
              class="p-1 rounded border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer"
            >
              <svg
                class="w-3.5 h-3.5"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
              >
                <polyline points="9 18 15 12 9 6" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
