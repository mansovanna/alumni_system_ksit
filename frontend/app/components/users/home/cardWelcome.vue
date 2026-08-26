<script setup lang="ts">
import { useAlumnisStore } from "~/stores/alumnis";

const blockUserStore = useBlockUserSt();
const authStore = useAuthStore();
const avatar = useAvatar();

const alumniStores = useAlumnisStore();
</script>

<template>
  <div
    v-if="!alumniStores.isLoading && alumniStores.data?.data.alumni"
    class="lg:col-span-8 w-full bg-white rounded-2xl p-6 border border-teal-100/80 shadow-xs flex flex-col gap-3"
  >
    <div class="flex flex-col justify-between">
      <div>
        <!-- User Header -->
        <div class="flex items-center gap-4">
          <!-- Avatar -->
          <div class="relative shrink-0">
            <!-- Loading Skeleton -->
            <div
              v-if="authStore.isLoading"
              class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-slate-200 animate-pulse"
            ></div>

            <!-- Avatar Image -->
            <img
              v-else
              :src="
                alumniStores.data.data.alumni.profile_url ??
                avatar.textToImage(
                  alumniStores.data.data.alumni.name_english ?? 'User',
                )
              "
              :alt="alumniStores.data.data.alumni.name_english"
              class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover ring-2 ring-emerald-500 ring-offset-2"
            />
          </div>

          <!-- User Info -->
          <div class="space-y-1">
            <!-- Name Loading -->
            <div v-if="authStore.isLoading" class="space-y-2">
              <div
                class="h-6 w-48 sm:w-64 bg-slate-200 rounded-md animate-pulse"
              ></div>

              <div
                class="h-4 w-40 sm:w-56 bg-slate-200 rounded-md animate-pulse"
              ></div>

              <div class="h-5 w-20 bg-slate-200 rounded-md animate-pulse"></div>
            </div>

            <!-- User Information -->
            <template v-else>
              <h1
                class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight"
              >
                Welcome back,
                <span class="capitalize">{{
                  authStore.user?.data.user.name_english
                }}</span>
              </h1>

              <p class="text-xs sm:text-sm text-slate-500 font-medium">
                Class of
                {{
                  alumniStores.data.data.alumni.alumni_info?.graduation_year ??
                  "N/A"
                }}
                •
                {{
                  alumniStores.data.data.alumni.alumni_info?.major?.name ??
                  "N/A"
                }}
              </p>

              <!-- Status Badge -->
              <div class="pt-0.5">
                <span
                  class="inline-flex capitalize items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-teal-50 text-teal-800 text-xs font-semibold"
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

                  {{
                    alumniStores.data.data.alumni.alumni_info?.employment_status
                  }}
                </span>
              </div>
            </template>
          </div>
        </div>

        <hr class="my-5 border-slate-100" />

        <!-- Description -->
        <div v-if="authStore.isLoading">
          <div
            class="h-4 w-full max-w-md bg-slate-200 rounded-md animate-pulse"
          ></div>
        </div>

        <p v-else class="text-xs sm:text-sm text-slate-500 font-normal">
          Update your career status to help current students find mentors.
        </p>
      </div>

      <!-- Update Status Button -->
      <div class="mt-4">
        <!-- Loading Button -->
        <div
          v-if="authStore.isLoading"
          class="h-10 w-32 bg-slate-200 rounded-xl animate-pulse"
        ></div>

        <!-- Actual Button -->
        <button
          v-else
          @click="blockUserStore.handleUpdateStatus"
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#036250] hover:bg-[#024a3c] text-white text-xs sm:text-sm font-semibold rounded-xl transition cursor-pointer shadow-xs"
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

          Update Status
        </button>
      </div>
    </div>
  </div>

  <div
    v-else-if="!alumniStores.isLoading && !alumniStores.data?.data.alumni"
    class="lg:col-span-8 w-full bg-white rounded-2xl p-6 border border-teal-500/20 shadow-xs flex flex-col gap-3"
  >
    <div class="flex justify-start w-full gap-2">
      <div
        class="w-22 h-22 bg-slate-200 animate-pulse duration-300 rounded-full"
      >
        <div class="size-22"></div>
      </div>
      <div class="w-full animate-pulse space-y-2">
        <div class="w-1/2 bg-slate-200 rounded-full p-3"></div>
        <div class="w-1/3 bg-slate-200 rounded-full p-2"></div>
        <div class="flex justify-start items-center gap-1">
          <div class="bg-slate-200 rounded-full p-2.5"></div>
          <div class="w-1/6 bg-slate-200 rounded-full p-2"></div>
        </div>
      </div>
    </div>
    <hr class="text-slate-100 my-2" />

    <div class="bg-slate-200 rounded-full p-2 animate-pulse"></div>
    <div class="bg-slate-200 rounded-full w-1/5 p-4 animate-pulse"></div>
  </div>
</template>
