<script setup lang="ts">
import CloseIcon from "~/components/icons/CloseIcon.vue";
import SpannerIcon from "~/components/icons/SpannerIcon.vue";
import type { MessageModel } from "~/types/message.response.model";

const messageStore = useMessageStore();
const emit = defineEmits(["close", "submitted"]);
/* ---------- Message error ------------*/
const messageError = ref(null);
//  props get data form main component
const props = defineProps<{ data: MessageModel }>();
const formData = ref<{
  title: string | null;
  body: string | null;
}>({
  title: props.data.title || null,
  body: props.data.body || null,
});

const errors = ref<{
  title: string | null;
  body: string | null;
}>({
  title: null,
  body: null,
});

const validate = () => {
  errors.value.title = "";
  errors.value.body = "";

  let hasError = false;

  if (!formData.value.title || formData.value.title.length <= 0) {
    errors.value.title = "Title is required!";
    hasError = true;
  }

  if (!formData.value.body || formData.value.body.length <= 0) {
    errors.value.body = "Body english is required!";
    hasError = true;
  }

  return hasError;
};

const isLoading = ref(false);

const submit = async () => {
  if (!validate()) {
    const data = new FormData();

    data.append("title", String(formData.value.title));
    data.append("body", String(formData.value.body));

    // post data
    isLoading.value = true;

    try {
      const res = await messageStore.updateMessage(props.data.id, data);

      if (messageStore.data?.data.data) {
        messageStore.data.data.data = messageStore.data?.data.data.map((e) =>
          e.id === res.data.data.id ? res.data.data : e,
        );
      }
      emit("close");
    } catch (e: any) {
      messageError.value = e.response?.data?.errors;
      errors.value.title = e.response?.data?.errors?.title[0];
    } finally {
      isLoading.value = false;
    }
  }
};
</script>

<template>
  <div
    @click="$emit('close')"
    class="w-full justify-center items-center fixed top-0 bottom-0 right-0 left-0 bg-black/20 z-50 backdrop-blur-xs flex p-4"
  >
    <div
      class="w-3/10 max-md:w-full max-lg:w-8/9 max-xl:w-3/6 bg-white rounded-2xl border border-slate-400"
      @click.stop
    >
      <div class="w-full flex justify-between items-center px-3 py-3">
        <p class="font-Inter text-slate-500">Update Message</p>
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
          <span class="text-red-500 text-xs">{{ messageError }}</span>
          <div
            class="w-full grid grid-cols-1 gap-3 max-md:grid-cols-1 max-lg:grid-cols-1"
          >
            <div class="w-full">
              <label class="text-xs text-slate-500"
                >Title<span class="text-red-500">*</span></label
              >
              <div class="w-full relative">
                <input
                  type="text"
                  v-model="formData.title"
                  class="w-full px-2 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  placeholder="Enter user name khmer"
                  :class="errors.title ? 'border-red-500' : 'border-slate-300'"
                />
              </div>
              <span class="text-xs text-red-500">{{ errors.title }}</span>
            </div>

            <div class="w-full">
              <label class="text-xs text-slate-500"
                >Body <span class="text-red-500">*</span></label
              >
              <div class="w-full relative">
                <textarea
                  v-model="formData.body"
                  class="w-full px-2 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  placeholder="Enter user name english"
                  :class="errors.body ? 'border-red-500' : 'border-slate-300'"
                  rows="4"
                ></textarea>
              </div>
              <span class="text-xs text-red-500">{{ errors.body }}</span>
            </div>
            <!--  -->

            <!--  -->

            <!-- ------------- -->

            <!--  -->
          </div>

          <div class="pb-2 flex items-center justify-end gap-6 mt-4">
            <button
              @click="$emit('close')"
              type="button"
              class="w-1/8 p-2 bg-red-600 text-white text-sm hover:bg-red-700 cursor-pointer rounded-lg min-w-20"
            >
              Cancel
            </button>

            <button
              type="submit"
              :disabled="isLoading"
              class="w-1/8 flex justify-center disabled:bg-secondary items-center text-sm bg-primary text-white rounded-lg p-2 cursor-pointer hover:bg-primary/80 transition-colors"
            >
              <span v-if="!isLoading">Save</span>
              <div v-else class="font-Inter flex justify-center items-end">
                <span class="line-clamp-1">Please wating</span>
                <SpannerIcon />
              </div>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
