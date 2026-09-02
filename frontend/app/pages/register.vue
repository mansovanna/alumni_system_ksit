<script setup>
import EyeIcon from "~/components/icons/EyeIcon.vue";
import LoadingIcon from "~/components/icons/LoadingIcon.vue";
import LockIcon from "~/components/icons/LockIcon.vue";
import OffEyeIcon from "~/components/icons/OffEyeIcon.vue";
import UserIcon from "~/components/icons/UserIcon.vue";

const authStore = useAuthStore();
const majorStore = useMajorStore();

definePageMeta({
  layout: false,
});

const isErrorMessage = ref("");
const isLoading = ref(false);
const router = useRouter();

const formData = reactive({
  fullNameKh: "",
  fullNameEn: "",
  email: "", // email or phone number
  graduationYear: "",
  majorId: "",
  password: "",
  passwordConfirmation: "",
});

const errors = reactive({
  fullNameKh: "",
  fullNameEn: "",
  email: "",
  graduationYear: "",
  majorId: "",
  password: "",
  passwordConfirmation: "",
});

const isPasswordVisible = ref(false);
const isConfirmPasswordVisible = ref(false);

// TODO: replace with real fetch from your majors API/store, e.g.
// const { data: majors } = await useFetch('/api/majors')

const currentYear = new Date().getFullYear();

const togglePasswordVisibility = () => {
  isPasswordVisible.value = !isPasswordVisible.value;
};
const toggleConfirmPasswordVisibility = () => {
  isConfirmPasswordVisible.value = !isConfirmPasswordVisible.value;
};

const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const phoneRegex = /^(0|\+855)[0-9]{7,10}$/;

const validateForm = () => {
  let isValid = true;

  if (!formData.fullNameKh.trim()) {
    errors.fullNameKh = "This field is required";
    isValid = false;
  } else {
    errors.fullNameKh = "";
  }

  if (!formData.fullNameEn.trim()) {
    errors.fullNameEn = "This field is required";
    isValid = false;
  } else {
    errors.fullNameEn = "";
  }

  if (!formData.email.trim()) {
    errors.email = "Email or phone number is required";
    isValid = false;
  } else if (
    !emailRegex.test(formData.email) &&
    !phoneRegex.test(formData.email)
  ) {
    errors.email = "Enter a valid email or phone number";
    isValid = false;
  } else {
    errors.email = "";
  }

  if (
    formData.graduationYear &&
    (formData.graduationYear < 1980 || formData.graduationYear > currentYear)
  ) {
    errors.graduationYear = `Enter a year between 1980 and ${currentYear}`;
    isValid = false;
  } else {
    errors.graduationYear = "";
  }

  errors.majorId = "";

  if (!formData.password) {
    errors.password = "This field is required";
    isValid = false;
  } else if (formData.password.length < 8) {
    errors.password = "Password must be at least 8 characters";
    isValid = false;
  } else {
    errors.password = "";
  }

  if (!formData.passwordConfirmation) {
    errors.passwordConfirmation = "This field is required";
    isValid = false;
  } else if (formData.password !== formData.passwordConfirmation) {
    errors.passwordConfirmation = "Passwords do not match";
    isValid = false;
  } else {
    errors.passwordConfirmation = "";
  }

  return isValid;
};

