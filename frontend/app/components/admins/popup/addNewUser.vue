<script setup lang="ts">
import { computed, onBeforeUnmount, ref, watch } from "vue";

import CloseIcon from "~/components/icons/CloseIcon.vue";
import EyeIcon from "~/components/icons/EyeIcon.vue";
import ImageIcon from "~/components/icons/ImageIcon.vue";
import OffEyeIcon from "~/components/icons/OffEyeIcon.vue";
import SpannerIcon from "~/components/icons/SpannerIcon.vue";
import OptionItemsArray from "~/components/options/OptionItemsArray.vue";

const userStore = useUserStore();
const majors = useMajorStore();
/* -------------------------------------------------- */
/* Emits                                              */
/* -------------------------------------------------- */

const emit = defineEmits<{
  close: [];
  submitted: [data: unknown];
}>();

/* -------------------------------------------------- */
/* Options                                            */
/* -------------------------------------------------- */

const genders = ["male", "female"];

const employmentTypes = [
  {
    value: "full_time",
    title: "Full Time",
  },
  {
    value: "part_time",
    title: "Part Time",
  },
  {
    value: "contract",
    title: "Contract",
  },
  {
    value: "internship",
    title: "Internship",
  },
  {
    value: "freelance",
    title: "Freelance",
  },
];

/*
 * Employment status.
 *
 * Admin only needs to select the current status.
 * Employment details can be completed by Alumni later.
 */
const workStatus = [
  {
    status: "employed",
    title: "Employed",
  },
  {
    status: "unemployed",
    title: "Unemployed",
  },
  {
    status: "self_employed",
    title: "Self Employed",
  },
  {
    status: "studying",
    title: "Studying",
  },
  {
    status: "unknown",
    title: "Unknown",
  },
];

const MAX_IMAGE_SIZE = 5 * 1024 * 1024;

/* -------------------------------------------------- */
/* Form Type                                          */
/* -------------------------------------------------- */

type FormData = {
  /* User */
  name_khmer: string;
  name_english: string;
  email: string;
  phone: string;

  /* Alumni */
  gender: string;
  dateOfBirth: string;
  major: string;
  year: string;
  gpa: string;
  address: string;
  bio: string;

  /* Employment */
  status: string;
  company_name: string;
  job_title: string;
  employment_type: string;
  industry: string;
  work_address: string;
  salary_range: string;
  start_date: string;
  end_date: string;
  is_current: boolean;

  /* Account */
  password: string;
  password_confirmation: string;

  /* Image */
  image: File | null;
};

type FormErrors = Partial<Record<keyof FormData, string>>;

/* -------------------------------------------------- */
/* Form State                                         */
/* -------------------------------------------------- */

const formData = ref<FormData>({
  /* User */
  name_khmer: "",
  name_english: "",
  email: "",
  phone: "",

  /* Alumni */
  gender: "",
  dateOfBirth: "",
  major: "",
  year: "",
  gpa: "",
  address: "",
  bio: "",

  /* Employment */
  status: "",
  company_name: "",
  job_title: "",
  employment_type: "",
  industry: "",
  work_address: "",
  salary_range: "",
  start_date: "",
  end_date: "",
  is_current: false,

  /* Account */
  password: "",
  password_confirmation: "",

  /* Image */
  image: null,
});

const errors = ref<FormErrors>({});

const message = ref("");

const isLoading = ref(false);

const imagePreview = ref<string | null>(null);

const imageInput = ref<HTMLInputElement | null>(null);

const majorActive = ref<string | null>(null);

const isPassword = ref(false);

/* -------------------------------------------------- */
/* Image                                              */
/* -------------------------------------------------- */

const openImagePicker = () => {
  if (isLoading.value) return;

  imageInput.value?.click();
};

const handleImageChange = (event: Event) => {
  const input = event.target as HTMLInputElement;

  const file = input.files?.[0];

  if (!file) return;

  errors.value.image = undefined;

  /* File size */
  if (file.size > MAX_IMAGE_SIZE) {
    errors.value.image = "Image size cannot be larger than 5MB.";

    input.value = "";

    return;
  }

  /* File type */
  if (!file.type.startsWith("image/")) {
    errors.value.image = "Please select a valid image.";

    input.value = "";

    return;
  }

  /* Revoke old preview */
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

  errors.value.image = undefined;
};

