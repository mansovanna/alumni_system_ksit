<script setup lang="ts">
import { ref, watch } from "vue";
import LoadingIcon from "~/components/icons/LoadingIcon.vue";
import { useAlumniProfileStore } from "~/stores/alumnis/alumni";

definePageMeta({
  middleware: ["auth", "alumni"],
  layout: "default",
});

const alumniProfileStore = useAlumniProfileStore();
const route = useRoute();
const router = useRouter();

// Form Data State — only fields that exist on AlumniModels / AlumniUserModel
const form = ref({
  nameEnglish: "",
  nameKhmer: "",
  email: "",
  mobile: "",
  address: "",
  graduationYear: "",
  majorName: "", // read-only, comes from major relation
  bio: "",
  linkedin: "",
  facebook: "",
});

const isSaving = ref(false);
const saveError = ref("");

// Sync form whenever the store's profile data changes (initial load or refetch)
watch(
  () => alumniProfileStore.data,
  (profile) => {
    if (!profile) return;
    form.value = {
      nameEnglish: profile.user?.name_english ?? "",
      nameKhmer: profile.user?.name_khmer ?? "",
      email: profile.user?.email ?? "",
      mobile: profile.user?.mobile ?? "",
      address: profile.address ?? "",
      graduationYear: profile.graduation_year ?? "",
      majorName: profile.major?.name ?? "",
      bio: profile.bio ?? "",
      linkedin: profile.linkedin_url ?? "",
      facebook: profile.facebook_url ?? "",
    };
  },
  { immediate: true }
);

const handleSave = async () => {
  isSaving.value = true;
  saveError.value = "";
  try {
    // Only fields that map back to real columns. user.* fields (name,
    // email, mobile) likely need a separate user-update endpoint —
    // confirm before wiring those up for real.
    const ok = await alumniProfileStore.updateProfile(Number(route.params.id), {
      address: form.value.address,
      graduation_year: form.value.graduationYear,
      bio: form.value.bio,
      linkedin_url: form.value.linkedin,
      facebook_url: form.value.facebook,
    });
    if (ok) {
      alert("រក្សាទុកព័ត៌មានជោគជ័យ!");
    } else {
      saveError.value = "រក្សាទុកមិនជោគជ័យ សូមព្យាយាមម្តងទៀត។";
    }
  } catch (err) {
    console.error("Failed to save profile:", err);
    saveError.value = "រក្សាទុកមិនជោគជ័យ សូមព្យាយាមម្តងទៀត។";
  } finally {
    isSaving.value = false;
  }
};

const handleCancel = () => {
  router.back();
};

onMounted(() => {
  alumniProfileStore.getProfileID(Number(route.params.id));
});
</script>

