<script setup lang="ts">
import { ref } from "vue";

definePageMeta({
  layout: "default",
});

// Form Data State
const form = ref({
  fullName: "Sarah Jenkins",
  email: "sarah.j@example.com",
  phone: "+1 (555) 123-4567",
  location: "San Francisco, CA",
  graduationYear: "2023",
  major: "Computer Science",
  currentRole: "Senior Product Designer",
  company: "Tech Innovations Inc.",
  linkedin: "linkedin.com/in/sarahj",
  portfolio: "sarahdesigns.co",
});

const graduationYears = ["2024", "2023", "2022", "2021", "2020"];

const handleSave = () => {
  console.log("Saving profile data:", form.value);
  alert("រក្សាទុកព័ត៌មានជោគជ័យ!");
};

const router = useRouter();

const handleCancel = () => {
  //   console.log("Cancelled");
  router.back();
};
</script>

<template>
  <div class="space-y-6">
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

    <!-- Full Screen Container -->
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
                src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&q=80&w=256"
                alt="Profile Picture"
                class="w-full h-full object-cover"
              />
            </div>
            <!-- Edit Badge Icon -->
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

        <!-- Form Content (Full Width) -->
        <form
          @submit.prevent="handleSave"
          class="w-full px-6 sm:px-12 lg:px-20 py-8 space-y-8"
        >
          <!-- Section 1: Personal Details -->
          <div class="space-y-4">
            <h2 class="text-lg font-bold text-slate-800 tracking-tight">
              Personal Details
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Full Name -->
              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-600"
                  >Full Name</label
                >
                <input
                  v-model="form.fullName"
                  type="text"
                  class="w-full bg-slate-50/60 border border-slate-200 focus:bg-white rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 transition-all duration-200"
                />
              </div>
              <!-- Email Address -->
              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-600"
                  >Email Address</label
                >
                <input
                  v-model="form.email"
                  type="email"
                  class="w-full bg-slate-50/60 border border-slate-200 focus:bg-white rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 transition-all duration-200"
                />
              </div>
              <!-- Phone Number -->
              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-600"
                  >Phone Number</label
                >
                <input
                  v-model="form.phone"
                  type="text"
                  class="w-full bg-slate-50/60 border border-slate-200 focus:bg-white rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 transition-all duration-200"
                />
              </div>
              <!-- Location -->
              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-600"
                  >Location</label
                >
                <input
                  v-model="form.location"
                  type="text"
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
              <!-- Graduation Year -->
              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-600"
                  >Graduation Year</label
                >
                <div class="relative">
                  <select
                    v-model="form.graduationYear"
                    class="w-full appearance-none bg-slate-50/60 border border-slate-200 focus:bg-white rounded-xl pl-4 pr-10 py-3 text-sm text-slate-800 focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 transition-all duration-200 cursor-pointer"
                  >
                    <option
                      v-for="year in graduationYears"
                      :key="year"
                      :value="year"
                    >
                      {{ year }}
                    </option>
                  </select>
                  <svg
                    class="w-4 h-4 absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="m19.5 8.25-7.5 7.5-7.5-7.5"
                    />
                  </svg>
                </div>
              </div>
              <!-- Major -->
              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-600"
                  >Major</label
                >
                <input
                  v-model="form.major"
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

          <!-- Section 3: Current Employment -->
          <div class="space-y-4">
            <h2 class="text-lg font-bold text-slate-800 tracking-tight">
              Current Employment
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Current Role -->
              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-600"
                  >Current Role</label
                >
                <input
                  v-model="form.currentRole"
                  type="text"
                  class="w-full bg-slate-50/60 border border-slate-200 focus:bg-white rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 transition-all duration-200"
                />
              </div>
              <!-- Company -->
              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-600"
                  >Company</label
                >
                <input
                  v-model="form.company"
                  type="text"
                  class="w-full bg-slate-50/60 border border-slate-200 focus:bg-white rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:border-teal-600 focus:ring-2 focus:ring-teal-600/20 transition-all duration-200"
                />
              </div>
            </div>
          </div>

          <hr class="border-slate-100" />

          <!-- Section 4: Web Links -->
          <div class="space-y-4">
            <h2 class="text-lg font-bold text-slate-800 tracking-tight">
              Web Links
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- LinkedIn Profile URL -->
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
              <!-- Portfolio / Website -->
              <div class="space-y-1.5">
                <label class="block text-xs font-semibold text-slate-600"
                  >Portfolio / Website</label
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
                    v-model="form.portfolio"
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
              class="px-6 py-3 rounded-xl border border-slate-200 text-slate-600 text-sm font-semibold hover:bg-slate-50 active:bg-slate-100 transition-colors duration-200"
            >
              Cancel
            </button>
            <button
              type="submit"
              class="px-8 py-3 rounded-xl bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white text-sm font-semibold shadow-md shadow-teal-600/20 transition-all duration-200"
            >
              Save Changes
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
