<script setup lang="ts">
import CloseIcon from "~/components/icons/CloseIcon.vue";
const props = defineProps({
    data: {
        type: Object,
    }
})
const formData = ref<{
  nameKhmer: string | null;
  nameEnglish: string | null;
  role: string | null;
}>({
  nameKhmer: props.data?.nameKhmer?? null,
  nameEnglish: props.data?.nameEnglish?? null,
  role: props.data?.role?? null,
});


const errors = ref<{
  nameKhmer: string | null;
  nameEnglish: string | null;
  role: string | null;
}>({
  nameKhmer: null,
  nameEnglish: null,
  role: null,
});

const emit = defineEmits<{
  close: [];
  submit: [payload: typeof formData.value];
}>();

const validate = () => {
  errors.value.nameKhmer = "";
  errors.value.nameEnglish = "";
  errors.value.role = "";

  let hasError = false;

  if (!formData.value.nameKhmer || formData.value.nameKhmer.length <= 0) {
    errors.value.nameKhmer = "Name Khmer is required!";
    hasError = true;
  }

  if (!formData.value.nameEnglish || formData.value.nameEnglish.length <= 0) {
    errors.value.nameEnglish = "Name english is required!";
    hasError = true;
  }

  return hasError;
};

const submit = () => {
  if (!validate()) {
    emit("submit", formData.value);
    emit("close");
    // alert("Form submitted successfully!");
  }
};

const isShowPassword = ref(false);
</script>

<template>
  <div
    @click="$emit('close')"
    class="w-full justify-center items-center fixed top-0 bottom-0 right-0 left-0 bg-black/20 z-50 backdrop-blur-xs flex p-4"
  >
    <div class="w-1/3 bg-white rounded-2xl border border-slate-400" @click.stop>
      <div class="w-full flex justify-between items-center px-3 py-3">
        <p class="font-Inter text-slate-500">Update New Staff</p>
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
          <div class="w-full flex gap-3">
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

          <button
            type="submit"
            class="w-full bg-primary hover:bg-primary/80 py-2 rounded-md text-white text-sm my-2 mt-10"
          >
            <span>Save</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
