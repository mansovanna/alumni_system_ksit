<script setup lang="ts">
definePageMeta({
  layout: "admin",
});

// ============================================================
// Types
// ============================================================

interface FormState {
  coverImage: File | null;
  coverPreview: string | null;
  title: string;
  eventType: string;
  location: string;
  date: string;
  time: string;
  description: string;
  registrationDeadlineDate: string;
  registrationDeadlineTime: string;
  capacity: string;
  externalLink: string;
}

interface AgendaItem {
  time: string;
  title: string;
}

interface Speaker {
  name: string;
  title: string;
}

interface AgendaRowErrors {
  time?: string;
  title?: string;
}

interface SpeakerRowErrors {
  name?: string;
  title?: string;
}

interface ErrorsState {
  coverImage: string;
  title: string;
  eventType: string;
  location: string;
  date: string;
  time: string;
  description: string;
  registrationDeadlineDate: string;
  registrationDeadlineTime: string;
  capacity: string;
  externalLink: string;
  agenda: Record<number, AgendaRowErrors>;
  speakers: Record<number, SpeakerRowErrors>;
}

// ============================================================
// Form State
// ============================================================

const form = ref<FormState>({
  coverImage: null,
  coverPreview: null,
  title: "",
  eventType: "Networking",
  location: "",
  date: "",
  time: "",
  description: "",
  registrationDeadlineDate: "",
  registrationDeadlineTime: "",
  capacity: "",
  externalLink: "",
});

// ============================================================
// Event Types
// ============================================================

const eventTypes = [
  "Networking",
  "Workshop",
  "Webinar",
  "Gala",
  "Reunion",
];

// ============================================================
// Agenda / Speakers
// ============================================================

const agenda = ref<AgendaItem[]>([
  {
    time: "",
    title: "",
  },
]);

const addAgendaItem = (): void => {
  agenda.value.push({
    time: "",
    title: "",
  });
};

const removeAgendaItem = (index: number): void => {
  agenda.value.splice(index, 1);
  delete errors.agenda[index];
};

const speakers = ref<Speaker[]>([
  {
    name: "",
    title: "",
  },
]);

const addSpeaker = (): void => {
  speakers.value.push({
    name: "",
    title: "",
  });
};

const removeSpeaker = (index: number): void => {
  speakers.value.splice(index, 1);
  delete errors.speakers[index];
};

// ============================================================
// Cover Image Upload
// ============================================================

const fileInput = ref<HTMLInputElement | null>(null);

const triggerFileSelect = (): void => {
  fileInput.value?.click();
};

const MAX_FILE_MB = 10;

const ALLOWED_TYPES = [
  "image/png",
  "image/jpeg",
  "image/gif",
];

const processFile = (file: File | undefined): void => {
  if (!file) return;

  if (!ALLOWED_TYPES.includes(file.type)) {
    errors.coverImage =
      "Only PNG, JPG or GIF files are allowed.";

    return;
  }

  if (file.size > MAX_FILE_MB * 1024 * 1024) {
    errors.coverImage =
      `File must be under ${MAX_FILE_MB}MB.`;

    return;
  }

  errors.coverImage = "";

  // Revoke old preview URL
  if (form.value.coverPreview) {
    URL.revokeObjectURL(form.value.coverPreview);
  }

  form.value.coverImage = file;
  form.value.coverPreview = URL.createObjectURL(file);
};

const handleFileChange = (
  event: Event
): void => {
  const target = event.target as HTMLInputElement;

  processFile(target.files?.[0]);
};

const handleDrop = (
  event: DragEvent
): void => {
  event.preventDefault();

  processFile(event.dataTransfer?.files?.[0]);
};

// ============================================================
// Validation State
// ============================================================

const errors = reactive<ErrorsState>({
  coverImage: "",
  title: "",
  eventType: "",
  location: "",
  date: "",
  time: "",
  description: "",
  registrationDeadlineDate: "",
  registrationDeadlineTime: "",
  capacity: "",
  externalLink: "",
  agenda: {},
  speakers: {},
});

