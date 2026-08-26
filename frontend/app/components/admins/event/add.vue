<script setup lang="ts">
import CloseIcon from "~/components/icons/CloseIcon.vue";
import SpannerIcon from "~/components/icons/SpannerIcon.vue";

const emit = defineEmits<{
  close: [];
  submitted: [data: unknown];
}>();

const eventStore = useEventStore();

// ============================================================
// Types
// ============================================================

interface FormState {
  coverImage: File | null;
  coverPreview: string | null;

  title: string;
  eventType: string;
  location: string;

  startDate: string;
  startTime: string;

  endDate: string;
  endTime: string;

  description: string;

  registrationDeadlineDate: string;
  registrationDeadlineTime: string;

  maxParticipants: string;
  externalLink: string;

  status: "draft" | "published";
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

  startDate: string;
  startTime: string;
  endDate: string;
  endTime: string;

  description: string;

  registrationDeadlineDate: string;
  registrationDeadlineTime: string;

  maxParticipants: string;
  externalLink: string;

  agenda: Record<number, AgendaRowErrors>;
  speakers: Record<number, SpeakerRowErrors>;
}

// ============================================================
// Message
// ============================================================

const message = ref<string>("");

// ============================================================
// Default form factory (used for init + reset)
// ============================================================

const createDefaultForm = (): FormState => ({
  coverImage: null,
  coverPreview: null,

  title: "",
  eventType: "job_fair",
  location: "",

  startDate: "",
  startTime: "",

  endDate: "",
  endTime: "",

  description: "",

  registrationDeadlineDate: "",
  registrationDeadlineTime: "",

  maxParticipants: "",
  externalLink: "",

  status: "draft",
});

const form = ref<FormState>(createDefaultForm());

// ============================================================
// Event Types
// ============================================================

const eventTypes = [
  { label: "Job Fair", value: "job_fair" },
  { label: "Reunion", value: "reunion" },
  { label: "Workshop", value: "workshop" },
  { label: "Seminar", value: "seminar" },
  { label: "Other", value: "other" },
];

// ============================================================
// Status options
// NOTE: "ongoing" / "completed" are excluded on purpose —
// those should be set automatically or via manual edit later,
// not at creation time.
// ============================================================

const statusOptions = [
  { label: "Save as Draft", value: "draft" },
  { label: "Publish Now", value: "published" },
];

// ============================================================
// Agenda
// ============================================================

const agenda = ref<AgendaItem[]>([{ time: "", title: "" }]);

const addAgendaItem = (): void => {
  agenda.value.push({ time: "", title: "" });
};

const removeAgendaItem = (index: number): void => {
  agenda.value.splice(index, 1);

  const newErrors: Record<number, AgendaRowErrors> = {};

  Object.entries(errors.agenda).forEach(([key, value]) => {
    const oldIndex = Number(key);

    if (oldIndex < index) {
      newErrors[oldIndex] = value;
    } else if (oldIndex > index) {
      newErrors[oldIndex - 1] = value;
    }
  });

  errors.agenda = newErrors;
};

// ============================================================
// Speakers
// ============================================================

const speakers = ref<Speaker[]>([{ name: "", title: "" }]);

const addSpeaker = (): void => {
  speakers.value.push({ name: "", title: "" });
};

const removeSpeaker = (index: number): void => {
  speakers.value.splice(index, 1);

  const newErrors: Record<number, SpeakerRowErrors> = {};

  Object.entries(errors.speakers).forEach(([key, value]) => {
    const oldIndex = Number(key);

    if (oldIndex < index) {
      newErrors[oldIndex] = value;
    } else if (oldIndex > index) {
      newErrors[oldIndex - 1] = value;
    }
  });

  errors.speakers = newErrors;
};

// ============================================================
// Cover Image
// ============================================================

const fileInput = ref<HTMLInputElement | null>(null);

const MAX_FILE_MB = 10;

const ALLOWED_TYPES = ["image/png", "image/jpeg", "image/gif"];

