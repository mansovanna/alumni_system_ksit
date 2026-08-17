<script setup lang="ts">
import CloseIcon from "~/components/icons/CloseIcon.vue";
import EyeIcon from "~/components/icons/EyeIcon.vue";
import OffEyeIcon from "~/components/icons/OffEyeIcon.vue";
import SpannerIcon from "~/components/icons/SpannerIcon.vue";

const staffStore = useStaffStore();

const emit = defineEmits(["close", "submitted"]);

const props = defineProps<{
  id: number;
}>();

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const messageError = ref<string | null>(null);

const formData = ref({
  password: "",
  password_confirm: "",
});

const errors = ref({
  password: "",
  password_confirm: "",
});

const isLoading = ref(false);
const isShowPassword = ref(false);

/*
|--------------------------------------------------------------------------
| Validation
|--------------------------------------------------------------------------
*/

const validate = () => {
  errors.value.password = "";
  errors.value.password_confirm = "";
  messageError.value = null;

  let hasError = false;

  if (!formData.value.password.trim()) {
    errors.value.password = "Password is required!";
    hasError = true;
  } else if (formData.value.password.length < 8) {
    errors.value.password = "Password must be at least 8 characters!";
    hasError = true;
  }

  if (!formData.value.password_confirm.trim()) {
    errors.value.password_confirm = "Please confirm your password!";
    hasError = true;
  } else if (
    formData.value.password !== formData.value.password_confirm
  ) {
    errors.value.password_confirm = "Passwords do not match!";
    hasError = true;
  }

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

  data.append("password", formData.value.password);
  data.append(
    "password_confirmation",
    formData.value.password_confirm,
  );

  isLoading.value = true;

  try {
    await staffStore.changePassword(props.id, data);

    emit("submitted");
    emit("close");
  } catch (e: any) {
    const apiErrors = e.response?.data?.errors;

    messageError.value =
      e.response?.data?.message || "Unable to reset password.";

    if (apiErrors?.password?.[0]) {
      errors.value.password = apiErrors.password[0];
    }

    if (apiErrors?.password_confirmation?.[0]) {
      errors.value.password_confirm =
        apiErrors.password_confirmation[0];
    }
  } finally {
    isLoading.value = false;
  }
};

/*
|--------------------------------------------------------------------------
| Close
|--------------------------------------------------------------------------
*/

const close = () => {
  if (isLoading.value) return;

  emit("close");
};
</script>

