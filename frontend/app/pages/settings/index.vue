<script setup lang="ts">
import { ref } from "vue";

const activeSection = ref("Security");
const sections = [
  { name: "Security", icon: "lock" },
  { name: "Notifications", icon: "bell" },
  { name: "Privacy", icon: "eye" },
];

const currentPassword = ref("");
const newPassword = ref("");
const confirmPassword = ref("");

const emailNotifications = ref(true);
const directoryVisibility = ref(false);
</script>

<template>
  <div class="bg-[#F2F9F7] p-4 sm:p-6 md:p-8 font-Inter text-slate-800">
    <div class="max-w-7xl mx-auto space-y-6">
      <div class="w-full  px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <h1 class="text-2xl font-extrabold text-slate-900">Account Settings</h1>
        <p class="text-sm text-slate-500">
          Manage your password, notifications, and privacy preferences.
        </p>
      </div>

      <div
        class="w-full grid grid-cols-1 md:grid-cols-[350px_1fr] gap-6 mt-8"
      >
        <!-- Sidebar -->
        <nav class="space-y-2 ">
          <button
            v-for="section in sections"
            :key="section.name"
            @click="activeSection = section.name"
            :style="
              activeSection === section.name
                ? { backgroundColor: '#E3F3EF', color: '#007A64' }
                : {}
            "
            class="w-full flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-100 transition-all"
          >
            <svg
              v-if="section.icon === 'lock'"
              class="w-4 h-4"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
              />
            </svg>
            <svg
              v-else-if="section.icon === 'bell'"
              class="w-4 h-4"
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
            <svg
              v-else
              class="w-4 h-4"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
              />
            </svg>
            <span>{{ section.name }}</span>
          </button>
        </nav>

        <!-- Content -->
        <div class="space-y-6">
          <!-- Change Password Card -->
          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm">
            <div
              class="flex items-center gap-2 px-6 py-4 border-b border-slate-100"
            >
              <svg
                class="w-4 h-4"
                :style="{ color: '#007A64' }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"
                />
              </svg>
              <h2 class="font-bold text-slate-900">Change Password</h2>
            </div>

            <div class="p-6 space-y-4">
              <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700"
                  >Current Password</label
                >
                <input
                  v-model="currentPassword"
                  type="password"
                  placeholder="Enter current password"
                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#007A64]/30 focus:border-[#007A64]"
                />
              </div>
              <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700"
                  >New Password</label
                >
                <input
                  v-model="newPassword"
                  type="password"
                  placeholder="Create new password"
                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#007A64]/30 focus:border-[#007A64]"
                />
              </div>
              <div class="space-y-1.5">
                <label class="text-sm font-semibold text-slate-700"
                  >Confirm New Password</label
                >
                <input
                  v-model="confirmPassword"
                  type="password"
                  placeholder="Confirm new password"
                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#007A64]/30 focus:border-[#007A64]"
                />
              </div>

              <div class="flex justify-end pt-2">
                <button
                  class="px-5 py-2.5 rounded-xl text-white text-sm font-semibold shadow-sm transition-all"
                  :style="{ backgroundColor: '#006B58' }"
                  onmouseover="this.style.backgroundColor = '#005747'"
                  onmouseout="this.style.backgroundColor = '#006B58'"
                >
                  Update Password
                </button>
              </div>
            </div>
          </div>

          <!-- Preferences Card -->
          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm">
            <div
              class="flex items-center gap-2 px-6 py-4 border-b border-slate-100"
            >
              <svg
                class="w-4 h-4"
                :style="{ color: '#007A64' }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M4 6h16M4 12h16M4 18h7"
                />
              </svg>
              <h2 class="font-bold text-slate-900">Preferences</h2>
            </div>

            <div class="divide-y divide-slate-100">
              <div class="flex items-center justify-between px-6 py-4">
                <div class="pr-4">
                  <p class="text-sm font-semibold text-slate-900">
                    Email Notifications
                  </p>
                  <p class="text-xs text-slate-500 mt-0.5">
                    Receive updates about alumni events, news, and directory
                    messages.
                  </p>
                </div>
                <button
                  @click="emailNotifications = !emailNotifications"
                  class="shrink-0 w-6 h-6 rounded-full flex items-center justify-center transition-all"
                  :style="
                    emailNotifications
                      ? { backgroundColor: '#2563EB' }
                      : { border: '2px solid #CBD5E1' }
                  "
                >
                  <svg
                    v-if="emailNotifications"
                    class="w-3.5 h-3.5 text-white"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="3"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                </button>
              </div>

              <div class="flex items-center justify-between px-6 py-4">
                <div class="pr-4">
                  <p class="text-sm font-semibold text-slate-900">
                    Directory Visibility
                  </p>
                  <p class="text-xs text-slate-500 mt-0.5">
                    Allow other alumni to find and view your profile in the
                    directory.
                  </p>
                </div>
                <button
                  @click="directoryVisibility = !directoryVisibility"
                  class="shrink-0 w-6 h-6 rounded-full flex items-center justify-center transition-all"
                  :style="
                    directoryVisibility
                      ? { backgroundColor: '#2563EB' }
                      : { border: '2px solid #CBD5E1' }
                  "
                >
                  <svg
                    v-if="directoryVisibility"
                    class="w-3.5 h-3.5 text-white"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="3"
                      d="M5 13l4 4L19 7"
                    />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Account Access Card -->
          <div
            class="bg-white rounded-2xl border border-slate-100 shadow-sm px-6 py-5 flex items-center justify-between"
          >
            <div>
              <p class="font-bold text-red-500">Account Access</p>
              <p class="text-xs text-slate-500 mt-0.5">
                Securely sign out of your account on this device.
              </p>
            </div>
            <button
              class="flex items-center gap-1.5 px-4 py-2 rounded-xl border border-red-200 text-red-500 text-sm font-semibold hover:bg-red-50 transition-all shrink-0"
            >
              <svg
                class="w-4 h-4"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"
                />
              </svg>
              <span>Logout</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
