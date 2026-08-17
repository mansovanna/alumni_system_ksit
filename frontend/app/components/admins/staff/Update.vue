<script setup lang="ts">
import CloseIcon from "~/components/icons/CloseIcon.vue";
import SpannerIcon from "~/components/icons/SpannerIcon.vue";
import type { UserModel } from "~/types/user.model";

const props = defineProps<{
  data: UserModel;
}>();

const emit = defineEmits<{
  (e: "close"): void;
  (e: "submitted", data: UserModel): void;
}>();

const staffStore = useStaffStore();

/* -------------------------------------------------------------------------- */
/* Error                                                                      */
/* -------------------------------------------------------------------------- */

const messageError = ref<string | null>(null);

/* -------------------------------------------------------------------------- */
/* Form                                                                       */
/* -------------------------------------------------------------------------- */

const formData = ref({
  nameKhmer: props.data.name_khmer || "",
  nameEnglish: props.data.name_english || "",
  login: props.data.email || props.data.mobile || "",
  role: props.data.role || "",
});

/* -------------------------------------------------------------------------- */
/* Validation                                                                 */
/* -------------------------------------------------------------------------- */

const errors = ref({
  nameKhmer: "",
  nameEnglish: "",
  login: "",
  role: "",
});

const validate = () => {
  errors.value = {
    nameKhmer: "",
    nameEnglish: "",
    login: "",
    role: "",
  };

  messageError.value = null;

  let hasError = false;

  if (!formData.value.nameKhmer.trim()) {
    errors.value.nameKhmer = "Name Khmer is required.";
    hasError = true;
  }

  if (!formData.value.nameEnglish.trim()) {
    errors.value.nameEnglish = "Name English is required.";
    hasError = true;
  }

  if (!formData.value.login.trim()) {
    errors.value.login = "Email or mobile is required.";
    hasError = true;
  }

  if (!formData.value.role) {
    errors.value.role = "Please select a role.";
    hasError = true;
  }

  return hasError;
};

/* -------------------------------------------------------------------------- */
/* Submit                                                                     */
/* -------------------------------------------------------------------------- */

const isLoading = ref(false);

const submit = async () => {
  if (validate()) return;

  const data = new FormData();

  data.append("name_khmer", formData.value.nameKhmer.trim());
  data.append("name_english", formData.value.nameEnglish.trim());
  data.append("login", formData.value.login.trim());
  data.append("role", formData.value.role);

  isLoading.value = true;
  messageError.value = null;

  try {
    const res = await staffStore.updateStaff(props.data.id, data);

    const updatedStaff = res?.data?.data;

    if (updatedStaff && staffStore.data?.data?.data) {
      staffStore.data.data.data = staffStore.data.data.data.map((item) =>
        item.id === updatedStaff.id ? updatedStaff : item,
      );
    }

    emit("submitted", updatedStaff);
    emit("close");
  } catch (e: any) {
    const serverErrors = e?.response?.data?.errors;

    messageError.value =
      e?.response?.data?.message || "Unable to update staff information.";

    if (serverErrors) {
      errors.value.nameKhmer = serverErrors.name_khmer?.[0] || "";
      errors.value.nameEnglish = serverErrors.name_english?.[0] || "";
      errors.value.login = serverErrors.login?.[0] || "";
      errors.value.role = serverErrors.role?.[0] || "";
    }
  } finally {
    isLoading.value = false;
  }
};

/* -------------------------------------------------------------------------- */
/* Close                                                                      */
/* -------------------------------------------------------------------------- */

const closeModal = () => {
  if (!isLoading.value) {
    emit("close");
  }
};
</script>

