<script setup lang="ts">
import { onBeforeUnmount, reactive, ref } from "vue";

import CloseIcon from "~/components/icons/CloseIcon.vue";
import EyeIcon from "~/components/icons/EyeIcon.vue";
import ImageIcon from "~/components/icons/ImageIcon.vue";
import OffEyeIcon from "~/components/icons/OffEyeIcon.vue";
import SpannerIcon from "~/components/icons/SpannerIcon.vue";

const userStore = useUserStore();
const majors = useMajorStore();

const emit = defineEmits<{
  close: [];
  submitted: [data: unknown];
}>();

/* -------------------------------------------------- */
/* Options                                             */
/* -------------------------------------------------- */

const genders = ["male", "female"];

const MAX_IMAGE_SIZE = 5 * 1024 * 1024;

/* -------------------------------------------------- */
/* Form Type                                           */
/* -------------------------------------------------- */

type FormData = {
  name_khmer: string;
  name_english: string;
  gender: string;
  dateOfBirth: string;
  phone: string;
  major: string;
  year: string;
  status: string;
  address: string;
  work_address: string;
  image: File | null;
  password: string;
  password_confirmation: string;
};

type FormErrors = Partial<Record<keyof FormData, string>>;

/* -------------------------------------------------- */
/* Form State                                          */
/* -------------------------------------------------- */

const formData = ref<FormData>({
  name_khmer: "",
  name_english: "",
  gender: "",
  dateOfBirth: "",
  phone: "",
  major: "",
  year: "",
  status: "",
  address: "",
  work_address: "",
  image: null,
  password: "",
  password_confirmation: "",
});

const errors = ref<FormErrors>({});
const message = ref("");
const isLoading = ref(false);

const imagePreview = ref<string | null>(null);
const imageInput = ref<HTMLInputElement | null>(null);

const majorActive = ref<string | null>(null);
const isPassword = ref(false);

/* -------------------------------------------------- */
/* Image                                               */
/* -------------------------------------------------- */

const openImagePicker = () => {
  imageInput.value?.click();
};

const handleImageChange = (event: Event) => {
  const input = event.target as HTMLInputElement;
  const file = input.files?.[0];

  if (!file) return;

  errors.value.image = undefined;

  if (file.size > MAX_IMAGE_SIZE) {
    errors.value.image = "Image size cannot be larger than 5MB.";
    input.value = "";
    return;
  }

  if (!file.type.startsWith("image/")) {
    errors.value.image = "Please select a valid image.";
    input.value = "";
    return;
  }

  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value);
  }

  formData.value.image = file;
  imagePreview.value = URL.createObjectURL(file);
};

const removeImage = () => {
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value);
  }

  imagePreview.value = null;
  formData.value.image = null;

  if (imageInput.value) {
    imageInput.value.value = "";
  }
};

onBeforeUnmount(() => {
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value);
  }
});

/* -------------------------------------------------- */
/* Major                                               */
/* -------------------------------------------------- */

const handleMajor = (data: any) => {
  if (!data) return;

  formData.value.major = String(data.id);
  majorActive.value = data.title;
};

/* -------------------------------------------------- */
/* Password                                            */
/* -------------------------------------------------- */

const togglePassword = () => {
  isPassword.value = !isPassword.value;
};

/* -------------------------------------------------- */
/* Validation                                          */
/* -------------------------------------------------- */

const validate = () => {
  errors.value = {};
  message.value = "";

  let hasError = false;

  const requiredFields: {
    key: keyof FormData;
    message: string;
  }[] = [
    {
      key: "name_khmer",
      message: "Khmer name is required.",
    },
    {
      key: "name_english",
      message: "English name is required.",
    },
    {
      key: "gender",
      message: "Gender is required.",
    },
    {
      key: "dateOfBirth",
      message: "Date of birth is required.",
    },
    {
      key: "phone",
      message: "Mobile phone is required.",
    },
    {
      key: "major",
      message: "Major is required.",
    },
    {
      key: "year",
      message: "Graduation year is required.",
    },
    {
      key: "status",
      message: "Work status is required.",
    },
    {
      key: "address",
      message: "Address is required.",
    },
    {
      key: "password",
      message: "Password is required.",
    },
    {
      key: "password_confirmation",
      message: "Password confirmation is required.",
    },
  ];

  for (const field of requiredFields) {
    const value = formData.value[field.key];

    if (
      value === null ||
      value === undefined ||
      String(value).trim() === ""
    ) {
      errors.value[field.key] = field.message;
      hasError = true;
    }
  }

  if (
    formData.value.password &&
    formData.value.password_confirmation &&
    formData.value.password !== formData.value.password_confirmation
  ) {
    errors.value.password_confirmation = "Passwords do not match.";
    hasError = true;
  }

  if (formData.value.password && formData.value.password.length < 8) {
    errors.value.password = "Password must be at least 8 characters.";
    hasError = true;
  }

  return hasError;
};

