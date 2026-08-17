<script setup lang="ts">
definePageMeta({
  layout: "admin",
});

const route = useRoute();

/*
|--------------------------------------------------------------------------
| Types
|--------------------------------------------------------------------------
*/

interface FormData {
  name: string;
  location: string;
  status: string;
  avatar: string;
  email: string;
  phone: string;
  linkedin: string;
  degree: string;
  major: string;
  graduationDate: string;
  jobTitle: string;
  company: string;
  employmentType: string;
  startDate: string;
  workLocation: string;
}

/*
|--------------------------------------------------------------------------
| Alumni ID
|--------------------------------------------------------------------------
*/

const alumniId = computed(() => String(route.params.id));

/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const form = reactive<FormData>({
  name: "Sarah Jenkins",
  location: "San Francisco, CA",
  status: "Employed",
  avatar: "https://i.pravatar.cc/150?img=5",
  email: "sarah.j.dev@example.com",
  phone: "+1 (555) 123-4567",
  linkedin: "linkedin.com/in/sarahjdev",
  degree: "Bachelor of Science",
  major: "Computer Science",
  graduationDate: "2023-05-15",
  jobTitle: "Frontend Engineer",
  company: "TechFlow Solutions Inc.",
  employmentType: "Full-time",
  startDate: "2023-08-01",
  workLocation: "San Francisco (Hybrid)",
});

/*
|--------------------------------------------------------------------------
| Options
|--------------------------------------------------------------------------
*/

const statusOptions = ["Employed", "Job Seeking", "Studying", "Unemployed"];

const employmentTypeOptions = [
  "Full-time",
  "Part-time",
  "Internship",
  "Contract",
];

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const isSaving = ref(false);
const avatarInput = ref<HTMLInputElement | null>(null);

/*
|--------------------------------------------------------------------------
| Avatar
|--------------------------------------------------------------------------
*/

function openAvatarPicker() {
  avatarInput.value?.click();
}

function handleAvatarChange(event: Event) {
  const input = event.target as HTMLInputElement;

  const file = input.files?.[0];

  if (!file) return;

  form.avatar = URL.createObjectURL(file);
}

/*
|--------------------------------------------------------------------------
| Save
|--------------------------------------------------------------------------
*/

async function handleSave() {
  if (isSaving.value) return;

  isSaving.value = true;

  try {
    // TODO:
    // Replace this with your Laravel API request.
    //
    // Example:
    // await $fetch(`/api/alumni/${alumniId.value}`, {
    //   method: "PUT",
    //   body: form,
    // });

    await new Promise((resolve) => setTimeout(resolve, 800));

    await navigateTo(`/alumni/${alumniId.value}`);
  } catch (error) {
    console.error("Failed to save alumni:", error);
  } finally {
    isSaving.value = false;
  }
}

/*
|--------------------------------------------------------------------------
| Cancel
|--------------------------------------------------------------------------
*/
const router = useRouter();
function handleCancel() {
  router.back();
}
</script>

