<script setup lang="ts">
import MessageIcon from "~/components/icons/MessageIcon.vue";

const majorStore = useMajorStore();
const messageStore = useMessageStore();

/* ------------------- Options ------------------- */
const notificationTypes = [
  "General",
  "Career Fair",
  "Job Posting",
  "Event",
  "Reminder",
];


const employmentStatuses = [
  "self_employed",
  "employed",
  "unemployed",
  "studying",
  "unknown",
];

const newMessage = ref({
  title: "",
  message: "",
  type: "General",
  filters: {
    graduationYear: "2024",
    major: "Computer Science",
    employmentStatus: "self_employed",
  },
});

onMounted(() => {
  if (!majorStore.data?.data?.length) {
    majorStore.getMajorAll?.();
  }
});

/* ------------------- Validation ------------------- */
const TITLE_MAX = 150;
const MESSAGE_MAX = 2000;

const submitted = ref(false);
const isSending = ref(false);
const serverError = ref("");

const errors = computed(() => {
  const e: Record<string, string> = {};

  const title = newMessage.value.title.trim();
  const message = newMessage.value.message.trim();

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

  if (!newMessage.value.type) {
    e.type = "Please select a notification type.";
  } else if (!notificationTypes.includes(newMessage.value.type)) {
    e.type = "Invalid notification type.";
  }

  if (
    !newMessage.value.filters.graduationYear &&
    !newMessage.value.filters.major &&
    !newMessage.value.filters.employmentStatus
  ) {
    e.filters = "Select at least one audience filter.";
  }

  return e;
});

const isValid = computed(() => Object.keys(errors.value).length === 0);

// only show a field's error after the user has touched it or tried to submit
const touched = ref<Record<string, boolean>>({});
const showError = (field: string) =>
  (touched.value[field] || submitted.value) && !!errors.value[field];

const touch = (field: string) => {
  touched.value[field] = true;
};

const handleSend = async () => {
  submitted.value = true;
  serverError.value = "";

  if (!isValid.value) {
    return;
  }

  const data = new FormData();

  data.append("title", newMessage.value.title.trim());
  data.append("message", newMessage.value.message.trim());
  data.append("type", newMessage.value.type);
  data.append("filters", JSON.stringify(newMessage.value.filters));

  isSending.value = true;

  try {
    const res = await messageStore.addMessage(data);
    messageStore.data?.data.data.unshift(res.data.data);
  } catch (err: any) {
    serverError.value =
      err?.data?.message || "Failed to send notification. Please try again.";
  } finally {
    isSending.value = false;
  }
};
</script>

<template>
  <div
    class="lg:col-span-5 bg-white rounded-xl p-5 border border-slate-200/80 shadow-xs space-y-5"
  >
    <!-- Section Header -->
    <div
      class="flex items-center gap-2 text-slate-900 font-bold text-base pb-2 border-b border-slate-100"
    >
      <MessageIcon />

      <span>New Message</span>
    </div>

    <!-- Title (backend: title) -->
    <div>
      <label class="block text-xs font-semibold text-slate-700 mb-1.5">
        Message Subject <span class="text-red-500">*</span>
      </label>

      <input
        v-model="newMessage.title"
        type="text"
        placeholder="e.g., Spring Career Fair 2024"
        @blur="touch('title')"
        :class="[
          'w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 placeholder:text-slate-400',
          showError('title')
            ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500'
            : 'border-slate-200 focus:ring-blue-500/20 focus:border-blue-500',
        ]"
      />

      <p v-if="showError('title')" class="mt-1 text-[11px] text-red-600">
        {{ errors.title }}
      </p>
    </div>

    <!-- Notification Type (backend: type) -->
    <div>
      <label class="block text-xs font-semibold text-slate-700 mb-1.5">
        Notification Type <span class="text-red-500">*</span>
      </label>

      <select
        v-model="newMessage.type"
        @blur="touch('type')"
        :class="[
          'w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 bg-white',
          showError('type')
            ? 'border-red-300 focus:ring-red-500/20 focus:border-red-500'
            : 'border-slate-200 focus:ring-blue-500/20 focus:border-blue-500',
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

    <!-- Message Body (backend: message) -->
    <div>
      <label class="block text-xs font-semibold text-slate-700 mb-1.5">
        Message Body
      </label>

      <div
        :class="[
          'border rounded-lg overflow-hidden focus-within:ring-2',
          showError('message')
            ? 'border-red-300 focus-within:ring-red-500/20 focus-within:border-red-500'
            : 'border-slate-200 focus-within:ring-blue-500/20 focus-within:border-blue-500',
        ]"
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
          v-model="newMessage.message"
          rows="4"
          placeholder="Write your message here..."
          @blur="touch('message')"
          class="w-full p-3 text-sm focus:outline-none placeholder:text-slate-400 resize-y"
        ></textarea>
      </div>

      <p v-if="showError('message')" class="mt-1 text-[11px] text-red-600">
        {{ errors.message }}
      </p>
    </div>

    <!-- =================================================
             AUDIENCE FILTERS (backend: filters JSON column)
        ================================================== -->
    <div class="space-y-3">
      <div class="flex items-center justify-between">
        <p class="text-xs font-bold text-slate-800">Audience Filters</p>

        <p v-if="showError('filters')" class="text-[11px] text-red-600">
          {{ errors.filters }}
        </p>
      </div>

      <!-- Graduation Year -->
      <div>
        <span class="block text-[11px] font-medium text-slate-500 mb-1.5">
          Graduation Year
        </span>
        <div class="flex flex-wrap gap-2">
          <button
            v-for="year in messageStore.data?.years"
            :key="year"
            type="button"
            @click="newMessage.filters.graduationYear = year"
            :class="[
              'px-3 py-1 text-xs rounded-full border transition cursor-pointer',
              newMessage.filters.graduationYear === year
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
            v-for="item in majorStore.data?.data"
            :key="Number(item.id)"
            type="button"
            @click="newMessage.filters.major = String(item.name)"
            :class="[
              'px-3 py-1 text-xs rounded-full border transition cursor-pointer',
              newMessage.filters.major === item.name
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
        <span class="block text-[11px] font-medium text-slate-500 mb-1.5">
          Employment Status
        </span>

        <div class="flex flex-wrap gap-2">
          <button
            v-for="st in employmentStatuses"
            :key="st"
            type="button"
            @click="newMessage.filters.employmentStatus = st"
            :class="[
              'px-3 py-1 text-xs capitalize rounded-full border transition cursor-pointer',
              newMessage.filters.employmentStatus === st
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
        <strong>{{ messageStore.data?.totalAlumni ?? 0 }} alumni</strong>
      </span>
    </div>

    <!-- Server-side error -->
    <p v-if="serverError" class="text-xs text-red-600">
      {{ serverError }}
    </p>

    <!-- =================================================
             ACTION BUTTONS
        ================================================== -->
    <div class="flex items-center gap-3 pt-2">
      <!-- Send -->
      <button
        type="button"
        @click="handleSend"
        :disabled="isSending || (submitted && !isValid)"
        :class="[
          'flex-1 inline-flex justify-center items-center gap-1.5 text-xs font-semibold py-2.5 rounded-lg transition shadow-xs',
          isSending || (submitted && !isValid)
            ? 'bg-blue-300 text-white cursor-not-allowed'
            : 'bg-blue-600 hover:bg-blue-700 text-white cursor-pointer',
        ]"
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

        {{ isSending ? "Sending..." : "Send Message" }}
      </button>
    </div>
  </div>
</template>