onBeforeUnmount(() => {
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value);
  }
});

/* -------------------------------------------------- */
/* Major                                              */
/* -------------------------------------------------- */

const handleMajor = (data: any) => {
  if (!data) return;

  formData.value.major = String(data.id);

  majorActive.value = data.title;

  errors.value.major = undefined;
};

/* -------------------------------------------------- */
/* Password                                           */
/* -------------------------------------------------- */

const togglePassword = () => {
  isPassword.value = !isPassword.value;
};

/* -------------------------------------------------- */
/* Employment                                         */
/* -------------------------------------------------- */

/*
 * Employment details are only displayed when
 * the alumni status is employed.
 *
 * IMPORTANT:
 * These fields are optional during Admin creation.
 * Alumni can complete them later.
 */
const hasEmployment = computed(() => {
  return formData.value.status === "employed";
});

/*
 * If current employment is selected,
 * clear the end date.
 */
watch(
  () => formData.value.is_current,
  (value) => {
    if (value) {
      formData.value.end_date = "";

      errors.value.end_date = undefined;
    }
  },
);

/*
 * If status changes away from employed,
 * clear employment information.
 */
watch(
  () => formData.value.status,
  (value) => {
    if (value !== "employed") {
      formData.value.company_name = "";
      formData.value.job_title = "";
      formData.value.employment_type = "";
      formData.value.industry = "";
      formData.value.work_address = "";
      formData.value.salary_range = "";
      formData.value.start_date = "";
      formData.value.end_date = "";
      formData.value.is_current = false;

      errors.value.company_name = undefined;
      errors.value.job_title = undefined;
      errors.value.employment_type = undefined;
      errors.value.industry = undefined;
      errors.value.work_address = undefined;
      errors.value.salary_range = undefined;
      errors.value.start_date = undefined;
      errors.value.end_date = undefined;
    }
  },
);

/*
 * Email / Phone.
 *
 * At least one of the two must be provided.
 */
watch(
  () => [formData.value.email, formData.value.phone],
  ([email, phone]) => {
    if (email || phone) {
      errors.value.email = undefined;
      errors.value.phone = undefined;
    }
  },
);

/* -------------------------------------------------- */
/* Validation                                         */
/* -------------------------------------------------- */

const validate = () => {
  errors.value = {};

  message.value = "";

  let hasError = false;

  /*
   * Required fields.
   *
   * GPA, Date of Birth, Address and Employment
   * details are optional.
   */
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

    if (value === null || value === undefined || String(value).trim() === "") {
      errors.value[field.key] = field.message;

      hasError = true;
    }
  }

  /* ------------------------------------------------ */
  /* Email / Phone                                    */
  /* ------------------------------------------------ */

  if (!formData.value.email.trim() && !formData.value.phone.trim()) {
    const contactMessage = "Please provide an email or a phone number.";

    errors.value.email = contactMessage;

    errors.value.phone = contactMessage;

    hasError = true;
  }

  /* ------------------------------------------------ */
  /* Email format                                     */
  /* ------------------------------------------------ */

  if (formData.value.email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailRegex.test(formData.value.email)) {
      errors.value.email = "Please enter a valid email address.";

      hasError = true;
    }
  }

  /* ------------------------------------------------ */
  /* Graduation year                                  */
  /* ------------------------------------------------ */

  if (formData.value.year) {
    const year = Number(formData.value.year);

    if (year < 1900 || year > 2100) {
      errors.value.year = "Graduation year must be between 1900 and 2100.";

      hasError = true;
    }
  }

  /* ------------------------------------------------ */
  /* GPA                                              */
  /* ------------------------------------------------ */

  if (formData.value.gpa) {
    const gpa = Number(formData.value.gpa);

    if (gpa < 0 || gpa > 4) {
      errors.value.gpa = "GPA must be between 0.00 and 4.00.";

      hasError = true;
    }
  }

  /* ------------------------------------------------ */
  /* Password confirmation                            */
  /* ------------------------------------------------ */

  if (
    formData.value.password &&
    formData.value.password_confirmation &&
    formData.value.password !== formData.value.password_confirmation
  ) {
    errors.value.password_confirmation = "Passwords do not match.";

    hasError = true;
  }

  /* ------------------------------------------------ */
  /* Password length                                  */
  /* ------------------------------------------------ */

  if (formData.value.password && formData.value.password.length < 8) {
    errors.value.password = "Password must be at least 8 characters.";

    hasError = true;
  }

  /*
   * IMPORTANT:
   *
   * Employment information is OPTIONAL during
   * Admin creation.
   *
   * Therefore we do NOT validate:
   *
   * company_id
   * job_title
   * employment_type
   * industry
   * location
   * salary_range
   * start_date
   * end_date
   *
   * Alumni can provide them later.
   */

  return hasError;
};

