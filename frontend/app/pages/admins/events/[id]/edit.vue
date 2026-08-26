<script setup lang="ts">
definePageMeta({
  layout: "admin",
});

const route = useRoute();
const router = useRouter();
const eventStore = useEventStore();

/*
|--------------------------------------------------------------------------
| Event ID
|--------------------------------------------------------------------------
*/

const eventId = computed(() => Number(route.params.id));

/*
|--------------------------------------------------------------------------
| Form State
|--------------------------------------------------------------------------
*/

const form = ref({
  title: "",
  description: "",
  eventType: "other",
  location: "",
  date: "",
  startTime: "",
  endTime: "",
  registrationDeadlineDate: "",
  registrationDeadlineTime: "",
  maxParticipants: null as number | null,
  externalLink: "",
  status: "draft",
});

/*
|--------------------------------------------------------------------------
| Event Types
|--------------------------------------------------------------------------
*/

const eventTypes = [
  {
    value: "job_fair",
    label: "Job Fair",
  },
  {
    value: "reunion",
    label: "Alumni Reunion",
  },
  {
    value: "workshop",
    label: "Workshop",
  },
  {
    value: "seminar",
    label: "Seminar",
  },
  {
    value: "other",
    label: "Other",
  },
];

/*
|--------------------------------------------------------------------------
| Status
|--------------------------------------------------------------------------
*/

const statuses = [
  {
    value: "draft",
    label: "Draft",
  },
  {
    value: "published",
    label: "Published",
  },
  {
    value: "ongoing",
    label: "Ongoing",
  },
  {
    value: "completed",
    label: "Completed",
  },
  {
    value: "cancelled",
    label: "Cancelled",
  },
];

/*
|--------------------------------------------------------------------------
| Agenda
|--------------------------------------------------------------------------
*/

interface AgendaItem {
  time: string;
  title: string;
}

const agenda = ref<AgendaItem[]>([]);

const addAgenda = () => {
  agenda.value.push({
    time: "09:00",
    title: "",
  });
};

const removeAgenda = (index: number) => {
  agenda.value.splice(index, 1);
};

/*
|--------------------------------------------------------------------------
| Speakers
|--------------------------------------------------------------------------
*/

interface SpeakerItem {
  name: string;
  professionalTitle: string;
}

const speakers = ref<SpeakerItem[]>([]);

const addSpeaker = () => {
  speakers.value.push({
    name: "",
    professionalTitle: "",
  });
};

const removeSpeaker = (index: number) => {
  speakers.value.splice(index, 1);
};

/*
|--------------------------------------------------------------------------
| Banner Image
|--------------------------------------------------------------------------
*/

const imageInput = ref<HTMLInputElement | null>(null);
const imagePreview = ref("");
const selectedImage = ref<File | null>(null);

const handleImageChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];

  if (!file) {
    return;
  }

  /*
  |--------------------------------------------------------------------------
  | Validate image size
  |--------------------------------------------------------------------------
  */

  if (file.size > 5 * 1024 * 1024) {
    alert("Image size must not exceed 5MB.");

    if (imageInput.value) {
      imageInput.value.value = "";
    }

    return;
  }

  /*
  |--------------------------------------------------------------------------
  | Validate image type
  |--------------------------------------------------------------------------
  */

  const allowedTypes = ["image/jpeg", "image/png", "image/gif"];

  if (!allowedTypes.includes(file.type)) {
    alert("Only JPG, JPEG, PNG or GIF images are allowed.");

    if (imageInput.value) {
      imageInput.value.value = "";
    }

    return;
  }

  selectedImage.value = file;

  /*
  |--------------------------------------------------------------------------
  | Revoke previous object URL
  |--------------------------------------------------------------------------
  */

  if (imagePreview.value.startsWith("blob:")) {
    URL.revokeObjectURL(imagePreview.value);
  }

  imagePreview.value = URL.createObjectURL(file);
};

const removeImage = () => {
  if (imagePreview.value.startsWith("blob:")) {
    URL.revokeObjectURL(imagePreview.value);
  }

  selectedImage.value = null;
  imagePreview.value = "";

  if (imageInput.value) {
    imageInput.value.value = "";
  }
};

const openImagePicker = () => {
  imageInput.value?.click();
};

/*
|--------------------------------------------------------------------------
| Loading
|--------------------------------------------------------------------------
*/

const isLoading = ref(false);
const isSaving = ref(false);

/*
|--------------------------------------------------------------------------
| Date Helpers
|--------------------------------------------------------------------------
*/

/**
 * Convert Laravel datetime to YYYY-MM-DD
 *
 * Supports:
 * 2026-08-22 08:00:00
 * 2026-08-22T08:00:00
 * 2026-08-22T08:00:00.000000Z
 */
const extractDate = (value: string | null | undefined): string => {
  if (!value) {
    return "";
  }

  const match = String(value).match(/^(\d{4}-\d{2}-\d{2})/);

  return match?.[1] ?? "";
};

/**
 * Convert Laravel datetime to HH:mm
 *
 * Supports:
 * 2026-08-22 08:00:00
 * 2026-08-22T08:00:00
 * 2026-08-22T08:00:00.000000Z
 */
