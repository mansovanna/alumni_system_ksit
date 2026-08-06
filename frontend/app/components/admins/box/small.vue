<script setup>
import { HugeiconsIcon } from "@hugeicons/vue";
import { UserGroup02Icon } from "@hugeicons/core-free-icons";
import { colorGroups, icons } from "~/stores/block_icons";

const props = defineProps({
  totalAlumni: { type: Number, default: 0 },
  title: { type: String, default: "Total Alumni" },
  color: { type: String, default: "success" },
  icon: { type: String, default: "users" },
});

const selectedColor = computed(
  () => colorGroups.find((c) => c.name === props.color) ?? colorGroups[0],
);

const gradientStyle = computed(() => ({
  backgroundImage: `linear-gradient(to bottom right, ${selectedColor.value.from} 10%, ${selectedColor.value.to})`,
}));
</script>

<template>
  <div
    class="w-full bg-white flex items-center justify-start rounded rounded-tl-4xl rounded-br-4xl gap-2 overflow-clip shadow-lg"
  >
    <div
      :style="gradientStyle"
      class="w-25 h-25 rounded-br-4xl text-white flex items-center justify-center shadow-lg"
    >
     <HugeiconsIcon :icon="icons.find((i) => i.name === icon)?.icon" class="w-12 h-12" />

    </div>

    <div class="flex flex-col gap-1 items-start justify-start">
      <h1 class="font-Inter font-semibold text-slate-500 text-xl">
        {{ title }}
      </h1>
      <p class="text-3xl font-black text-success-dark">{{ totalAlumni }}</p>
    </div>
  </div>
</template>