/* -------------------------------------------------- */
/* Submit                                             */
/* -------------------------------------------------- */

const submit = async () => {
  if (validate()) {
    return;
  }

  const data = new FormData();

  /* ----------------------------------------------- */
  /* User                                            */
  /* ----------------------------------------------- */

  data.append("name_khmer", formData.value.name_khmer);

  data.append("name_english", formData.value.name_english);

  if (formData.value.email) {
    data.append("email", formData.value.email);
  }

  if (formData.value.phone) {
    data.append("mobile", formData.value.phone);
  }

  /* ----------------------------------------------- */
  /* Alumni                                          */
  /* ----------------------------------------------- */

  data.append("gender", formData.value.gender);

  if (formData.value.dateOfBirth) {
    data.append("date_of_birth", formData.value.dateOfBirth);
  }

  data.append("major_id", formData.value.major);

  data.append("graduation_year", formData.value.year);

  if (formData.value.gpa) {
    data.append("gpa", formData.value.gpa);
  }

  if (formData.value.address) {
    data.append("address", formData.value.address);
  }

  if (formData.value.bio) {
    data.append("bio", formData.value.bio);
  }

  data.append("employment_status", formData.value.status);

  /* ----------------------------------------------- */
  /* Employment                                      */
  /* ----------------------------------------------- */

  /*
   * Employment is optional.
   *
   * We only send these fields if the Admin actually
   * provided some employment information.
   */

  const hasEmploymentData =
    formData.value.company_name ||
    formData.value.job_title ||
    formData.value.employment_type ||
    formData.value.industry ||
    formData.value.work_address ||
    formData.value.salary_range ||
    formData.value.start_date ||
    formData.value.end_date ||
    formData.value.is_current;

  if (hasEmploymentData) {
    if (formData.value.company_name) {
      data.append("company_id", formData.value.company_name);
    }

    if (formData.value.job_title) {
      data.append("job_title", formData.value.job_title);
    }

    if (formData.value.employment_type) {
      data.append("employment_type", formData.value.employment_type);
    }

    if (formData.value.industry) {
      data.append("industry", formData.value.industry);
    }

    if (formData.value.work_address) {
      data.append("location", formData.value.work_address);
    }

    if (formData.value.salary_range) {
      data.append("salary_range", formData.value.salary_range);
    }

    if (formData.value.start_date) {
      data.append("start_date", formData.value.start_date);
    }

    if (formData.value.end_date) {
      data.append("end_date", formData.value.end_date);
    }

    data.append("is_current", formData.value.is_current ? "1" : "0");
  }

  /* ----------------------------------------------- */
  /* Account                                         */
  /* ----------------------------------------------- */

  data.append("password", formData.value.password);

  data.append("password_confirmation", formData.value.password_confirmation);

  /* ----------------------------------------------- */
  /* Image                                           */
  /* ----------------------------------------------- */

  if (formData.value.image instanceof File) {
    data.append("image", formData.value.image);
  }

  /* ----------------------------------------------- */
  /* Request                                         */
  /* ----------------------------------------------- */

  isLoading.value = true;

  message.value = "";

  try {
    const res = await userStore.addUser(data);

    emit("submitted", res?.data?.data);

    emit("close");
  } catch (error: any) {
    const response = error?.response?.data;

    if (typeof response === "string") {
      message.value = response;
    } else if (response?.message) {
      message.value = response.message;
    } else if (response?.errors) {
      const backendErrors = response.errors;

      Object.keys(backendErrors).forEach((key) => {
        if (key in formData.value) {
          const formKey = key as keyof FormData;

          errors.value[formKey] = Array.isArray(backendErrors[key])
            ? backendErrors[key][0]
            : backendErrors[key];
        }
      });

      message.value = "Please check the form and correct the errors.";
    } else {
      message.value = "Something went wrong. Please try again.";
    }
  } finally {
    isLoading.value = false;
  }
};