const extractTime = (value: string | null | undefined): string => {
  if (!value) {
    return "";
  }

  const match = String(value).match(/[T\s](\d{2}):(\d{2})/);

  if (!match) {
    return "";
  }

  return `${match[1]}:${match[2]}`;
};

/*
|--------------------------------------------------------------------------
| Build Laravel DateTime
|--------------------------------------------------------------------------
*/

const buildDateTime = (date: string, time: string): string | null => {
  if (!date || !time) {
    return null;
  }

  return `${date} ${time}:00`;
};

/*
|--------------------------------------------------------------------------
| Compare DateTime Strings
|--------------------------------------------------------------------------
|
| Because format is:
|
| YYYY-MM-DD HH:mm:ss
|
| Lexical comparison works correctly.
|
*/

const isEndDateTimeValid = (startDate: string, endDate: string): boolean => {
  if (!startDate || !endDate) {
    return false;
  }

  return endDate >= startDate;
};

/*
|--------------------------------------------------------------------------
| Load Event
|--------------------------------------------------------------------------
*/

const handleDataAPI = async (id: number) => {
  if (!id) {
    return;
  }

  isLoading.value = true;

  try {
    const res = await eventStore.getEventById(id);

    const event = res.data;

    // console.log("Event API:", event);

    /*
    |--------------------------------------------------------------------------
    | Basic Information
    |--------------------------------------------------------------------------
    */

    form.value.title = event.title ?? "";
    form.value.description = event.description ?? "";
    form.value.eventType = event.event_type ?? "other";
    form.value.location = event.location ?? "";
    form.value.status = event.status ?? "draft";

    /*
    |--------------------------------------------------------------------------
    | Start Date / Time
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | Do not use:
    |
    | new Date(event.start_date)
    |
    | because timezone can shift the date.
    |
    */

    if (event.start_date) {
      form.value.date = extractDate(event.start_date);
      form.value.startTime = extractTime(event.start_date);
    } else {
      form.value.date = "";
      form.value.startTime = "";
    }

    /*
    |--------------------------------------------------------------------------
    | End Date / Time
    |--------------------------------------------------------------------------
    |
    | Current UI uses ONE event date.
    |
    | Therefore end_date uses the same date + end time.
    |
    */

    if (event.end_date) {
      const endDate = extractDate(event.end_date);
      const endTime = extractTime(event.end_date);

      /*
      |--------------------------------------------------------------------------
      | If backend end date is different from start date
      |--------------------------------------------------------------------------
      |
      | Since current form only has one date field,
      | use the end date only if it is actually different.
      |
      */

      if (endDate && form.value.date && endDate !== form.value.date) {
        console.warn(
          "Event has different start/end dates:",
          form.value.date,
          endDate,
        );
      }

      form.value.endTime = endTime;
    } else {
      form.value.endTime = "";
    }

    /*
    |--------------------------------------------------------------------------
    | Registration
    |--------------------------------------------------------------------------
    */

    form.value.maxParticipants = event.max_participants ?? null;

    form.value.externalLink = event.external_link ?? "";

    /*
    |--------------------------------------------------------------------------
    | Registration Deadline
    |--------------------------------------------------------------------------
    */

    if (event.registration_deadline) {
      form.value.registrationDeadlineDate = extractDate(
        event.registration_deadline,
      );

      form.value.registrationDeadlineTime = extractTime(
        event.registration_deadline,
      );
    } else {
      form.value.registrationDeadlineDate = "";
      form.value.registrationDeadlineTime = "";
    }

    /*
    |--------------------------------------------------------------------------
    | Banner Image
    |--------------------------------------------------------------------------
    */

    imagePreview.value = event.banner_image_url ?? event.banner_image ?? "";

    /*
    |--------------------------------------------------------------------------
    | Agenda
    |--------------------------------------------------------------------------
    */

    agenda.value = (event.agendas ?? []).map((item: any) => ({
      time: item.time ? String(item.time).substring(0, 5) : "",
      title: item.title ?? "",
    }));

    /*
    |--------------------------------------------------------------------------
    | Speakers
    |--------------------------------------------------------------------------
    */

    speakers.value = (event.speakers ?? []).map((item: any) => ({
      name: item.name ?? "",
      professionalTitle: item.professional_title ?? "",
    }));

    /*
    |--------------------------------------------------------------------------
    | Debug
    |--------------------------------------------------------------------------
    */

    // console.log("Loaded Form:", {
    //   date: form.value.date,
    //   startTime: form.value.startTime,
    //   endTime: form.value.endTime,
    //   startDate: buildDateTime(form.value.date, form.value.startTime),
    //   endDate: buildDateTime(form.value.date, form.value.endTime),
    // });
  } catch (error: any) {
    console.error("Failed to load event:", error?.response?.data ?? error);
  } finally {
    isLoading.value = false;
  }
};

/*
|--------------------------------------------------------------------------
| Build FormData
|--------------------------------------------------------------------------
*/