<template>
  <!-- Overlay -->
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 backdrop-blur-sm p-4"
    @click="closeModal"
  >
    <!-- Modal -->
    <div
      class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl border border-slate-200"
      @click.stop
    >
      <!-- ================================================================ -->
      <!-- Header -->
      <!-- ================================================================ -->

      <div
        class="flex items-center justify-between border-b border-slate-100 px-6 py-4"
      >
        <div class="flex items-center gap-3">
          <!-- Icon -->
          <div
            class="flex size-10 items-center justify-center rounded-xl bg-primary/10 text-primary"
          >
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="size-5"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="1.8"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 20h9"
              />
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M16.5 3.5a2.121 2.121 0 013 3L8 18l-4 1 1-4 11.5-11.5z"
              />
            </svg>
          </div>

          <div>
            <h2 class="text-base font-semibold text-slate-800">
              Edit Staff
            </h2>

            <p class="mt-0.5 text-xs text-slate-400">
              Update staff account information
            </p>
          </div>
        </div>

        <!-- Close -->
        <button
          type="button"
          :disabled="isLoading"
          @click="closeModal"
          class="flex size-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-500 disabled:cursor-not-allowed disabled:opacity-50"
        >
          <CloseIcon class="size-5" />
        </button>
      </div>

      <!-- ================================================================ -->
      <!-- Body -->
      <!-- ================================================================ -->

      <form @submit.prevent="submit">
        <div class="max-h-[70vh] overflow-y-auto px-6 py-6">
          <!-- Server Error -->

          <div
            v-if="messageError"
            class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3"
          >
            <div
              class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600"
            >
              <span class="text-xs font-bold">!</span>
            </div>

            <div>
              <p class="text-sm font-medium text-red-700">
                Update failed
              </p>

              <p class="mt-0.5 text-xs text-red-600">
                {{ messageError }}
              </p>
            </div>
          </div>

          <!-- Section -->

          <div class="mb-6">
            <div class="mb-4">
              <h3 class="text-sm font-semibold text-slate-700">
                Personal Information
              </h3>

              <p class="mt-1 text-xs text-slate-400">
                Update the staff member's name and account details.
              </p>
            </div>

            <!-- Grid -->

            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
              <!-- ====================================================== -->
              <!-- Name Khmer -->
              <!-- ====================================================== -->

              <div>
                <label
                  class="mb-1.5 block text-xs font-medium text-slate-600"
                >
                  Name Khmer
                  <span class="text-red-500">*</span>
                </label>

                <div class="relative">
                  <!-- Icon -->

                  <div
                    class="pointer-events-none absolute inset-y-0 left-0 flex w-10 items-center justify-center text-slate-400"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="size-4"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.8"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15 19a6 6 0 00-12 0"
                      />
                      <circle cx="9" cy="7" r="4" />
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19 8v6M22 11h-6"
                      />
                    </svg>
                  </div>

                  <input
                    v-model="formData.nameKhmer"
                    type="text"
                    placeholder="Enter Khmer name"
                    class="h-11 w-full rounded-xl border bg-white pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-4 focus:ring-primary/10"
                    :class="
                      errors.nameKhmer
                        ? 'border-red-400 focus:border-red-500'
                        : 'border-slate-200 focus:border-primary'
                    "
                  />
                </div>

                <p
                  v-if="errors.nameKhmer"
                  class="mt-1.5 text-xs text-red-500"
                >
                  {{ errors.nameKhmer }}
                </p>
              </div>

              <!-- ====================================================== -->
              <!-- Name English -->
              <!-- ====================================================== -->

              <div>
                <label
                  class="mb-1.5 block text-xs font-medium text-slate-600"
                >
                  Name English
                  <span class="text-red-500">*</span>
                </label>

                <div class="relative">
                  <div
                    class="pointer-events-none absolute inset-y-0 left-0 flex w-10 items-center justify-center text-slate-400"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="size-4"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.8"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M15 19a6 6 0 00-12 0"
                      />
                      <circle cx="9" cy="7" r="4" />
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19 8v6M22 11h-6"
                      />
                    </svg>
                  </div>

                  <input
                    v-model="formData.nameEnglish"
                    type="text"
                    placeholder="Enter English name"
                    class="h-11 w-full rounded-xl border bg-white pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-4 focus:ring-primary/10"
                    :class="
                      errors.nameEnglish
                        ? 'border-red-400 focus:border-red-500'
                        : 'border-slate-200 focus:border-primary'
                    "
                  />
                </div>

                <p
                  v-if="errors.nameEnglish"
                  class="mt-1.5 text-xs text-red-500"
                >
                  {{ errors.nameEnglish }}
                </p>
              </div>

              <!-- ====================================================== -->
              <!-- Email -->
              <!-- ====================================================== -->

              <div>
                <label
                  class="mb-1.5 block text-xs font-medium text-slate-600"
                >
                  Email / Mobile
                  <span class="text-red-500">*</span>
                </label>

                <div class="relative">
                  <div
                    class="pointer-events-none absolute inset-y-0 left-0 flex w-10 items-center justify-center text-slate-400"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="size-4"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.8"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
                      />
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M22 6l-10 7L2 6"
                      />
                    </svg>
                  </div>

                  <input
                    v-model="formData.login"
                    type="text"
                    placeholder="Enter email or mobile"
                    class="h-11 w-full rounded-xl border bg-white pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:ring-4 focus:ring-primary/10"
                    :class="
                      errors.login
                        ? 'border-red-400 focus:border-red-500'
                        : 'border-slate-200 focus:border-primary'
                    "
                  />
                </div>

                <p v-if="errors.login" class="mt-1.5 text-xs text-red-500">
                  {{ errors.login }}
                </p>
              </div>

              <!-- ====================================================== -->
              <!-- Role -->
              <!-- ====================================================== -->

              <div>
                <label
                  class="mb-1.5 block text-xs font-medium text-slate-600"
                >
                  Role
                  <span class="text-red-500">*</span>
                </label>

                <div class="relative">
                  <div
                    class="pointer-events-none absolute inset-y-0 left-0 flex w-10 items-center justify-center text-slate-400"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="size-4"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="1.8"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 15a3 3 0 100-6 3 3 0 000 6z"
                      />
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19.4 15a1.7 1.7 0 00.34 1.88l.06.06-1.4 1.4-.06-.06a1.7 1.7 0 00-1.88-.34 1.7 1.7 0 00-1.04 1.56V21h-2v-.5a1.7 1.7 0 00-1.04-1.56 1.7 1.7 0 00-1.88.34l-.06.06-1.4-1.4.06-.06A1.7 1.7 0 008.4 15a1.7 1.7 0 00-1.56-1.04H6v-2h.84A1.7 1.7 0 008.4 10.9a1.7 1.7 0 00-.34-1.88L8 8.96l1.4-1.4.06.06a1.7 1.7 0 001.88.34A1.7 1.7 0 0012.38 6.4V6h2v.4a1.7 1.7 0 001.04 1.56 1.7 1.7 0 001.88-.34l.06-.06 1.4 1.4-.06.06A1.7 1.7 0 0018.4 10.9a1.7 1.7 0 001.56 1.04H20v2h-.04A1.7 1.7 0 0019.4 15z"
                      />
                    </svg>
                  </div>

                  <select
                    v-model="formData.role"
                    class="h-11 w-full appearance-none rounded-xl border bg-white pl-10 pr-10 text-sm capitalize text-slate-700 outline-none transition focus:ring-4 focus:ring-primary/10"
                    :class="
                      errors.role
                        ? 'border-red-400 focus:border-red-500'
                        : 'border-slate-200 focus:border-primary'
                    "
                  >
                    <option value="" disabled>
                      Select role
                    </option>

                    <option value="admin">
                      Admin
                    </option>

                    <option value="staff">
                      Staff
                    </option>
                  </select>

                  <!-- Arrow -->

                  <div
                    class="pointer-events-none absolute inset-y-0 right-0 flex w-10 items-center justify-center text-slate-400"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="size-4"
                      viewBox="0 0 24 24"
                      fill="none"
                      stroke="currentColor"
                      stroke-width="2"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 9l6 6 6-6"
                      />
                    </svg>
                  </div>
                </div>

                <p v-if="errors.role" class="mt-1.5 text-xs text-red-500">
                  {{ errors.role }}
                </p>
              </div>
            </div>
          </div>

          <!-- ============================================================ -->
          <!-- Account Preview -->
          <!-- ============================================================ -->

          <div
            class="mt-2 rounded-xl border border-slate-200 bg-slate-50/70 p-4"
          >
            <div class="flex items-center gap-3">
              <div
                class="flex size-10 items-center justify-center rounded-full bg-primary text-sm font-semibold text-white"
              >
                {{
                  formData.nameEnglish?.charAt(0)?.toUpperCase() || "S"
                }}
              </div>

              <div class="min-w-0">
                <p class="truncate text-sm font-semibold text-slate-700">
                  {{ formData.nameEnglish || "Staff Name" }}
                </p>

                <p class="truncate text-xs text-slate-400">
                  {{ formData.login || "Email or mobile" }}
                </p>
              </div>

              <span
                class="ml-auto rounded-full px-2.5 py-1 text-[11px] font-medium capitalize"
                :class="
                  formData.role === 'admin'
                    ? 'bg-purple-100 text-purple-600'
                    : 'bg-blue-100 text-blue-600'
                "
              >
                {{ formData.role || "staff" }}
              </span>
            </div>
          </div>
        </div>

        <!-- ================================================================ -->
        <!-- Footer -->
        <!-- ================================================================ -->

        <div
          class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50/50 px-6 py-4"
        >
          <button
            type="button"
            :disabled="isLoading"
            @click="closeModal"
            class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-50"
          >
            Cancel
          </button>

          <button
            type="submit"
            :disabled="isLoading"
            class="inline-flex min-w-[125px] items-center justify-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-medium text-white shadow-sm shadow-primary/20 transition hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-60"
          >
            <template v-if="!isLoading">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="size-4"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M5 12l4 4L19 6"
                />
              </svg>

              <span>Save Changes</span>
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