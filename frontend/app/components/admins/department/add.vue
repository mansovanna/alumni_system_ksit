<script setup lang="ts">
import CloseIcon from "~/components/icons/CloseIcon.vue";
import SpannerIcon from "~/components/icons/SpannerIcon.vue";

const majorStore = useMajorStore();

const emit = defineEmits<{
  close: [];
  submitted: [data?: any];
}>();

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const messageError = ref("");

const isLoading = ref(false);

const formData = ref<{
  title: string;
  body: string;
  color_from: string;
  color_to: string;
  icon: string;
}>({
  title: "",
  body: "",
  color_from: "#51A2FF",
  color_to: "#193CB8",
  icon: "user",
});

const errors = ref<{
  title: string;
  body: string;
  color_from: string;
  color_to: string;
  icon: string;
}>({
  title: "",
  body: "",
  color_from: "",
  color_to: "",
  icon: "",
});

/*
|--------------------------------------------------------------------------
| Validation
|--------------------------------------------------------------------------
*/

const validate = () => {
  errors.value = {
    title: "",
    body: "",
    color_from: "",
    color_to: "",
    icon: "",
  };

  messageError.value = "";

  let hasError = false;

  if (!formData.value.title.trim()) {
    errors.value.title = "Title is required.";
    hasError = true;
  }

  if (!formData.value.body.trim()) {
    errors.value.body = "Description is required.";
    hasError = true;
  }

  // if (!formData.value.color_from) {
  //   errors.value.color_from = "Color is required.";
  //   hasError = true;
  // }

  // if (!formData.value.color_to) {
  //   errors.value.color_to = "Color is required.";
  //   hasError = true;
  // }

  // if (!formData.value.icon) {
  //   errors.value.icon = "Icon is required.";
  //   hasError = true;
  // }

  return hasError;
};

/*
|--------------------------------------------------------------------------
| Submit
|--------------------------------------------------------------------------
*/

const submit = async () => {
  if (validate()) return;

  const data = new FormData();

  data.append("name", formData.value.title.trim());
  data.append("description", formData.value.body.trim());
  // data.append("color_from", formData.value.color_from);
  // data.append("color_to", formData.value.color_to);
  // data.append("icon", formData.value.icon);

  isLoading.value = true;

  try {
    const res = await majorStore.addMajor(data);

    const newMajor = res.data?.data;

    if (newMajor && majorStore.majors?.data) {
      majorStore.majors.data.unshift(newMajor);
    }

    emit("submitted", newMajor);
    emit("close");
  } catch (e: any) {
    const serverErrors = e?.response?.data?.errors;

    messageError.value =
      e?.response?.data?.message || "Something went wrong. Please try again.";

    if (serverErrors) {
      errors.value.title = serverErrors?.title?.[0] ?? "";
      errors.value.body = serverErrors?.body?.[0] ?? "";
      errors.value.color_from = serverErrors?.color_from?.[0] ?? "";
      errors.value.color_to = serverErrors?.color_to?.[0] ?? "";
      errors.value.icon = serverErrors?.icon?.[0] ?? "";
    }
  } finally {
    isLoading.value = false;
  }
};

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