// ============================================================
// Helpers
// ============================================================

const isValidUrl = (
  value: string
): boolean => {
  try {
    new URL(value);

    return true;
  } catch {
    return false;
  }
};

const resetErrors = (): void => {
  errors.coverImage = "";
  errors.title = "";
  errors.eventType = "";
  errors.location = "";
  errors.date = "";
  errors.time = "";
  errors.description = "";
  errors.registrationDeadlineDate = "";
  errors.registrationDeadlineTime = "";
  errors.capacity = "";
  errors.externalLink = "";

  errors.agenda = {};
  errors.speakers = {};
};

// ============================================================
// Validation
// ============================================================

const validate = (): boolean => {
  resetErrors();

  let isValid = true;

  const fail = (
    field: keyof Omit<
      ErrorsState,
      "agenda" | "speakers"
    >,
    message: string
  ): void => {
    errors[field] = message;

    isValid = false;
  };

  // ----------------------------------------------------------
  // Event Title
  // ----------------------------------------------------------

  if (!form.value.title.trim()) {
    fail(
      "title",
      "Event title is required."
    );
  } else if (
    form.value.title.trim().length < 3
  ) {
    fail(
      "title",
      "Title must be at least 3 characters."
    );
  }

  // ----------------------------------------------------------
  // Event Type
  // ----------------------------------------------------------

  if (!form.value.eventType) {
    fail(
      "eventType",
      "Please select an event type."
    );
  }

  // ----------------------------------------------------------
  // Location
  // ----------------------------------------------------------

  if (!form.value.location.trim()) {
    fail(
      "location",
      "Location is required."
    );
  }

  // ----------------------------------------------------------
  // Date
  // ----------------------------------------------------------

  if (!form.value.date) {
    fail(
      "date",
      "Event date is required."
    );
  } else {
    const today = new Date();

    today.setHours(
      0,
      0,
      0,
      0
    );

    const eventDate = new Date(
      `${form.value.date}T00:00:00`
    );

    if (eventDate < today) {
      fail(
        "date",
        "Date cannot be in the past."
      );
    }
  }

  // ----------------------------------------------------------
  // Time
  // ----------------------------------------------------------

  if (!form.value.time) {
    fail(
      "time",
      "Event time is required."
    );
  }

  // ----------------------------------------------------------
  // Description
  // ----------------------------------------------------------

  if (!form.value.description.trim()) {
    fail(
      "description",
      "Description is required."
    );
  } else if (
    form.value.description.trim().length < 20
  ) {
    fail(
      "description",
      "Description should be at least 20 characters."
    );
  }

  // ----------------------------------------------------------
  // Agenda
  // ----------------------------------------------------------

  agenda.value.forEach(
    (
      item: AgendaItem,
      index: number
    ) => {
      const rowErrors: AgendaRowErrors = {};

      const touched =
        Boolean(item.time) ||
        Boolean(item.title.trim());

      if (touched) {
        if (!item.time) {
          rowErrors.time =
            "Time required.";
        }

        if (!item.title.trim()) {
          rowErrors.title =
            "Activity title required.";
        }
      }

      if (
        Object.keys(rowErrors).length
      ) {
        errors.agenda[index] =
          rowErrors;

        isValid = false;
      }
    }
  );

  // ----------------------------------------------------------
  // Speakers
  // ----------------------------------------------------------

  speakers.value.forEach(
    (
      speaker: Speaker,
      index: number
    ) => {
      const rowErrors: SpeakerRowErrors = {};

      const touched =
        Boolean(speaker.name.trim()) ||
        Boolean(speaker.title.trim());

      if (touched) {
        if (!speaker.name.trim()) {
          rowErrors.name =
            "Speaker name required.";
        }

        if (!speaker.title.trim()) {
          rowErrors.title =
            "Professional title required.";
        }
      }

      if (
        Object.keys(rowErrors).length
      ) {
        errors.speakers[index] =
          rowErrors;

        isValid = false;
      }
    }
  );

  // ----------------------------------------------------------
  // Registration Settings
  // ----------------------------------------------------------

  const {
    registrationDeadlineDate,
    registrationDeadlineTime,
    capacity,
    externalLink,
  } = form.value;

  // Deadline Date
  if (
    registrationDeadlineDate &&
    form.value.date
  ) {
    const deadlineDate =
      new Date(
        `${registrationDeadlineDate}T00:00:00`
      );

    const eventDate =
      new Date(
        `${form.value.date}T00:00:00`
      );

    if (deadlineDate > eventDate) {
      fail(
        "registrationDeadlineDate",
        "Deadline must be before the event date."
      );
    }
  }

  // Deadline Time without Date
  if (
    registrationDeadlineTime &&
    !registrationDeadlineDate
  ) {
    fail(
      "registrationDeadlineDate",
      "Pick a deadline date to go with the time."
    );
  }

  // Deadline Date without Time
  if (
    registrationDeadlineDate &&
    !registrationDeadlineTime
  ) {
    fail(
      "registrationDeadlineTime",
      "Pick a deadline time to go with the date."
    );
  }

  // ----------------------------------------------------------
  // Capacity
  // ----------------------------------------------------------

  if (capacity) {
    const numberCapacity =
      Number(capacity);

    if (
      Number.isNaN(numberCapacity) ||
      numberCapacity <= 0
    ) {
      fail(
        "capacity",
        "Capacity must be a positive number."
      );
    }
  }

  // ----------------------------------------------------------
  // External URL
  // ----------------------------------------------------------

  if (
    externalLink &&
    !isValidUrl(externalLink)
  ) {
    fail(
      "externalLink",
      "Enter a valid URL (e.g. https://example.com)."
    );
  }

  return isValid;
};

