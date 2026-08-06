<script setup>



definePageMeta({
  layout: "admin",
});
</script>

<template>
  <div class="flex flex-col gap-2">
    <div class="w-full grid grid-cols-4 gap-6 place-items-center">
      <AdminsBoxSmall :totalAlumni="100" :title="'Total Alumni'"  />
      <AdminsBoxSmall :totalAlumni="100" :title="'Departments'" :icon="'class'" :color="'emerald'" />
      <AdminsBoxSmall :totalAlumni="100" :title="'Computer of Technology'" :icon="'computer'" :color="'info'" />
      <AdminsBoxSmall :totalAlumni="100" :title="'Food of Technology'" :icon="'physicsIcon'" :color="'pink'" />
      <AdminsBoxSmall :totalAlumni="100" :title="'Electrical Engineering'" :icon="'tools'" :color="'rose'" />
      <AdminsBoxSmall :totalAlumni="100" :title="'Crop Science'" :icon="'wheatIcon'" :color="'success'" />
      <AdminsBoxSmall :totalAlumni="100" :title="'Animal Science'" :icon="'engineIcon'" :color="'danger'" />

    </div>
    <!-- Page Content -->

    <!-- Chart -->

    <div class="mt-4"></div>

    <div class="w-1/2">
      <AlumniChart/>
    </div>

    <AlumniBarChar/>
    
  </div>
</template>