<template>
  <!-- Overlay -->
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm"
    @click="close"
  >
    <!-- Modal -->
    <div
      class="w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl"
      @click.stop
    >
      <!-- ============================================================
           Header
      ============================================================= -->

      <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
        <div class="flex items-center gap-3">
          <!-- Icon -->
          <div
            class="flex size-10 items-center justify-center rounded-xl bg-amber-50 text-amber-600"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="size-5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="1.8"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M15.75 5.25a3.75 3.75 0 0 1 5.25 5.25L12 19.5l-5.25.75.75-5.25 9.25-9.75Z"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="m14.25 6.75 3 3"
              />
            </svg>
          </div>

          <div>
            <h2 class="text-sm font-semibold text-slate-800">
              Reset Password
            </h2>

            <p class="mt-0.5 text-xs text-slate-400">
              Create a new password for this staff account
            </p>
          </div>
        </div>

        <!-- Close -->
        <button
          type="button"
          :disabled="isLoading"
          @click="close"
          class="flex size-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-500 disabled:cursor-not-allowed disabled:opacity-50"
        >
          <CloseIcon class="size-4" />
        </button>
      </div>

      <!-- ============================================================
           Body
      ============================================================= -->

      <form @submit.prevent="submit">
        <div class="flex flex-col gap-5 px-5 py-5">

          <!-- Error Message -->

          <div
            v-if="messageError"
            class="flex items-start gap-3 rounded-xl border border-red-100 bg-red-50 px-3.5 py-3"
          >
            <div
              class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600"
            >
              !
            </div>

            <p class="text-xs leading-5 text-red-600">
              {{ messageError }}
            </p>
          </div>

          <!-- Information -->

          <div
            class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3"
          >
            <p class="text-xs font-medium text-blue-700">
              Password security
            </p>

            <p class="mt-1 text-xs leading-5 text-blue-600">
              Use at least 8 characters. A combination of letters,
              numbers, and symbols is recommended.
            </p>
          </div>

          <!-- ========================================================
               New Password
          ========================================================= -->

          <div class="w-full">
            <label class="mb-1.5 block text-xs font-medium text-slate-600">
              New Password
              <span class="text-red-500">*</span>
            </label>

            <div class="relative">
              <input
                v-model="formData.password"
                :type="isShowPassword ? 'text' : 'password'"
                autocomplete="new-password"
                placeholder="Enter new password"
                :disabled="isLoading"
                class="h-11 w-full rounded-xl border bg-white px-3.5 pr-11 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-4 disabled:cursor-not-allowed disabled:bg-slate-50"
                :class="
                  errors.password
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500/10'
                    : 'border-slate-200 focus:border-primary focus:ring-primary/10'
                "
              />

              <button
                type="button"
                @click="isShowPassword = !isShowPassword"
                class="absolute right-2 top-1/2 flex size-8 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
              >
                <component
                  :is="isShowPassword ? OffEyeIcon : EyeIcon"
                  class="size-4"
                />
              </button>
            </div>

            <p
              v-if="errors.password"
              class="mt-1.5 text-xs text-red-500"
            >
              {{ errors.password }}
            </p>
          </div>

          <!-- ========================================================
               Confirm Password
          ========================================================= -->

          <div class="w-full">
            <label class="mb-1.5 block text-xs font-medium text-slate-600">
              Confirm Password
              <span class="text-red-500">*</span>
            </label>

            <div class="relative">
              <input
                v-model="formData.password_confirm"
                :type="isShowPassword ? 'text' : 'password'"
                autocomplete="new-password"
                placeholder="Re-enter new password"
                :disabled="isLoading"
                class="h-11 w-full rounded-xl border bg-white px-3.5 pr-11 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-4 disabled:cursor-not-allowed disabled:bg-slate-50"
                :class="
                  errors.password_confirm
                    ? 'border-red-300 focus:border-red-500 focus:ring-red-500/10'
                    : 'border-slate-200 focus:border-primary focus:ring-primary/10'
                "
              />

              <button
                type="button"
                @click="isShowPassword = !isShowPassword"
                class="absolute right-2 top-1/2 flex size-8 -translate-y-1/2 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
              >
                <component
                  :is="isShowPassword ? OffEyeIcon : EyeIcon"
                  class="size-4"
                />
              </button>
            </div>

            <p
              v-if="errors.password_confirm"
              class="mt-1.5 text-xs text-red-500"
            >
              {{ errors.password_confirm }}
            </p>
          </div>

          <!-- Password Match -->

          <div
            v-if="
              formData.password &&
              formData.password_confirm &&
              !errors.password_confirm
            "
            class="flex items-center gap-2 text-xs text-emerald-600"
          >
            <div
              class="flex size-4 items-center justify-center rounded-full bg-emerald-100"
            >
              ✓
            </div>

            <span>Passwords match</span>
          </div>
        </div>

        <!-- ============================================================
             Footer
        ============================================================= -->

        <div
          class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50/60 px-5 py-4"
        >
          <button
            type="button"
            :disabled="isLoading"
            @click="close"
            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
          >
            Cancel
          </button>

          <button
            type="submit"
            :disabled="isLoading"
            class="inline-flex min-w-32 items-center justify-center gap-2 rounded-xl bg-primary px-4 py-2.5 text-sm font-medium text-white shadow-sm shadow-primary/20 transition hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-60"
          >
            <template v-if="!isLoading">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="size-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="1.8"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M16.5 10.5V7.75a4.5 4.5 0 0 0-9 0v2.75m-.75 0h10.5A1.75 1.75 0 0 1 19 12.25v7A1.75 1.75 0 0 1 17.25 21h-10.5A1.75 1.75 0 0 1 5 19.25v-7A1.75 1.75 0 0 1 6.75 10.5Z"
                />
              </svg>

              <span>Reset Password</span>
            </template>

            <template v-else>
              <SpannerIcon class="size-4 animate-spin" />
              <span>Updating...</span>
            </template>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>