const handleSubmit = async () => {
  isErrorMessage.value = "";

  if (!validateForm()) return;

  isLoading.value = true;
  try {
    const payload = {
      full_name_kh: formData.fullNameKh,
      full_name_en: formData.fullNameEn,
      contact: formData.email,
      major_id: formData.majorId || null,
      graduation_year: formData.graduationYear || null,
      password: formData.password,
      password_confirmation: formData.passwordConfirmation,
    };
    const res = await authStore.register(payload);
    if (res.status === 201) {
      router.push("/login");
    }
  } catch (error) {
    isErrorMessage.value =
      error.response?.data?.message || "An error occurred during registration.";
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <div class="font-Inter w-full h-screen flex relative overflow-hidden">
    <div class="w-full h-screen flex flex-row">
      <div class="w-1/2 max-lg:w-full h-screen bg-primary relative">
        <img
          src="../assets/svg/border-white.svg"
          alt=""
          class="top-0 left-0 right-0 absolute"
        />
        <img
          src="../assets/svg/border-white.svg"
          alt=""
          class="bottom-0 left-0 right-0 absolute rotate-180"
        />

        <div
          class="w-full h-full flex flex-row justify-between items-center absolute top-0 left-0 right-0"
        >
          <img
            src="../assets/svg/center-right.svg"
            class="absolute left-2/3 max-md:left-4/5"
            alt=""
          />
          <img
            src="../assets/svg/center-left.svg"
            class="absolute right-2/3 max-md:right-4/5"
            alt=""
          />
        </div>
      </div>
      <div class="w-1/2 h-screen bg-white max-lg:hidden relative">
        <img
          src="../assets/svg/center-left-primary.svg"
          alt=""
          class="top-0 left-0 w-60 absolute"
        />

        <img
          src="../assets/svg/center-right-primary.svg"
          alt=""
          class="top-0 right-0 w-60 absolute"
        />

        <img
          src="../assets/svg/bottm-left-primary.svg"
          alt=""
          class="bottom-0 left-0 w-60 absolute"
        />

        <img
          src="../assets/svg/bottm-right-primary.svg"
          alt=""
          class="bottom-0 right-0 w-60 absolute"
        />
      </div>
    </div>

    <!--  -->
    <div
      class="fixed top-0 bottom-0 left-0 right-0 flex flex-row max-lg:flex-col max-lg:justify-center max-lg:items-center max-lg:gap-5 overflow-y-auto max-lg:p-6"
    >
      <div
        class="w-1/2 h-screen max-lg:h-auto flex flex-row text-nowrap justify-center items-center relative"
      >
        <!-- Block Logo -->
        <div class="w-full flex flex-row justify-between gap-2 items-center">
          <!-- name and logo -->
          <div class="w-full flex flex-col justify-center items-center">
            <div class="size-30">
              <img class="size-30" src="../assets/svg/logo-shcool.svg" alt="" />
            </div>
            <h1 class="text-xl text-white mt-5 font-moul">
              សមាគមន៍អតីតនិស្សិត
            </h1>
            <p class="text-white text-lg mt-1">Alumni Association</p>
          </div>
        </div>
        <!-- End name and logo -->
      </div>
      <div
        class="w-1/2 max-lg:w-full h-screen max-lg:h-auto flex justify-center items-center flex-col gap-3"
      >
        <div
          class="max-w-md w-full max-md:w-full flex flex-col justify-center items-center bg-white rounded-2xl p-5 border border-slate-200"
        >
          <h1
            class="text-md font-moul text-primary text-left w-full flex jus-st"
          >
            Register Account
          </h1>

          <!-- Server / submit error -->
          <p
            v-if="isErrorMessage"
            class="w-full text-left text-red-500 text-xs mt-2"
          >
            {{ isErrorMessage }}
          </p>

          <form
            @submit.prevent="handleSubmit"
            class="w-full flex flex-col mt-5 gap-3"
          >
            <!-- Full Name Khmer / English -->
            <div class="w-full grid grid-cols-2 gap-3">
              <div class="flex flex-col gap-1">
                <label for="fullNameKh" class="text-left text-xs text-slate-500"
                  >Full Name (Khmer)
                  <span class="text-red-500 text-xs">*</span></label
                >
                <div class="relative">
                  <input
                    id="fullNameKh"
                    type="text"
                    v-model="formData.fullNameKh"
                    class="w-full border rounded-lg p-2 pl-8 text-sm focus:outline-none focus:border-primary/50 focus:ring-2 focus:ring-primary/30"
                    :class="
                      errors.fullNameKh ? 'border-red-500' : 'border-slate-200'
                    "
                    placeholder="នាមត្រកូល និងនាមខ្លួន"
                  />
                  <div class="absolute top-2 left-2">
                    <UserIcon class="w-5 h-5 text-slate-400" />
                  </div>
                </div>
                <span class="text-red-500 text-xs">{{
                  errors.fullNameKh
                }}</span>
              </div>

              <div class="flex flex-col gap-1">
                <label for="fullNameEn" class="text-left text-xs text-slate-500"
                  >Full Name (English)
                  <span class="text-red-500 text-xs">*</span></label
                >
                <div class="relative">
                  <input
                    id="fullNameEn"
                    type="text"
                    v-model="formData.fullNameEn"
                    class="w-full border rounded-lg p-2 pl-8 text-sm focus:outline-none focus:border-primary/50 focus:ring-2 focus:ring-primary/30"
                    :class="
                      errors.fullNameEn ? 'border-red-500' : 'border-slate-200'
                    "
                    placeholder="First and Last Name"
                  />
                  <div class="absolute top-2 left-2">
                    <UserIcon class="w-5 h-5 text-slate-400" />
                  </div>
                </div>
                <span class="text-red-500 text-xs">{{
                  errors.fullNameEn
                }}</span>
              </div>
            </div>

            <!-- Email or Phone -->
            <div class="w-full flex flex-col gap-1">
              <label for="email" class="text-left text-xs text-slate-500"
                >Email or Phone Number
                <span class="text-red-500 text-xs">*</span></label
              >
              <div class="relative">
                <input
                  id="email"
                  type="text"
                  v-model="formData.email"
                  class="w-full border rounded-lg p-2 pl-8 text-sm focus:outline-none focus:border-primary/50 focus:ring-2 focus:ring-primary/30"
                  :class="errors.email ? 'border-red-500' : 'border-slate-200'"
                  placeholder="you@example.com or 0XX XXX XXX"
                />
                <div class="absolute top-2 left-2">
                  <svg
                    class="w-5 h-5 text-slate-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.5"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M2.25 6.75c0-.414.336-.75.75-.75h18a.75.75 0 01.75.75v10.5a.75.75 0 01-.75.75h-18a.75.75 0 01-.75-.75V6.75z"
                    />
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M3 7l9 6 9-6"
                    />
                  </svg>
                </div>
              </div>
              <span class="text-red-500 text-xs">{{ errors.email }}</span>
            </div>

            <!-- Graduation Year / Major -->
            <div class="w-full grid grid-cols-2 gap-3">
              <div class="flex flex-col gap-1">
                <label
                  for="graduationYear"
                  class="text-left text-xs text-slate-500"
                  >Graduation Year</label
                >
                <div class="relative">
                  <input
                    id="graduationYear"
                    type="number"
                    v-model="formData.graduationYear"
                    inputmode="numeric"
                    :min="1980"
                    :max="currentYear"
                    class="w-full border rounded-lg p-2 pl-8 text-sm focus:outline-none focus:border-primary/50 focus:ring-2 focus:ring-primary/30"
                    :class="
                      errors.graduationYear
                        ? 'border-red-500'
                        : 'border-slate-200'
                    "
                    placeholder="e.g. 2024"
                  />
                  <div class="absolute top-2 left-2">
                    <svg
                      class="w-5 h-5 text-slate-400"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      stroke-width="1.5"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0V11.25a2.25 2.25 0 012.25-2.25h13.5a2.25 2.25 0 012.25 2.25v7.5"
                      />
                    </svg>
                  </div>
                </div>
                <span class="text-red-500 text-xs">{{
                  errors.graduationYear
                }}</span>
              </div>

              <div class="flex flex-col gap-1">
                <label for="majorId" class="text-left text-xs text-slate-500"
                  >Major</label
                >
                <div class="relative">
                  <select
                    id="majorId"
                    v-model="formData.majorId"
                    class="w-full border rounded-lg p-2 pl-8 text-sm appearance-none bg-white border-slate-200 focus:outline-none focus:border-primary/50 focus:ring-2 focus:ring-primary/30"
                  >
                    <option value="" disabled selected>Select Major</option>
                    <option
                      v-for="major in majorStore.data?.data ?? []"
                      :key="major.id"
                      :value="major.id"
                    >
                      {{ major.name }}
                    </option>
                  </select>
                  <div class="absolute top-2 left-2">
                    <LoadingIcon
                      v-if="majorStore.isLoading"
                      class="w-5 h-5 text-slate-400 animate-spin"
                    />
                    <svg
                      v-else
                      class="w-5 h-5 text-slate-400"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      stroke-width="1.5"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 14l9-5-9-5-9 5 9 5z"
                      />
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 14l6.16-3.42A12.083 12.083 0 0121 15.5V18M12 14L5.84 10.58A12.083 12.083 0 003 15.5V18m9-4v7"
                      />
                    </svg>
                  </div>
                </div>
              </div>
            </div>

            <!-- Password -->
            <div class="w-full flex flex-col gap-1">
              <label for="password" class="text-left text-xs text-slate-500"
                >Password <span class="text-red-500 text-xs">*</span></label
              >
              <div class="relative">
                <input
                  id="password"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  autocomplete="new-password"
                  class="w-full border rounded-lg p-2 pl-8 text-sm focus:outline-none focus:border-primary/50 focus:ring-2 focus:ring-primary/30"
                  v-model="formData.password"
                  :class="
                    errors.password ? 'border-red-500' : 'border-slate-200'
                  "
                  placeholder="*******"
                />
                <div class="absolute top-2 left-2">
                  <LockIcon class="w-5 h-5 text-slate-400" />
                </div>
                <button
                  type="button"
                  @click="togglePasswordVisibility"
                  class="absolute top-2 right-2 cursor-pointer text-slate-400 hover:text-primary"
                >
                  <EyeIcon class="w-5 h-5" v-if="!isPasswordVisible" />
                  <OffEyeIcon
                    class="w-5 h-5 text-red-500"
                    v-if="isPasswordVisible"
                  />
                </button>
              </div>
              <span class="text-red-500 text-xs">{{ errors.password }}</span>
            </div>

            <!-- Confirm Password -->
            <div class="w-full flex flex-col gap-1">
              <label
                for="passwordConfirmation"
                class="text-left text-xs text-slate-500"
                >Confirm Password
                <span class="text-red-500 text-xs">*</span></label
              >
              <div class="relative">
                <input
                  id="passwordConfirmation"
                  :type="isConfirmPasswordVisible ? 'text' : 'password'"
                  autocomplete="new-password"
                  class="w-full border rounded-lg p-2 pl-8 text-sm focus:outline-none focus:border-primary/50 focus:ring-2 focus:ring-primary/30"
                  v-model="formData.passwordConfirmation"
                  :class="
                    errors.passwordConfirmation
                      ? 'border-red-500'
                      : 'border-slate-200'
                  "
                  placeholder="*******"
                />
                <div class="absolute top-2 left-2">
                  <LockIcon class="w-5 h-5 text-slate-400" />
                </div>
                <button
                  type="button"
                  @click="toggleConfirmPasswordVisibility"
                  class="absolute top-2 right-2 cursor-pointer text-slate-400 hover:text-primary"
                >
                  <EyeIcon class="w-5 h-5" v-if="!isConfirmPasswordVisible" />
                  <OffEyeIcon
                    class="w-5 h-5 text-red-500"
                    v-if="isConfirmPasswordVisible"
                  />
                </button>
              </div>
              <span class="text-red-500 text-xs">{{
                errors.passwordConfirmation
              }}</span>
            </div>

            <button
              type="submit"
              :disabled="isLoading"
              class="w-full text-sm bg-primary text-white rounded-lg p-3 cursor-pointer mt-2 hover:bg-primary/80 transition-colors disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2 font-semibold"
            >
              <LoadingIcon v-if="isLoading" class="w-4 h-4 animate-spin" />
              <span>{{ isLoading ? "Registering..." : "Register Now" }}</span>
            </button>
          </form>
          <hr class="w-full border-slate-200 my-4" />
          <!-- ask user about account -->
          <div class="w-full flex flex-col gap-1 text-center">
            <p class="text-xs text-slate-500">
              Do you have an account ready?
              <a href="/login" class="text-primary hover:underline">Login</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
