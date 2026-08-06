<script setup lang="ts">
import CloseIcon from "~/components/icons/CloseIcon.vue";
import EditIcon from "~/components/icons/EditIcon.vue";

const emit = defineEmits(["close", "submitted"]);
const props = defineProps({
  data: {
    type: Object,
    required: true,
  },
});
</script>

<template>
  <div
    @click="$emit('close')"
    class="w-full justify-center items-center fixed top-0 bottom-0 right-0 left-0 bg-black/20 z-30 backdrop-blur-xs flex p-4"
  >
    <div
      class="w-1/3 max-md:w-full max-lg:w-2/3 bg-white rounded-2xl border border-slate-400"
      @click.stop
    >
      <!-- Header -->
      <div class="w-full flex justify-between items-center px-3 py-3">
        <p class="font-Inter text-slate-500">Profile Info</p>
        <button
          @click="$emit('close')"
          class="flex justify-center items-center rounded-md hover:text-red-500 cursor-pointer"
        >
          <CloseIcon />
        </button>
      </div>
      <hr class="text-slate-100" />
      <div class="p-4 flex justify-start items-center gap-3">
        <div
          class="size-25 rounded-full border-2 border-primary ring-3 ring-primary outline outline-dashed outline-white relative"
        >
          <img
            class="rounded-full object-cover object-center"
            src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?fm=jpg&q=60&w=3000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8dXNlciUyMHByb2ZpbGV8ZW58MHx8MHx8fDA%3D"
            alt=""
          />

          <label
            class="size-8 cursor-pointer hover:bg-slate-200 hover:text-primary absolute bottom-0 right-0 bg-white rounded-full flex justify-center items-center"
          >
            <EditIcon />
            <input hidden type="file" name="" id="" />
          </label>
        </div>
        <div>
          <h1 class="text-xl font-kantumruy-pro font-medium">
            Alumni {{ data }}
          </h1>
          <p class="text-sm text-slate-400">000 - 000 - 000{{ data }}</p>
        </div>
      </div>

    

      <div class="px-4 flex flex-col pb-4 text-sm gap-4">
          <hr class="text-slate-100" />
        <div class="flex justify-between items-center gap-2">
          <p>Name Khmer</p>
          <p class="font-kantumruy-pro text-slate-500">{{ data ?? null }}</p>
        </div>

        <div class="flex justify-between items-center gap-2">
          <p>Name English</p>
          <p  class=" text-slate-500">{{ data ?? null }}</p>
        </div>

        <div class="flex justify-between items-center gap-2">
          <p>Date of Birth</p>
          <p  class=" text-slate-500">{{ data ?? null }}</p>
        </div>

        <div class="flex justify-between items-center gap-2">
          <p>Mobile Phone</p>
          <p  class=" text-slate-500">{{ data ?? null }}</p>
        </div>

        <div class="flex justify-between items-center gap-2">
          <p>Major</p>
          <p  class=" text-slate-500">{{ data ?? null }}</p>
        </div>

        <div class="flex justify-between items-center gap-2">
          <p>Last Year</p>
          <p  class=" text-slate-500">{{ data ?? null }}</p>
        </div>

        <div class="flex justify-between items-center gap-2">
          <p>Status Work</p>
          <p class=" text-slate-500">{{ data ?? null }}</p>
        </div>

        <div class="flex justify-between items-center gap-2">
          <p>Work Address</p>
          <p  class=" text-slate-500">{{ data ?? null }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

.fade-text-enter-active,
.fade-text-leave-active {
  transition: all 0.2s ease;
}

.fade-text-enter-from,
.fade-text-leave-to {
  opacity: 0;
  transform: translateY(6px);
}
</style>