const triggerFileSelect = (): void => {
  fileInput.value?.click();
};

const processFile = (file: File | undefined): void => {
  if (!file) return;

  if (!ALLOWED_TYPES.includes(file.type)) {
    errors.coverImage = "Only PNG, JPG or GIF files are allowed.";
    return;
  }

  if (file.size > MAX_FILE_MB * 1024 * 1024) {
    errors.coverImage = `File must be under ${MAX_FILE_MB}MB.`;
    return;
  }

  errors.coverImage = "";

  if (form.value.coverPreview) {
    URL.revokeObjectURL(form.value.coverPreview);
  }

  form.value.coverImage = file;
  form.value.coverPreview = URL.createObjectURL(file);
};

const handleFileChange = (event: Event): void => {
  const target = event.target as HTMLInputElement;
  processFile(target.files?.[0]);
};

const handleDrop = (event: DragEvent): void => {
  event.preventDefault();
  processFile(event.dataTransfer?.files?.[0]);
};

const removeCoverImage = (): void => {
  if (form.value.coverPreview) {
    URL.revokeObjectURL(form.value.coverPreview);
  }

  form.value.coverImage = null;
  form.value.coverPreview = null;

  if (fileInput.value) fileInput.value.value = "";
};

// ============================================================
// Validation State
// ============================================================

const errors = reactive<ErrorsState>({
  coverImage: "",
  title: "",
  eventType: "",
  location: "",

  startDate: "",
  startTime: "",
  endDate: "",
  endTime: "",

  description: "",

  registrationDeadlineDate: "",
  registrationDeadlineTime: "",

  maxParticipants: "",
  externalLink: "",

  agenda: {},
  speakers: {},
});

// ============================================================
// Helpers
// ============================================================

const isValidUrl = (value: string): boolean => {
  try {
    new URL(value);
    return true;
  } catch {
    return false;
  }
};

// Combine a "YYYY-MM-DD" date and "HH:mm" time into a
// backend-friendly "YYYY-MM-DD HH:mm:00" string.
const combineDateTime = (date: string, time: string): string | null => {
  if (!date || !time) return null;
  return `${date} ${time}:00`;
};

const resetErrors = (): void => {
  errors.coverImage = "";
  errors.title = "";
  errors.eventType = "";
  errors.location = "";

  errors.startDate = "";
  errors.startTime = "";
  errors.endDate = "";
  errors.endTime = "";

  errors.description = "";

  errors.registrationDeadlineDate = "";
  errors.registrationDeadlineTime = "";

  errors.maxParticipants = "";
  errors.externalLink = "";

  errors.agenda = {};
  errors.speakers = {};

  message.value = "";
};

// ============================================================
// Validation
// ============================================================