/* -------------------------------------------------- */
/* Submit                                              */
/* -------------------------------------------------- */

const submit = async () => {
  if (validate()) return;

  const data = new FormData();

  data.append("name_khmer", formData.value.name_khmer);
  data.append("name_english", formData.value.name_english);
  data.append("gender", formData.value.gender);
  data.append("date_of_birth", formData.value.dateOfBirth);
  data.append("major_id", formData.value.major);
  data.append("address", formData.value.address);
  data.append("work", formData.value.status);
  data.append("last_year", formData.value.year);
  data.append("mobile", formData.value.phone);
  data.append("work_address", formData.value.work_address);
  data.append("password", formData.value.password);
  data.append(
    "password_confirmation",
    formData.value.password_confirmation,
  );

  if (formData.value.image instanceof File) {
    data.append("image", formData.value.image);
  }

  isLoading.value = true;
  message.value = "";

  try {
    const res = await userStore.addUser(data);

    if (userStore.data?.data?.data && res?.data?.data) {
      userStore.data.data.data.unshift(res.data.data);
    }

    emit("submitted", res?.data?.data);
    emit("close");
  } catch (error: any) {
    const response = error?.response?.data;

    if (typeof response === "string") {
      message.value = response;
    } else if (response?.message) {
      message.value = response.message;
    } else {
      message.value = "Something went wrong. Please try again.";
    }
  } finally {
    isLoading.value = false;
  }
};

/* -------------------------------------------------- */
/* Close                                               */
/* -------------------------------------------------- */

const close = () => {
  if (isLoading.value) return;

  emit("close");
};
</script>

