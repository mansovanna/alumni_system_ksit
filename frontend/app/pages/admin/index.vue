<script setup>
import ApexCharts from "~/components/graphs/ApexCharts.vue";
import DonutChart from "~/components/graphs/DonutChart.vue";
import LoadingChartBar from "~/widgets/LoadingChartBar.vue";
import LoadingItems from "~/widgets/LoadingItems.vue";

definePageMeta({
  middleware: ["auth", "auth"],
  layout: "admin",
});

const dashStore = useDashStore();

onMounted(async () => {
  try {
    await dashStore.getDash();
    await dashStore.chartByWorkStatus();
    await dashStore.chartByYears();
  } catch (error) {
    console.error("Dashboard loading error:", error);
  }
});
</script>

<template>
  <div class="flex flex-col gap-2">
    <div
      v-if="dashStore.data"
      class="w-full grid grid-cols-4 gap-6 place-items-center max-xl:grid-cols-3 max-lg:grid-cols-2 max-md:grid-cols-1"
    >
      <AdminsBoxSmall
        :totalAlumni="dashStore.data.total_alumni ?? 0"
        :title="'Total Alumni'"
      />
      <AdminsBoxSmall
        :totalAlumni="dashStore.data.major ?? 0"
        :title="'Departments'"
        :icon="'class'"
        :color="'emerald'"
      />
      <AdminsBoxSmall
        v-for="(item, index) in dashStore.data.majors"
        :key="index"
        :totalAlumni="item.users_count"
        :title="item.title"
        :body="item.body"
        :icon="item.icon"
        :color_from="item.color_from"
        :color_to="item.color_to"
      />
    </div>
    <!-- Page Content -->

    <!-- Chart -->
    <div
      v-else-if="dashStore.loading && !dashStore.data"
      class="w-full justify-start py-4 animate-pulse grid grid-cols-4 gap-6 place-items-center max-xl:grid-cols-3 max-lg:grid-cols-2 max-md:grid-cols-1"
    >
      <LoadingItems />
      <LoadingItems />
      <LoadingItems />
      <LoadingItems />
    </div>
    <div class="mt-0"></div>

    <div class="w-1/2">
      <!-- <AlumniChart /> -->
    </div>

    <!-- <AlumniBarChar /> -->
    <div class="flex w-full max-lg:flex-col gap-6">
      <div v-if="dashStore.chartWorks" class="w-1/3 max-lg:w-full">
        <DonutChart />
      </div>

      <div
        v-else-if="dashStore.isLoadingByWork && !dashStore.chartWorks"
        class="w-1/3 max-lg:w-full rounded-xl bg-white shadow"
      >
        <div
          class="w-full h-[56vh] animate-pulse flex flex-col justify-between items-center gap-3 p-4"
        >
          <div class="w-full">
            <div class="w-1/2 bg-slate-200 rounded-full p-2.5"></div>
            <div class="w-1/2 bg-slate-200 rounded-full p-1 mt-2"></div>
          </div>
          <div class="w-full flex justify-center items-center">
            <div class="size-60 bg-slate-200 rounded-full p-1"></div>
          </div>

          <div class="w-full">
            <div class="flex w-full gap-5">
              <div class="flex gap-2 w-full">
                <div class="size-3 bg-slate-200 rounded-full p-1 mt-2"></div>
                <div class="w-full bg-slate-200 rounded-full p-1 mt-2"></div>
              </div>
              <div class="w-1/3 bg-slate-200 rounded-full p-1 mt-2"></div>
            </div>

            <div class="flex w-full gap-5">
              <div class="flex gap-2 w-full">
                <div class="size-3 bg-slate-200 rounded-full p-1 mt-2"></div>
                <div class="w-full bg-slate-200 rounded-full p-1 mt-2"></div>
              </div>
              <div class="w-1/3 bg-slate-200 rounded-full p-1 mt-2"></div>
            </div>

            <div class="flex w-full gap-5">
              <div class="flex gap-2 w-full">
                <div class="size-3 bg-slate-200 rounded-full p-1 mt-2"></div>
                <div class="w-full bg-slate-200 rounded-full p-1 mt-2"></div>
              </div>
              <div class="w-1/3 bg-slate-200 rounded-full p-1 mt-2"></div>
            </div>

            <div class="flex w-full gap-5">
              <div class="flex gap-2 w-full">
                <div class="size-3 bg-slate-200 rounded-full p-1 mt-2"></div>
                <div class="w-full bg-slate-200 rounded-full p-1 mt-2"></div>
              </div>
              <div class="w-1/3 bg-slate-200 rounded-full p-1 mt-2"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="w-full">
        <div v-if="dashStore.chartYears">
          <ApexCharts />
        </div>

        <div v-else-if="dashStore.isLoadingByYear && !dashStore.chartYears">
          <LoadingChartBar />
        </div>
      </div>
    </div>
  </div>
</template>
