<script setup lang="ts">
import CloseIcon from "~/components/icons/CloseIcon.vue";
import EyeIcon from "~/components/icons/EyeIcon.vue";
import OffEyeIcon from "~/components/icons/OffEyeIcon.vue";
import SpannerIcon from "~/components/icons/SpannerIcon.vue";

const staffStore = useStaffStore();

const emit = defineEmits(["close", "submitted"]);

const messageError = ref<string | null>(null);

const formData = ref({
  nameKhmer: null as string | null,
  nameEnglish: null as string | null,
  login: null as string | null,
  role: null as string | null,
  password: null as string | null,
  password_confirm: null as string | null,
});

const errors = ref({
  nameKhmer: null as string | null,
  nameEnglish: null as string | null,
  login: null as string | null,
  role: null as string | null,
  password: null as string | null,
  password_confirm: null as string | null,
});

const isLoading = ref(false);
const isShowPassword = ref(false);

const validate = () => {
  errors.value = {
    nameKhmer: "",
    nameEnglish: "",
    login: "",
    role: "",
    password: "",
    password_confirm: "",
  };

  let hasError = false;

  if (!formData.value.nameKhmer?.trim()) {
    errors.value.nameKhmer = "Name Khmer is required.";
    hasError = true;
  }

  if (!formData.value.nameEnglish?.trim()) {
    errors.value.nameEnglish = "Name English is required.";
    hasError = true;
  }

  if (!formData.value.login?.trim()) {
    errors.value.login = "Email is required.";
    hasError = true;
  }

  if (!formData.value.role) {
    errors.value.role = "Please select a role.";
    hasError = true;
  }

  if (!formData.value.password) {
    errors.value.password = "Password is required.";
    hasError = true;
  } else if (formData.value.password.length < 8) {
    errors.value.password = "Password must be at least 8 characters.";
    hasError = true;
  }

  if (!formData.value.password_confirm) {
    errors.value.password_confirm =
      "Password confirmation is required.";
    hasError = true;
  } else if (
    formData.value.password !==
    formData.value.password_confirm
  ) {
    errors.value.password_confirm = "Passwords do not match.";
    hasError = true;
  }

  return hasError;
};