/* -------------------------------------------------- */
/* Close                                              */
/* -------------------------------------------------- */

const close = () => {
  if (isLoading.value) {
    return;
  }

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
      class="flex max-h-[92vh] w-full max-w-6xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
    >
      <!-- Header -->
      <div
        class="flex items-center justify-between border-b border-slate-200 px-6 py-4"
      >
        <div>
          <h2 class="text-base font-semibold text-slate-800">Add New Alumni</h2>

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

      <!-- Error -->
      <div
        v-if="message"
        class="mx-6 mt-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-600"
      >
        {{ message }}
      </div>

      <!-- Form -->
      <form class="flex min-h-0 flex-1 flex-col" @submit.prevent="submit">
        <!-- Scroll Area -->
        <div class="flex-1 overflow-y-auto px-6 py-5">
          <!-- ================================================= -->
          <!-- PERSONAL INFORMATION                              -->
          <!-- ================================================= -->

          <div
            class="mb-6 rounded-xl border border-slate-200 bg-slate-50/60 p-4"
          >
            <div class="mb-4">
              <h3 class="text-sm font-semibold text-slate-800">
                Personal Information
              </h3>

              <p class="mt-1 text-xs text-slate-500">
                Basic personal information of the alumni.
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
                  :class="{
                    'form-input-error': errors.name_khmer,
                  }"
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
                  :class="{
                    'form-input-error': errors.name_english,
                  }"
                />

                <p v-if="errors.name_english" class="form-error">
                  {{ errors.name_english }}
                </p>
              </div>

              <!-- Email -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Email
                  <span class="font-normal text-slate-400">
                    (email or phone)
                  </span>
                </label>

                <input
                  v-model="formData.email"
                  type="email"
                  placeholder="Enter email address"
                  class="form-input"
                  :class="{
                    'form-input-error': errors.email,
                  }"
                />

                <p v-if="errors.email" class="form-error">
                  {{ errors.email }}
                </p>
              </div>

              <!-- Phone -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Mobile Phone
                  <span class="font-normal text-slate-400">
                    (email or phone)
                  </span>
                </label>

                <input
                  v-model="formData.phone"
                  type="tel"
                  placeholder="Enter mobile phone"
                  class="form-input"
                  :class="{
                    'form-input-error': errors.phone,
                  }"
                />

                <p v-if="errors.phone" class="form-error">
                  {{ errors.phone }}
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
                  :class="{
                    'form-input-error': errors.gender,
                  }"
                >
                  <option value="" disabled>Select gender</option>

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
                  <span class="font-normal text-slate-400"> (optional) </span>
                </label>

                <input
                  v-model="formData.dateOfBirth"
                  type="date"
                  class="form-input"
                  :class="{
                    'form-input-error': errors.dateOfBirth,
                  }"
                />

                <p v-if="errors.dateOfBirth" class="form-error">
                  {{ errors.dateOfBirth }}
                </p>
              </div>

              <!-- Address -->
              <div class="md:col-span-2 lg:col-span-3">
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Address
                  <span class="font-normal text-slate-400"> (optional) </span>
                </label>

                <input
                  v-model="formData.address"
                  type="text"
                  placeholder="Enter current address"
                  class="form-input"
                  :class="{
                    'form-input-error': errors.address,
                  }"
                />

                <p v-if="errors.address" class="form-error">
                  {{ errors.address }}
                </p>
              </div>
            </div>
          </div>

          <!-- ================================================= -->
          <!-- ACADEMIC INFORMATION                              -->
          <!-- ================================================= -->

          <div class="mb-6 rounded-xl border border-slate-200 bg-white p-4">
            <div class="mb-4">
              <h3 class="text-sm font-semibold text-slate-800">
                Academic Information
              </h3>

              <p class="mt-1 text-xs text-slate-500">
                Academic background of the alumni.
              </p>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
              <!-- Major -->
              <div class="relative">
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Major
                  <span class="text-red-500">*</span>
                </label>

                <OptionItemsArray
                  :items="majors.data?.data || []"
                  :active="majorActive ?? ''"
                  title="All Majors"
                  @update:active="handleMajor"
                  zIndex="z-100"
                />

                <p v-if="errors.major" class="form-error">
                  {{ errors.major }}
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
                  :class="{
                    'form-input-error': errors.year,
                  }"
                />

                <p v-if="errors.year" class="form-error">
                  {{ errors.year }}
                </p>
              </div>

              <!-- GPA -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  GPA
                  <span class="font-normal text-slate-400"> (optional) </span>
                </label>

                <input
                  v-model="formData.gpa"
                  type="number"
                  min="0"
                  max="4"
                  step="0.01"
                  placeholder="e.g. 3.50"
                  class="form-input"
                  :class="{
                    'form-input-error': errors.gpa,
                  }"
                />

                <p v-if="errors.gpa" class="form-error">
                  {{ errors.gpa }}
                </p>
              </div>

              <!-- Bio -->
              <div class="md:col-span-2 lg:col-span-3">
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Bio
                  <span class="font-normal text-slate-400"> (optional) </span>
                </label>

                <textarea
                  v-model="formData.bio"
                  rows="3"
                  placeholder="Write a short biography..."
                  class="form-input resize-none"
                ></textarea>
              </div>
            </div>
          </div>

          <!-- ================================================= -->
          <!-- CAREER INFORMATION                                -->
          <!-- ================================================= -->

          <div class="mb-6 rounded-xl border border-slate-200 bg-white p-4">
            <div class="mb-4">
              <h3 class="text-sm font-semibold text-slate-800">
                Career Information
              </h3>

              <p class="mt-1 text-xs text-slate-500">
                Select the current employment status. Employment details can be
                completed later by the alumni.
              </p>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
              <!-- Work Status -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Work Status
                  <span class="text-red-500">*</span>
                </label>

                <select
                  v-model="formData.status"
                  class="form-input"
                  :class="{
                    'form-input-error': errors.status,
                  }"
                >
                  <option value="" disabled>Select work status</option>

                  <option
                    v-for="item in workStatus"
                    :key="item.status"
                    :value="item.status"
                  >
                    {{ item.title }}
                  </option>
                </select>

                <p v-if="errors.status" class="form-error">
                  {{ errors.status }}
                </p>
              </div>
            </div>
          </div>

          <!-- ================================================= -->
          <!-- EMPLOYMENT INFORMATION                            -->
          <!-- ================================================= -->

          <div
            v-if="hasEmployment"
            class="mb-6 rounded-xl border border-blue-100 bg-blue-50/40 p-4"
          >
            <div class="mb-4">
              <div class="flex items-start justify-between gap-4">
                <div>
                  <h3 class="text-sm font-semibold text-slate-800">
                    Employment Information
                  </h3>

                  <p class="mt-1 text-xs text-slate-500">
                    Optional. Alumni can complete this information later from
                    their profile.
                  </p>
                </div>

                <span
                  class="rounded-full bg-blue-100 px-2.5 py-1 text-[10px] font-medium text-blue-600"
                >
                  Optional
                </span>
              </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
              <!-- Company -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Company 
                  <span class="font-normal text-slate-400"> (optional) </span>
                </label>

                <input
                  v-model="formData.company_name"
                  type="text"
                  min="1"
                  placeholder="Optional"
                  class="form-input"
                  :class="{
                    'form-input-error': errors.company_name,
                  }"
                />

                <p class="mt-1 text-[10px] text-slate-400">
                  Leave empty if the company is not known.
                </p>

                <p v-if="errors.company_name" class="form-error">
                  {{ errors.company_name }}
                </p>
              </div>

              <!-- Job Title -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Job Title
                  <span class="font-normal text-slate-400"> (optional) </span>
                </label>

                <input
                  v-model="formData.job_title"
                  type="text"
                  placeholder="e.g. Software Developer"
                  class="form-input"
                />
              </div>

              <!-- Employment Type -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Employment Type
                  <span class="font-normal text-slate-400"> (optional) </span>
                </label>

                <select v-model="formData.employment_type" class="form-input">
                  <option value="" disabled>Select employment type</option>

                  <option
                    v-for="item in employmentTypes"
                    :key="item.value"
                    :value="item.value"
                  >
                    {{ item.title }}
                  </option>
                </select>
              </div>

              <!-- Industry -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Industry
                  <span class="font-normal text-slate-400"> (optional) </span>
                </label>

                <input
                  v-model="formData.industry"
                  type="text"
                  placeholder="e.g. Information Technology"
                  class="form-input"
                />
              </div>

              <!-- Location -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Work Location
                  <span class="font-normal text-slate-400"> (optional) </span>
                </label>

                <input
                  v-model="formData.work_address"
                  type="text"
                  placeholder="Enter workplace location"
                  class="form-input"
                />
              </div>

              <!-- Salary -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Salary Range
                  <span class="font-normal text-slate-400"> (optional) </span>
                </label>

                <input
                  v-model="formData.salary_range"
                  type="text"
                  placeholder="e.g. $500 - $800"
                  class="form-input"
                />
              </div>

              <!-- Start Date -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Start Date
                  <span class="font-normal text-slate-400"> (optional) </span>
                </label>

                <input
                  v-model="formData.start_date"
                  type="date"
                  class="form-input"
                />
              </div>

              <!-- End Date -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  End Date
                  <span class="font-normal text-slate-400"> (optional) </span>
                </label>

                <input
                  v-model="formData.end_date"
                  type="date"
                  class="form-input"
                  :disabled="formData.is_current"
                />
              </div>

              <!-- Current Job -->
              <div class="flex items-center pt-6">
                <label class="flex cursor-pointer items-center gap-3">
                  <input
                    v-model="formData.is_current"
                    type="checkbox"
                    class="size-4 rounded border-slate-300 text-primary focus:ring-primary"
                  />

                  <span class="text-sm font-medium text-slate-600">
                    Currently working here
                  </span>
                </label>
              </div>
            </div>
          </div>

          <!-- ================================================= -->
          <!-- ACCOUNT & IMAGE                                   -->
          <!-- ================================================= -->

          <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
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
                  <label class="mb-1 block text-xs font-medium text-slate-600">
                    Password
                    <span class="text-red-500">*</span>
                  </label>

                  <div class="relative">
                    <input
                      v-model="formData.password"
                      :type="isPassword ? 'text' : 'password'"
                      placeholder="Minimum 8 characters"
                      class="form-input pr-11"
                      :class="{
                        'form-input-error': errors.password,
                      }"
                    />

                    <button
                      type="button"
                      class="absolute right-0 top-0 flex h-full w-10 items-center justify-center text-slate-400 transition hover:text-primary"
                      @click="togglePassword"
                    >
                      <component :is="isPassword ? EyeIcon : OffEyeIcon" />
                    </button>
                  </div>

                  <p v-if="errors.password" class="form-error">
                    {{ errors.password }}
                  </p>
                </div>

                <!-- Confirm Password -->
                <div>
                  <label class="mb-1 block text-xs font-medium text-slate-600">
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
                      <component :is="isPassword ? EyeIcon : OffEyeIcon" />
                    </button>
                  </div>

                  <p v-if="errors.password_confirmation" class="form-error">
                    {{ errors.password_confirmation }}
                  </p>
                </div>
              </div>

              <!-- Image -->
              <div>
                <label class="mb-1 block text-xs font-medium text-slate-600">
                  Profile Image
                  <span class="font-normal text-slate-400"> (optional) </span>
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

        <!-- ================================================= -->
        <!-- FOOTER                                            -->
        <!-- ================================================= -->

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
            <template v-if="!isLoading"> Save Alumni </template>

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

.form-input:disabled {
  cursor: not-allowed;
  background: rgb(241 245 249);
  color: rgb(148 163 184);
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
