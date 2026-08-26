<script setup lang="ts">
import DeleteIcon from "~/components/icons/DeleteIcon.vue";
import LoadingIcon from "~/components/icons/LoadingIcon.vue";
import SearchIcon from "~/components/icons/SearchIcon.vue";
import type { NotificationItem } from "~/types/notification"; // adjust path to wherever you saved the model

definePageMeta({
  layout: "admin",
});

/* ------------------- Store & States ------------------- */
const messageStore = useMessageStore();

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

  const month = date.toLocaleDateString("en-US", { month: "short" });
  const day = String(date.getDate()).padStart(2, "0");

  const time = date.toLocaleTimeString("en-US", {
    hour: "2-digit",
    minute: "2-digit",
    hour12: true,
  });

  return `${month} ${day}, ${time}`;
};

/* ------------------- Filters Display ------------------- */
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

const audienceSummary = (item: NotificationItem) => {
  const f = parseFilters(item.filters);

  if (!f) return "All Alumni";

  const parts = [f.graduationYear, f.major, f.employmentStatus].filter(Boolean);

  return parts.length ? parts.join(" · ") : "All Alumni";
};

/* ------------------- Type Badge Color ------------------- */
const typeBadgeClass = (type: string) => {
  switch (type) {
    case "Career Fair":
      return "bg-purple-100 text-purple-700";
    case "Job Posting":
      return "bg-blue-100 text-blue-700";
    case "Event":
      return "bg-amber-100 text-amber-700";
    case "Reminder":
      return "bg-rose-100 text-rose-700";
    default:
      return "bg-slate-100 text-slate-600";
  }
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

const loadings = ref({
  id: 0,
  value: false,
});

const messageError = ref(null);
const deleteMessage = async (id: number) => {
  if (id) {
    loadings.value.id = id;
    loadings.value.value = true;
    messageError.value = null;

    try {
      const res = await messageStore.deleteMessage(id);

      if (res.status === 200 || res.status === 204) {
        if (messageStore.data?.data.data) {
          messageStore.data.data.data = messageStore.data.data.data.filter(
            (e) => e.id !== id,
          );
        }
      }
    } catch (e: any) {
      messageError.value = e.response;
    } finally {
      loadings.value.value = false;
    }
  }
};


</script>


<template>
  <!-- Block Form Add -->
  <!-- -------------------------------And form Add Event -------------------- -->
  <!-- ---------------------------------------------------------------- -->

  <div class="text-slate-800 font-Inter rounded-xl overflow-clip">
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
      <!-- LEFT COLUMN -->
      <AdminsMessageNoti />

      <!-- RIGHT COLUMN -->
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
                placeholder="Search by title..."
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
                  <th class="px-3 py-3">Type</th>
                  <th class="px-3 py-3">Audience</th>
                  <th class="px-3 py-3">Date Sent</th>
                  <th class="px-4 py-3 text-right">Actions</th>
                </tr>
              </thead>

              <tbody class="divide-y divide-slate-100">
                <!-- Loading -->
                <template
                  v-if="
                    messageStore.isLoading && !messageStore.data?.data?.data
                  "
                >
                  <tr v-for="n in 4" :key="n" class="animate-pulse">
                    <td class="px-4 py-4">
                      <div class="h-3.5 bg-slate-200 rounded w-3/4"></div>
                    </td>

                    <td class="px-3 py-4">
                      <div class="h-4 bg-slate-200 rounded-full w-16"></div>
                    </td>

                    <td class="px-3 py-4">
                      <div class="h-3.5 bg-slate-200 rounded w-24"></div>
                    </td>

                    <td class="px-3 py-4">
                      <div class="h-3.5 bg-slate-200 rounded w-16"></div>
                    </td>

                    <td class="px-4 py-4 text-right">
                      <div class="h-6 bg-slate-200 rounded w-16 ml-auto"></div>
                    </td>
                  </tr>
                </template>

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

                <tr
                  v-else-if="
                    messageStore.isLoading && messageStore.data?.data?.data
                  "
                >
                  <td
                    colspan="5"
                    class="px-4 py-8 text-center text-slate-400 font-medium"
                  >
                    <div class="w-full flex justify-center items-center">
                      <LoadingIcon class="text-primary size-8" />
                    </div>
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
                    :title="item.title"
                  >
                    {{ item.title || "Untitled Message" }}
                  </td>

                  <!-- Type -->
                  <td class="px-3 py-3.5 whitespace-nowrap">
                    <span
                      class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-semibold"
                      :class="typeBadgeClass(item.type)"
                    >
                      {{ item.type }}
                    </span>
                  </td>

                  <!-- Audience -->
                  <td
                    class="px-3 py-3.5 text-slate-500 max-w-[160px] truncate"
                    :title="audienceSummary(item)"
                  >
                    {{ audienceSummary(item) }}
                  </td>

                  <!-- Date -->
                  <td class="px-3 py-3.5 text-slate-500 whitespace-nowrap">
                    {{ formatDate(item.created_at) }}
                  </td>

                  <!-- Actions -->
                  <td class="px-4 py-3.5 text-right whitespace-nowrap">
                    <div class="inline-flex items-center justify-end gap-2">
                      <!-- View -->

                      <!-- Edit -->
                      <button
                        type="button"
                        title="Edit Message"
                        @click="
                          $router.push({
                            name: 'admins-messages-id',
                            params: { id: item.id },
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

                      <!-- Edit -->
                      <button
                        type="button"
                        title="Delete Message"
                        @click="deleteMessage(item.id)"
                        :disabled="loadings.value && loadings.id == item.id"
                        class="p-1.5 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition border border-red-200/80 cursor-pointer"
                      >
                        <LoadingIcon
                          v-if="loadings.value && loadings.id == item.id"
                        />
                        <DeleteIcon v-else />
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
