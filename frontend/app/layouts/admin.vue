<template>
  <div class="w-full h-screen flex bg-bg relative">
    <!-- Block SideBar -->
    <aside>
      <AdminsSidebar />
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
