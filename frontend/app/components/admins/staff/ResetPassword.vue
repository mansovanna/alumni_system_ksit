<script setup lang="ts">
import CloseIcon from "~/components/icons/CloseIcon.vue";
import EyeIcon from "~/components/icons/EyeIcon.vue";
import OffEyeIcon from "~/components/icons/OffEyeIcon.vue";
import SpannerIcon from "~/components/icons/SpannerIcon.vue";
import type { UserModel } from "~/types/user.model";

const staffStore = useStaffStore();
const emit = defineEmits(["close", "submitted"]);
const props = defineProps({
  id: {
    type: Number,
    required: true
  }
});
/* ---------- Message error ------------*/
const messageError = ref(null);

const formData = ref<{
  password: string | null;
  password_confirm: string | null;
}>({
  password: null,
  password_confirm: null,
});

const errors = ref<{
  password: string | null;
  password_confirm: string | null;
}>({
  password: null,
  password_confirm: null,
});

const validate = () => {
  errors.value.password = "";
  errors.value.password_confirm = "";

  let hasError = false;

  if (!formData.value.password || formData.value.password.length <= 0) {
    errors.value.password = "Password is required!";
    hasError = true;
  }

  if (
    !formData.value.password_confirm ||
    formData.value.password_confirm.length <= 0
  ) {
    errors.value.password_confirm = "Password Confirm is required!";
    hasError = true;
  } else if (formData.value.password != formData.value.password_confirm) {
    errors.value.password_confirm = "Password not much!";
    hasError = true;
  }

  return hasError;
};

const isLoading = ref(false);

const submit = async () => {
  if (!validate()) {
    const data = new FormData();

    data.append("password", String(formData.value.password));
    data.append(
      "password_confirmation",
      String(formData.value.password_confirm),
    );

    // post data
    isLoading.value = true;
    try {
      const res = await staffStore.changePassword(props.id, data);

      staffStore.data?.data?.data.unshift(res.data?.data);
      emit("close");
    } catch (e: any) {
      messageError.value = e.response?.data?.errors;
    } finally {
      isLoading.value = false;
    }
  }
};

const isShowPassword = ref(false);
</script>

<template>
  <div
    @click="$emit('close')"
    class="w-full justify-center items-center fixed top-0 bottom-0 right-0 left-0 bg-black/20 z-50 backdrop-blur-xs flex p-4"
  >
    <div
      class="w-3/8 max-md:w-full max-lg:w-8/9 max-xl:w-3/4 bg-white rounded-2xl border border-slate-400"
      @click.stop
    >
      <div class="w-full flex justify-between items-center px-3 py-3">
        <p class="font-Inter text-slate-500">Reset Password</p>
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
            class="w-full grid grid-cols-2 gap-3 max-md:grid-cols-1 max-lg:grid-cols-2"
          >
            <!-- ------------- -->

            <div class="w-full">
              <label class="text-xs text-slate-500"
                >Password <span class="text-red-500">*</span></label
              >
              <div class="w-full relative">
                <input
                  :type="isShowPassword ? 'text' : 'password'"
                  v-model="formData.password"
                  class="w-full px-2 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  placeholder="Create Password"
                  :class="
                    errors.password ? 'border-red-500' : 'border-slate-300'
                  "
                />

                <div
                  class="absolute top-0 bottom-0 flex justify-center items-center right-0"
                >
                  <button
                    type="button"
                    @click="isShowPassword = !isShowPassword"
                    class="p-2 cursor-pointer hover:text-red-500"
                  >
                    <component :is="isShowPassword ? OffEyeIcon : EyeIcon" />
                  </button>
                </div>
              </div>
              <span class="text-xs text-red-500">{{ errors.password }}</span>
            </div>

            <div class="w-full">
              <label class="text-xs text-slate-500"
                >Password Confirm <span class="text-red-500">*</span></label
              >
              <div class="w-full relative">
                <input
                  :type="isShowPassword ? 'text' : 'password'"
                  v-model="formData.password_confirm"
                  class="w-full px-2 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  placeholder="Confirm Password"
                  :class="
                    errors.password_confirm
                      ? 'border-red-500'
                      : 'border-slate-300'
                  "
                />

                <div
                  class="absolute top-0 bottom-0 flex justify-center items-center right-0"
                >
                  <button
                    type="button"
                    @click="isShowPassword = !isShowPassword"
                    class="p-2 cursor-pointer hover:text-red-500"
                  >
                    <component :is="isShowPassword ? OffEyeIcon : EyeIcon" />
                  </button>
                </div>
              </div>
              <span class="text-xs text-red-500">{{
                errors.password_confirm
              }}</span>
            </div>

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
