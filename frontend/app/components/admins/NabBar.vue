<script setup lang="ts">
import SideBarIcon from "../icons/SideBarIcon.vue";

const sideBarStore = useSideBarStore();
const authStore = useAuthStore();
const avata = useAvatar();

function splitName(name: string) {
  if (!name) return "";
  const parts = name.replace(/-/g, " ").trim().split(/\s+/);
  return parts[parts.length - 1];
}
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

      <!-- កែប្រែ៖ រុំ ClientOnly លើ route name ដើម្បីជៀសវាង Server/Client មិនដូចគ្នា -->
      <ClientOnly>
        <h1 class="text-lg font-semibold text-gray-800 capitalize">
          {{ $route.name }}
        </h1>
        <template #fallback>
          <div class="h-6 w-24 bg-gray-200 animate-pulse rounded"></div>
        </template>
      </ClientOnly>
    </div>

    <!-- Block user name and avatar -->
    <!-- កែប្រែ៖ រុំ ClientOnly លើផ្នែក User Profile ទាំងមូល -->
    <ClientOnly>
      <div class="flex items-center gap-2 leading-2">
        <div class="flex flex-col items-end">
          <span class="text-gray-800 font-medium text-sm">
            {{
              splitName(authStore.user?.data.user.name_english ?? "") || "N/A"
            }}
          </span>
          <p class="text-[11px] text-gray-500 capitalize">
            {{ authStore.user?.data.user.role.name ?? "N/A" }}
          </p>
        </div>
        <div>
          <img
            :src="
              authStore.user?.data.user.avatar ??
              avata.textToImage(
                authStore.user?.data.user.name_english ?? 'User',
              )
            "
            alt="User Avatar"
            class="w-10 h-10 rounded-full object-center object-cover ring-2 ring-primary border border-white"
          />
        </div>
      </div>

      <!-- ផ្នែក Loading (Fallback) ការពារកុំឲ្យ UI លោតញ័រពេល Render  -->
      <template #fallback>
        <div class="flex items-center gap-2">
          <div class="flex flex-col items-end gap-1">
            <div class="h-4 w-28 bg-gray-200 animate-pulse rounded"></div>
            <div class="h-3 w-16 bg-gray-200 animate-pulse rounded"></div>
          </div>
          <div class="w-10 h-10 rounded-full bg-gray-200 animate-pulse"></div>
        </div>
      </template>
    </ClientOnly>
  </nav>
</template>
