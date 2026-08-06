<script setup lang="ts">
import AnalyIcon from "../icons/AnalyIcon.vue";
import CourseIcon from "../icons/CourseIcon.vue";
import DashboardIcon from "../icons/DashboardIcon.vue";
import DateIcon from "../icons/DateIcon.vue";
import MessageIcon from "../icons/MessageIcon.vue";
import SettingIcon from "../icons/SettingIcon.vue";
import UserAdminIcon from "../icons/UserAdminIcon.vue";
import UserIcon from "../icons/UserIcon.vue";

const pages = reactive([
  { name: "Dashboard", icon: DashboardIcon, route: "/admin" },
  { name: "Users", icon: UserIcon, route: "/admin/users" },
  { name: "Staff", icon: UserAdminIcon, route: "/admin/staff" },
  { name: "Departments", icon: CourseIcon, route: "/admin/department" },
  { name: "Report & Statistics", icon: AnalyIcon, route: "/admin/report" },
  { name: "Messages", icon: MessageIcon, route: "/admin/message" },
  { name: "Events", icon: DateIcon, route: "/admin/event" },
  { name: "Settings", icon: SettingIcon, route: "/admin/setting" },
]);
</script>

<template>
  <div
    class="w-64 h-screen bg-secondary text-white flex flex-col justify-start"
  >
    <div class="p-4 flex justify-start gap-2 items-center">
      <img src="../../assets/svg/LOGO-APP.svg" alt="" class="w-12 h-12" />
      <div>
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
              class="flex items-center gap-2 hover:bg-primary/50 rounded-md px-2 py-2 transition-colors duration-300"
              :class="$route.path == page.route ? 'bg-primary' : ''"
            >
              <component :is="page.icon" class="w-5 h-5 inline-block" />
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
