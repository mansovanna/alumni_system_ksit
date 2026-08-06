<script setup lang="ts">
import CloseIcon from "~/components/icons/CloseIcon.vue";

const props = defineProps({
  id: {
    type: String,
  },
  name: {
    type: String,
  },
});
const formData = ref({
  id: props.id ?? "",
  name: props.name ?? "",
});

const errors = ref<{
  name: string | null;
}>({
  name: null,
});

const emit = defineEmits<{
  close: [];
  submit: [payload: typeof formData.value];
}>();

const validate = () => {
  errors.value.name = null;

  if (!formData.value.name || formData.value.name.trim().length === 0) {
    errors.value.name = "Department name is required!";
    return false;
  }
  return true;
};

const submit = () => {
  if (validate()) {
    emit("submit", formData.value);
    emit("close");
  }
};
</script>

<template>
  <div
    @click="$emit('close')"
    class="w-full justify-center items-center fixed top-0 bottom-0 right-0 left-0 bg-black/20 z-50 backdrop-blur-xs flex p-4"
  >
    <div class="w-1/3 bg-white rounded-2xl border border-slate-400" @click.stop>
      <div class="w-full flex justify-between items-center px-3 py-3">
        <p class="font-Inter text-slate-500">Add New Department</p>
        <button
          @click="$emit('close')"
          class="flex justify-center items-center rounded-md hover:text-red-500 cursor-pointer"
        >
          <CloseIcon />
        </button>
      </div>
      <hr class="text-slate-100" />

      <form @submit.prevent="submit">
        <div class="w-full px-4 py-2 flex flex-col gap-2">
          <!--  -->
          <div class="w-full">
            <label class="text-xs text-slate-500"
              >Name Department <span class="text-red-500">*</span></label
            >
            <div class="w-full relative">
              <input
                type="text"
                v-model="formData.name"
                class="w-full px-2 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                placeholder="Enter user name"
                :class="errors.name ? 'border-red-500' : 'border-slate-300'"
              />
            </div>
            <span class="text-xs text-red-500">{{ errors.name }}</span>
          </div>

          <button
            type="submit"
            class="w-full bg-primary hover:bg-primary/80 py-2 rounded-md text-white text-sm my-2 mt-10"
          >
            <span>Save</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