const submit = async () => {
  if (validate()) return;

  const data = new FormData();

  data.append("name_khmer", String(formData.value.nameKhmer));
  data.append("name_english", String(formData.value.nameEnglish));
  data.append("login", String(formData.value.login));
  data.append("role", String(formData.value.role));
  data.append("password", String(formData.value.password));
  data.append(
    "password_confirmation",
    String(formData.value.password_confirm),
  );

  isLoading.value = true;
  messageError.value = null;

  try {
    const res = await staffStore.addStaff(data);

    staffStore.data?.data?.data.unshift(res.data?.data);

    emit("submitted", res.data?.data);
    emit("close");
  } catch (e: any) {
    messageError.value =
      e.response?.data?.message ??
      "Unable to create staff account.";

    const serverErrors = e.response?.data?.errors;

    if (serverErrors) {
      if (serverErrors.name_khmer?.[0])
        errors.value.nameKhmer = serverErrors.name_khmer[0];

      if (serverErrors.name_english?.[0])
        errors.value.nameEnglish = serverErrors.name_english[0];

      if (serverErrors.login?.[0])
        errors.value.login = serverErrors.login[0];

      if (serverErrors.role?.[0])
        errors.value.role = serverErrors.role[0];

      if (serverErrors.password?.[0])
        errors.value.password = serverErrors.password[0];
    }
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <!-- Overlay -->

  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 backdrop-blur-sm p-4"
    @click="$emit('close')"
  >

    <!-- Modal -->

    <div
      class="w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden"
      @click.stop
    >

      <!-- ============================================================
           HEADER
      ============================================================= -->

      <div
        class="px-6 py-4 border-b border-slate-100 flex items-center justify-between"
      >

        <div class="flex items-center gap-3">

          <div
            class="size-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center"
          >
            <SpannerIcon class="size-5" />
          </div>

          <div>

            <h2 class="text-base font-semibold text-slate-800">
              Add New Staff
            </h2>

            <p class="text-xs text-slate-400 mt-0.5">
              Create a new staff account
            </p>

          </div>

        </div>

        <button
          type="button"
          @click="$emit('close')"
          class="size-9 rounded-lg flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition cursor-pointer"
        >
          <CloseIcon class="size-5" />
        </button>

      </div>

      <!-- ============================================================
           FORM
      ============================================================= -->

      <form
        @submit.prevent="submit"
        class="max-h-[calc(100vh-160px)] overflow-y-auto"
      >

        <div class="px-6 py-5 space-y-5">

          <!-- Error -->

          <div
            v-if="messageError"
            class="px-4 py-3 rounded-xl bg-red-50 border border-red-100 text-sm text-red-600"
          >
            {{ messageError }}
          </div>

          <!-- Account Information -->

          <div>

            <div class="mb-3">
              <h3 class="text-sm font-semibold text-slate-700">
                Staff Information
              </h3>

              <p class="text-xs text-slate-400 mt-0.5">
                Enter the staff member's basic information.
              </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

              <!-- Khmer -->

              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">
                  Name Khmer
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model="formData.nameKhmer"
                  type="text"
                  placeholder="Enter Khmer name"
                  class="w-full h-11 px-3 rounded-xl border bg-white text-sm outline-none transition focus:ring-4 focus:ring-primary/10 focus:border-primary"
                  :class="
                    errors.nameKhmer
                      ? 'border-red-400'
                      : 'border-slate-200'
                  "
                />

                <p
                  v-if="errors.nameKhmer"
                  class="mt-1 text-xs text-red-500"
                >
                  {{ errors.nameKhmer }}
                </p>
              </div>

              <!-- English -->

              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">
                  Name English
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model="formData.nameEnglish"
                  type="text"
                  placeholder="Enter English name"
                  class="w-full h-11 px-3 rounded-xl border bg-white text-sm outline-none transition focus:ring-4 focus:ring-primary/10 focus:border-primary"
                  :class="
                    errors.nameEnglish
                      ? 'border-red-400'
                      : 'border-slate-200'
                  "
                />

                <p
                  v-if="errors.nameEnglish"
                  class="mt-1 text-xs text-red-500"
                >
                  {{ errors.nameEnglish }}
                </p>
              </div>

              <!-- Email -->

              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">
                  Email
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model="formData.login"
                  type="email"
                  placeholder="staff@example.com"
                  class="w-full h-11 px-3 rounded-xl border bg-white text-sm outline-none transition focus:ring-4 focus:ring-primary/10 focus:border-primary"
                  :class="
                    errors.login
                      ? 'border-red-400'
                      : 'border-slate-200'
                  "
                />

                <p
                  v-if="errors.login"
                  class="mt-1 text-xs text-red-500"
                >
                  {{ errors.login }}
                </p>
              </div>

              <!-- Role -->

              <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">
                  Role
                  <span class="text-red-500">*</span>
                </label>

                <select
                  v-model="formData.role"
                  class="w-full h-11 px-3 rounded-xl border bg-white text-sm capitalize outline-none transition focus:ring-4 focus:ring-primary/10 focus:border-primary"
                  :class="
                    errors.role
                      ? 'border-red-400'
                      : 'border-slate-200'
                  "
                >
                  <option :value="null" disabled>
                    Select role
                  </option>

                  <option value="admin">
                    Admin
                  </option>

                  <option value="staff">
                    Staff
                  </option>
                </select>

                <p
                  v-if="errors.role"
                  class="mt-1 text-xs text-red-500"
                >
                  {{ errors.role }}
                </p>
              </div>

            </div>

          </div>

          <!-- Security -->

          <div>

            <div class="mb-3">
              <h3 class="text-sm font-semibold text-slate-700">
                Security
              </h3>

              <p class="text-xs text-slate-400 mt-0.5">
                Create a secure password for this account.
              </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

              <!-- Password -->

              <div>

                <label class="block text-xs font-medium text-slate-600 mb-1.5">
                  Password
                  <span class="text-red-500">*</span>
                </label>

                <div class="relative">

                  <input
                    v-model="formData.password"
                    :type="
                      isShowPassword
                        ? 'text'
                        : 'password'
                    "
                    placeholder="Create password"
                    class="w-full h-11 px-3 pr-11 rounded-xl border bg-white text-sm outline-none transition focus:ring-4 focus:ring-primary/10 focus:border-primary"
                    :class="
                      errors.password
                        ? 'border-red-400'
                        : 'border-slate-200'
                    "
                  />

                  <button
                    type="button"
                    @click="
                      isShowPassword =
                        !isShowPassword
                    "
                    class="absolute right-0 top-0 h-11 w-11 flex items-center justify-center text-slate-400 hover:text-primary cursor-pointer"
                  >
                    <component
                      :is="
                        isShowPassword
                          ? OffEyeIcon
                          : EyeIcon
                      "
                    />
                  </button>

                </div>

                <p
                  v-if="errors.password"
                  class="mt-1 text-xs text-red-500"
                >
                  {{ errors.password }}
                </p>

              </div>

              <!-- Confirm -->

              <div>

                <label class="block text-xs font-medium text-slate-600 mb-1.5">
                  Confirm Password
                  <span class="text-red-500">*</span>
                </label>

                <div class="relative">

                  <input
                    v-model="formData.password_confirm"
                    :type="
                      isShowPassword
                        ? 'text'
                        : 'password'
                    "
                    placeholder="Confirm password"
                    class="w-full h-11 px-3 pr-11 rounded-xl border bg-white text-sm outline-none transition focus:ring-4 focus:ring-primary/10 focus:border-primary"
                    :class="
                      errors.password_confirm
                        ? 'border-red-400'
                        : 'border-slate-200'
                    "
                  />

                  <button
                    type="button"
                    @click="
                      isShowPassword =
                        !isShowPassword
                    "
                    class="absolute right-0 top-0 h-11 w-11 flex items-center justify-center text-slate-400 hover:text-primary cursor-pointer"
                  >
                    <component
                      :is="
                        isShowPassword
                          ? OffEyeIcon
                          : EyeIcon
                      "
                    />
                  </button>

                </div>

                <p
                  v-if="errors.password_confirm"
                  class="mt-1 text-xs text-red-500"
                >
                  {{ errors.password_confirm }}
                </p>

              </div>

            </div>

          </div>

        </div>

        <!-- ============================================================
             FOOTER
        ============================================================= -->

        <div
          class="px-6 py-4 bg-slate-50/70 border-t border-slate-100 flex items-center justify-end gap-3"
        >

          <button
            type="button"
            @click="$emit('close')"
            class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-600 text-sm font-medium hover:bg-slate-50 transition cursor-pointer"
          >
            Cancel
          </button>

          <button
            type="submit"
            :disabled="isLoading"
            class="min-w-28 px-4 py-2.5 rounded-xl bg-primary text-white text-sm font-medium inline-flex items-center justify-center gap-2 hover:bg-primary/90 transition disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer"
          >

            <template v-if="!isLoading">
              <AddIcon class="size-4" />
              <span>Create Staff</span>
            </template>

            <template v-else>
              <SpannerIcon class="size-4 animate-spin" />
              <span>Creating...</span>
            </template>

          </button>

        </div>

      </form>

    </div>

  </div>
</template>