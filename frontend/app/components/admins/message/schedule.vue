<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: true
  },
  recipientCount: {
    type: Number,
    default: 342
  }
})

const emit = defineEmits(['close', 'confirm'])

const sendDate = ref('2023-11-15')
const sendTime = ref('09:00')

const handleConfirm = () => {
  emit('confirm', {
    date: sendDate.value,
    time: sendTime.value
  })
}
</script>

<template>
  <Teleport to="body">
    <!-- Backdrop Blur & Overlay -->
    <Transition name="fade">
      <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs p-4 font-['Inter']"
        @click.self="emit('close')"
      >
        <!-- Modal Card -->
        <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden text-slate-800">
          
          <!-- Header -->
          <div class="flex items-center justify-between px-6 pt-6 pb-4">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                  <line x1="16" y1="2" x2="16" y2="6"/>
                  <line x1="8" y1="2" x2="8" y2="6"/>
                  <line x1="3" y1="10" x2="21" y2="10"/>
                  <path d="M8 14h.01"/><path d="M12 14h.01"/><path d="M16 14h.01"/>
                  <path d="M8 18h.01"/><path d="M12 18h.01"/>
                </svg>
              </div>
              <h2 class="text-base font-bold text-slate-900 tracking-tight">Schedule Message</h2>
            </div>

            <!-- Close Button -->
            <button
              @click="emit('close')"
              type="button"
              class="text-slate-400 hover:text-slate-600 transition p-1 rounded-lg hover:bg-slate-100 cursor-pointer"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M18 6L6 18M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Body -->
          <div class="px-6 space-y-4">
            <p class="text-xs text-slate-500 leading-relaxed font-normal">
              Select a date and time to automatically send this message to the 
              <span class="font-semibold text-slate-700">{{ recipientCount }} selected recipients</span>.
            </p>

            <!-- Send Date Field -->
            <div class="space-y-1">
              <label class="block text-xs font-bold text-slate-700">Send Date</label>
              <div class="relative flex items-center">
                <input
                  v-model="sendDate"
                  type="date"
                  class="w-full pl-9 pr-3 py-2 text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-800 transition font-['Inter']"
                />
                <svg class="w-4 h-4 text-slate-400 absolute left-3 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                  <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
              </div>
            </div>

            <!-- Send Time Field -->
            <div class="space-y-1">
              <label class="block text-xs font-bold text-slate-700">Send Time (Local)</label>
              <div class="relative flex items-center">
                <input
                  v-model="sendTime"
                  type="time"
                  class="w-full pl-9 pr-3 py-2 text-xs font-medium text-slate-800 bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-800 transition font-['Inter']"
                />
                <svg class="w-4 h-4 text-slate-400 absolute left-3 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
              </div>
            </div>

            <!-- Info Note Box -->
            <div class="p-3 bg-slate-50 border border-slate-200/60 rounded-xl flex items-start gap-2.5">
              <svg class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>
              </svg>
              <p class="text-[11px] text-slate-500 leading-normal">
                Message will be sent according to the recipient's primary timezone if available, otherwise it defaults to Eastern Time (ET).
              </p>
            </div>
          </div>

          <!-- Footer Actions -->
          <div class="px-6 py-4 mt-2 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end gap-3">
            <button
              @click="emit('close')"
              type="button"
              class="px-4 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 rounded-lg transition cursor-pointer shadow-2xs"
            >
              Cancel
            </button>
            <button
              @click="handleConfirm"
              type="button"
              class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-slate-950 hover:bg-slate-800 rounded-lg transition shadow-xs cursor-pointer"
            >
              Confirm Schedule
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M5 12h14M12 5l7 7-7 7"/>
              </svg>
            </button>
          </div>

        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>