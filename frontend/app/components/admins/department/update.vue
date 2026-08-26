<script setup lang="ts">
import CloseIcon from "~/components/icons/CloseIcon.vue";
import SpannerIcon from "~/components/icons/SpannerIcon.vue";
import type { MajorModel } from "~/types/major";

const majorStore = useMajorStore();

const props = defineProps<{
  data: MajorModel;
}>();

const emit = defineEmits<{
  close: [];
  submitted: [data?: MajorModel];
}>();

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const messageError = ref("");

const isLoading = ref(false);

const formData = ref({
  name: props.data?.name ?? "",
  description: props.data?.description ?? "",
});

const errors = ref({
  title: "",
  description: "",
});

/*
|--------------------------------------------------------------------------
| Validation
|--------------------------------------------------------------------------
*/

const validate = () => {
  errors.value = {
    title: "",
    description: "",
  };

  messageError.value = "";

  let hasError = false;

  if (!formData.value.name.trim()) {
    errors.value.title = "Title is required.";
    hasError = true;
  }

  if (!formData.value.description.trim()) {
    errors.value.description = "Description is required.";
    hasError = true;
  }

  return hasError;
};

/*
|--------------------------------------------------------------------------
| Close
|--------------------------------------------------------------------------
*/

const closeModal = () => {
  if (!isLoading.value) {
    emit("close");
  }
};

/*
|--------------------------------------------------------------------------
| Submit
|--------------------------------------------------------------------------
*/

const submit = async () => {
  if (validate()) return;

  const data = new FormData();

  data.append("name", formData.value.name.trim());
  data.append("description", formData.value.description.trim());

  isLoading.value = true;

  try {
    const res = await majorStore.updateMajor(Number(props.data.id), data);

    const updatedMajor = res.data?.data;

    if (updatedMajor && majorStore.majors?.data) {
      majorStore.majors.data = majorStore.majors.data.map((item) =>
        item.id === updatedMajor.id ? updatedMajor : item,
      );
    }

    emit("submitted", updatedMajor);
    emit("close");
  } catch (e: any) {
    const serverErrors = e?.response?.data?.errors;

    messageError.value =
      e?.response?.data?.message || "Something went wrong. Please try again.";

    if (serverErrors) {
      errors.value.title = serverErrors?.title?.[0] ?? "";

      errors.value.description = serverErrors?.description?.[0] ?? "";
    }
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <!-- ================================================================
       Overlay
  ================================================================= -->

  <div
    class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/40 backdrop-blur-sm"
    @click="closeModal"
  >
    <!-- ================================================================
         Modal
    ================================================================= -->

    <div
      class="relative w-full max-w-2xl max-h-[92vh] overflow-hidden bg-white rounded-2xl shadow-2xl shadow-slate-900/20 border border-slate-200"
      @click.stop
    >
      <!-- ================================================================
           Header
      ================================================================= -->

      <div class="px-6 py-5 border-b border-slate-100 bg-white">
        <div class="flex items-start justify-between gap-4">
          <div class="flex items-center gap-3">
            <!-- Icon -->

            <div
              class="size-11 shrink-0 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"
            >
              <SpannerIcon class="size-5" />
            </div>

            <!-- Title -->

            <div>
              <h2 class="text-base font-semibold text-slate-800">Edit Major</h2>

              <p class="text-xs text-slate-400 mt-1">
                Update major or department information.
              </p>
            </div>
          </div>

          <!-- Close -->

          <button
            type="button"
            @click="closeModal"
            :disabled="isLoading"
            class="size-9 shrink-0 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <CloseIcon class="size-5" />
          </button>
        </div>
      </div>

      <!-- ================================================================
           Form
      ================================================================= -->

      <form
        @submit.prevent="submit"
        class="overflow-y-auto max-h-[calc(92vh-145px)]"
      >
        <div class="px-6 py-5 space-y-5">
          <!-- ============================================================
               Server Error
          ============================================================= -->

          <div
            v-if="messageError"
            class="flex items-start gap-3 p-3.5 rounded-xl border border-red-200 bg-red-50 text-red-600"
          >
            <div
              class="size-5 shrink-0 rounded-full bg-red-100 flex items-center justify-center text-xs font-bold"
            >
              !
            </div>

            <p class="text-sm">
              {{ messageError }}
            </p>
          </div>

          <!-- ============================================================
               Basic Information
          ============================================================= -->

          <div>
            <div class="flex items-center gap-2 mb-3">
              <div class="size-1.5 rounded-full bg-blue-500"></div>

              <h3 class="text-sm font-semibold text-slate-700">
                Basic Information
              </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Title -->

              <div class="md:col-span-2">
                <label class="block text-xs font-medium text-slate-600 mb-1.5">
                  Major / Department Name
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model="formData.name"
                  type="text"
                  autocomplete="off"
                  placeholder="e.g. Computer Science"
                  class="w-full h-11 px-3.5 bg-slate-50 border rounded-xl text-sm text-slate-700 placeholder:text-slate-400 outline-none transition-all focus:bg-white focus:ring-4 focus:ring-blue-500/10"
                  :class="
                    errors.title
                      ? 'border-red-400 focus:border-red-400'
                      : 'border-slate-200 focus:border-blue-500'
                  "
                />

                <p v-if="errors.title" class="mt-1.5 text-xs text-red-500">
                  {{ errors.title }}
                </p>
              </div>

              <!-- Body -->

              <div class="md:col-span-2">
                <label class="block text-xs font-medium text-slate-600 mb-1.5">
                  Description
                  <span class="text-red-500">*</span>
                </label>

                <textarea
                  v-model="formData.description"
                  rows="3"
                  placeholder="Enter a short description..."
                  class="w-full px-3.5 py-3 bg-slate-50 border rounded-xl text-sm text-slate-700 placeholder:text-slate-400 outline-none resize-none transition-all focus:bg-white focus:ring-4 focus:ring-blue-500/10"
                  :class="
                    errors.description
                      ? 'border-red-400 focus:border-red-400'
                      : 'border-slate-200 focus:border-blue-500'
                  "
                ></textarea>

                <p
                  v-if="errors.description"
                  class="mt-1.5 text-xs text-red-500"
                >
                  {{ errors.description }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <div
          class="sticky bottom-0 px-6 py-4 bg-white border-t border-slate-100 flex items-center justify-end gap-3"
        >
          <!-- Cancel -->

          <button
            type="button"
            @click="closeModal"
            :disabled="isLoading"
            class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 text-sm font-medium hover:bg-slate-50 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Cancel
          </button>

          <!-- Update -->

          <button
            type="submit"
            :disabled="isLoading"
            class="min-w-32 px-4 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium flex items-center justify-center gap-2 shadow-sm shadow-blue-600/20 hover:bg-blue-700 transition-all cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed"
          >
            <template v-if="!isLoading">
              <span> Update Major </span>
            </template>

            <template v-else>
              <SpannerIcon class="size-4 animate-spin" />

              <span> Updating... </span>
            </template>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