// ============================================================
// Actions
// ============================================================



const isSubmitting = ref(false);

const handleSaveEvent =
  async (): Promise<void> => {
    if (!validate()) {
      await nextTick();

      document
        .querySelector(".field-error")
        ?.scrollIntoView({
          behavior: "smooth",
          block: "center",
        });

      return;
    }

    isSubmitting.value = true;

    try {
      const payload = {
        ...form.value,
        agenda: agenda.value,
        speakers: speakers.value,
      };

      console.log(
        "Event payload:",
        payload
      );

      /*
      Example API request:

      const formData = new FormData();

      if (form.value.coverImage) {
        formData.append(
          "cover_image",
          form.value.coverImage
        );
      }

      formData.append(
        "title",
        form.value.title
      );

      formData.append(
        "event_type",
        form.value.eventType
      );

      formData.append(
        "location",
        form.value.location
      );

      formData.append(
        "date",
        form.value.date
      );

      formData.append(
        "time",
        form.value.time
      );

      formData.append(
        "description",
        form.value.description
      );

      formData.append(
        "registration_deadline_date",
        form.value.registrationDeadlineDate
      );

      formData.append(
        "registration_deadline_time",
        form.value.registrationDeadlineTime
      );

      formData.append(
        "capacity",
        form.value.capacity
      );

      formData.append(
        "external_link",
        form.value.externalLink
      );

      formData.append(
        "agenda",
        JSON.stringify(agenda.value)
      );

      formData.append(
        "speakers",
        JSON.stringify(speakers.value)
      );

      await $fetch("/api/events", {
        method: "POST",
        body: formData,
      });
      */

      await navigateTo(
        "/admins/events"
      );
    } catch (error) {
      console.error(
        "Failed to save event:",
        error
      );
    } finally {
      isSubmitting.value = false;
    }
  };

// ============================================================
// Cleanup Object URL
// ============================================================