const validate = (): boolean => {
  resetErrors();

  let isValid = true;

  const fail = (
    field: keyof Omit<ErrorsState, "agenda" | "speakers">,
    errorMessage: string,
  ): void => {
    errors[field] = errorMessage;
    isValid = false;
  };

  if (!form.value.title.trim()) {
    fail("title", "Event title is required.");
  } else if (form.value.title.trim().length < 3) {
    fail("title", "Title must be at least 3 characters.");
  }

  if (!form.value.eventType) {
    fail("eventType", "Please select an event type.");
  }

  if (!form.value.location.trim()) {
    fail("location", "Location is required.");
  }

  if (!form.value.startDate) {
    fail("startDate", "Start date is required.");
  } else {
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const startDate = new Date(`${form.value.startDate}T00:00:00`);

    if (startDate < today) {
      fail("startDate", "Start date cannot be in the past.");
    }
  }

  if (!form.value.startTime) {
    fail("startTime", "Start time is required.");
  }

  if (!form.value.endDate) {
    fail("endDate", "End date is required.");
  }

  if (!form.value.endTime) {
    fail("endTime", "End time is required.");
  }

  if (
    form.value.startDate &&
    form.value.startTime &&
    form.value.endDate &&
    form.value.endTime
  ) {
    const startDateTime = new Date(
      `${form.value.startDate}T${form.value.startTime}`,
    );
    const endDateTime = new Date(`${form.value.endDate}T${form.value.endTime}`);

    if (endDateTime <= startDateTime) {
      fail("endDate", "End date/time must be after start date/time.");
    }
  }

  if (!form.value.description.trim()) {
    fail("description", "Description is required.");
  } else if (form.value.description.trim().length < 20) {
    fail("description", "Description should be at least 20 characters.");
  }

  agenda.value.forEach((item: AgendaItem, index: number) => {
    const rowErrors: AgendaRowErrors = {};
    const touched = Boolean(item.time) || Boolean(item.title.trim());

    if (touched) {
      if (!item.time) rowErrors.time = "Time required.";
      if (!item.title.trim()) rowErrors.title = "Activity title required.";
    }

    if (Object.keys(rowErrors).length > 0) {
      errors.agenda[index] = rowErrors;
      isValid = false;
    }
  });

  speakers.value.forEach((speaker: Speaker, index: number) => {
    const rowErrors: SpeakerRowErrors = {};
    const touched =
      Boolean(speaker.name.trim()) || Boolean(speaker.title.trim());

    if (touched) {
      if (!speaker.name.trim()) rowErrors.name = "Speaker name required.";
      if (!speaker.title.trim())
        rowErrors.title = "Professional title required.";
    }

    if (Object.keys(rowErrors).length > 0) {
      errors.speakers[index] = rowErrors;
      isValid = false;
    }
  });

  const {
    registrationDeadlineDate,
    registrationDeadlineTime,
    maxParticipants,
    externalLink,
  } = form.value;

  if (registrationDeadlineDate && !registrationDeadlineTime) {
    fail("registrationDeadlineTime", "Pick a deadline time.");
  }

  if (registrationDeadlineTime && !registrationDeadlineDate) {
    fail("registrationDeadlineDate", "Pick a deadline date.");
  }

  if (
    registrationDeadlineDate &&
    registrationDeadlineTime &&
    form.value.startDate &&
    form.value.startTime
  ) {
    const deadline = new Date(
      `${registrationDeadlineDate}T${registrationDeadlineTime}`,
    );
    const start = new Date(`${form.value.startDate}T${form.value.startTime}`);

    if (deadline >= start) {
      fail(
        "registrationDeadlineDate",
        "Registration deadline must be before the event starts.",
      );
    }
  }

  if (maxParticipants) {
    const capacity = Number(maxParticipants);

    if (Number.isNaN(capacity) || capacity <= 0) {
      fail(
        "maxParticipants",
        "Maximum participants must be a positive number.",
      );
    }
  }

  if (externalLink && !isValidUrl(externalLink)) {
    fail("externalLink", "Enter a valid URL (e.g. https://example.com).");
  }

  return isValid;
};

// ============================================================
// Build FormData
// ============================================================

