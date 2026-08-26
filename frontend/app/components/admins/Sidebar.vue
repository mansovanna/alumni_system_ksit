<script setup lang="ts">
import AnalyIcon from "../icons/AnalyIcon.vue";
import CourseIcon from "../icons/CourseIcon.vue";
import DashboardIcon from "../icons/DashboardIcon.vue";
import DateIcon from "../icons/DateIcon.vue";
import MessageIcon from "../icons/MessageIcon.vue";
import UserAdminIcon from "../icons/UserAdminIcon.vue";
import UserIcon from "../icons/UserIcon.vue";
import { markRaw } from "vue";
const pages = reactive([
  { name: "Dashboard", icon: markRaw(DashboardIcon), route: "/admins" },
  { name: "Users", icon: markRaw(UserIcon), route: "/admins/users" },
  { name: "Staff", icon: markRaw(UserAdminIcon), route: "/admins/staffs" },
  {
    name: "Departments",
    icon: markRaw(CourseIcon),
    route: "/admins/department",
  },
  {
    name: "Report & Statistics",
    icon: markRaw(AnalyIcon),
    route: "/admins/report",
  },
  { name: "Messages", icon: markRaw(MessageIcon), route: "/admins/messages" },
  { name: "Events", icon: markRaw(DateIcon), route: "/admins/events" },
]);

const sideBarStore = useSideBarStore();
</script>

<template>
  <div
    class="h-screen bg-secondary text-white flex flex-col justify-start text-nowrap duration-500 ease-in-out"
    :class="sideBarStore.sidebar ? 'w-64' : 'w-18 max-lg:w-64'"
  >
    <div
      class="px-4 py-2 flex justify-start gap-4 items-center overflow-x-clip w-full"
    >
      <img src="../../assets/svg/LOGO-APP.svg" alt="" class="w-12 h-12" />
      <div class=" overflow-clip">
        <h1 class="text-sm font-bokor">ប្រព័ន្ធគ្រប់គ្រងអតីតនិស្សនិត</h1>
        <p class="text-sm text-gray-400 font-Poppins">Alumni Portal</p>
      </div>
    </div>
    <hr class="border-gray-600" />
    <nav class="p-4">
      <ul>
        <li
          v-for="(page, index) in pages"
          :key="index"
          class="flex flex-col gap-2"
        >
          <RouterLink
            :to="page.route"
            class="text-gray-300 hover:text-white mb-0.5"
          >
            <div
              class="flex justify-start items-center gap-3 hover:bg-primary/50 rounded-md px-2.5 py-2 transition-colors duration-300 overflow-clip"
              :class="$route.path == page.route ? 'bg-primary' : ''"
            >
              <div class="w-5 h-5 flex justify-center items-center">
                <component :is="page.icon" class="w-5 h-5 inline-block" />
              </div>
              <span class="">{{ page.name }} </span>
            </div>
          </RouterLink>
        </li>
      </ul>
    </nav>

    <!-- Block Logout -->

    <div class="mt-auto">
      <hr class="border-gray-600" />
      <button
        class="w-full text-gray-400 py-3 px-4 rounded-md hover:bg-primary-dark transition-colors duration-300"
      >
        Logout
      </button>
    </div>
  </div>
</template>
