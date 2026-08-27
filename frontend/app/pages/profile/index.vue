<script setup lang="ts">
import { useProfileAlumni } from "~/stores/alumnis/profile";

definePageMeta({
  middleware: ["auth", "alumni"],
  layout: "default",
});

const profileStore = useProfileAlumni();

const avatar = useAvatar();

// Profile Data State
const profile = ref({
  name: "Alex Chen",
  avatar:
    "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=300&auto=format&fit=crop",
  classYear: "2021",
  degree: "B.S. Computer Science",
  email: "alex.chen@alumni.edu",
  phone: "+1 (555) 123-4567",
  location: "San Francisco, CA",
});

// ----------------------------------------------
function formatDayMonthShort(dateInput: string) {
  const date = new Date(dateInput);
  return date.toLocaleDateString("en-US", {
    weekday: "short",
    month: "short",
    day: "numeric",
  });
}

// Career Timeline State
const timeline = ref([
  {
    id: 1,
    period: "Aug 2023 - Present",
    role: "Senior Frontend Developer",
    company: "TechCorp Solutions",
    location: "San Francisco, CA",
    active: true,
  },
  {
    id: 2,
    period: "Jun 2021 - Jul 2023",
    role: "Junior Web Developer",
    company: "Creative Digital Agency",
    location: "Seattle, WA",
    active: false,
  },
  {
    id: 3,
    period: "May 2021",
    role: "Graduated B.S. Computer Science",
    company: "University Alumni Program",
    location: "",
    active: false,
  },
]);

function getDateMonth(dateStr: string) {
  return new Date(dateStr)
    .toLocaleDateString("en-US", {
      month: "short", // OCT
    })
    .toUpperCase();
}

function getDateDay(dateStr: string) {
  return new Date(dateStr).getDate(); // 12
}

const isLoading = ref({
  id: 0,
  value: false,
});

const unRegister = async (id: number) => {
  if (id) {
    isLoading.value.id = id;
    isLoading.value.value = true;
    try {
      const res = await profileStore.unRegister(id);

      if (res.status == 200 && profileStore.data?.event_registrations) {
        profileStore.data.event_registrations =
          profileStore.data.event_registrations.filter((e) => e.id != id);
      }
    } catch (e: any) {
      console.log(e);
    } finally {
      isLoading.value.value = false;
    }
  }
};

onMounted(() => {
  profileStore.getProfile();
});
</script>