const buildFormData = (): FormData => {
  const formData = new FormData();

  if (form.value.coverImage) {
    formData.append("banner_image", form.value.coverImage);
  }

  formData.append("title", form.value.title.trim());
  formData.append("event_type", form.value.eventType);
  formData.append("location", form.value.location.trim());
  formData.append("description", form.value.description.trim());
  formData.append("status", form.value.status);

  // --------------------------------------------------------
  // Combine date + time into single datetime fields so the
  // backend column (`start_date` / `end_date` datetime) gets
  // exactly one value, matching the EventItem interface.
  // --------------------------------------------------------

  const startDateTime = combineDateTime(
    form.value.startDate,
    form.value.startTime,
  );

  const endDateTime = combineDateTime(form.value.endDate, form.value.endTime);

  if (startDateTime) formData.append("start_date", startDateTime);
  if (endDateTime) formData.append("end_date", endDateTime);

  const registrationDeadline = combineDateTime(
    form.value.registrationDeadlineDate,
    form.value.registrationDeadlineTime,
  );

  if (registrationDeadline) {
    formData.append("registration_deadline", registrationDeadline);
  }

  if (form.value.maxParticipants) {
    formData.append("max_participants", form.value.maxParticipants);
  }

  if (form.value.externalLink.trim()) {
    formData.append("external_link", form.value.externalLink.trim());
  }

  // --------------------------------------------------------
  // Agenda — reindexed so Laravel always receives a
  // sequential array (0, 1, 2...), never gaps like (0, 2).
  // --------------------------------------------------------

  const filledAgenda = agenda.value.filter(
    (item) => item.time || item.title.trim(),
  );

  filledAgenda.forEach((item: AgendaItem, index: number) => {
    formData.append(`agenda[${index}][time]`, item.time);
    formData.append(`agenda[${index}][title]`, item.title.trim());
  });

  // --------------------------------------------------------
  // Speakers — same reindexing fix
  // --------------------------------------------------------

  const filledSpeakers = speakers.value.filter(
    (speaker) => speaker.name.trim() || speaker.title.trim(),
  );

  filledSpeakers.forEach((speaker: Speaker, index: number) => {
    formData.append(`speakers[${index}][name]`, speaker.name.trim());
    formData.append(`speakers[${index}][title]`, speaker.title.trim());
  });

  return formData;
};

// ============================================================
// Submit
// ============================================================

const isSubmitting = ref(false);

const handleSaveEvent = async (): Promise<void> => {
  message.value = "";

  if (!validate()) {
    await nextTick();

    document.querySelector(".field-error")?.scrollIntoView({
      behavior: "smooth",
      block: "center",
    });

    return;
  }

  const formData = buildFormData();

  isSubmitting.value = true;

  try {
    const res = await eventStore.addEvent(formData);

    emit("submitted", res.data.data);

    resetForm();
    close();
  } catch (error: any) {
    console.error("Failed to save event:", error);

    if (error?.response?.data?.errors) {
      console.error("Laravel Validation Errors:", error.response.data.errors);
    }

    message.value =
      error?.response?.data?.message ||
      "Failed to create event. Please try again.";
  } finally {
    isSubmitting.value = false;
  }
};

// ============================================================
// Reset / Close
// ============================================================

const resetForm = (): void => {
  if (form.value.coverPreview) {
    URL.revokeObjectURL(form.value.coverPreview);
  }

  form.value = createDefaultForm();
  agenda.value = [{ time: "", title: "" }];
  speakers.value = [{ name: "", title: "" }];

  if (fileInput.value) fileInput.value.value = "";

  resetErrors();
};

const close = (): void => {
  if (isSubmitting.value) return;
  emit("close");
};

onBeforeUnmount(() => {
  if (form.value.coverPreview) {
    URL.revokeObjectURL(form.value.coverPreview);
  }
});
</script>