const buildFormData = () => {
  const data = new FormData();

  /*
  |--------------------------------------------------------------------------
  | Basic Information
  |--------------------------------------------------------------------------
  */

  data.append("title", form.value.title.trim());

  data.append("description", form.value.description?.trim() || "");

  data.append("event_type", form.value.eventType);

  data.append("location", form.value.location?.trim() || "");

  /*
  |--------------------------------------------------------------------------
  | Date & Time
  |--------------------------------------------------------------------------
  */

  const startDate = buildDateTime(form.value.date, form.value.startTime);

  const endDate = buildDateTime(form.value.date, form.value.endTime);

  if (startDate) {
    data.append("start_date", startDate);
  }

  if (endDate) {
    data.append("end_date", endDate);
  }

  /*
  |--------------------------------------------------------------------------
  | Registration Deadline
  |--------------------------------------------------------------------------
  */

  const registrationDeadline = buildDateTime(
    form.value.registrationDeadlineDate,
    form.value.registrationDeadlineTime,
  );

  if (registrationDeadline) {
    data.append("registration_deadline", registrationDeadline);
  }

  /*
  |--------------------------------------------------------------------------
  | Registration Settings
  |--------------------------------------------------------------------------
  */

  if (
    form.value.maxParticipants !== null &&
    form.value.maxParticipants !== undefined
  ) {
    data.append("max_participants", String(form.value.maxParticipants));
  }

  /*
  |--------------------------------------------------------------------------
  | External Link
  |--------------------------------------------------------------------------
  */

  if (form.value.externalLink) {
    data.append("external_link", form.value.externalLink.trim());
  }

  /*
  |--------------------------------------------------------------------------
  | Status
  |--------------------------------------------------------------------------
  */

  data.append("status", form.value.status);

  /*
  |--------------------------------------------------------------------------
  | Banner Image
  |--------------------------------------------------------------------------
  */

  if (selectedImage.value) {
    data.append("banner_image", selectedImage.value);
  }

  /*
  |--------------------------------------------------------------------------
  | Agenda
  |--------------------------------------------------------------------------
  */

  agenda.value.forEach((item, index) => {
    const time = item.time?.trim();
    const title = item.title?.trim();

    if (!time || !title) {
      return;
    }

    data.append(`agenda[${index}][time]`, time);

    data.append(`agenda[${index}][title]`, title);
  });

  /*
  |--------------------------------------------------------------------------
  | Speakers
  |--------------------------------------------------------------------------
  */

  speakers.value.forEach((speaker, index) => {
    const name = speaker.name?.trim();
    const professionalTitle = speaker.professionalTitle?.trim();

    if (!name || !professionalTitle) {
      return;
    }

    data.append(`speakers[${index}][name]`, name);

    data.append(`speakers[${index}][professional_title]`, professionalTitle);
  });

  /*
  |--------------------------------------------------------------------------
  | Laravel Method Spoofing
  |--------------------------------------------------------------------------
  |
  | IMPORTANT when sending multipart/form-data.
  |
  | Laravel can have problems parsing PUT + FormData
  | depending on the request setup.
  |
  | Use POST + _method=PUT.
  |
  */

  data.append("_method", "PUT");

  return data;
};

/*
|--------------------------------------------------------------------------
| Client Validation
|--------------------------------------------------------------------------
*/

