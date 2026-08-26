<script setup lang="ts">
definePageMeta({
  layout: "default",
});

const quickLinks = ref([
  { id: 1, title: "Mentorship Program", icon: "academic-cap" },
  { id: 2, title: "Job Board", icon: "briefcase" },
  { id: 3, title: "Give Back", icon: "heart" },
]);
</script>

<template>
  <div class=" sm:space-y-6 space-y-4"> 
    <!-- ================= TOP ROW ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <!-- Welcome Card (Left) -->
      <UsersHomeCardWelcome />
      <!-- Network Strength Card (Right) -->
      <UsersHomeNetworkStreagth />
    </div>

    <!-- ================= BOTTOM ROW ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
      <!-- Upcoming Events Section (Left) -->
      <div class="lg:col-span-8 space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-bold text-slate-900 tracking-tight">
            Upcoming Events
          </h2>
          <a href="#" class="text-xs font-bold text-[#036250] hover:underline"
            >View All</a
          >
        </div>

        <!-- Cards Container -->
        <UsersHomeUpcomingEvent />
      </div>

      <!-- Right Side: Recent Notifications & Quick Links -->
      <div class="lg:col-span-4 space-y-6">
        <!-- Recent Notifications Card -->

        <UsersHomeRecentNotifications />
        <!-- Quick Links Card -->
        <div
          class="bg-white rounded-2xl p-5 border border-teal-100/80 shadow-xs space-y-4"
        >
          <h3 class="text-xs font-bold text-slate-800">Quick Links</h3>
          <hr class="border-slate-100 -mx-5" />

          <div class="space-y-1">
            <a
              v-for="link in quickLinks"
              :key="link.id"
              href="#"
              class="flex items-center justify-between p-2 rounded-xl hover:bg-slate-50 text-xs font-semibold text-slate-700 hover:text-slate-900 transition group"
            >
              <div class="flex items-center gap-3">
                <div
                  class="w-7 h-7 rounded-lg bg-teal-50 text-[#036250] flex items-center justify-center"
                >
                  <svg
                    v-if="link.icon === 'academic-cap'"
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                  >
                    <path d="M12 14l9-5-9-5-9 5 9 5z" />
                    <path
                      d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"
                    />
                  </svg>
                  <svg
                    v-else-if="link.icon === 'briefcase'"
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                  >
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                  </svg>
                  <svg
                    v-else
                    class="w-4 h-4"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                  >
                    <path
                      d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"
                    />
                  </svg>
                </div>
                <span>{{ link.title }}</span>
              </div>

              <svg
                class="w-3.5 h-3.5 text-slate-300 group-hover:text-slate-500 transition"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
              >
                <path d="M9 5l7 7-7 7" />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
