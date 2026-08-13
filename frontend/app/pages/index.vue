<script setup lang="ts">
definePageMeta({
  middleware: ["auth", "guest"],
  layout: 'default',
});
</script>

<template>
  <div class="flex flex-col gap-2">
    <div v-for="index in 40" :key="index" class="w-full bg-amber-500">
      {{ index }}
    </div>
    <!-- Page Content -->
  </div>
</template>
