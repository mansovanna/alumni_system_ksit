<script setup lang="ts">
import SideBarIcon from "../icons/SideBarIcon.vue";

const sideBarStore = useSideBarStore();
</script>

<template>
  <nav
    class="w-full h-14 bg-white border-b border-slate-200 flex justify-between items-center py-1 px-6"
  >
    <div class="flex items-center gap-2">
      <button
        @click="sideBarStore.isSide = !sideBarStore.isSide"
        class="p-2 rounded-md hover:bg-gray-100 transition-colors duration-300 cursor-pointer"
      >
        <side-bar-icon class="w-6 h-6" />
      </button>
      <h1 class="text-lg font-semibold text-gray-800 capitalize">
        {{ $route.name }}
      </h1>
    </div>

    <!-- Block user name and avatar -->
    <div class="flex items-center gap-2">
      <div class="flex flex-col items-end">
        <span class="text-gray-800 font-medium">Admin Name</span>
        <p class="text-[12px] text-gray-500">Admin</p>
      </div>
      <div>
        <img
          src="https://plus.unsplash.com/premium_photo-1689568126014-06fea9d5d341?fm=jpg&q=60&w=3000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8cHJvZmlsZSUyMHBpY3R1cmV8ZW58MHx8MHx8fDA%3D"
          alt="User Avatar"
          class="w-10 h-10 rounded-full object-center object-cover"
        />
      </div>
    </div>
  </nav>
</template>
