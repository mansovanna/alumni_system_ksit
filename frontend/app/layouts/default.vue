<script setup lang="ts">
import { useRoute } from "#imports";

const navLinks = [
  { name: "Home", path: "/", icon: "home" },
  { name: "Events", path: "/events", icon: "calendar" },
  { name: "Directory", path: "/directory", icon: "users" },
  { name: "Profile", path: "/profile", icon: "user" },
];

const route = useRoute();

const authStore = useAuthStore();
const avata = useAvatar();
</script>

<template>
  <div
    class="w-full min-h-screen flex flex-col bg-[#F2F9F7] relative pb-16 md:pb-0 font-Inter"
  >
    <!-- Navbar Header -->
    <header
      class="w-full bg-white border-b border-slate-200 shrink-0 sticky top-0 z-30 flex justify-center items-center"
    >
      <div
        class="max-w-6xl mx-auto w-full px-4 sm:px-8 py-1.5 flex items-center justify-between"
      >
        <!-- Brand Logo / Title -->
        <NuxtLink to="/" class="flex items-center gap-2">
          <img
            src="../assets/svg/LOGO-APP2.svg"
            alt="Avatar"
            class="w-12 h-12 object-center object-cover"
          />
        </NuxtLink>

        <!-- Desktop Navigation Links -->
        <nav class="hidden md:flex items-center space-x-8">
          <NuxtLink
            v-for="link in navLinks"
            :key="link.name"
            :to="link.path"
            class="relative py-2 text-xs font-semibold transition-colors"
            :class="
              route.path === link.path ||
              (link.path === '/' && route.path === '')
                ? 'text-successDark'
                : 'text-slate-600 hover:text-slate-900'
            "
          >
            {{ link.name }}
            <span
              v-if="
                route.path === link.path ||
                (link.path === '/' && route.path === '')
              "
              class="absolute bottom-0 left-0 right-0 h-0.5 bg-successDark rounded-full"
            ></span>
          </NuxtLink>
        </nav>

        <!-- Right Action Icons -->
        <div class="flex items-center space-x-3 sm:space-x-4">
          <NuxtLink
            to="/notifications"
            class="text-slate-500 hover:text-successDark transition p-1"
          >
            <svg
              class="w-5 h-5 text-successDark"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
              />
            </svg>
          </NuxtLink>

          <NuxtLink to="/profile">
            <ClientOnly>
              <img
                :src="
                  authStore.user?.data.user.avatar ??
                  avata.textToImage(
                    authStore.user?.data.user.name_english ?? 'User',
                  )
                "
                alt="Profile Avatar"
                class="w-8 h-8 rounded-full object-cover border border-slate-200 hover:ring-2 hover:ring-successDark transition"
              />

              <!-- ផ្នែក Loading (Fallback) ការពារកុំឲ្យ UI លោតញ័រពេល Render  -->
              <template #fallback>
                <div class="flex items-center gap-2">
                  <div
                    class="w-10 h-10 rounded-full bg-gray-200 animate-pulse"
                  ></div>
                </div>
              </template>
            </ClientOnly>
          </NuxtLink>
        </div>
      </div>
    </header>

    <!-- Page Content -->
    <main class="flex-1 max-w-6xl w-full mx-auto p-4 sm:p-6 md:p-8">
      <slot />
    </main>

    <!-- Mobile Bottom Navigation Bar -->
    <div
      class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-slate-100 py-2 px-4 flex justify-around items-center z-40"
    >
      <NuxtLink
        v-for="link in navLinks"
        :key="link.name"
        :to="link.path"
        class="flex flex-col items-center gap-1 px-4 py-1.5 rounded-2xl transition"
        :class="
          route.path === link.path || (link.path === '/' && route.path === '')
            ? 'bg-successDark text-white font-medium'
            : 'text-slate-500 hover:text-slate-800'
        "
      >
        <svg
          v-if="link.icon === 'home'"
          class="w-4 h-4"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 00-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
          />
        </svg>
        <svg
          v-else-if="link.icon === 'calendar'"
          class="w-4 h-4"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
          />
        </svg>
        <svg
          v-else-if="link.icon === 'users'"
          class="w-4 h-4"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
          />
        </svg>
        <svg
          v-else-if="link.icon === 'user'"
          class="w-4 h-4"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
          />
        </svg>
        <span class="text-[8px]">{{ link.name }}</span>
      </NuxtLink>
    </div>
  </div>
</template>
