
<template>
  <div
    class="w-full h-screen flex flex-col overflow-clip justify-center items-center"
  >
    <!-- Block  -->
    <div class="w-full h-full flex flex-col overflow-clip">
      <div class="w-full p-5 bg-red-500"></div>
      <slot />
    </div>
    <!-- End  -->
  </div>
</template>
