<script setup lang="ts">
const sidebarStore = useSideBarStore();
</script>

<template>
  <div class="w-full h-screen flex bg-bg relative overflow-hidden">
    <!-- Block SideBar -->
    <aside
      @click="sidebarStore.isSide = !sidebarStore.isSide"
      class="block max-lg:fixed max-lg:top-0 bg-black/40 max-lg:bottom-0 max-lg:z-50  ease-in-out duration-500"
      :class="
        !sidebarStore.sidebar ? 'max-lg:w-full max-lg:left-full' : ' max-lg:w-full top-0 bottom-0'
      "
    >
      <AdminsSidebar @click.stop />
    </aside>
    <!-- Block Side bar -->
    <main class="flex-1 overflow-auto w-full">
      <AdminsNabBar class="w-full sticky top-0 z-30" />
      <div class="p-4">
        <slot />
      </div>
    </main>
  </div>
</template>
