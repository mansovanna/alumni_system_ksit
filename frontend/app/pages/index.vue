<script setup lang="ts">
import { useAlumnisStore } from "~/stores/alumnis";

definePageMeta({
  middleware: ["auth", "alumni"],
  layout: "default",
});

const alumnisStore = useAlumnisStore();

const blockUserStore = useBlockUserSt();
const quickLinks = ref([
  { id: 1, title: "Mentorship Program", icon: "academic-cap" },
  { id: 2, title: "Job Board", icon: "briefcase" },
  { id: 3, title: "Give Back", icon: "heart" },
]);

onMounted(() => {
  alumnisStore.getAlumnis();
});
</script>

<template>
  <!-- Block Update status -->
  <UsersInfosUpdateStatus
    v-if="blockUserStore.isUpdateStatus"
    @close="blockUserStore.isUpdateStatus = false"
  />
  <!-- End block update status -->
  <div class="sm:space-y-6 space-y-4">
    <!-- ================= TOP ROW ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <!-- Welcome Card (Left) -->
      <UsersHomeCardWelcome />
      <!-- Network Strength Card (Right) -->
      <UsersHomeRecentNotifications />
    </div>

    <!-- ================= BOTTOM ROW ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start w-full">
      <!-- Upcoming Events Section (Left) -->
      <div class="lg:col-span-15 space-y-4">
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

      
    </div>
  </div>
</template>
