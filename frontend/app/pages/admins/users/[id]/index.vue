<script setup lang="ts">
import { useAlumniStore } from "#imports";
import LoadingIcon from "~/components/icons/LoadingIcon.vue";

definePageMeta({
  layout: "admin",
});

const avata = useAvatar();
/*
|--------------------------------------------------------------------------
| Types
|--------------------------------------------------------------------------
*/

interface StatusHistoryItem {
  status: string;
  date: string;
  description: string;
  color: string;
}

interface EventItem {
  day: string;
  month: string;
  title: string;
  status: string;
}

const route = useRoute();

/*
|--------------------------------------------------------------------------
| Static Alumni Data
|--------------------------------------------------------------------------
*/

const alumni = {
  name: "Sarah Jenkins",
  degree: "B.S. Computer Science (2023)",
  location: "San Francisco, CA",
  id: "8934-211",
  status: "Employed",
  avatar: "https://i.pravatar.cc/150?img=5",

  email: "sarah.j.dev@example.com",
  phone: "+1 (555) 123-4567",
  linkedin: "linkedin.com/in/sarahjdev",

  academic: {
    degree: "Bachelor of Science",
    major: "Computer Science",
    graduationDate: "May 15, 2023",
  },

  employment: {
    title: "Frontend Engineer",
    company: "TechFlow Solutions Inc.",
    type: "Full-time",
    startDate: "August 2023",
    location: "San Francisco (Hybrid)",
  },
};

/*
|--------------------------------------------------------------------------
| Status History
|--------------------------------------------------------------------------
*/

const statusHistory: StatusHistoryItem[] = [
  {
    status: "Employed",
    date: "Aug 12, 2023",
    description: "Reported new role at TechFlow Solutions.",
    color: "bg-green-500",
  },
  {
    status: "Job Seeking",
    date: "Jun 01, 2023",
    description: "Updated resume uploaded to portal.",
    color: "bg-yellow-500",
  },
  {
    status: "Studying",
    date: "Sep 01, 2019",
    description: "Enrolled in BS Computer Science program.",
    color: "bg-blue-500",
  },
];

/*
|--------------------------------------------------------------------------
| Events
|--------------------------------------------------------------------------
*/

const events: EventItem[] = [
  {
    day: "15",
    month: "OCT",
    title: "Tech Alumni Mixer 2023",
    status: "Attended",
  },
  {
    day: "02",
    month: "NOV",
    title: "Career Fair - Fall",
    status: "Registered",
  },
];

/*
|--------------------------------------------------------------------------
| Navigation
|--------------------------------------------------------------------------
*/

const editProfile = () => {
  navigateTo({
    name: "admins-users-id-edit",
    params: {
      id: alumni.id,
    },
  });
};

const alumniStore = useAlumniStore();
onMounted(async () => {
  await alumniStore.getAlumniProfile(Number(route.params.id));
});

/*
|--------------------------------------------------------------------------
| Update Status
|--------------------------------------------------------------------------
*/

const showStatusModal = ref(false);
const selectedStatus = ref(alumni.status);
const statusNotes = ref("");

const updateStatus = () => {
  selectedStatus.value = alumni.status;
  statusNotes.value = "";
  showStatusModal.value = true;
};

const handleSaveStatus = () => {
  alumni.status = selectedStatus.value;
  showStatusModal.value = false;
};

const formatMonthYear = (date: string | null | undefined) => {
  if (!date) return "N/A";

  return new Date(date).toLocaleDateString("en-US", {
    month: "long",
    year: "numeric",
  });
};
</script>