<template>
  <div class="min-h-full w-full bg-gray-50 rounded-xl overflow-clip">
    <!-- ============================================================= -->
    <!-- HEADER -->
    <!-- ============================================================= -->

    <div
      class="sticky top-0 z-20 border-b border-gray-200 bg-white/95 backdrop-blur"
    >
      <div class="flex w-full items-center justify-between gap-4 px-6 py-4">
        <!-- Left -->
        <div class="flex min-w-0 items-center gap-3">
          <button
            type="button"
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-gray-300 bg-white text-gray-700 transition hover:bg-gray-50"
            @click="handleCancel"
          >
            <!-- Arrow Left -->
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M19 12H5" />
              <path d="m12 19-7-7 7-7" />
            </svg>
          </button>

          <div class="min-w-0">
            <h1 class="truncate text-xl font-semibold text-gray-900">
              Edit Alumni Profile
            </h1>

            <p class="mt-0.5 text-sm text-gray-500">
              Update alumni information and employment details
            </p>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex shrink-0 items-center gap-2">
          <button
            type="button"
            class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
            @click="handleCancel"
          >
            Cancel
          </button>

          <button
            type="button"
            :disabled="isSaving"
            class="flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50"
            @click="handleSave"
          >
            <!-- Loading -->
            <svg
              v-if="isSaving"
              xmlns="http://www.w3.org/2000/svg"
              width="17"
              height="17"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="animate-spin"
            >
              <path d="M21 12a9 9 0 1 1-6.7-8.7" />
            </svg>

            <!-- Save -->
            <svg
              v-else
              xmlns="http://www.w3.org/2000/svg"
              width="17"
              height="17"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path
                d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"
              />
              <path d="M14 2v6H8V2" />
              <path d="M8 18h8" />
            </svg>

            {{ isSaving ? "Saving..." : "Save Changes" }}
          </button>
        </div>
      </div>
    </div>

    <!-- ============================================================= -->
    <!-- CONTENT -->
    <!-- ============================================================= -->

    <div class="w-full px-6 py-6">
      <div class="grid w-full grid-cols-1 gap-6 xl:grid-cols-12">
        <!-- ========================================================= -->
        <!-- LEFT COLUMN -->
        <!-- ========================================================= -->

        <div class="space-y-6 xl:col-span-4">
          <!-- ======================================================= -->
          <!-- PROFILE -->
          <!-- ======================================================= -->

          <section class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="mb-5">
              <h2 class="text-base font-semibold text-gray-900">Profile</h2>

              <p class="mt-1 text-sm text-gray-500">
                Basic alumni profile information
              </p>
            </div>

            <!-- Avatar -->
            <div class="flex flex-col items-center">
              <div class="relative">
                <img
                  :src="form.avatar"
                  alt="Alumni avatar"
                  class="h-32 w-32 rounded-2xl object-cover ring-4 ring-gray-100"
                />

                <button
                  type="button"
                  class="absolute bottom-[-8px] right-[-8px] flex h-9 w-9 items-center justify-center rounded-full border-2 border-white bg-gray-900 text-white shadow-sm transition hover:bg-gray-800"
                  @click="openAvatarPicker"
                >
                  <!-- Camera -->
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="17"
                    height="17"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path
                      d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3z"
                    />
                    <circle cx="12" cy="13" r="3" />
                  </svg>
                </button>

                <input
                  ref="avatarInput"
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="handleAvatarChange"
                />
              </div>

              <p class="mt-4 text-xs text-gray-400">
                JPG, PNG or WEBP. Maximum 5MB.
              </p>
            </div>

            <!-- Name -->
            <div class="mt-6">
              <label
                for="name"
                class="mb-1.5 block text-sm font-medium text-gray-700"
              >
                Full Name
              </label>

              <input
                id="name"
                v-model="form.name"
                type="text"
                class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
              />
            </div>

            <!-- Status -->
            <div class="mt-4">
              <label
                for="status"
                class="mb-1.5 block text-sm font-medium text-gray-700"
              >
                Employment Status
              </label>

              <select
                id="status"
                v-model="form.status"
                class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
              >
                <option
                  v-for="status in statusOptions"
                  :key="status"
                  :value="status"
                >
                  {{ status }}
                </option>
              </select>
            </div>
          </section>

          <!-- ======================================================= -->
          <!-- CONTACT -->
          <!-- ======================================================= -->

          <section class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="mb-5">
              <h2 class="text-base font-semibold text-gray-900">
                Contact Information
              </h2>

              <p class="mt-1 text-sm text-gray-500">
                How the alumni can be contacted
              </p>
            </div>

            <!-- Email -->
            <div>
              <label
                for="email"
                class="mb-1.5 block text-sm font-medium text-gray-700"
              >
                Email
              </label>

              <input
                id="email"
                v-model="form.email"
                type="email"
                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
              />
            </div>

            <!-- Phone -->
            <div class="mt-4">
              <label
                for="phone"
                class="mb-1.5 block text-sm font-medium text-gray-700"
              >
                Phone
              </label>

              <input
                id="phone"
                v-model="form.phone"
                type="tel"
                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
              />
            </div>

            <!-- LinkedIn -->
            <div class="mt-4">
              <label
                for="linkedin"
                class="mb-1.5 block text-sm font-medium text-gray-700"
              >
                LinkedIn
              </label>

              <input
                id="linkedin"
                v-model="form.linkedin"
                type="text"
                placeholder="linkedin.com/in/username"
                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
              />
            </div>

            <!-- Location -->
            <div class="mt-4">
              <label
                for="location"
                class="mb-1.5 block text-sm font-medium text-gray-700"
              >
                Current Location
              </label>

              <input
                id="location"
                v-model="form.location"
                type="text"
                class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
              />
            </div>
          </section>
        </div>

        <!-- ========================================================= -->
        <!-- RIGHT COLUMN -->
        <!-- ========================================================= -->

        <div class="space-y-6 xl:col-span-8">
          <!-- ======================================================= -->
          <!-- ACADEMIC -->
          <!-- ======================================================= -->

          <section class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="mb-6 flex items-start justify-between gap-4">
              <div>
                <h2 class="text-base font-semibold text-gray-900">
                  Academic Record
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                  Education and graduation information
                </p>
              </div>

              <!-- Certificate SVG -->
              <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-700"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M4 4h16v12H4z" />
                  <path d="M8 8h8" />
                  <path d="M8 12h5" />
                  <path d="m8 20 4-2 4 2" />
                </svg>
              </div>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
              <!-- Degree -->
              <div>
                <label
                  for="degree"
                  class="mb-1.5 block text-sm font-medium text-gray-700"
                >
                  Degree
                </label>

                <input
                  id="degree"
                  v-model="form.degree"
                  type="text"
                  class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- Major -->
              <div>
                <label
                  for="major"
                  class="mb-1.5 block text-sm font-medium text-gray-700"
                >
                  Major
                </label>

                <input
                  id="major"
                  v-model="form.major"
                  type="text"
                  class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- Graduation -->
              <div class="md:col-span-2">
                <label
                  for="graduationDate"
                  class="mb-1.5 block text-sm font-medium text-gray-700"
                >
                  Graduation Date
                </label>

                <input
                  id="graduationDate"
                  v-model="form.graduationDate"
                  type="date"
                  class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>
            </div>
          </section>

          <!-- ======================================================= -->
          <!-- EMPLOYMENT -->
          <!-- ======================================================= -->

          <section class="rounded-xl border border-gray-200 bg-white p-6">
            <div class="mb-6 flex items-start justify-between gap-4">
              <div>
                <h2 class="text-base font-semibold text-gray-900">
                  Current Employment
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                  Current job and employment information
                </p>
              </div>

              <!-- Briefcase SVG -->
              <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-700"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <rect width="20" height="14" x="2" y="7" rx="2" ry="2" />
                  <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                  <path d="M2 12h20" />
                </svg>
              </div>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
              <!-- Job Title -->
              <div>
                <label
                  for="jobTitle"
                  class="mb-1.5 block text-sm font-medium text-gray-700"
                >
                  Job Title
                </label>

                <input
                  id="jobTitle"
                  v-model="form.jobTitle"
                  type="text"
                  class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- Company -->
              <div>
                <label
                  for="company"
                  class="mb-1.5 block text-sm font-medium text-gray-700"
                >
                  Company
                </label>

                <input
                  id="company"
                  v-model="form.company"
                  type="text"
                  class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- Employment Type -->
              <div>
                <label
                  for="employmentType"
                  class="mb-1.5 block text-sm font-medium text-gray-700"
                >
                  Employment Type
                </label>

                <select
                  id="employmentType"
                  v-model="form.employmentType"
                  class="w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                >
                  <option
                    v-for="type in employmentTypeOptions"
                    :key="type"
                    :value="type"
                  >
                    {{ type }}
                  </option>
                </select>
              </div>

              <!-- Start Date -->
              <div>
                <label
                  for="startDate"
                  class="mb-1.5 block text-sm font-medium text-gray-700"
                >
                  Start Date
                </label>

                <input
                  id="startDate"
                  v-model="form.startDate"
                  type="date"
                  class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>

              <!-- Work Location -->
              <div class="md:col-span-2">
                <label
                  for="workLocation"
                  class="mb-1.5 block text-sm font-medium text-gray-700"
                >
                  Work Location
                </label>

                <input
                  id="workLocation"
                  v-model="form.workLocation"
                  type="text"
                  class="w-full rounded-lg border border-gray-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-gray-900 focus:ring-1 focus:ring-gray-900"
                />
              </div>
            </div>
          </section>

          <!-- ======================================================= -->
          <!-- BOTTOM ACTIONS -->
          <!-- ======================================================= -->

          <div
            class="flex flex-col-reverse gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:items-center sm:justify-end"
          >
            <button
              type="button"
              class="w-full rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 sm:w-auto"
              @click="handleCancel"
            >
              Cancel
            </button>

            <button
              type="button"
              :disabled="isSaving"
              class="flex w-full items-center justify-center gap-2 rounded-lg bg-gray-900 px-5 py-2.5 text-sm font-medium text-white transition hover:bg-gray-800 disabled:cursor-not-allowed disabled:opacity-50 sm:w-auto"
              @click="handleSave"
            >
              <!-- Loading -->
              <svg
                v-if="isSaving"
                xmlns="http://www.w3.org/2000/svg"
                width="17"
                height="17"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="animate-spin"
              >
                <path d="M21 12a9 9 0 1 1-6.7-8.7" />
              </svg>

              <!-- Save -->
              <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                width="17"
                height="17"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path
                  d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"
                />
                <path d="M14 2v6H8V2" />
                <path d="M8 18h8" />
              </svg>

              {{ isSaving ? "Saving..." : "Save Changes" }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
