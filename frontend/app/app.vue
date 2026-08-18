<script setup lang="ts">
const majorStore = useMajorStore()

onMounted(()=>{
  majorStore.getMajorAll()
})

</script>

<template>
 <div class=" font-Inter">
   <NuxtLayout>
    <NuxtPage />
  </NuxtLayout>
 </div>
</template>