<template>
  <div
    class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 items-start"
  >
    <!-- ================= LEFT COLUMN: PROFILE CARD (col-span-4) ================= -->
    <div class="lg:col-span-4">
      <div
        v-if="!profileStore.isLoading && profileStore.data"
        class="w-full bg-white rounded-2xl p-6 border border-teal-100/80 shadow-xs flex flex-col items-center text-center space-y-5"
      >
        <!-- Avatar with Edit Icon -->
        <div class="relative">
          <img
            :src="
              profileStore.data.user.profile_url ??
              avatar.textToImage(profileStore.data.user.name_english)
            "
            :alt="profileStore.data.user.name_english"
            class="w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover ring-4 ring-emerald-500/20"
          />
          <button
            @click="$router.push({ name: 'profile-id', params: { id: 1 } })"
            type="button"
            class="absolute bottom-1 right-1 bg-[#036250] hover:bg-[#024a3c] text-white p-1.5 rounded-full shadow-md transition cursor-pointer"
            aria-label="Edit Profile"
          >
            <svg
              class="w-3.5 h-3.5"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <path
                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
              />
            </svg>
          </button>
        </div>

        <!-- Name & Education -->
        <div class="space-y-1">
          <h1 class="text-xl font-bold text-slate-900 tracking-tight">
            {{ profileStore.data.user.name_english }}
          </h1>
          <p class="text-xs text-slate-500 font-medium capitalize">
            Class of {{ profileStore.data.graduation_year }} •
            {{ profileStore.data.major.name }}
          </p>
        </div>

        <hr class="w-full border-slate-100" />

        <!-- Contact Info -->
        <div
          class="w-full space-y-3 text-left text-xs font-medium text-slate-600"
        >
          <div class="flex items-center gap-3">
            <svg
              class="w-4 h-4 text-slate-400 shrink-0"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <path
                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
              />
            </svg>
            <span class="truncate">{{
              profileStore.data.user.email ?? "N/A"
            }}</span>
          </div>

          <div class="flex items-center gap-3">
            <svg
              class="w-4 h-4 text-slate-400 shrink-0"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <path
                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
              />
            </svg>
            <span>{{ profileStore.data.user.mobile ?? "N/A" }}</span>
          </div>

          <div class="flex items-center gap-3">
            <svg
              class="w-4 h-4 text-slate-400 shrink-0"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              viewBox="0 0 24 24"
            >
              <path
                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
              />
              <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>{{ profileStore.data.address ?? "N/A" }}</span>
          </div>
        </div>

        <!-- Save Button -->
        <!-- <button
          type="button"
          class="w-full py-2.5 bg-[#036250] hover:bg-[#024a3c] text-white text-xs font-semibold rounded-xl transition cursor-pointer shadow-xs mt-2"
        >
          Save Changes
        </button> -->
      </div>

      <!-- loading -->

      <div
        v-else-if="profileStore.isLoading && !profileStore.data"
        class="w-full bg-white rounded-2xl p-6 border border-teal-100/80 shadow-xs flex flex-col items-center text-center space-y-5"
      >
        <div class="size-30 bg-slate-200 animate-pulse rounded-full"></div>
        <div
          class="w-full space-y-1 flex flex-col justify-center items-center animate-pulse"
        >
          <div class="w-1/2 bg-slate-200 rounded-full p-2.5"></div>
          <div class="w-full bg-slate-200 rounded-full p-1.5"></div>
        </div>
        <hr class="w-full text-slate-200" />

        <div
          class="w-full space-y-1 flex flex-col justify-center items-center animate-pulse"
        >
          <div class="w-full flex gap-1 items-center">
            <div class="bg-slate-200 rounded-full p-2"></div>
            <div class="w-4/5 bg-slate-200 rounded-full p-1.5"></div>
          </div>

          <div class="w-full flex gap-1 items-center">
            <div class="bg-slate-200 rounded-full p-2"></div>
            <div class="w-4/9 bg-slate-200 rounded-full p-1.5"></div>
          </div>

          <div class="w-full flex gap-1 items-center">
            <div class="bg-slate-200 rounded-full p-2"></div>
            <div class="w-3/5 bg-slate-200 rounded-full p-1.5"></div>
          </div>
        </div>
        <button
          class="w-full bg-slate-200 p-4 rounded-full animate-pulse"
        ></button>
      </div>
    </div>

    <!-- ================= RIGHT COLUMN: DETAILS (col-span-8) ================= -->
    <div class="lg:col-span-8">
      <div class="w-full space-y-6">
        <!-- 1. Current Employment Status Card -->
        <div>
          <div
            v-if="!profileStore.isLoading && profileStore.data?.employment"
            class="bg-white rounded-2xl p-6 border border-teal-100/80 shadow-xs space-y-4"
          >
            <div class="flex items-center justify-between">
              <h2 class="text-base font-bold text-slate-900 tracking-tight">
                Current Employment Status
              </h2>
              <button
                type="button"
                @click="
                  $router.push({
                    name: 'profile-update-current-status-id',
                    params: { id: 1 },
                  })
                "
                class="text-teal-700 hover:text-teal-900 transition cursor-pointer"
              >
                <svg
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                >
                  <path
                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                  />
                </svg>
              </button>
            </div>

            <div>
              <span
                class="inline-flex capitalize items-center gap-1.5 px-3 py-1 rounded-full bg-teal-50 text-teal-800 text-xs font-semibold"
              >
                <svg
                  class="w-3.5 h-3.5 text-teal-700"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h0a1 1 0 011 1v3a1 1 0 01-1 1h0a1 1 0 01-1-1v-3z"
                    clip-rule="evenodd"
                  />
                </svg>
                {{ profileStore.data.employment_status }}
              </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
              <div
                class="bg-slate-50/70 border border-slate-200/60 rounded-xl p-3"
              >
                <span
                  class="block text-[11px] font-medium text-slate-400 mb-0.5"
                  >Role</span
                >
                <span class="text-xs font-bold text-slate-800">{{
                  profileStore.data.employment.job_title ?? "Null"
                }}</span>
              </div>

              <div
                class="bg-slate-50/70 border border-slate-200/60 rounded-xl p-3"
              >
                <span
                  class="block text-[11px] font-medium text-slate-400 mb-0.5"
                  >Company</span
                >
                <span class="text-xs font-bold text-slate-800">{{
                  profileStore.data.employment.company_name ?? "Null"
                }}</span>
              </div>
            </div>
          </div>

          <!-- loading -->
          <div
            v-if="profileStore.isLoading && !profileStore.data?.employment"
            class="bg-white rounded-2xl p-6 border border-teal-100/80 shadow-xs space-y-4"
          >
            <div class="flex items-center justify-between">
              <div
                class="w-2/6 p-2 bg-slate-200 animate-pulse rounded-full"
              ></div>
              <div class="size-5 bg-slate-200 animate-pulse rounded-full"></div>
            </div>

            <div class="flex w-full gap-4">
              <div
                class="w-full p-3 border border-slate-200 rounded-xl space-y-3"
              >
                <div
                  class="w-1/3 bg-slate-200 rounded-full p-1.5 animate-pulse"
                ></div>
                <div
                  class="w-1/3 bg-slate-200 rounded-full p-2 animate-pulse"
                ></div>
              </div>

              <div
                class="w-full p-3 border border-slate-200 rounded-xl space-y-3"
              >
                <div
                  class="w-1/3 bg-slate-200 rounded-full p-1.5 animate-pulse"
                ></div>
                <div
                  class="w-1/3 bg-slate-200 rounded-full p-2 animate-pulse"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- 3. Registered Events Card -->
        <div
          class="bg-white rounded-2xl p-6 border border-teal-100/80 shadow-xs space-y-4"
        >
          <h2 class="text-base font-bold text-slate-900 tracking-tight">
            Registered Events
          </h2>

          <div class="space-y-3">
            <div
              v-if="
                !profileStore.isLoading &&
                profileStore.data?.event_registrations
              "
              v-for="event in profileStore.data?.event_registrations"
              :key="event.id"
              class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-3.5 bg-slate-50/60 border border-slate-200/60 rounded-xl gap-4"
            >
              <div class="flex items-center gap-3">
                <div
                  class="bg-[#036250] text-white rounded-lg px-2.5 py-1.5 text-center min-w-[42px] shrink-0"
                >
                  <span
                    class="block text-[9px] font-extrabold uppercase leading-none opacity-80"
                  >
                    {{ getDateMonth(event.event.start_date) }}
                  </span>
                  <span class="block text-xs font-black leading-tight">
                    {{ getDateDay(event.event.start_date) }}
                  </span>
                </div>

                <div class="space-y-0.5">
                  <h3 class="text-xs font-bold text-slate-900">
                    {{ event.event.title }}
                  </h3>
                  <div
                    class="flex items-center gap-1 text-[11px] text-slate-500 font-medium"
                  >
                    <svg
                      v-if="event.event.event_type === 'time'"
                      class="w-3 h-3 text-slate-400"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      viewBox="0 0 24 24"
                    >
                      <circle cx="12" cy="12" r="10" />
                      <path d="M12 6v6l4 2" />
                    </svg>
                    <svg
                      v-else
                      class="w-3 h-3 text-slate-400"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      viewBox="0 0 24 24"
                    >
                      <polygon points="23 7 16 12 23 17 23 7" />
                      <rect x="1" y="5" width="15" height="14" rx="2" ry="2" />
                    </svg>
                    <span class="capitalize">{{ event.event.event_type }}</span>
                  </div>
                </div>
              </div>

              <button
                type="button"
                @click="unRegister(event.id)"
                class="w-full sm:w-auto px-3.5 py-1.5 text-xs font-semibold text-rose-700 bg-white border border-rose-200 hover:bg-rose-50 rounded-lg transition cursor-pointer"
              >
                Unregister
              </button>
            </div>

            <div
              v-else-if="
                profileStore.isLoading &&
                !profileStore.data?.event_registrations
              "
              class="w-full rounded-xl border border-slate-200 p-3 flex justify-between items-center gap-3"
            >
              <div class="w-full flex justify-start items-center gap-3">
                <div
                  class="w-11 h-9 rounded-lg animate-pulse bg-slate-200"
                ></div>
                <div class="w-full space-y-1">
                  <div
                    class="p-2 bg-slate-200 animate-pulse w-1/4 rounded-full"
                  ></div>
                  <div class="flex justify-start gap-1 items-center">
                    <div
                      class="p-2 bg-slate-200 animate-pulse rounded-full"
                    ></div>
                    <div
                      class="p-1.5 bg-slate-200 animate-pulse w-1/8 rounded-full"
                    ></div>
                  </div>
                </div>
              </div>

              <div class="w-25 bg-slate-200 animate-pulse p-4 rounded-xl"></div>
            </div>

            <div
              v-if="
                !profileStore.isLoading &&
                !profileStore.data?.event_registrations
              "
              class="w-full flex justify-center items-center font-Inter text-xs text-slate-400"
            >
              <p>No Join events</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