<template>
  <!-- Overlay -->
  <div
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/40 p-4 backdrop-blur-sm"
    @click.self="close"
  >
    <!-- Modal -->
    <div
      class="flex max-h-[92vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
    >
      <!-- Header -->
      <div
        class="flex items-center justify-between border-b border-slate-200 px-6 py-4"
      >
        <div>
          <h2 class="text-base font-semibold text-slate-800">
            Add New Alumni
          </h2>

          <p class="mt-1 text-xs text-slate-500">
            Create a new alumni account and profile.
          </p>
        </div>

        <button
          type="button"
          class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-red-50 hover:text-red-500"
          @click="close"
        >
          <CloseIcon />
        </button>
      </div>

      <!-- Error Message -->
      <div
        v-if="message"
        class="mx-6 mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-600"
      >
        {{ message }}
      </div>

      <!-- Form -->
      <form
        class="flex min-h-0 flex-1 flex-col"
        @submit.prevent="submit"
      >
        <!-- Scroll Area -->
        <div class="flex-1 overflow-y-auto px-6 py-5">
          <!-- Profile Section -->
          <div
            class="mb-6 rounded-xl border border-slate-200 bg-slate-50/60 p-4"
          >
            <div class="mb-4">
              <h3 class="text-sm font-semibold text-slate-800">
                Profile Information
              </h3>

              <p class="mt-1 text-xs text-slate-500">
                Basic information about the alumni.
              </p>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
              <!-- Khmer Name -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Name Khmer
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model="formData.name_khmer"
                  type="text"
                  placeholder="Enter Khmer name"
                  class="form-input"
                  :class="{ 'form-input-error': errors.name_khmer }"
                />

                <p v-if="errors.name_khmer" class="form-error">
                  {{ errors.name_khmer }}
                </p>
              </div>

              <!-- English Name -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Name English
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model="formData.name_english"
                  type="text"
                  placeholder="Enter English name"
                  class="form-input"
                  :class="{ 'form-input-error': errors.name_english }"
                />

                <p v-if="errors.name_english" class="form-error">
                  {{ errors.name_english }}
                </p>
              </div>

              <!-- Gender -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Gender
                  <span class="text-red-500">*</span>
                </label>

                <select
                  v-model="formData.gender"
                  class="form-input"
                  :class="{ 'form-input-error': errors.gender }"
                >
                  <option value="" disabled>
                    Select gender
                  </option>

                  <option
                    v-for="gender in genders"
                    :key="gender"
                    :value="gender"
                  >
                    {{ gender.charAt(0).toUpperCase() + gender.slice(1) }}
                  </option>
                </select>

                <p v-if="errors.gender" class="form-error">
                  {{ errors.gender }}
                </p>
              </div>

              <!-- Date of Birth -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Date of Birth
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model="formData.dateOfBirth"
                  type="date"
                  class="form-input"
                  :class="{ 'form-input-error': errors.dateOfBirth }"
                />

                <p v-if="errors.dateOfBirth" class="form-error">
                  {{ errors.dateOfBirth }}
                </p>
              </div>

              <!-- Phone -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Mobile Phone
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model="formData.phone"
                  type="tel"
                  placeholder="Enter mobile phone"
                  class="form-input"
                  :class="{ 'form-input-error': errors.phone }"
                />

                <p v-if="errors.phone" class="form-error">
                  {{ errors.phone }}
                </p>
              </div>

              <!-- Graduation Year -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Graduation Year
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model="formData.year"
                  type="number"
                  min="1900"
                  max="2100"
                  placeholder="e.g. 2025"
                  class="form-input"
                  :class="{ 'form-input-error': errors.year }"
                />

                <p v-if="errors.year" class="form-error">
                  {{ errors.year }}
                </p>
              </div>
            </div>
          </div>

          <!-- Academic & Career -->
          <div
            class="mb-6 rounded-xl border border-slate-200 bg-white p-4"
          >
            <div class="mb-4">
              <h3 class="text-sm font-semibold text-slate-800">
                Academic & Career
              </h3>

              <p class="mt-1 text-xs text-slate-500">
                Academic background and current work information.
              </p>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
              <!-- Major -->
              <div class="relative">
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Major
                  <span class="text-red-500">*</span>
                </label>

                <OptionsOption
                  :items="majors.data?.data ?? []"
                  title="Select Major"
                  :active="majorActive ?? undefined"
                  z-index="z-30"
                  @update:active="handleMajor"
                />

                <p v-if="errors.major" class="form-error">
                  {{ errors.major }}
                </p>
              </div>

              <!-- Work Status -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Work Status
                  <span class="text-red-500">*</span>
                </label>

                <select
                  v-model="formData.status"
                  class="form-input"
                  :class="{ 'form-input-error': errors.status }"
                >
                  <option value="" disabled>
                    Select work status
                  </option>

                  <option
                    v-for="(item, index) in workStatus"
                    :key="index"
                    :value="item.status"
                  >
                    {{ item.title }}
                  </option>
                </select>

                <p v-if="errors.status" class="form-error">
                  {{ errors.status }}
                </p>
              </div>

              <!-- Address -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Address
                  <span class="text-red-500">*</span>
                </label>

                <input
                  v-model="formData.address"
                  type="text"
                  placeholder="Enter current address"
                  class="form-input"
                  :class="{ 'form-input-error': errors.address }"
                />

                <p v-if="errors.address" class="form-error">
                  {{ errors.address }}
                </p>
              </div>

              <!-- Work Address -->
              <div class="md:col-span-2 lg:col-span-3">
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Work Address
                </label>

                <input
                  v-model="formData.work_address"
                  type="text"
                  placeholder="Enter company / workplace address"
                  class="form-input"
                />
              </div>
            </div>
          </div>

          <!-- Account & Image -->
          <div
            class="rounded-xl border border-slate-200 bg-slate-50/60 p-4"
          >
            <div class="mb-4">
              <h3 class="text-sm font-semibold text-slate-800">
                Account & Profile Image
              </h3>

              <p class="mt-1 text-xs text-slate-500">
                Set login credentials and upload profile image.
              </p>
            </div>

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
              <!-- Password -->
              <div class="space-y-4">
                <div>
                  <label
                    class="mb-1 block text-xs font-medium text-slate-600"
                  >
                    Password
                    <span class="text-red-500">*</span>
                  </label>

                  <div class="relative">
                    <input
                      v-model="formData.password"
                      :type="isPassword ? 'text' : 'password'"
                      placeholder="Minimum 8 characters"
                      class="form-input pr-11"
                      :class="{ 'form-input-error': errors.password }"
                    />

                    <button
                      type="button"
                      class="absolute right-0 top-0 flex h-full w-10 items-center justify-center text-slate-400 transition hover:text-primary"
                      @click="togglePassword"
                    >
                      <component
                        :is="isPassword ? EyeIcon : OffEyeIcon"
                      />
                    </button>
                  </div>

                  <p v-if="errors.password" class="form-error">
                    {{ errors.password }}
                  </p>
                </div>

                <!-- Confirm -->
                <div>
                  <label
                    class="mb-1 block text-xs font-medium text-slate-600"
                  >
                    Confirm Password
                    <span class="text-red-500">*</span>
                  </label>

                  <div class="relative">
                    <input
                      v-model="formData.password_confirmation"
                      :type="isPassword ? 'text' : 'password'"
                      placeholder="Repeat password"
                      class="form-input pr-11"
                      :class="{
                        'form-input-error': errors.password_confirmation,
                      }"
                    />

                    <button
                      type="button"
                      class="absolute right-0 top-0 flex h-full w-10 items-center justify-center text-slate-400 transition hover:text-primary"
                      @click="togglePassword"
                    >
                      <component
                        :is="isPassword ? EyeIcon : OffEyeIcon"
                      />
                    </button>
                  </div>

                  <p
                    v-if="errors.password_confirmation"
                    class="form-error"
                  >
                    {{ errors.password_confirmation }}
                  </p>
                </div>
              </div>

              <!-- Image -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Profile Image
                </label>

                <input
                  ref="imageInput"
                  type="file"
                  accept="image/*"
                  class="hidden"
                  @change="handleImageChange"
                />

                <div
                  class="relative flex min-h-40 cursor-pointer items-center justify-center overflow-hidden rounded-xl border border-dashed border-slate-300 bg-white transition hover:border-primary hover:bg-primary/5"
                  :class="{
                    'border-red-500': errors.image,
                  }"
                  @click="openImagePicker"
                >
                  <img
                    v-if="imagePreview"
                    :src="imagePreview"
                    alt="Profile preview"
                    class="h-40 w-full object-contain"
                  />

                  <div
                    v-else
                    class="flex flex-col items-center justify-center text-center"
                  >
                    <div
                      class="mb-2 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100"
                    >
                      <ImageIcon class="size-7 text-slate-400" />
                    </div>

                    <p class="text-xs font-medium text-slate-600">
                      Click to upload image
                    </p>

                    <p class="mt-1 text-[11px] text-slate-400">
                      PNG, JPG or JPEG · Max 5MB
                    </p>
                  </div>

                  <button
                    v-if="imagePreview"
                    type="button"
                    class="absolute right-2 top-2 rounded-lg bg-red-500 px-2 py-1 text-[11px] text-white shadow hover:bg-red-600"
                    @click.stop="removeImage"
                  >
                    Remove
                  </button>
                </div>

                <p v-if="errors.image" class="form-error">
                  {{ errors.image }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Footer -->
        <div
          class="flex items-center justify-end gap-3 border-t border-slate-200 bg-white px-6 py-4"
        >
          <button
            type="button"
            :disabled="isLoading"
            class="rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-50"
            @click="close"
          >
            Cancel
          </button>

          <button
            type="submit"
            :disabled="isLoading"
            class="flex min-w-28 items-center justify-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-medium text-white transition hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-60"
          >
            <template v-if="!isLoading">
              Save Alumni
            </template>

            <template v-else>
              <span>Saving...</span>
              <SpannerIcon />
            </template>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped>
.form-input {
  width: 100%;
  border: 1px solid rgb(203 213 225);
  border-radius: 0.5rem;
  padding: 0.625rem 0.75rem;
  font-size: 0.8125rem;
  color: rgb(51 65 85);
  background: white;
  outline: none;
  transition:
    border-color 0.15s ease,
    box-shadow 0.15s ease;
}

.form-input::placeholder {
  color: rgb(148 163 184);
}

.form-input:focus {
  border-color: rgb(59 130 246);
  box-shadow: 0 0 0 3px rgb(59 130 246 / 0.1);
}

.form-input-error {
  border-color: rgb(239 68 68);
}

.form-input-error:focus {
  border-color: rgb(239 68 68);
  box-shadow: 0 0 0 3px rgb(239 68 68 / 0.1);
}

.form-error {
  margin-top: 0.25rem;
  font-size: 0.6875rem;
  line-height: 1rem;
  color: rgb(239 68 68);
}
</style>