onBeforeUnmount(() => {
  if (form.value.coverPreview) {
    URL.revokeObjectURL(
      form.value.coverPreview
    );
  }
});
</script>

<template>
  <div class="flex flex-col gap-4 w-full">

    <!-- Breadcrumb -->
    <div
      class="flex items-center gap-1.5 text-xs sm:text-sm text-slate-400"
    >
      <NuxtLink
        to="/admin/events"
        class="hover:text-slate-600"
      >
        Events
      </NuxtLink>

      <span>›</span>

      <span class="text-slate-500">
        Create New Event
      </span>
    </div>

    <!-- Title -->
    <div>
      <h1
        class="text-xl sm:text-2xl font-semibold text-slate-800"
      >
        Create New Event
      </h1>

      <p
        class="text-xs sm:text-sm text-slate-500"
      >
        Set up a new event for the alumni community.
        Fill in the details below.
      </p>
    </div>

    <!-- Form Card -->
    <form
      @submit.prevent="handleSaveEvent"
      class="bg-white border border-slate-200 rounded-xl p-4 sm:p-6 lg:p-8 flex flex-col gap-5 sm:gap-6 w-full"
    >

      <!-- Cover Image -->
      <div class="flex flex-col gap-2">

        <label
          class="text-sm font-medium text-slate-700"
        >
          Cover Image
        </label>

        <div
          @click="triggerFileSelect"
          @dragover.prevent
          @drop="handleDrop"
          class="border-2 border-dashed rounded-xl h-40 sm:h-48 lg:h-56 flex flex-col items-center justify-center gap-2 cursor-pointer hover:border-primary/50 hover:bg-slate-50 transition overflow-hidden relative"
          :class="
            errors.coverImage
              ? 'border-red-300 bg-red-50/40'
              : 'border-slate-200'
          "
        >

          <!-- Preview -->
          <img
            v-if="form.coverPreview"
            :src="form.coverPreview"
            alt="Cover preview"
            class="absolute inset-0 w-full h-full object-cover"
          />

          <!-- Upload Placeholder -->
          <template v-else>

            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
              class="size-8 text-slate-400"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 16.5V9.75m0 0l-3 3m3-3l3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z"
              />
            </svg>

            <p
              class="text-sm text-slate-600 text-center px-4"
            >
              <span
                class="font-medium text-slate-800"
              >
                Upload a file
              </span>

              or drag and drop
            </p>

            <p
              class="text-xs text-slate-400"
            >
              PNG, JPG, GIF up to 10MB
            </p>

          </template>

          <input
            ref="fileInput"
            type="file"
            accept="image/png,image/jpeg,image/gif"
            class="hidden"
            @change="handleFileChange"
          />

        </div>

        <p
          v-if="errors.coverImage"
          class="field-error text-xs text-red-500"
        >
          {{ errors.coverImage }}
        </p>

      </div>

      <!-- Event Title -->
      <div class="flex flex-col gap-1.5">

        <label
          class="text-sm font-medium text-slate-700"
        >
          Event Title
          <span class="text-red-500">*</span>
        </label>

        <input
          v-model="form.title"
          type="text"
          placeholder="e.g., Annual Tech Alumni Mixer"
          class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
          :class="
            errors.title
              ? 'border-red-400 focus:ring-red-100 field-error'
              : 'border-slate-200 focus:border-primary focus:ring-primary/20'
          "
        />

        <p
          v-if="errors.title"
          class="text-xs text-red-500"
        >
          {{ errors.title }}
        </p>

      </div>

      <!-- Event Type + Location -->
      <div
        class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6"
      >

        <!-- Event Type -->
        <div class="flex flex-col gap-1.5">

          <label
            class="text-sm font-medium text-slate-700"
          >
            Event Type
            <span class="text-red-500">*</span>
          </label>

          <select
            v-model="form.eventType"
            class="w-full border rounded-lg px-3 py-2 text-sm text-primary focus:outline-none focus:ring-2"
            :class="
              errors.eventType
                ? 'border-red-400 focus:ring-red-100 field-error'
                : 'border-slate-200 focus:border-primary focus:ring-primary/20'
            "
          >
            <option
              v-for="type in eventTypes"
              :key="type"
              :value="type"
            >
              {{ type }}
            </option>
          </select>

          <p
            v-if="errors.eventType"
            class="text-xs text-red-500"
          >
            {{ errors.eventType }}
          </p>

        </div>

        <!-- Location -->
        <div class="flex flex-col gap-1.5">

          <label
            class="text-sm font-medium text-slate-700"
          >
            Location
            <span class="text-red-500">*</span>
          </label>

          <div class="relative">

            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              class="size-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"
              />
            </svg>

            <input
              v-model="form.location"
              type="text"
              placeholder="Virtual or Physical Address"
              class="w-full border rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2"
              :class="
                errors.location
                  ? 'border-red-400 focus:ring-red-100 field-error'
                  : 'border-slate-200 focus:border-primary focus:ring-primary/20'
              "
            />

          </div>

          <p
            v-if="errors.location"
            class="text-xs text-red-500"
          >
            {{ errors.location }}
          </p>

        </div>

      </div>

      <!-- Date + Time -->
      <div
        class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6"
      >

        <!-- Date -->
        <div class="flex flex-col gap-1.5">

          <label
            class="text-sm font-medium text-slate-700"
          >
            Date
            <span class="text-red-500">*</span>
          </label>

          <input
            v-model="form.date"
            type="date"
            class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
            :class="
              errors.date
                ? 'border-red-400 focus:ring-red-100 field-error'
                : 'border-slate-200 focus:border-primary focus:ring-primary/20'
            "
          />

          <p
            v-if="errors.date"
            class="text-xs text-red-500"
          >
            {{ errors.date }}
          </p>

        </div>

        <!-- Time -->
        <div class="flex flex-col gap-1.5">

          <label
            class="text-sm font-medium text-slate-700"
          >
            Time
            <span class="text-red-500">*</span>
          </label>

          <input
            v-model="form.time"
            type="time"
            class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
            :class="
              errors.time
                ? 'border-red-400 focus:ring-red-100 field-error'
                : 'border-slate-200 focus:border-primary focus:ring-primary/20'
            "
          />

          <p
            v-if="errors.time"
            class="text-xs text-red-500"
          >
            {{ errors.time }}
          </p>

        </div>

      </div>

      <!-- Description -->
      <div class="flex flex-col gap-1.5">

        <label
          class="text-sm font-medium text-slate-700"
        >
          Description
          <span class="text-red-500">*</span>
        </label>

        <div
          class="border rounded-lg overflow-hidden"
          :class="
            errors.description
              ? 'border-red-400 field-error'
              : 'border-slate-200'
          "
        >

          <div
            class="flex items-center gap-3 px-3 py-2 border-b border-slate-200 bg-slate-50 text-slate-500 text-sm"
          >
            <button
              type="button"
              class="font-bold hover:text-slate-800"
            >
              B
            </button>

            <button
              type="button"
              class="italic hover:text-slate-800"
            >
              I
            </button>

            <button
              type="button"
              class="hover:text-slate-800"
            >
              ≡
            </button>

            <button
              type="button"
              class="hover:text-slate-800"
            >
              🔗
            </button>
          </div>

          <textarea
            v-model="form.description"
            rows="4"
            placeholder="Detailed event description..."
            class="w-full px-3 py-2 text-sm focus:outline-none resize-none"
          ></textarea>

        </div>

        <div
          class="flex items-center justify-between"
        >

          <p
            v-if="errors.description"
            class="text-xs text-red-500"
          >
            {{ errors.description }}
          </p>

          <p
            class="text-xs text-slate-400 ml-auto"
          >
            {{ form.description.length }} chars
          </p>

        </div>

      </div>

      <!-- Agenda -->
      <div class="flex flex-col gap-3">

        <div
          class="flex items-center justify-between"
        >

          <label
            class="text-sm font-semibold text-slate-800"
          >
            Agenda
          </label>

          <button
            type="button"
            @click="addAgendaItem"
            class="flex items-center gap-1 text-xs sm:text-sm text-primary font-medium hover:underline"
          >
            + Add Activity
          </button>

        </div>

        <div
          v-for="(item, index) in agenda"
          :key="index"
          class="flex flex-col gap-1"
        >

          <div
            class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2"
          >

            <input
              v-model="item.time"
              type="time"
              class="w-full sm:w-32 shrink-0 border rounded-lg px-2 py-2 text-xs sm:text-sm focus:outline-none focus:ring-2"
              :class="
                errors.agenda[index]?.time
                  ? 'border-red-400 focus:ring-red-100'
                  : 'border-slate-200 focus:border-primary focus:ring-primary/20'
              "
            />

            <div
              class="flex items-center gap-2 flex-1 min-w-0"
            >

              <input
                v-model="item.title"
                type="text"
                placeholder="Activity Title"
                class="flex-1 min-w-0 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
                :class="
                  errors.agenda[index]?.title
                    ? 'border-red-400 focus:ring-red-100'
                    : 'border-slate-200 focus:border-primary focus:ring-primary/20'
                "
              />

              <button
                type="button"
                @click="removeAgendaItem(index)"
                class="shrink-0 size-9 flex items-center justify-center text-slate-400 hover:text-red-500 border border-slate-200 rounded-lg hover:bg-slate-50"
              >

                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.8"
                  class="size-4"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                  />
                </svg>

              </button>

            </div>

          </div>

          <p
            v-if="
              errors.agenda[index]?.time ||
              errors.agenda[index]?.title
            "
            class="text-xs text-red-500"
          >
            {{
              errors.agenda[index]?.time ||
              errors.agenda[index]?.title
            }}
          </p>

        </div>

      </div>

      <!-- Speakers -->
      <div class="flex flex-col gap-3">

        <div
          class="flex items-center justify-between"
        >

          <label
            class="text-sm font-semibold text-slate-800"
          >
            Speakers
          </label>

          <button
            type="button"
            @click="addSpeaker"
            class="flex items-center gap-1 text-xs sm:text-sm text-primary font-medium hover:underline"
          >
            + Add Speaker
          </button>

        </div>

        <div
          v-for="(speaker, index) in speakers"
          :key="index"
          class="flex flex-col gap-1"
        >

          <div
            class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2"
          >

            <!-- Speaker Icon -->
            <div
              class="hidden sm:flex shrink-0 size-9 rounded-full bg-slate-100 border border-slate-200 items-center justify-center text-slate-400"
            >

              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                class="size-4"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"
                />
              </svg>

            </div>

            <div
              class="flex flex-col sm:flex-row gap-2 flex-1 min-w-0"
            >

              <input
                v-model="speaker.name"
                type="text"
                placeholder="Speaker Name"
                class="flex-1 min-w-0 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
                :class="
                  errors.speakers[index]?.name
                    ? 'border-red-400 focus:ring-red-100'
                    : 'border-slate-200 focus:border-primary focus:ring-primary/20'
                "
              />

              <input
                v-model="speaker.title"
                type="text"
                placeholder="Professional Title"
                class="flex-1 min-w-0 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
                :class="
                  errors.speakers[index]?.title
                    ? 'border-red-400 focus:ring-red-100'
                    : 'border-slate-200 focus:border-primary focus:ring-primary/20'
                "
              />

            </div>

            <button
              type="button"
              @click="removeSpeaker(index)"
              class="shrink-0 size-9 flex items-center justify-center text-slate-400 hover:text-red-500 border border-slate-200 rounded-lg hover:bg-slate-50 self-end sm:self-auto"
            >

              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                class="size-4"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                />
              </svg>

            </button>

          </div>

          <p
            v-if="
              errors.speakers[index]?.name ||
              errors.speakers[index]?.title
            "
            class="text-xs text-red-500"
          >
            {{
              errors.speakers[index]?.name ||
              errors.speakers[index]?.title
            }}
          </p>

        </div>

      </div>

      <!-- Registration Settings -->
      <div
        class="border-t border-slate-200 pt-5 flex flex-col gap-4"
      >

        <label
          class="text-sm font-semibold text-slate-800"
        >
          Registration Settings
        </label>

        <div
          class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6"
        >

          <!-- Deadline -->
          <div
            class="flex flex-col gap-1.5"
          >

            <label
              class="text-xs text-slate-500"
            >
              Registration Deadline
            </label>

            <div class="flex gap-2">

              <input
                v-model="form.registrationDeadlineDate"
                type="date"
                class="w-1/2 border rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2"
                :class="
                  errors.registrationDeadlineDate
                    ? 'border-red-400 focus:ring-red-100'
                    : 'border-slate-200 focus:border-primary focus:ring-primary/20'
                "
              />

              <input
                v-model="form.registrationDeadlineTime"
                type="time"
                class="w-1/2 border rounded-lg px-2 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                :class="
                  errors.registrationDeadlineTime
                    ? 'border-red-400 focus:ring-red-100'
                    : 'border-slate-200'
                "
              />

            </div>

            <p
              v-if="errors.registrationDeadlineDate"
              class="text-xs text-red-500"
            >
              {{ errors.registrationDeadlineDate }}
            </p>

            <p
              v-if="errors.registrationDeadlineTime"
              class="text-xs text-red-500"
            >
              {{ errors.registrationDeadlineTime }}
            </p>

          </div>

          <!-- Capacity -->
          <div
            class="flex flex-col gap-1.5"
          >

            <label
              class="text-xs text-slate-500"
            >
              Capacity Limit
            </label>

            <input
              v-model="form.capacity"
              type="number"
              min="1"
              placeholder="e.g., 100"
              class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2"
              :class="
                errors.capacity
                  ? 'border-red-400 focus:ring-red-100'
                  : 'border-slate-200 focus:border-primary focus:ring-primary/20'
              "
            />

            <p
              v-if="errors.capacity"
              class="text-xs text-red-500"
            >
              {{ errors.capacity }}
            </p>

          </div>

        </div>

        <!-- External Registration Link -->
        <div class="flex flex-col gap-1.5">

          <label
            class="text-xs text-slate-500"
          >
            External Registration Link
            (Optional)
          </label>

          <div class="relative">

            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              class="size-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"
              />
            </svg>

            <input
              v-model="form.externalLink"
              type="url"
              placeholder="https://"
              class="w-full border rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2"
              :class="
                errors.externalLink
                  ? 'border-red-400 focus:ring-red-100'
                  : 'border-slate-200 focus:border-primary focus:ring-primary/20'
              "
            />

          </div>

          <p
            v-if="errors.externalLink"
            class="text-xs text-red-500"
          >
            {{ errors.externalLink }}
          </p>

        </div>

      </div>

      <!-- Actions -->
      <div
        class="flex flex-col-reverse sm:flex-row justify-end gap-3 border-t border-slate-200 pt-5"
      >

        <button
          type="button"
          @click="$router.back()"
          class="px-4 py-2 text-sm border border-slate-300 rounded-lg hover:bg-slate-50 w-full sm:w-auto"
        >
          Discard
        </button>

        <button
          type="submit"
          :disabled="isSubmitting"
          class="px-4 py-2 text-sm bg-slate-900 text-white rounded-lg hover:bg-slate-800 disabled:opacity-60 disabled:cursor-not-allowed w-full sm:w-auto"
        >
          {{
            isSubmitting
              ? "Saving..."
              : "Save Event"
          }}
        </button>

      </div>

    </form>
  </div>
</template>