const closeModal = () => {
  if (!isLoading.value) {
    emit("close");
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
              class="size-11 shrink-0 rounded-xl bg-primary/10 text-primary flex items-center justify-center"
            >
              <SpannerIcon class="size-5" />
            </div>

            <!-- Title -->

            <div>
              <h2 class="text-base font-semibold text-slate-800">
                Add New Major
              </h2>

              <p class="text-xs text-slate-400 mt-1">
                Create a new academic major or department.
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
           Body
      ================================================================= -->

      <form
        @submit.prevent="submit"
        class="overflow-y-auto max-h-[calc(92vh-145px)]"
      >
        <div class="px-6 py-5 space-y-5">
          <!-- Server Error -->

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
              <div class="size-1.5 rounded-full bg-primary"></div>

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
                  v-model="formData.title"
                  type="text"
                  placeholder="e.g. Computer Science"
                  autocomplete="off"
                  class="w-full h-11 px-3.5 bg-slate-50 border rounded-xl text-sm text-slate-700 placeholder:text-slate-400 outline-none transition-all focus:bg-white focus:ring-4 focus:ring-primary/10"
                  :class="
                    errors.title
                      ? 'border-red-400 focus:border-red-400'
                      : 'border-slate-200 focus:border-primary'
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
                  v-model="formData.body"
                  rows="3"
                  placeholder="Enter a short description..."
                  class="w-full px-3.5 py-3 bg-slate-50 border rounded-xl text-sm text-slate-700 placeholder:text-slate-400 outline-none resize-none transition-all focus:bg-white focus:ring-4 focus:ring-primary/10"
                  :class="
                    errors.body
                      ? 'border-red-400 focus:border-red-400'
                      : 'border-slate-200 focus:border-primary'
                  "
                ></textarea>

                <div class="flex justify-between mt-1.5">
                  <p v-if="errors.body" class="text-xs text-red-500">
                    {{ errors.body }}
                  </p>

                  <span v-else class="text-xs text-slate-400">
                    Add a short description for this major.
                  </span>
                </div>
              </div>

              <!-- Icon -->

              <!-- <div class="md:col-span-2">

                <label
                  class="block text-xs font-medium text-slate-600 mb-1.5"
                >
                  Icon
                  <span class="text-red-500">*</span>
                </label>

                <div
                  class="p-2.5 rounded-xl border border-slate-200 bg-slate-50"
                >
                  <OptionsOptionItemIcon
                    :items="icons ?? []"
                    zIndex="z-[120]"
                    :active="formData.icon"
                    @update:active="formData.icon = $event"
                  />
                </div>

                <p
                  v-if="errors.icon"
                  class="mt-1.5 text-xs text-red-500"
                >
                  {{ errors.icon }}
                </p>

              </div> -->
            </div>
          </div>

          <!-- ============================================================
               Color
          ============================================================= -->

          <div>
            <!-- <div class="flex items-center justify-between mb-3">

              <div class="flex items-center gap-2">

                <div
                  class="size-1.5 rounded-full bg-primary"
                ></div>

                <h3
                  class="text-sm font-semibold text-slate-700"
                >
                  Gradient Colors
                </h3>

              </div>

              <span
                class="text-xs text-slate-400"
              >
                Click a color to change
              </span>

            </div> -->

            <!-- Preview -->

            <!-- <div
              class="relative h-24 rounded-xl overflow-hidden border border-slate-200 shadow-inner"
              :style="{
                backgroundImage: `linear-gradient(110deg, ${formData.color_from}, ${formData.color_to})`,
              }"
            > -->

            <!-- <div
                class="absolute inset-0 bg-white/5"
              ></div>

              <div
                class="absolute inset-0 flex items-center justify-center"
              >

                <div
                  class="px-4 py-2 rounded-lg bg-white/15 backdrop-blur-md border border-white/20 text-white text-sm font-medium shadow-sm"
                >
                  Gradient Preview
                </div>

              </div> -->

            <!-- <label
                class="absolute left-3 top-3 flex items-center gap-2 cursor-pointer group"
              >

                <span
                  class="size-9 rounded-lg border-2 border-white shadow-lg ring-1 ring-black/10 group-hover:scale-105 transition-transform"
                  :style="{
                    backgroundColor: formData.color_from,
                  }"
                ></span>

                <input
                  v-model="formData.color_from"
                  type="color"
                  class="sr-only"
                />

              </label> -->

            <!-- 
              <label
                class="absolute right-3 top-3 flex items-center gap-2 cursor-pointer group"
              >

                <input
                  v-model="formData.color_to"
                  type="color"
                  class="sr-only"
                />

                <span
                  class="size-9 rounded-lg border-2 border-white shadow-lg ring-1 ring-black/10 group-hover:scale-105 transition-transform"
                  :style="{
                    backgroundColor: formData.color_to,
                  }"
                ></span>

              </label> -->

            <!-- </div> -->

            <!-- Color Values -->

            <!-- <div class="grid grid-cols-2 gap-3 mt-3">



              <div>

                <label
                  class="block text-xs text-slate-500 mb-1.5"
                >
                  Start Color
                </label>

                <div class="relative">

                  <span
                    class="absolute left-3 top-1/2 -translate-y-1/2 size-4 rounded-full border border-slate-200"
                    :style="{
                      backgroundColor: formData.color_from,
                    }"
                  ></span>

                  <input
                    v-model="formData.color_from"
                    type="text"
                    maxlength="7"
                    class="w-full h-10 pl-9 pr-3 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono uppercase text-slate-600 outline-none focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10"
                  />

                </div>

                <p
                  v-if="errors.color_from"
                  class="mt-1 text-xs text-red-500"
                >
                  {{ errors.color_from }}
                </p>

              </div>

             
              <div>

                <label
                  class="block text-xs text-slate-500 mb-1.5"
                >
                  End Color
                </label>

                <div class="relative">

                  <span
                    class="absolute left-3 top-1/2 -translate-y-1/2 size-4 rounded-full border border-slate-200"
                    :style="{
                      backgroundColor: formData.color_to,
                    }"
                  ></span>

                  <input
                    v-model="formData.color_to"
                    type="text"
                    maxlength="7"
                    class="w-full h-10 pl-9 pr-3 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono uppercase text-slate-600 outline-none focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10"
                  />

                </div>

                <p
                  v-if="errors.color_to"
                  class="mt-1 text-xs text-red-500"
                >
                  {{ errors.color_to }}
                </p>

              </div>

            </div> -->
          </div>
        </div>

        <!-- ================================================================
             Footer
        ================================================================= -->

        <div
          class="sticky bottom-0 px-6 py-4 bg-white border-t border-slate-100 flex items-center justify-end gap-3"
        >
          <button
            type="button"
            @click="closeModal"
            :disabled="isLoading"
            class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 text-sm font-medium hover:bg-slate-50 hover:text-slate-700 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Cancel
          </button>

          <button
            type="submit"
            :disabled="isLoading"
            class="min-w-28 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-medium flex items-center justify-center gap-2 shadow-sm shadow-primary/20 hover:bg-primary/90 transition-all cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed"
          >
            <template v-if="!isLoading">
              <span>Save Major</span>
            </template>

            <template v-else>
              <SpannerIcon class="size-4 animate-spin" />

              <span> Saving... </span>
            </template>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
