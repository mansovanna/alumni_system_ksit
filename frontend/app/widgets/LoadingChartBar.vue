<template>
  <div class="w-full ">
    <div class="w-full flex flex-col gap-2 animate-pulse ">
      <div>
        <div class="w-1/3 p-2 bg-slate-200 rounded-full"></div>
      </div>

      <div class="w-full flex justify-center items-center gap-4 py-4">
        <div class="flex w-1/7 gap-1.5">
            <div class="p-1.5 bg-slate-200 rounded-full"></div>
            <div class="p-1.5 w-full bg-slate-200 rounded-full"></div>
        </div>

         <div class="flex w-1/7 gap-1.5">
            <div class="p-1.5 bg-slate-200 rounded-full"></div>
            <div class="p-1.5 w-full bg-slate-200 rounded-full"></div>
        </div>

         <div class="flex w-1/7 gap-1.5">
            <div class="p-1.5 bg-slate-200 rounded-full"></div>
            <div class="p-1.5 w-full bg-slate-200 rounded-full"></div>
        </div>

         <div class="flex w-1/7 gap-1.5">
            <div class="p-1.5 bg-slate-200 rounded-full"></div>
            <div class="p-1.5 w-full bg-slate-200 rounded-full"></div>
        </div>
      </div>

      <div class="flex justify-between gap-3 h-[40vh]">
        <div v-for="index in 4" class="flex gap-3">
          <div v-for="item in 4" class="p-3.5 bg-slate-200 rounded"></div>
        </div>
      </div>
      <div class="w-full flex justify-between items-center gap-6 mt-3 px-6">
        <div v-for="app in 4" class="p-1.5 w-1/9 bg-slate-200 rounded-full"></div>
      </div>
    </div>
  </div>
</template>
