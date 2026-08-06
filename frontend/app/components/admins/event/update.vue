<script setup lang="ts">
import CloseIcon from "~/components/icons/CloseIcon.vue";

const props = defineProps({
  id: {
    type: String,
  },
  data: {
    type: Object,
  },
});

const formData = ref<{
  tilte: string | null;
  body: string | null;
  date: string | null;
}>({
  tilte: props.data?.title || null,
  body: props.data?.body || null,
  date: props.data?.date || null,
});

const emit = defineEmits<{
  close: [];
  submit: [payload: typeof formData.value];
}>();

const errors = ref<{
  tilte: string | null;
  body: string | null;
  date: string | null;
}>({
  tilte: null,
  body: null,
  date: null,
});

const validate = () => {
  errors.value.tilte = "";
  errors.value.body = "";
  errors.value.date = "";

  let hasError = false;

  if (!formData.value.tilte || formData.value.tilte.length <= 0) {
    errors.value.tilte = "title is required!";
    hasError = true;
  }

  if (!formData.value.body || formData.value.body.length <= 0) {
    errors.value.body = "body is required!";
    hasError = true;
  }

  if (!formData.value.date || formData.value.date.length <= 0) {
    errors.value.body = "date is required!";
    hasError = true;
  }

  return hasError;
};

const submit = () => {
  if (!validate()) {
    alert("Form submitted successfully!");
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
        <p class="font-Inter text-slate-500">Update Event</p>
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
          <div class="w-full flex flex-col gap-3">
            <div class="w-full">
              <label class="text-xs text-slate-500"
                >Title<span class="text-red-500">*</span></label
              >
              <div class="w-full relative">
                <input
                  type="text"
                  v-model="formData.tilte"
                  class="w-full px-2 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  placeholder="Title"
                  :class="errors.tilte ? 'border-red-500' : 'border-slate-300'"
                />
              </div>
              <span class="text-xs text-red-500">{{ errors.tilte }}</span>
            </div>

            <div class="w-full">
              <label class="text-xs text-slate-500"
                >Body<span class="text-red-500">*</span></label
              >
              <div class="w-full relative">
                <input
                  type="text"
                  v-model="formData.body"
                  class="w-full px-2 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  placeholder="Body"
                  :class="errors.body ? 'border-red-500' : 'border-slate-300'"
                />
              </div>
              <span class="text-xs text-red-500">{{ errors.body }}</span>
            </div>
          </div>

          <!--  -->
          <div class="w-full">
            <label class="text-xs text-slate-500"
              >Date<span class="text-red-500">*</span></label
            >
            <div class="w-full relative">
              <input
                type="date"
                v-model="formData.date"
                class="w-full px-2 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                placeholder="Body"
                :class="errors.date ? 'border-red-500' : 'border-slate-300'"
              />
            </div>
            <span class="text-xs text-red-500">{{ errors.date }}</span>
          </div>
          <!--  -->

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