<template>
  <!-- ====================================================== -->
  <!-- Overlay                                                -->
  <!-- ====================================================== -->

  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm"
    @click.self="close"
  >
    <!-- ==================================================== -->
    <!-- Modal                                                 -->
    <!-- ==================================================== -->

    <div
      class="flex max-h-[92vh] w-full max-w-6xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
    >
      <!-- ================================================== -->
      <!-- Header                                             -->
      <!-- ================================================== -->

      <div
        class="flex items-center justify-between border-b border-slate-200 px-6 py-4"
      >
        <div>
          <h2 class="text-base font-semibold text-slate-800">Add New Event</h2>

          <p class="mt-1 text-xs text-slate-500">Create a new event</p>
        </div>

        <button
          type="button"
          :disabled="isSubmitting"
          class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-500 disabled:cursor-not-allowed disabled:opacity-50"
          @click="close"
        >
          <CloseIcon />
        </button>
      </div>

      <!-- ================================================== -->
      <!-- Error Message                                      -->
      <!-- ================================================== -->

      <div
        v-if="message"
        class="mx-6 mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-600"
      >
        {{ message }}
      </div>

      <!-- ================================================== -->
      <!-- Form                                                -->
      <!-- ================================================== -->

      <form
        class="flex min-h-0 flex-1 flex-col"
        @submit.prevent="handleSaveEvent"
      >
        <!-- ================================================= -->
        <!-- Scroll Area                                       -->
        <!-- ================================================= -->

        <div class="flex flex-1 flex-col gap-5 overflow-y-auto px-6 py-4">
          <!-- ================================================= -->
          <!-- Cover Image                                       -->
          <!-- ================================================= -->

          <div class="flex flex-col gap-2">
            <label class="text-sm font-medium text-slate-700">
              Cover Image
            </label>

            <div
              class="relative flex h-40 cursor-pointer flex-col items-center justify-center gap-2 overflow-hidden rounded-xl border-2 border-dashed transition sm:h-48 lg:h-56"
              :class="
                errors.coverImage
                  ? 'border-red-300 bg-red-50/40'
                  : 'border-slate-200 hover:border-primary/50 hover:bg-slate-50'
              "
              @click="triggerFileSelect"
              @dragover.prevent
              @drop="handleDrop"
            >
              <!-- Preview -->

              <img
                v-if="form.coverPreview"
                :src="form.coverPreview"
                alt="Cover preview"
                class="absolute inset-0 h-full w-full object-cover"
              />

              <!-- Placeholder -->

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

                <p class="px-4 text-center text-sm text-slate-600">
                  <span class="font-medium text-slate-800">
                    Upload a file
                  </span>

                  or drag and drop
                </p>

                <p class="text-xs text-slate-400">PNG, JPG, GIF up to 10MB</p>
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

          <!-- ================================================= -->
          <!-- Event Title                                       -->
          <!-- ================================================= -->

          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">
              Event Title
              <span class="text-red-500">*</span>
            </label>

            <input
              v-model="form.title"
              type="text"
              placeholder="e.g. Annual Alumni Job Fair"
              class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2"
              :class="
                errors.title
                  ? 'border-red-400 focus:ring-red-100 field-error'
                  : 'border-slate-200 focus:border-primary focus:ring-primary/20'
              "
            />

            <p v-if="errors.title" class="text-xs text-red-500">
              {{ errors.title }}
            </p>
          </div>

          <!-- ================================================= -->
          <!-- Event Type + Location                            -->
          <!-- ================================================= -->

          <div class="grid grid-cols-1 gap-4 md:grid-cols-2 sm:gap-6">
            <!-- Event Type -->

            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-medium text-slate-700">
                Event Type
                <span class="text-red-500">*</span>
              </label>

              <select
                v-model="form.eventType"
                class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2"
                :class="
                  errors.eventType
                    ? 'border-red-400 focus:ring-red-100 field-error'
                    : 'border-slate-200 focus:border-primary focus:ring-primary/20'
                "
              >
                <option
                  v-for="type in eventTypes"
                  :key="type.value"
                  :value="type.value"
                >
                  {{ type.label }}
                </option>
              </select>

              <p v-if="errors.eventType" class="text-xs text-red-500">
                {{ errors.eventType }}
              </p>
            </div>

            <!-- Location -->

            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-medium text-slate-700">
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
                  class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400"
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
                  class="w-full rounded-lg border py-2 pl-9 pr-3 text-sm focus:outline-none focus:ring-2"
                  :class="
                    errors.location
                      ? 'border-red-400 focus:ring-red-100 field-error'
                      : 'border-slate-200 focus:border-primary focus:ring-primary/20'
                  "
                />
              </div>

              <p v-if="errors.location" class="text-xs text-red-500">
                {{ errors.location }}
              </p>
            </div>
          </div>

          <!-- ================================================= -->
          <!-- Start Date + Start Time                          -->
          <!-- ================================================= -->

          <div class="grid grid-cols-1 gap-4 md:grid-cols-2 sm:gap-6">
            <!-- Start Date -->

            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-medium text-slate-700">
                Start Date
                <span class="text-red-500">*</span>
              </label>

              <input
                v-model="form.startDate"
                type="date"
                class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2"
                :class="
                  errors.startDate
                    ? 'border-red-400 focus:ring-red-100 field-error'
                    : 'border-slate-200 focus:border-primary focus:ring-primary/20'
                "
              />

              <p v-if="errors.startDate" class="text-xs text-red-500">
                {{ errors.startDate }}
              </p>
            </div>

            <!-- Start Time -->

            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-medium text-slate-700">
                Start Time
                <span class="text-red-500">*</span>
              </label>

              <input
                v-model="form.startTime"
                type="time"
                class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2"
                :class="
                  errors.startTime
                    ? 'border-red-400 focus:ring-red-100 field-error'
                    : 'border-slate-200 focus:border-primary focus:ring-primary/20'
                "
              />

              <p v-if="errors.startTime" class="text-xs text-red-500">
                {{ errors.startTime }}
              </p>
            </div>
          </div>

          <!-- ================================================= -->
          <!-- End Date + End Time                              -->
          <!-- ================================================= -->

          <div class="grid grid-cols-1 gap-4 md:grid-cols-2 sm:gap-6">
            <!-- End Date -->

            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-medium text-slate-700">
                End Date
                <span class="text-red-500">*</span>
              </label>

              <input
                v-model="form.endDate"
                type="date"
                class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2"
                :class="
                  errors.endDate
                    ? 'border-red-400 focus:ring-red-100 field-error'
                    : 'border-slate-200 focus:border-primary focus:ring-primary/20'
                "
              />

              <p v-if="errors.endDate" class="text-xs text-red-500">
                {{ errors.endDate }}
              </p>
            </div>

            <!-- End Time -->

            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-medium text-slate-700">
                End Time
                <span class="text-red-500">*</span>
              </label>

              <input
                v-model="form.endTime"
                type="time"
                class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2"
                :class="
                  errors.endTime
                    ? 'border-red-400 focus:ring-red-100 field-error'
                    : 'border-slate-200 focus:border-primary focus:ring-primary/20'
                "
              />

              <p v-if="errors.endTime" class="text-xs text-red-500">
                {{ errors.endTime }}
              </p>
            </div>
          </div>

          <!-- ================================================= -->
          <!-- Description                                      -->
          <!-- ================================================= -->

          <div class="flex flex-col gap-1.5">
            <label class="text-sm font-medium text-slate-700">
              Description
              <span class="text-red-500">*</span>
            </label>

            <div
              class="overflow-hidden rounded-lg border"
              :class="
                errors.description
                  ? 'border-red-400 field-error'
                  : 'border-slate-200'
              "
            >
              <div
                class="flex items-center gap-3 border-b border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-500"
              >
                <button type="button" class="font-bold hover:text-slate-800">
                  B
                </button>

                <button type="button" class="italic hover:text-slate-800">
                  I
                </button>

                <button type="button" class="hover:text-slate-800">≡</button>

                <button type="button" class="hover:text-slate-800">🔗</button>
              </div>

              <textarea
                v-model="form.description"
                rows="4"
                placeholder="Detailed event description..."
                class="w-full resize-none px-3 py-2 text-sm focus:outline-none"
              ></textarea>
            </div>

            <div class="flex items-center justify-between">
              <p v-if="errors.description" class="text-xs text-red-500">
                {{ errors.description }}
              </p>

              <p class="ml-auto text-xs text-slate-400">
                {{ form.description.length }}
                chars
              </p>
            </div>
          </div>

          <!-- ================================================= -->
          <!-- Agenda                                           -->
          <!-- ================================================= -->

          <div class="flex flex-col gap-3">
            <div class="flex items-center justify-between">
              <label class="text-sm font-semibold text-slate-800">
                Agenda
              </label>

              <button
                type="button"
                @click="addAgendaItem"
                class="text-xs font-medium text-primary hover:underline sm:text-sm"
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
                class="flex flex-col items-stretch gap-2 sm:flex-row sm:items-center"
              >
                <input
                  v-model="item.time"
                  type="time"
                  class="w-full shrink-0 rounded-lg border px-2 py-2 text-xs focus:outline-none focus:ring-2 sm:w-32 sm:text-sm"
                  :class="
                    errors.agenda[index]?.time
                      ? 'border-red-400 focus:ring-red-100'
                      : 'border-slate-200 focus:border-primary focus:ring-primary/20'
                  "
                />

                <div class="flex min-w-0 flex-1 items-center gap-2">
                  <input
                    v-model="item.title"
                    type="text"
                    placeholder="Activity Title"
                    class="min-w-0 flex-1 rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2"
                    :class="
                      errors.agenda[index]?.title
                        ? 'border-red-400 focus:ring-red-100'
                        : 'border-slate-200 focus:border-primary focus:ring-primary/20'
                    "
                  />

                  <button
                    type="button"
                    @click="removeAgendaItem(index)"
                    class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-slate-50 hover:text-red-500"
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
                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m0 0a48.108 48.108 0 00-3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                      />
                    </svg>
                  </button>
                </div>
              </div>

              <p
                v-if="errors.agenda[index]?.time || errors.agenda[index]?.title"
                class="text-xs text-red-500"
              >
                {{ errors.agenda[index]?.time || errors.agenda[index]?.title }}
              </p>
            </div>
          </div>

          <!-- ================================================= -->
          <!-- Speakers                                         -->
          <!-- ================================================= -->

          <div class="flex flex-col gap-3">
            <div class="flex items-center justify-between">
              <label class="text-sm font-semibold text-slate-800">
                Speakers
              </label>

              <button
                type="button"
                @click="addSpeaker"
                class="text-xs font-medium text-primary hover:underline sm:text-sm"
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
                class="flex flex-col items-stretch gap-2 sm:flex-row sm:items-center"
              >
                <!-- Speaker Icon -->

                <div
                  class="hidden size-9 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-slate-100 text-slate-400 sm:flex"
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

                <div class="flex min-w-0 flex-1 flex-col gap-2 sm:flex-row">
                  <input
                    v-model="speaker.name"
                    type="text"
                    placeholder="Speaker Name"
                    class="min-w-0 flex-1 rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2"
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
                    class="min-w-0 flex-1 rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2"
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
                  class="flex size-9 shrink-0 items-center justify-center self-end rounded-lg border border-slate-200 text-slate-400 hover:bg-slate-50 hover:text-red-500 sm:self-auto"
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
                      d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.682-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                    />
                  </svg>
                </button>
              </div>

              <p
                v-if="
                  errors.speakers[index]?.name || errors.speakers[index]?.title
                "
                class="text-xs text-red-500"
              >
                {{
                  errors.speakers[index]?.name || errors.speakers[index]?.title
                }}
              </p>
            </div>
          </div>

          <!-- ================================================= -->
          <!-- Registration Settings                            -->
          <!-- ================================================= -->

          <div class="mt-2 flex flex-col gap-4 border-t border-slate-200 pt-5">
            <label class="text-sm font-semibold text-slate-800">
              Registration Settings
            </label>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 sm:gap-6">
              <!-- Deadline -->

              <div class="flex flex-col gap-1.5">
                <label class="text-xs text-slate-500">
                  Registration Deadline
                </label>

                <div class="flex gap-2">
                  <input
                    v-model="form.registrationDeadlineDate"
                    type="date"
                    class="w-1/2 rounded-lg border px-2 py-2 text-sm focus:outline-none focus:ring-2"
                    :class="
                      errors.registrationDeadlineDate
                        ? 'border-red-400 focus:ring-red-100'
                        : 'border-slate-200 focus:border-primary focus:ring-primary/20'
                    "
                  />

                  <input
                    v-model="form.registrationDeadlineTime"
                    type="time"
                    class="w-1/2 rounded-lg border px-2 py-2 text-sm focus:outline-none focus:ring-2"
                    :class="
                      errors.registrationDeadlineTime
                        ? 'border-red-400 focus:ring-red-100'
                        : 'border-slate-200 focus:border-primary focus:ring-primary/20'
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

              <!-- Max Participants -->

              <div class="flex flex-col gap-1.5">
                <label class="text-xs text-slate-500"> Capacity Limit </label>

                <input
                  v-model="form.maxParticipants"
                  type="number"
                  min="1"
                  placeholder="e.g. 100"
                  class="w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2"
                  :class="
                    errors.maxParticipants
                      ? 'border-red-400 focus:ring-red-100'
                      : 'border-slate-200 focus:border-primary focus:ring-primary/20'
                  "
                />

                <p v-if="errors.maxParticipants" class="text-xs text-red-500">
                  {{ errors.maxParticipants }}
                </p>
              </div>
            </div>

            <!-- External Link -->

            <div class="flex flex-col gap-1.5">
              <label class="text-xs text-slate-500">
                External Registration Link (Optional)
              </label>

              <div class="relative">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  class="absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400"
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
                  class="w-full rounded-lg border py-2 pl-9 pr-3 text-sm focus:outline-none focus:ring-2"
                  :class="
                    errors.externalLink
                      ? 'border-red-400 focus:ring-red-100'
                      : 'border-slate-200 focus:border-primary focus:ring-primary/20'
                  "
                />
              </div>

              <p v-if="errors.externalLink" class="text-xs text-red-500">
                {{ errors.externalLink }}
              </p>
            </div>

            <!-- Status -->
            <div class="flex flex-col gap-1.5">
              <label class="text-sm font-medium text-slate-700">Status</label>

              <select
                v-model="form.status"
                class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
              >
                <option
                  v-for="opt in statusOptions"
                  :key="opt.value"
                  :value="opt.value"
                >
                  {{ opt.label }}
                </option>
              </select>

              <p class="text-xs text-slate-400">
                Draft events are hidden from alumni until published.
              </p>
            </div>
          </div>
        </div>

        <!-- ================================================= -->
        <!-- Footer                                            -->
        <!-- ================================================= -->

        <div
          class="flex items-center justify-end gap-3 border-t border-slate-200 bg-white px-6 py-4"
        >
          <button
            type="button"
            :disabled="isSubmitting"
            class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
            @click="close"
          >
            Cancel
          </button>

          <button
            type="submit"
            :disabled="isSubmitting"
            class="flex min-w-28 items-center justify-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-medium text-white transition hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-60"
          >
            <template v-if="isSubmitting">
              <span>Saving...</span>

              <SpannerIcon />
            </template>

            <template v-else>
              <span>Save Event</span>
            </template>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.form-input {
  width: 100%;
  border: 1px solid rgb(203 213 225);
  border-radius: 0.5rem;
  padding: 0.625rem 0.75rem;
  font-size: 0.8125rem;
  color: rgb(51 65 85);
  background: white;
  outline: none;
  transition:
    border-color 0.15s ease,
    box-shadow 0.15s ease;
}

.form-input::placeholder {
  color: rgb(148 163 184);
}

.form-input:focus {
  border-color: rgb(59 130 246);
  box-shadow: 0 0 0 3px rgb(59 130 246 / 0.1);
}

.form-input:disabled {
  cursor: not-allowed;
  background: rgb(241 245 249);
  color: rgb(148 163 184);
}

.form-input-error {
  border-color: rgb(239 68 68);
}

.form-input-error:focus {
  border-color: rgb(239 68 68);
  box-shadow: 0 0 0 3px rgb(239 68 68 / 0.1);
}

.form-error {
  margin-top: 0.25rem;
  font-size: 0.6875rem;
  line-height: 1rem;
  color: rgb(239 68 68);
}
</style>
