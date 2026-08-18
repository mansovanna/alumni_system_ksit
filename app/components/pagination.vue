<script setup lang="ts">
import { computed } from "vue";
import ArrowIcon from "./icons/ArrowIcon.vue";

const props = defineProps<{
  currentPage: number;
  lastPage: number;
}>();

const emit = defineEmits<{
  (e: "change", page: number): void;
}>();

const pages = computed(() => {
  const total = props.lastPage;
  const current = props.currentPage;
  const delta = 1;
  const range: (number | string)[] = [];

  for (let i = 1; i <= total; i++) {
    if (
      i === 1 ||
      i === total ||
      (i >= current - delta && i <= current + delta)
    ) {
      range.push(i);
    } else if (range[range.length - 1] !== "...") {
      range.push("...");
    }
  }

  return range;
});

function goTo(page: number | string) {
  if (typeof page !== "number") return;
  if (page < 1 || page > props.lastPage || page === props.currentPage) return;
  emit("change", page);
}
</script>

<template>
  <div class="w-full flex justify-center items-center font-Inter font-medium">
    <div
      class="p-1 bg-white rounded-full border border-slate-300 flex justify-between items-center gap-2"
    >
      <button
        :disabled="currentPage === 1"
        class="size-8 bg-slate-100 rounded-full border hover:bg-slate-200 cursor-pointer border-slate-200 flex justify-center items-center disabled:opacity-40 disabled:cursor-not-allowed"
        @click="goTo(currentPage - 1)"
      >
        <ArrowIcon class="rotate-180" />
      </button>

      <div class="flex justify-center items-center gap-2">
        <template v-for="(page, idx) in pages" :key="idx">
          <div v-if="page === '...'" class="text-xs text-slate-400 px-1">
            ...
          </div>
          <button
            v-else
            class="size-8 text-xs rounded-full border cursor-pointer flex justify-center items-center transition-colors"
            :class="
              page === currentPage
                ? 'bg-info text-white border-info'
                : 'bg-slate-100 hover:bg-slate-200 border-slate-200'
            "
            @click="goTo(page)"
          >
            {{ page }}
          </button>
        </template>
      </div>

      <button
        :disabled="currentPage === lastPage"
        class="size-8 bg-slate-100 rounded-full border hover:bg-slate-200 cursor-pointer border-slate-200 flex justify-center items-center disabled:opacity-40 disabled:cursor-not-allowed"
        @click="goTo(currentPage + 1)"
      >
        <ArrowIcon class="rotate-0" />
      </button>
    </div>
  </div>
</template>
