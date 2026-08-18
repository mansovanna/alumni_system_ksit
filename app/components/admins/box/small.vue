<script setup>
import { HugeiconsIcon } from "@hugeicons/vue";
import { colorGroups, icons } from "~/stores/block_icons";

const props = defineProps({
  totalAlumni: { type: Number, default: 0 },
  title: { type: String, default: "Total Alumni" },
  body: { type: String, default: "Total Alumni all" },
  color: { type: String, default: "success" },
  color_from: { type: String, default: "#22c55e" },
  color_to: { type: String, default: "#15803d" },
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
      :style="{
        background: `linear-gradient(135deg, ${color_from}, ${color_to})`,
      }"
      class="w-28 h-28 rounded-br-4xl text-white flex items-center justify-center shadow-lg"
    >
      <HugeiconsIcon
        :icon="icons.find((i) => i.name === icon)?.icon"
        class="w-12 h-12"
      />
    </div>

    <div class="flex flex-col items-start justify-start py-2">
      <h1 class="font-Inter w-full line-clamp-1 font-semibold text-slate-500 text-xl">
        {{ title }}
      </h1>
      <p class="font-Inter font-semibold text-slate-500 text-xs">{{ body }}</p>

      <p class="text-3xl font-black text-success-dark mt-2">
        {{ totalAlumni }}
      </p>
    </div>
  </div>
</template>
