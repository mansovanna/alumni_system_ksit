<script setup>
import EyeIcon from "~/components/icons/EyeIcon.vue";
import LockIcon from "~/components/icons/LockIcon.vue";
import OffEyeIcon from "~/components/icons/OffEyeIcon.vue";
import UserIcon from "~/components/icons/UserIcon.vue";

definePageMeta({
  layout: false,
});

const route = useRouter();

const formData = reactive({
  username: "",
  password: "",
  passwordConfirmation: "",
});

const errors = reactive({
  username: "",
  password: "",
  passwordConfirmation: "",
});

const isPasswordVisible = ref(false);

const togglePasswordVisibility = () => {
  isPasswordVisible.value = !isPasswordVisible.value;
  //   const passwordInput = document.querySelector('input[type="password"]');
  //   if (passwordInput) {
  //     passwordInput.type =
  //       passwordInput.type === "password" ? "text" : "password";
  //   }
};
const validateForm = () => {
  let isValid = true;

  if (!formData.username) {
    errors.username = "This field is required username";
    isValid = false;
  } else {
    errors.username = "";
  }

  if (!formData.password) {
    errors.password = "This field is required password";
    isValid = false;
  } else {
    errors.password = "";
  }

  if (!formData.passwordConfirmation) {
    errors.passwordConfirmation =
      "This field is required password confirmation";
    isValid = false;
  } else if (formData.password !== formData.passwordConfirmation) {
    errors.passwordConfirmation = "Passwords do not match";
    isValid = false;
  } else {
    errors.passwordConfirmation = "";
  }

  return isValid;
};

const handleSubmit = () => {
  if (validateForm()) {
    // Handle form submission logic here
    console.log("Form submitted:", formData);
    route.push("/login");
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
              វិទ្យាស្ថានបច្ចេកវិទ្យាកំពង់ស្ពឺ
            </h1>
            <p class="text-white text-xs mt-1">
              KAMPONT SPUE INSTITUTES OF TECHNOLOGY
            </p>
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
          <h1 class="text-md font-moul text-primary text-center">
            ប្រព័ន្ទគ្រប់គ្រងអតីតនិស្សិត
          </h1>
          <p class="text-center text-xs text-primary font-Inter mt-1">
            Alumni Management System
          </p>

          <form
            @submit.prevent="handleSubmit"
            class="w-full flex flex-col mt-4"
          >
            <div class="w-full flex flex-col gap-1 text-left">
              <!-- Block User name -->
              <div class="w-full flex flex-col gap-1">
                <label class="text-left text-xs text-slate-500"
                  >User Name <span class="text-red-500 text-xs">*</span></label
                >
                <div class="relative">
                  <input
                    type="text"
                    v-model="formData.username"
                    class="w-full border rounded-lg p-2 pl-8 text-sm focus:outline-none focus:border-primary/50 focus:ring-2 focus:ring-primary/30"
                    :class="
                      errors.username ? 'border-red-500' : 'border-slate-200'
                    "
                    placeholder="Enter your username"
                  />
                  <div class="absolute top-2 left-2">
                    <UserIcon class="w-5 h-5 text-slate-400" />
                  </div>
                </div>
                <span class="text-red-500 text-xs">{{ errors.username }}</span>
              </div>
              <!-- End User name -->

              <!-- Block Password -->
              <div class="w-full flex flex-col gap-1">
                <label class="text-left text-xs text-slate-500"
                  >Password <span class="text-red-500 text-xs">*</span></label
                >
                <div class="relative">
                  <input
                    :type="isPasswordVisible ? 'text' : 'password'"
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

                  <div
                    class="absolute top-0 right-0 bottom-0 flex justify-center items-center"
                  >
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
                </div>
                <span class="text-red-500 text-xs">{{ errors.password }}</span>
              </div>
              <!-- End Password -->

              <!-- Block Password Confirmation -->
              <div class="w-full flex flex-col gap-1">
                <label class="text-left text-xs text-slate-500"
                  >Confirm Password
                  <span class="text-red-500 text-xs">*</span></label
                >
                <div class="relative">
                  <input
                    :type="isPasswordVisible ? 'text' : 'password'"
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

                  <div
                    class="absolute top-0 right-0 bottom-0 flex justify-center items-center"
                  >
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
                </div>
                <span class="text-red-500 text-xs">{{ errors.password }}</span>
              </div>
              <!-- End Password -->

              <button
                type="submit"
                class="w-full text-sm bg-primary text-white rounded-lg p-2 cursor-pointer mt-4 hover:bg-primary/80 transition-colors"
              >
                Register Now!
              </button>
            </div>
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