<template>
  <div>
    <!-- Header -->
    <div class="w-full flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-semibold text-slate-900">Profil info</h1>
        <p class="text-slate-400 text-sm mt-0.5">
          Keep your alumni network informed about your professional journey.
        </p>
      </div>

      <button
        @click="handleCancel"
        type="button"
        class="p-1.5 px-4 flex justify-center items-center gap-1.5 bg-slate-900 text-white hover:bg-slate-800 rounded-lg active:bg-slate-700 transition cursor-pointer shadow-xs"
        aria-label="Go back"
      >
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

    <!-- Loading state -->
    <div
      v-if="alumniProfileStore.isLoading"
      class="w-full flex justify-center items-center py-24"
    >
      <LoadingIcon class="size-10 text-primary" />
    </div>

    <!-- Error/empty state: request finished but no data -->
    <div
      v-else-if="!alumniProfileStore.data"
      class="w-full text-center text-slate-400 py-24 text-sm"
    >
      {{ alumniProfileStore.messageError || "Couldn't load this profile." }}
    </div>

    <!-- Loaded state -->
    <div v-else class="space-y-6 mt-6">
      <div
        class="w-full bg-white font-Inter flex flex-col justify-between rounded-2xl overflow-clip border border-successDark/20"
      >
        <div>
          <!-- Profile Picture Header -->
          <div
            class="w-full pt-10 pb-8 px-6 bg-gradient-to-b from-slate-50 to-white flex flex-col items-center justify-center border-b border-slate-100"
          >
            <div class="relative group cursor-pointer">
              <div
                class="w-28 h-28 sm:w-32 sm:h-32 rounded-full overflow-hidden ring-4 ring-white shadow-md transition-transform duration-300 group-hover:scale-105"
              >
                <img
                  :src="
                    alumniProfileStore.data.profile_photo ||
                    'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=256'
                  "
                  alt="Profile Picture"
                  class="w-full h-full object-cover"
                />
              </div>
              <button
                type="button"
                class="absolute bottom-0 right-0 p-2.5 bg-teal-600 hover:bg-teal-700 text-white rounded-full shadow-lg transition-all duration-200 active:scale-95 border-2 border-white"
                aria-label="Change profile picture"
              >
                <svg
                  class="w-4 h-4"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  viewBox="0 0 24 24"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.89 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.892L16.863 4.487Zm0 0L19.5 7.125"
                  />
                </svg>
              </button>
            </div>
            <p class="text-xs sm:text-sm font-medium text-slate-500 mt-3">
              Tap to change profile picture
            </p>
          </div>

          <!-- Form Content -->
          <form
            @submit.prevent="handleSave"
            class="w-full px-6 sm:px-12 lg:px-20 py-8 space-y-8"
          >
            <div
              v-if="saveError"
              class="rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3"
            >
              {{ saveError }}
            </div>

            <!-- Section 1: Personal Details -->
            <div class="space-y-4">
              <h2 class="text-lg font-bold text-slate-800 tracking-tight">
                Personal Details
              </h2>
              <p class="text-[11px] text-slate-400 -mt-2">
                Name, email, and phone are managed on your account — contact
                admin to update these.
              </p>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-600"
                    >Full Name (English)</label
                  >
                  <input
                    v-model="form.nameEnglish"
                    type="text"
                    disabled
                    class="w-full bg-slate-100/70 border border-slate-200/80 rounded-xl px-4 py-3 text-sm text-slate-500 cursor-not-allowed select-none"
                  />
                </div>
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-600"
                    >Full Name (Khmer)</label
                  >
                  <input
                    v-model="form.nameKhmer"
                    type="text"
                    disabled
                    class="w-full bg-slate-100/70 border border-slate-200/80 rounded-xl px-4 py-3 text-sm text-slate-500 cursor-not-allowed select-none"
                  />
                </div>
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-600"
                    >Email Address</label
                  >
                  <input
                    v-model="form.email"
                    type="email"
                    disabled
                    class="w-full bg-slate-100/70 border border-slate-200/80 rounded-xl px-4 py-3 text-sm text-slate-500 cursor-not-allowed select-none"
                  />
                </div>
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-600"
                    >Phone Number</label
                  >
                  <input
                    v-model="form.mobile"
                    type="text"
                    disabled
                    class="w-full bg-slate-100/70 border border-slate-200/80 rounded-xl px-4 py-3 text-sm text-slate-500 cursor-not-allowed select-none"
                  />
                </div>
                <div class="space-y-1.5 md:col-span-2">
                  <label class="block text-xs font-semibold text-slate-600"
                    >Address</label
                  >
                  <input
                    v-model="form.address"
                    type="text"
                    class="w-full bg-slate-50/60 border border-slate-200 focus:bg-white rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 transition-all duration-200"
                  />
                </div>
                <div class="space-y-1.5 md:col-span-2">
                  <label class="block text-xs font-semibold text-slate-600"
                    >Bio</label
                  >
                  <textarea
                    v-model="form.bio"
                    rows="3"
                    class="w-full bg-slate-50/60 border border-slate-200 focus:bg-white rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 transition-all duration-200"
                  />
                </div>
              </div>
            </div>

            <hr class="border-slate-100" />

            <!-- Section 2: Academic Background -->
            <div class="space-y-4">
              <h2 class="text-lg font-bold text-slate-800 tracking-tight">
                Academic Background
              </h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-600"
                    >Graduation Year</label
                  >
                  <input
                    v-model="form.graduationYear"
                    type="text"
                    class="w-full bg-slate-50/60 border border-slate-200 focus:bg-white rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 transition-all duration-200"
                  />
                </div>
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-600"
                    >Major</label
                  >
                  <input
                    v-model="form.majorName"
                    type="text"
                    disabled
                    class="w-full bg-slate-100/70 border border-slate-200/80 rounded-xl px-4 py-3 text-sm text-slate-500 cursor-not-allowed select-none"
                  />
                  <p class="text-[11px] text-slate-400 italic">
                    Contact admin to update major.
                  </p>
                </div>
              </div>
            </div>

            <hr class="border-slate-100" />

            <!-- Section 3: Employment -->
            <div class="space-y-4">
              <h2 class="text-lg font-bold text-slate-800 tracking-tight">
                Employment
              </h2>
              <div
                v-if="alumniProfileStore.data.employment"
                class="text-sm text-slate-600 space-y-1"
              >
                <p>{{ alumniProfileStore.data.employment.job_title }}</p>
                <p class="text-slate-400">
                  {{ alumniProfileStore.data.employment.location }}
                </p>
              </div>
              <p v-else class="text-sm text-slate-400 italic">
                No employment record on file yet.
              </p>
            </div>

            <hr class="border-slate-100" />

            <!-- Section 4: Web Links -->
            <div class="space-y-4">
              <h2 class="text-lg font-bold text-slate-800 tracking-tight">
                Web Links
              </h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-600"
                    >LinkedIn Profile URL</label
                  >
                  <div class="relative">
                    <svg
                      class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.882 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"
                      />
                    </svg>
                    <input
                      v-model="form.linkedin"
                      type="text"
                      class="w-full bg-slate-50/60 border border-slate-200 focus:bg-white rounded-xl pl-10 pr-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 transition-all duration-200"
                    />
                  </div>
                </div>
                <div class="space-y-1.5">
                  <label class="block text-xs font-semibold text-slate-600"
                    >Facebook URL</label
                  >
                  <div class="relative">
                    <svg
                      class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m-17.432-6.5A8.959 8.959 0 0 0 3 12c0 .778.099 1.533.284 2.253"
                      />
                    </svg>
                    <input
                      v-model="form.facebook"
                      type="text"
                      class="w-full bg-slate-50/60 border border-slate-200 focus:bg-white rounded-xl pl-10 pr-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 transition-all duration-200"
                    />
                  </div>
                </div>
              </div>
            </div>

            <!-- Sticky Action Footer Bar -->
            <div
              class="pt-6 pb-4 flex items-center justify-end gap-3 border-t border-slate-100 sticky bottom-0 bg-white/95 backdrop-blur-md"
            >
              <button
                type="button"
                @click="handleCancel"
                :disabled="isSaving"
                class="px-6 py-3 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 active:bg-slate-100 transition-colors duration-200 disabled:opacity-50"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isSaving"
                class="px-8 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white text-sm font-semibold shadow-md shadow-teal-600/20 transition-all duration-200 disabled:opacity-60 flex items-center gap-2"
              >
                <LoadingIcon v-if="isSaving" class="size-4" />
                {{ isSaving ? "Saving..." : "Save Changes" }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>