<template>
  <div class="w-full flex justify-between items-center mb-6 gap-6">
    <div>
      <h1 class="text-2xl font-semibold text-slate-900">Alumni Details</h1>
      <p class="text-slate-400 text-sm line-clamp-1">
        Keep your alumni network informed about your professional journey.
      </p>
    </div>

    <button
      @click="$router.back"
      type="button"
      class="p-1.5 px-4 flex justify-center items-center gap-1.5 bg-slate-900 text-white hover:bg-slate-800 rounded-lg active:bg-slate-700 transition cursor-pointer shadow-xs"
      aria-label="Go back"
    >
      <!-- ArrowLeft Icon SVG -->
      <svg
        class="w-4 h-4"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
        viewBox="0 0 24 24"
      >
        <path d="m12 19-7-7 7-7" />
        <path d="M19 12H5" />
      </svg>
      <span class="text-sm font-medium">Back</span>
    </button>
  </div>

  <div v-if="!alumniStore.isLoading" class="flex flex-col gap-4">
    <!-- ============================================================
         HEADER CARD
    ============================================================= -->

    <div
      class="bg-white rounded-xl border border-gray-200 p-5 flex items-center justify-between flex-wrap gap-4"
    >
      <!-- Profile -->
      <div class="flex items-center gap-4">
        <!-- Avatar -->
        <img
          :src="
            alumniStore.alumniProfile?.user?.avatar
              ? alumniStore.alumniProfile.user.avatar
              : avata.textToImage(
                  alumniStore.alumniProfile?.user?.name_english ?? 'User',
                )
          "
          :alt="alumniStore.alumniProfile?.user?.name_english ?? 'User'"
          class="w-24 h-24 rounded-lg object-cover border border-gray-100"
        />

        <div>
          <!-- Name -->
          <h1 class="text-xl font-semibold text-gray-900">
            {{ alumniStore.alumniProfile?.user?.name_english ?? "N/A" }}
          </h1>

          <!-- Basic Information -->
          <div
            class="flex items-center gap-3 text-sm text-gray-500 mt-1 flex-wrap"
          >
            <!-- Degree -->
            <span class="flex items-center gap-1.5">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                class="w-4 h-4"
              >
                <path d="M22 10 12 5 2 10l10 5 10-5Z" />
                <path d="M6 12.5V17c3.5 2.5 8.5 2.5 12 0v-4.5" />
                <path d="M22 10v6" />
              </svg>

              {{ alumniStore.alumniProfile?.major?.name ?? "N/A" }} ({{
                alumniStore.alumniProfile?.graduation_year ?? "N/A"
              }})
            </span>

            <!-- Location -->
            <span class="flex items-center gap-1.5">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                class="w-4 h-4"
              >
                <path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z" />
                <circle cx="12" cy="10" r="2.5" />
              </svg>

              {{ alumniStore.alumniProfile?.address ?? "N/A" }}
            </span>

            <!-- ID -->
            <span class="flex items-center gap-1.5">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                class="w-4 h-4"
              >
                <rect x="3" y="5" width="18" height="14" rx="2" />
                <circle cx="8" cy="11" r="2" />
                <path d="M13 10h5M13 14h4" />
              </svg>
              {{
                alumniStore.alumniProfile?.user.mobile ??
                alumniStore.alumniProfile?.user.email ??
                "N/A"
              }}
            </span>
          </div>

          <!-- Status -->
          <span
            class="inline-flex items-center gap-1 mt-2 text-xs font-medium text-green-700 bg-green-100 px-2.5 py-1 rounded-full"
          >
            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
            {{ alumniStore.alumniProfile?.employment_status }}
          </span>
        </div>
      </div>

      <!-- Header Actions -->
      <div class="flex items-center gap-2">
        <!-- Edit -->
        <button
          @click="editProfile"
          class="flex items-center gap-1.5 border border-gray-300 text-gray-700 text-sm px-3 py-2 rounded-lg hover:bg-gray-50 transition cursor-pointer"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            class="w-4 h-4"
          >
            <path d="M12 20h9" />
            <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4L16.5 3.5Z" />
          </svg>

          Edit Profile
        </button>

        <!-- Update Status -->
        <button
          @click="updateStatus"
          class="flex items-center gap-1.5 bg-gray-900 text-white text-sm px-3 py-2 rounded-lg hover:bg-gray-800 transition cursor-pointer"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            class="w-4 h-4"
          >
            <path d="M20 12a8 8 0 1 1-2.34-5.66" />
            <path d="M20 4v6h-6" />
          </svg>

          Update Status
        </button>
      </div>
    </div>

    <!-- ============================================================
         CONTENT GRID
    ============================================================= -->

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <!-- ==========================================================
           LEFT COLUMN
      =========================================================== -->

      <div class="flex flex-col gap-4 lg:col-span-2">
        <!-- ========================================================
             PERSONAL DETAILS
        ========================================================= -->

        <div class="bg-white rounded-xl border border-gray-200 p-5">
          <h2 class="flex items-center gap-2 font-semibold text-gray-900 mb-4">
            <!-- Account SVG -->
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.8"
              class="w-[18px] h-[18px]"
            >
              <circle cx="12" cy="8" r="4" />
              <path d="M4 21a8 8 0 0 1 16 0" />
            </svg>

            Personal Details
          </h2>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <!-- Email -->
            <div>
              <p class="text-gray-400 text-xs mb-1">Email</p>

              <p class="text-gray-900">
                {{ alumniStore.alumniProfile?.user.email ?? "N/A" }}
              </p>
            </div>

            <!-- Phone -->
            <div>
              <p class="text-gray-400 text-xs mb-1">Phone</p>

              <p class="text-gray-900">
                +855 {{ alumniStore.alumniProfile?.user.mobile ?? "N/A" }}
              </p>
            </div>

            <!-- FaceBook Link -->
            <div>
              <p class="text-gray-400 text-xs mb-1">FaceBook Link</p>

              <a
                :href="alumniStore.alumniProfile?.facebook_url ?? '#'"
                class="text-blue-600 hover:underline line-clamp-1"
              >
                {{ alumniStore.alumniProfile?.facebook_url ?? "N/A" }}
              </a>
            </div>

            <div>
              <p class="text-gray-400 text-xs mb-1">LinkedIn</p>

              <a
                :href="alumniStore.alumniProfile?.linkedin_url ?? '#'"
                class="text-blue-600 hover:underline line-clamp-1"
              >
                {{ alumniStore.alumniProfile?.linkedin_url ?? "N/A" }}
              </a>
            </div>
          </div>
        </div>

        <!-- ========================================================
             ACADEMIC RECORD
        ========================================================= -->

        <div class="bg-white rounded-xl border border-gray-200 p-5">
          <h2 class="flex items-center gap-2 font-semibold text-gray-900 mb-4">
            <!-- Certificate SVG -->
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.8"
              class="w-[18px] h-[18px]"
            >
              <path d="M5 4h14v16H5z" rx="1" />
              <path d="M8 8h8M8 12h8M8 16h5" />
              <path d="m16 16 1.5 1.5L20 15" />
            </svg>

            Academic Record
          </h2>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <!-- Degree -->
            <div>
              <p class="text-gray-400 text-xs mb-1">Degree</p>

              <p class="text-gray-900">
                {{ alumniStore.alumniProfile?.bio ?? "N/A" }}
              </p>
            </div>

            <!-- Major -->
            <div>
              <p class="text-gray-400 text-xs mb-1">Major</p>

              <p class="text-gray-900">
                {{ alumniStore.alumniProfile?.major?.name ?? "N/A" }}
              </p>
            </div>

            <!-- Graduation -->
            <div>
              <p class="text-gray-400 text-xs mb-1">Graduation Date</p>

              <p class="text-gray-900">
                {{ alumniStore.alumniProfile?.graduation_year ?? "N/A" }}
              </p>
            </div>
          </div>
        </div>

        <!-- ========================================================
             CURRENT EMPLOYMENT
        ========================================================= -->

        <div class="bg-white rounded-xl border border-gray-200 p-5">
          <div class="flex items-center justify-between mb-4">
            <h2 class="flex items-center gap-2 font-semibold text-gray-900">
              <!-- Briefcase SVG -->
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                class="w-[18px] h-[18px]"
              >
                <rect x="3" y="7" width="18" height="13" rx="2" />
                <path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                <path d="M3 12h18" />
              </svg>

              Current Employment
            </h2>

            <!-- More -->
            <button
              class="text-gray-400 hover:text-gray-600 cursor-pointer"
              title="More"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="currentColor"
                class="w-[18px] h-[18px]"
              >
                <circle cx="5" cy="12" r="1.5" />
                <circle cx="12" cy="12" r="1.5" />
                <circle cx="19" cy="12" r="1.5" />
              </svg>
            </button>
          </div>

          <div class="flex gap-3">
            <!-- Company Icon -->
            <div
              class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center shrink-0"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.8"
                class="w-5 h-5 text-gray-500"
              >
                <path d="M4 21V4h10v17" />
                <path d="M14 9h6v12" />
                <path d="M8 8h2M8 12h2M8 16h2M16 13h2M16 17h2" />
              </svg>
            </div>

            <div class="flex-1 capitalize">
              <p class="font-medium text-gray-900">
                {{ alumniStore.alumniProfile?.employment?.job_title ?? "N/A" }}
              </p>

              <p class="text-sm text-gray-500 mb-3">
                {{ alumniStore.alumniProfile?.employment?.company_id ?? "N/A" }}
                •
                {{
                  alumniStore.alumniProfile?.employment?.employment_type ??
                  "N/A"
                }}
              </p>

              <div
                class="grid grid-cols-2 gap-4 text-sm border-t border-gray-100 pt-3"
              >
                <!-- Start Date -->
                <div>
                  <p class="text-gray-400 text-xs mb-1">Start Date</p>

                  <p class="text-gray-900">
                    {{
                      formatMonthYear(
                        alumniStore.alumniProfile?.employment?.start_date,
                      ) ?? "N/A"
                    }}
                  </p>
                </div>

                <!-- Location -->
                <div>
                  <p class="text-gray-400 text-xs mb-1">Location</p>

                  <p class="text-gray-900">
                    {{
                      alumniStore.alumniProfile?.employment?.location ?? "N/A"
                    }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ==========================================================
           RIGHT COLUMN
      =========================================================== -->

      <div class="flex flex-col gap-4">
        <!-- ========================================================
             STATUS HISTORY
        ========================================================= -->

      </div>
    </div>
  </div>

  <div v-else class="w-full p-5 flex justify-center items-center">
    <LoadingIcon class="size-10 text-primary" />
  </div>

  <!-- ==============================================================
       SIMPLE UPDATE STATUS MODAL
  ============================================================== -->

  <!-- ==============================================================
        UPDATE STATUS MODAL
  ============================================================== -->
  <Transition
    enter-active-class="transition duration-200 ease-out"
    enter-from-class="opacity-0 scale-95"
    enter-to-class="opacity-100 scale-100"
    leave-active-class="transition duration-150 ease-in"
    leave-from-class="opacity-100 scale-100"
    leave-to-class="opacity-0 scale-95"
  >
    <div
      v-if="showStatusModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4"
      @click.self="showStatusModal = false"
    >
      <div
        class="w-full max-w-md bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-100"
      >
        <!-- Modal Header -->
        <div class="px-6 pt-5 pb-4 border-b border-gray-100 relative">
          <h3 class="text-lg font-bold text-gray-900">Update Status</h3>
          <p class="text-sm text-gray-500 mt-0.5">
            For
            <span class="font-semibold text-gray-900">{{ alumni.name }}</span>
          </p>

          <button
            @click="showStatusModal = false"
            class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition p-1 rounded-lg hover:bg-gray-100 cursor-pointer"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              class="w-5 h-5"
            >
              <path d="M18 6 6 18M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-5">
          <!-- Current Status Dropdown -->
          <div>
            <label
              class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2"
            >
              Current Status
            </label>
            <div class="relative">
              <select
                v-model="selectedStatus"
                class="w-full appearance-none bg-white border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition pr-10 cursor-pointer"
              >
                <option value="Employed">Employed</option>
                <option value="Job Seeking">Job Seeking</option>
                <option value="Studying">Studying</option>
                <option value="Unemployed">Unemployed</option>
              </select>
              <div
                class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  class="w-4 h-4"
                >
                  <path d="m6 9 6 6 6-6" />
                </svg>
              </div>
            </div>
          </div>

          <!-- Notes / Comments -->
          <div>
            <label
              class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-2"
            >
              Notes / Comments
            </label>
            <textarea
              v-model="statusNotes"
              rows="3"
              placeholder="Add any relevant details about this status change..."
              class="w-full border border-gray-300 rounded-lg p-3 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition resize-none"
            ></textarea>
          </div>

          <!-- Recent History -->
          <div>
            <span
              class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2"
            >
              RECENT HISTORY
            </span>
            <div
              class="bg-gray-50/80 border border-gray-200/80 rounded-lg p-3.5 flex items-start gap-3"
            >
              <div
                class="p-1.5 bg-blue-100/70 text-blue-600 rounded-full shrink-0 mt-0.5"
              >
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  class="w-4 h-4"
                >
                  <path d="M3 12a9 9 0 1 0 3-6.7" />
                  <path d="M3 4v6h6" />
                  <path d="M12 7v5l3 2" />
                </svg>
              </div>
              <div class="text-xs leading-relaxed text-gray-600">
                <p>
                  Status changed from
                  <span class="font-bold text-gray-900">Studying</span> to
                  <span class="font-bold text-gray-900">Job Seeking</span>
                </p>
                <p class="text-[11px] text-gray-400 mt-0.5">
                  Oct 12, 2023 by Admin User
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Footer -->
        <div
          class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex items-center justify-end gap-3"
        >
          <button
            type="button"
            @click="showStatusModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition cursor-pointer"
          >
            Cancel
          </button>

          <button
            type="button"
            @click="handleSaveStatus"
            class="inline-flex items-center gap-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition shadow-xs cursor-pointer"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              class="w-4 h-4"
            >
              <path
                d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"
              />
              <polyline points="17 21 17 13 7 13 7 21" />
              <polyline points="7 3 7 8 15 8" />
            </svg>
            Save Changes
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>