const validateForm = (): string | null => {
  /*
  |--------------------------------------------------------------------------
  | Required fields
  |--------------------------------------------------------------------------
  */

  if (!form.value.title.trim()) {
    return "Event title is required.";
  }

  if (!form.value.date) {
    return "Event date is required.";
  }

  if (!form.value.startTime) {
    return "Start time is required.";
  }

  if (!form.value.endTime) {
    return "End time is required.";
  }

  /*
  |--------------------------------------------------------------------------
  | Build DateTime
  |--------------------------------------------------------------------------
  */

  const startDate = buildDateTime(form.value.date, form.value.startTime);

  const endDate = buildDateTime(form.value.date, form.value.endTime);

  if (!startDate) {
    return "Start date and time are required.";
  }

  if (!endDate) {
    return "End date and time are required.";
  }

  /*
  |--------------------------------------------------------------------------
  | End must be >= Start
  |--------------------------------------------------------------------------
  */

  if (!isEndDateTimeValid(startDate, endDate)) {
    return (
      `End time must be after or equal to start time.\n\n` +
      `Start: ${startDate}\n` +
      `End: ${endDate}`
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Registration Deadline
  |--------------------------------------------------------------------------
  */

  const registrationDeadline = buildDateTime(
    form.value.registrationDeadlineDate,
    form.value.registrationDeadlineTime,
  );

  if (registrationDeadline && registrationDeadline > startDate) {
    return "Registration deadline must be before the event starts.";
  }

  /*
  |--------------------------------------------------------------------------
  | Maximum Participants
  |--------------------------------------------------------------------------
  */

  if (form.value.maxParticipants !== null && form.value.maxParticipants < 1) {
    return "Maximum participants must be at least 1.";
  }

  return null;
};

/*
|--------------------------------------------------------------------------
| Save / Update Event
|--------------------------------------------------------------------------
*/

const saveEvent = async () => {
  /*
  |--------------------------------------------------------------------------
  | Prevent double click
  |--------------------------------------------------------------------------
  */

  if (isSaving.value) {
    return;
  }

  /*
  |--------------------------------------------------------------------------
  | Start loading
  |--------------------------------------------------------------------------
  */

  isSaving.value = true;

  try {
    /*
    |--------------------------------------------------------------------------
    | Validate
    |--------------------------------------------------------------------------
    */

    const validationError = validateForm();

    if (validationError) {
      alert(validationError);
      return;
    }

    /*
    |--------------------------------------------------------------------------
    | Debug DateTime
    |--------------------------------------------------------------------------
    */

    const startDate = buildDateTime(form.value.date, form.value.startTime);

    const endDate = buildDateTime(form.value.date, form.value.endTime);

    // console.log("====================================");

    // console.log("Updating Event:", eventId.value);

    // console.log("Start Date:", startDate);

    // console.log("End Date:", endDate);

    // console.log("====================================");

    /*
    |--------------------------------------------------------------------------
    | Build FormData
    |--------------------------------------------------------------------------
    */

    const formData = buildFormData();

    /*
    |--------------------------------------------------------------------------
    | Debug FormData
    |--------------------------------------------------------------------------
    */

    // for (const [key, value] of formData.entries()) {
    //   console.log("FormData:", key, value);
    // }

    /*
    |--------------------------------------------------------------------------
    | Update API
    |--------------------------------------------------------------------------
    */

    await eventStore.updateEvent(eventId.value, formData);

    /*
    |--------------------------------------------------------------------------
    | Success
    |--------------------------------------------------------------------------
    */

    alert("Event updated successfully.");

    await navigateTo(`/admins/events/${eventId.value}`);
  } catch (error: any) {
    /*
    |--------------------------------------------------------------------------
    | API Error
    |--------------------------------------------------------------------------
    */

    console.error("Failed to update event:", error?.response?.data ?? error);

    const responseData = error?.response?.data;

    /*
    |--------------------------------------------------------------------------
    | Laravel Validation Errors
    |--------------------------------------------------------------------------
    */

    if (responseData?.errors) {
      const messages: string[] = [];

      Object.values(responseData.errors).forEach((value: any) => {
        if (Array.isArray(value)) {
          messages.push(...value);
        }
      });

      alert(
        messages.length
          ? messages.join("\n")
          : (responseData.message ?? "Failed to update event."),
      );

      return;
    }

    /*
    |--------------------------------------------------------------------------
    | General Error
    |--------------------------------------------------------------------------
    */

    alert(responseData?.message ?? error?.message ?? "Failed to update event.");
  } finally {
    isSaving.value = false;
  }
};

/*
|--------------------------------------------------------------------------
| Cancel
|--------------------------------------------------------------------------
*/

const cancelEdit = () => {
  router.back();
};

/*
|--------------------------------------------------------------------------
| Mounted
|--------------------------------------------------------------------------
*/

onMounted(() => {
  handleDataAPI(eventId.value);
});
</script>
<template>
  <div class="w-full overflow-hidden rounded-2xl bg-gray-50">
    <!-- ===================================================== -->
    <!-- HEADER -->
    <!-- ===================================================== -->

    <header class="border-b border-gray-200 bg-white">
      <div class="px-4 py-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <div class="mt-2 mb-4 flex items-center gap-2 text-xs text-gray-500">
          <NuxtLink to="/admins/events" class="transition hover:text-gray-900">
            Events
          </NuxtLink>

          <svg
            class="h-3.5 w-3.5 text-gray-400"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.24 4.24a.75.75 0 0 1 0 1.06l-4.24 4.24a.75.75 0 0 1-1.08 0Z"
              clip-rule="evenodd"
            />
          </svg>

          <NuxtLink
            :to="`/admins/events/${eventId}`"
            class="transition hover:text-gray-900"
          >
            Event Detail
          </NuxtLink>

          <svg
            class="h-3.5 w-3.5 text-gray-400"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.24 4.24a.75.75 0 0 1 0 1.06l-4.24 4.24a.75.75 0 0 1-1.08 0Z"
              clip-rule="evenodd"
            />
          </svg>

          <span class="font-medium text-gray-700"> Edit Event </span>
        </div>

        <!-- Title + Actions -->
        <div
          class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
          <div>
            <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">
              Edit Event
            </h1>

            <p class="mt-1 text-xs text-gray-500">
              Update event information, schedule, speakers and registration.
            </p>
          </div>

          <div class="flex items-center gap-2">
            <button
              type="button"
              class="h-9 rounded-md border border-gray-300 bg-white px-4 text-xs font-medium text-gray-700 transition hover:bg-gray-50"
              @click="cancelEdit"
            >
              Cancel
            </button>

            <button
              type="button"
              :disabled="isSaving || isLoading"
              class="inline-flex h-9 items-center gap-2 rounded-md bg-gray-900 px-4 text-xs font-medium text-white transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-60"
              @click="saveEvent"
            >
              <svg
                v-if="!isSaving"
                class="h-4 w-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M5 4h11l3 3v13H5V4Z"
                />
                <path stroke-linecap="round" d="M8 4v6h8V4M8 20v-6h8v6" />
              </svg>

              <svg
                v-else
                class="h-4 w-4 animate-spin"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <circle cx="12" cy="12" r="9" class="opacity-25" />

                <path d="M21 12a9 9 0 0 1-9 9" class="opacity-90" />
              </svg>

              {{ isSaving ? "Saving..." : "Save Changes" }}
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- ===================================================== -->
    <!-- LOADING -->
    <!-- ===================================================== -->

    <div v-if="isLoading" class="flex min-h-125 items-center justify-center">
      <div class="flex flex-col items-center gap-3">
        <svg
          class="h-8 w-8 animate-spin text-gray-700"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <circle cx="12" cy="12" r="9" class="opacity-25" />

          <path d="M21 12a9 9 0 0 1-9 9" class="opacity-90" />
        </svg>

        <p class="text-xs text-gray-500">Loading event...</p>
      </div>
    </div>

    <!-- ===================================================== -->
    <!-- CONTENT -->
    <!-- ===================================================== -->

    <main v-else class="px-4 py-6 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_340px]">
        <!-- ================================================= -->
        <!-- LEFT -->
        <!-- ================================================= -->

        <div class="min-w-0 space-y-6">
          <!-- ================================================= -->
          <!-- BASIC INFORMATION -->
          <!-- ================================================= -->

          <section
            class="overflow-hidden rounded-xl border border-gray-200 bg-white"
          >
            <div class="border-b border-gray-100 px-5 py-4">
              <div class="flex items-center gap-3">
                <div
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100"
                >
                  <svg
                    class="h-4 w-4 text-gray-700"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                  >
                    <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h10" />
                  </svg>
                </div>

                <div>
                  <h2 class="text-sm font-semibold text-gray-900">
                    Basic Information
                  </h2>

                  <p class="mt-0.5 text-[10px] text-gray-400">
                    General information about the event
                  </p>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">
              <!-- Title -->
              <div class="md:col-span-2">
                <label class="mb-1.5 block text-xs font-medium text-gray-700">
                  Event Title
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model="form.title"
                  type="text"
                  placeholder="Enter event title"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- Event Type -->
              <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-700">
                  Event Type
                  <span class="text-red-500">*</span>
                </label>

                <select
                  v-model="form.eventType"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                >
                  <option
                    v-for="type in eventTypes"
                    :key="type.value"
                    :value="type.value"
                  >
                    {{ type.label }}
                  </option>
                </select>
              </div>

              <!-- Status -->
              <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-700">
                  Status
                </label>

                <select
                  v-model="form.status"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                >
                  <option
                    v-for="status in statuses"
                    :key="status.value"
                    :value="status.value"
                  >
                    {{ status.label }}
                  </option>
                </select>
              </div>

              <!-- Description -->
              <div class="md:col-span-2">
                <label class="mb-1.5 block text-xs font-medium text-gray-700">
                  Description
                  <span class="text-red-500">*</span>
                </label>

                <textarea
                  v-model="form.description"
                  rows="7"
                  placeholder="Describe the event..."
                  class="w-full resize-y rounded-md border border-gray-300 bg-white px-3 py-2.5 text-xs leading-6 text-gray-900 outline-none placeholder:text-gray-400 focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />

                <p class="mt-1 text-[10px] text-gray-400">
                  Provide a clear description of the event.
                </p>
              </div>
            </div>
          </section>

          <!-- ================================================= -->
          <!-- DATE & LOCATION -->
          <!-- ================================================= -->

          <section
            class="overflow-hidden rounded-xl border border-gray-200 bg-white"
          >
            <div class="border-b border-gray-100 px-5 py-4">
              <div class="flex items-center gap-3">
                <div
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100"
                >
                  <svg
                    class="h-4 w-4 text-gray-700"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                  >
                    <rect x="4" y="5" width="16" height="15" rx="2" />

                    <path d="M8 3v4M16 3v4M4 9h16" stroke-linecap="round" />
                  </svg>
                </div>

                <div>
                  <h2 class="text-sm font-semibold text-gray-900">
                    Date, Time & Location
                  </h2>

                  <p class="mt-0.5 text-[10px] text-gray-400">
                    When and where the event will take place
                  </p>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">
              <!-- Event Date -->
              <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-700">
                  Event Date
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model="form.date"
                  type="date"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- Location -->
              <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-700">
                  Location
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model="form.location"
                  type="text"
                  placeholder="Event location"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- Start Time -->
              <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-700">
                  Start Time
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model="form.startTime"
                  type="time"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- End Time -->
              <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-700">
                  End Time
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model="form.endTime"
                  type="time"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>
            </div>
          </section>

          <!-- ================================================= -->
          <!-- REGISTRATION -->
          <!-- ================================================= -->

          <section
            class="overflow-hidden rounded-xl border border-gray-200 bg-white"
          >
            <div class="border-b border-gray-100 px-5 py-4">
              <div class="flex items-center gap-3">
                <div
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100"
                >
                  <svg
                    class="h-4 w-4 text-gray-700"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                  >
                    <circle cx="9" cy="8" r="3" />

                    <path
                      stroke-linecap="round"
                      d="M3.5 19c.5-3.2 2.3-5 5.5-5s5 1.8 5.5 5"
                    />

                    <path stroke-linecap="round" d="M16 11a3 3 0 1 0 0-6" />

                    <path stroke-linecap="round" d="M16 14c2.3.4 3.8 2 4.2 5" />
                  </svg>
                </div>

                <div>
                  <h2 class="text-sm font-semibold text-gray-900">
                    Registration Settings
                  </h2>

                  <p class="mt-0.5 text-[10px] text-gray-400">
                    Configure registration and capacity
                  </p>
                </div>
              </div>
            </div>

            <div class="grid grid-cols-1 gap-5 p-5 md:grid-cols-2">
              <!-- Deadline Date -->
              <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-700">
                  Registration Deadline
                </label>

                <input
                  v-model="form.registrationDeadlineDate"
                  type="date"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- Deadline Time -->
              <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-700">
                  Deadline Time
                </label>

                <input
                  v-model="form.registrationDeadlineTime"
                  type="time"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- Max Participants -->
              <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-700">
                  Maximum Participants
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model.number="form.maxParticipants"
                  type="number"
                  min="1"
                  placeholder="100"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- External Link -->
              <div>
                <label class="mb-1.5 block text-xs font-medium text-gray-700">
                  External Registration Link
                </label>

                <input
                  v-model="form.externalLink"
                  type="url"
                  placeholder="https://example.com/register"
                  class="h-10 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />

                <p class="mt-1 text-[10px] text-gray-400">
                  Optional external registration URL.
                </p>
              </div>
            </div>
          </section>

          <!-- ================================================= -->
          <!-- AGENDA -->
          <!-- ================================================= -->

          <section
            class="overflow-hidden rounded-xl border border-gray-200 bg-white"
          >
            <div
              class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
            >
              <div class="flex items-center gap-3">
                <div
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100"
                >
                  <svg
                    class="h-4 w-4 text-gray-700"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                  >
                    <circle cx="12" cy="12" r="9" />

                    <path stroke-linecap="round" d="M12 7v5l3 2" />
                  </svg>
                </div>

                <div>
                  <h2 class="text-sm font-semibold text-gray-900">
                    Event Agenda
                  </h2>

                  <p class="mt-0.5 text-[10px] text-gray-400">
                    Add event activities and schedule
                  </p>
                </div>
              </div>

              <button
                type="button"
                class="inline-flex h-8 items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-3 text-[10px] font-medium text-gray-700 transition hover:bg-gray-50"
                @click="addAgenda"
              >
                <svg
                  class="h-3.5 w-3.5"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                </svg>

                Add Agenda
              </button>
            </div>

            <div class="p-5">
              <!-- Empty -->
              <div
                v-if="agenda.length === 0"
                class="rounded-lg border border-dashed border-gray-300 py-10 text-center"
              >
                <svg
                  class="mx-auto h-8 w-8 text-gray-300"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                >
                  <circle cx="12" cy="12" r="9" />

                  <path stroke-linecap="round" d="M12 8v8M8 12h8" />
                </svg>

                <p class="mt-2 text-xs font-medium text-gray-600">
                  No agenda items
                </p>

                <p class="mt-1 text-[10px] text-gray-400">
                  Add an agenda item for this event.
                </p>
              </div>

              <!-- Agenda Items -->
              <div v-else class="space-y-4">
                <div
                  v-for="(item, index) in agenda"
                  :key="index"
                  class="rounded-lg border border-gray-200 bg-gray-50 p-4"
                >
                  <div class="mb-4 flex items-center justify-between">
                    <span
                      class="text-[10px] font-semibold uppercase tracking-wider text-gray-400"
                    >
                      Agenda #{{ index + 1 }}
                    </span>

                    <button
                      type="button"
                      class="inline-flex h-7 w-7 items-center justify-center rounded-md text-gray-400 transition hover:bg-red-50 hover:text-red-600"
                      @click="removeAgenda(index)"
                    >
                      <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                      >
                        <path
                          stroke-linecap="round"
                          d="M5 7h14M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"
                        />
                      </svg>
                    </button>
                  </div>

                  <div
                    class="grid grid-cols-1 gap-4 md:grid-cols-[180px_minmax(0,1fr)]"
                  >
                    <!-- Time -->
                    <div>
                      <label
                        class="mb-1.5 block text-[10px] font-medium text-gray-600"
                      >
                        Time
                      </label>

                      <input
                        v-model="item.time"
                        type="time"
                        class="h-9 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                      />
                    </div>

                    <!-- Title -->
                    <div>
                      <label
                        class="mb-1.5 block text-[10px] font-medium text-gray-600"
                      >
                        Activity Title
                      </label>

                      <input
                        v-model="item.title"
                        type="text"
                        placeholder="Activity title"
                        class="h-9 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- ================================================= -->
          <!-- SPEAKERS -->
          <!-- ================================================= -->

          <section
            class="overflow-hidden rounded-xl border border-gray-200 bg-white"
          >
            <div
              class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between"
            >
              <div class="flex items-center gap-3">
                <div
                  class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100"
                >
                  <svg
                    class="h-4 w-4 text-gray-700"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                  >
                    <circle cx="9" cy="8" r="3" />

                    <path
                      stroke-linecap="round"
                      d="M3.5 19c.5-3.2 2.3-5 5.5-5s5 1.8 5.5 5"
                    />

                    <path stroke-linecap="round" d="M16 11a3 3 0 1 0 0-6" />
                  </svg>
                </div>

                <div>
                  <h2 class="text-sm font-semibold text-gray-900">
                    Event Speakers
                  </h2>

                  <p class="mt-0.5 text-[10px] text-gray-400">
                    Add speakers and their professional titles
                  </p>
                </div>
              </div>

              <button
                type="button"
                class="inline-flex h-8 items-center justify-center gap-2 rounded-md border border-gray-300 bg-white px-3 text-[10px] font-medium text-gray-700 transition hover:bg-gray-50"
                @click="addSpeaker"
              >
                <svg
                  class="h-3.5 w-3.5"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                </svg>

                Add Speaker
              </button>
            </div>

            <div class="p-5">
              <!-- Empty -->
              <div
                v-if="speakers.length === 0"
                class="rounded-lg border border-dashed border-gray-300 py-10 text-center"
              >
                <svg
                  class="mx-auto h-8 w-8 text-gray-300"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                >
                  <circle cx="12" cy="8" r="3" />

                  <path
                    stroke-linecap="round"
                    d="M5 20c.7-4 3-6 7-6s6.3 2 7 6"
                  />
                </svg>

                <p class="mt-2 text-xs font-medium text-gray-600">
                  No speakers
                </p>

                <p class="mt-1 text-[10px] text-gray-400">
                  Add speakers for this event.
                </p>
              </div>

              <!-- Speakers -->
              <div v-else class="space-y-4">
                <div
                  v-for="(speaker, index) in speakers"
                  :key="index"
                  class="rounded-lg border border-gray-200 p-4"
                >
                  <div class="mb-4 flex items-center justify-between">
                    <span
                      class="text-[10px] font-semibold uppercase tracking-wider text-gray-400"
                    >
                      Speaker #{{ index + 1 }}
                    </span>

                    <button
                      type="button"
                      class="inline-flex h-7 w-7 items-center justify-center rounded-md text-gray-400 transition hover:bg-red-50 hover:text-red-600"
                      @click="removeSpeaker(index)"
                    >
                      <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.8"
                      >
                        <path
                          stroke-linecap="round"
                          d="M5 7h14M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"
                        />
                      </svg>
                    </button>
                  </div>

                  <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <!-- Name -->
                    <div>
                      <label
                        class="mb-1.5 block text-[10px] font-medium text-gray-600"
                      >
                        Speaker Name
                      </label>

                      <input
                        v-model="speaker.name"
                        type="text"
                        placeholder="Speaker name"
                        class="h-9 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                      />
                    </div>

                    <!-- Professional Title -->
                    <div>
                      <label
                        class="mb-1.5 block text-[10px] font-medium text-gray-600"
                      >
                        Professional Title
                      </label>

                      <input
                        v-model="speaker.professionalTitle"
                        type="text"
                        placeholder="e.g. Software Engineer"
                        class="h-9 w-full rounded-md border border-gray-300 bg-white px-3 text-xs text-gray-900 outline-none focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>

        <!-- ================================================= -->
        <!-- RIGHT -->
        <!-- ================================================= -->

        <aside class="min-w-0 space-y-6">
          <!-- ================================================= -->
          <!-- BANNER IMAGE -->
          <!-- ================================================= -->

          <section
            class="overflow-hidden rounded-xl border border-gray-200 bg-white"
          >
            <div class="border-b border-gray-100 px-5 py-4">
              <h2 class="text-sm font-semibold text-gray-900">Banner Image</h2>

              <p class="mt-0.5 text-[10px] text-gray-400">Event banner image</p>
            </div>

            <div class="p-5">
              <div
                class="group relative overflow-hidden rounded-lg border border-gray-200 bg-gray-100"
              >
                <img
                  v-if="imagePreview"
                  :src="imagePreview"
                  :alt="form.title"
                  class="h-52 w-full object-cover"
                />

                <div
                  v-else
                  class="flex h-52 flex-col items-center justify-center"
                >
                  <svg
                    class="h-8 w-8 text-gray-300"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                  >
                    <rect x="3" y="4" width="18" height="16" rx="2" />

                    <circle cx="8.5" cy="9" r="1.5" />

                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="m3 17 5-5 4 4 2-2 7 6"
                    />
                  </svg>

                  <span class="mt-2 text-[10px] text-gray-400">
                    No banner image
                  </span>
                </div>

                <!-- Overlay -->
                <div
                  v-if="imagePreview"
                  class="absolute inset-0 flex items-center justify-center gap-2 bg-black/50 opacity-0 transition group-hover:opacity-100"
                >
                  <button
                    type="button"
                    class="inline-flex h-9 items-center gap-2 rounded-md bg-white px-3 text-xs font-medium text-gray-800"
                    @click="openImagePicker"
                  >
                    Change
                  </button>

                  <button
                    type="button"
                    class="inline-flex h-9 items-center rounded-md bg-white px-3 text-xs font-medium text-red-600"
                    @click="removeImage"
                  >
                    Remove
                  </button>
                </div>
              </div>

              <input
                ref="imageInput"
                type="file"
                accept="image/jpeg,image/png,image/gif"
                class="hidden"
                @change="handleImageChange"
              />

              <button
                v-if="!imagePreview"
                type="button"
                class="mt-3 flex h-10 w-full items-center justify-center gap-2 rounded-md border border-dashed border-gray-300 text-xs font-medium text-gray-600 transition hover:border-gray-500 hover:bg-gray-50"
                @click="openImagePicker"
              >
                <svg
                  class="h-4 w-4"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.8"
                >
                  <path stroke-linecap="round" d="M12 5v14M5 12h14" />
                </svg>

                Upload Banner Image
              </button>

              <p class="mt-2 text-center text-[9px] text-gray-400">
                JPG, JPEG, PNG or GIF · Maximum 5MB
              </p>
            </div>
          </section>

          <!-- ================================================= -->
          <!-- EVENT SUMMARY -->
          <!-- ================================================= -->

          <section class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="mb-5">
              <h2 class="text-sm font-semibold text-gray-900">Event Summary</h2>

              <p class="mt-0.5 text-[10px] text-gray-400">
                Current event information
              </p>
            </div>

            <div class="space-y-4">
              <!-- Location -->
              <div class="flex items-start gap-3">
                <div
                  class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-100"
                >
                  <svg
                    class="h-4 w-4 text-gray-600"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11Z"
                    />

                    <circle cx="12" cy="10" r="2.5" />
                  </svg>
                </div>

                <div class="min-w-0">
                  <p class="text-[10px] text-gray-400">Location</p>

                  <p
                    class="mt-0.5 wrap-break-word text-xs font-medium text-gray-800"
                  >
                    {{ form.location || "Not specified" }}
                  </p>
                </div>
              </div>

              <!-- Date -->
              <div class="flex items-start gap-3">
                <div
                  class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-100"
                >
                  <svg
                    class="h-4 w-4 text-gray-600"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                  >
                    <rect x="4" y="5" width="16" height="15" rx="2" />

                    <path d="M8 3v4M16 3v4M4 9h16" stroke-linecap="round" />
                  </svg>
                </div>

                <div>
                  <p class="text-[10px] text-gray-400">Date</p>

                  <p class="mt-0.5 text-xs font-medium text-gray-800">
                    {{ form.date || "Not specified" }}
                  </p>
                </div>
              </div>

              <!-- Time -->
              <div class="flex items-start gap-3">
                <div
                  class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-100"
                >
                  <svg
                    class="h-4 w-4 text-gray-600"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                  >
                    <circle cx="12" cy="12" r="9" />

                    <path stroke-linecap="round" d="M12 7v5l3 2" />
                  </svg>
                </div>

                <div>
                  <p class="text-[10px] text-gray-400">Time</p>

                  <p class="mt-0.5 text-xs font-medium text-gray-800">
                    {{ form.startTime || "--:--" }}
                    -
                    {{ form.endTime || "--:--" }}
                  </p>
                </div>
              </div>

              <!-- Capacity -->
              <div class="flex items-start gap-3">
                <div
                  class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-100"
                >
                  <svg
                    class="h-4 w-4 text-gray-600"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.8"
                  >
                    <circle cx="9" cy="8" r="3" />

                    <path
                      stroke-linecap="round"
                      d="M3.5 19c.5-3.2 2.3-5 5.5-5s5 1.8 5.5 5"
                    />
                  </svg>
                </div>

                <div>
                  <p class="text-[10px] text-gray-400">Maximum Participants</p>

                  <p class="mt-0.5 text-xs font-medium text-gray-800">
                    {{ form.maxParticipants ?? "Not specified" }}
                  </p>
                </div>
              </div>
            </div>
          </section>

          <!-- ================================================= -->
          <!-- SAVE -->
          <!-- ================================================= -->

          <section class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-start gap-3">
              <div
                class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-gray-100"
              >
                <svg
                  class="h-4 w-4 text-gray-600"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.8"
                >
                  <path stroke-linecap="round" d="M12 3v12M8 11l4 4 4-4" />

                  <path stroke-linecap="round" d="M5 20h14" />
                </svg>
              </div>

              <div>
                <h3 class="text-xs font-semibold text-gray-800">
                  Save your changes
                </h3>

                <p class="mt-1 text-[10px] leading-5 text-gray-400">
                  Make sure all event information is correct before saving.
                </p>
              </div>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-2">
              <button
                type="button"
                class="h-9 rounded-md border border-gray-300 bg-white text-xs font-medium text-gray-700 transition hover:bg-gray-50"
                @click="cancelEdit"
              >
                Cancel
              </button>

              <button
                type="button"
                :disabled="isSaving"
                class="h-9 rounded-md bg-gray-900 text-xs font-medium text-white transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50"
                @click="saveEvent"
              >
                {{ isSaving ? "Saving..." : "Save Changes" }}
              </button>
            </div>
          </section>
        </aside>
      </div>
    </main>
  </div>
</template>
