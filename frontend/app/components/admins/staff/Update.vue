<script setup lang="ts">
import CloseIcon from "~/components/icons/CloseIcon.vue";
import SpannerIcon from "~/components/icons/SpannerIcon.vue";
import type { UserModel } from "~/types/user.model";

// prop

const props = defineProps<{ data: UserModel }>();

const staffStore = useStaffStore();
const emit = defineEmits(["close", "submitted"]);
/* ---------- Message error ------------*/
const messageError = ref(null);

const formData = ref<{
  nameKhmer: string | null;
  nameEnglish: string | null;
  login: string | null;
  role: string | null;
}>({
  nameKhmer: props.data.name_khmer || null,
  nameEnglish: props.data.name_english || null,
  login: props.data.email || props.data.mobile || null,
  role: props.data.role || null,
});

const errors = ref<{
  nameKhmer: string | null;
  nameEnglish: string | null;
  login: string | null;
  role: string | null;
}>({
  nameKhmer: null,
  nameEnglish: null,
  login: null,
  role: null,
});

const validate = () => {
  errors.value.nameKhmer = "";
  errors.value.nameEnglish = "";
  errors.value.role = "";
  errors.value.login = "";

  let hasError = false;

  if (!formData.value.nameKhmer || formData.value.nameKhmer.length <= 0) {
    errors.value.nameKhmer = "Name Khmer is required!";
    hasError = true;
  }

  if (!formData.value.nameEnglish || formData.value.nameEnglish.length <= 0) {
    errors.value.nameEnglish = "Name english is required!";
    hasError = true;
  }

  if (!formData.value.login || formData.value.login.length <= 0) {
    errors.value.login = "Email or Mobile is required!";
    hasError = true;
  }

  return hasError;
};

const isLoading = ref(false);

const submit = async () => {
  if (!validate()) {
    const data = new FormData();

    data.append("name_khmer", String(formData.value.nameKhmer));
    data.append("name_english", String(formData.value.nameEnglish));
    data.append("login", String(formData.value.login));
    data.append("role", String(formData.value.role));

    // post data
    isLoading.value = true;

    try {
      const res = await staffStore.updateStaff(props.data.id, data);

      if (staffStore.data?.data.data) {
        staffStore.data.data.data = staffStore.data?.data.data.map((e) =>
          e.id === res.data.data.id ? res.data.data : e,
        );
      }
      emit("close");
   
    } catch (e: any) {
      messageError.value = e.response?.data?.errors;
      errors.value.login = e.response?.data?.errors?.login[0];
    } finally {
      isLoading.value = false;
    }
  }
};
</script>

<template>
  <div
    @click="$emit('close')"
    class="w-full justify-center items-center fixed top-0 bottom-0 right-0 left-0 bg-black/20 z-50 backdrop-blur-xs flex p-4"
  >
    <div
      class="w-3/5 max-md:w-full max-lg:w-8/9 max-xl:w-3/4 bg-white rounded-2xl border border-slate-400"
      @click.stop
    >
      <div class="w-full flex justify-between items-center px-3 py-3">
        <p class="font-Inter text-slate-500">Update info staff</p>
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
            <div class="w-full">
              <label class="text-xs text-slate-500"
                >Name Khmer <span class="text-red-500">*</span></label
              >
              <div class="w-full relative">
                <input
                  type="text"
                  v-model="formData.nameKhmer"
                  class="w-full px-2 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  placeholder="Enter user name khmer"
                  :class="
                    errors.nameKhmer ? 'border-red-500' : 'border-slate-300'
                  "
                />
              </div>
              <span class="text-xs text-red-500">{{ errors.nameKhmer }}</span>
            </div>

            <div class="w-full">
              <label class="text-xs text-slate-500"
                >Name English <span class="text-red-500">*</span></label
              >
              <div class="w-full relative">
                <input
                  type="text"
                  v-model="formData.nameEnglish"
                  class="w-full px-2 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  placeholder="Enter user name english"
                  :class="
                    errors.nameEnglish ? 'border-red-500' : 'border-slate-300'
                  "
                />
              </div>
              <span class="text-xs text-red-500">{{ errors.nameEnglish }}</span>
            </div>
            <!--  -->

            <div class="w-full">
              <label class="text-xs text-slate-500"
                >Email <span class="text-red-500">*</span></label
              >
              <div class="w-full relative">
                <input
                  type="text"
                  v-model="formData.login"
                  class="w-full px-2 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  placeholder="Enter user name english"
                  :class="
                    errors.nameEnglish ? 'border-red-500' : 'border-slate-300'
                  "
                />
              </div>
              <span class="text-xs text-red-500">{{ errors.login }}</span>
            </div>

            <!--  -->

            <div class="w-full">
              <label class="text-xs text-slate-500"
                >Role <span class="text-red-500">*</span></label
              >
              <div class="w-full relative">
                <select
                  v-model="formData.role"
                  class="w-full px-2 py-2 border capitalize placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  :class="errors.role ? 'border-red-500' : 'border-slate-300'"
                >
                  <option value="" disabled>សូមជ្រើសរើស</option>
                  <option value="admin">admin</option>
                  <option value="staff">staff</option>
                </select>
              </div>
              <span class="text-xs text-red-500">{{ errors.role }}</span>
            </div>

            <!-- ------------- -->

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
