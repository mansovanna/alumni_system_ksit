<script setup lang="ts">
const blockUserStore = useBlockUserSt();
const user = ref({
  name: "Sarah",
  avatar:
    "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=200&auto=format&fit=crop",
  classYear: "2021",
  role: "Software Engineer at TechCorp",
  status: "Employed",
  networkStrength: 84,
  connections: 142,
  mentoring: 5,
});
</script>

<template>
  <div
    class="lg:col-span-8 bg-white rounded-2xl p-6 border border-teal-100/80 shadow-xs flex flex-col justify-between"
  >
    <div>
      <div class="flex items-center gap-4">
        <!-- Avatar with Ring -->
        <div class="relative shrink-0">
          <img
            :src="user.avatar"
            :alt="user.name"
            class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover ring-2 ring-emerald-500 ring-offset-2"
          />
        </div>

        <!-- User Info -->
        <div class="space-y-1">
          <h1
            class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight"
          >
            Welcome back, {{ user.name }}
          </h1>
          <p class="text-xs sm:text-sm text-slate-500 font-medium">
            Class of {{ user.classYear }} • {{ user.role }}
          </p>
          <!-- Status Badge -->
          <div class="pt-0.5">
            <span
              class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-teal-50 text-teal-800 text-xs font-semibold"
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
              {{ user.status }}
            </span>
          </div>
        </div>
      </div>

      <hr class="my-5 border-slate-100" />

      <p class="text-xs sm:text-sm text-slate-500 font-normal">
        Update your career status to help current students find mentors.
      </p>
    </div>

    <!-- Update Status Button -->
    <div class="mt-4">
      <button
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
</template>
