<script setup lang="ts">
import CloseIcon from "~/components/icons/CloseIcon.vue";
import ImageIcon from "~/components/icons/ImageIcon.vue";

/* -------------------------------------------------- */
/* Options                                              */
/* -------------------------------------------------- */
const genders = reactive(["male", "female"]);

const works = reactive([
  "មានការងារធ្វើ",
  "កំពុងស្វែងរកការងារធ្វើ",
  "សិក្សាបន្ដ",
  "មិនទាន់បានការងារធ្វើ",
]);

const majors = reactive([
  "computer of technology",
  "food of technology",
  "crope sciences",
  "animal sciences",
  "electrical of technology",
  "9+3",
]);

const MAX_IMAGE_SIZE = 5 * 1024 * 1024; // 5MB

/* -------------------------------------------------- */
/* Form State                                           */
/* -------------------------------------------------- */
type FormData = {
  userName: string | null;
  gender: string | null;
  dateOfBirth: string | null;
  phone: string | null;
  major: string | null;
  year: string | null;
  status: string | null;
  address: string | null;
  image: File | null;
};

type FormErrors = Partial<Record<keyof FormData, string>>;

const formData = ref<FormData>({
  userName: null,
  gender: null,
  dateOfBirth: null,
  phone: null,
  major: null,
  year: null,
  status: null,
  address: null,
  image: null,
});

const errors = ref<FormErrors>({});
const imagePreview = ref<string | null>(null);

/* -------------------------------------------------- */
/* Image Upload                                         */
/* -------------------------------------------------- */
const handleImageChange = (event: Event) => {
  const file = (event.target as HTMLInputElement).files?.[0];
  if (!file) return;

  if (file.size > MAX_IMAGE_SIZE) {
    errors.value.image = "រូបភាពមិនអាចធំជាង 5MB បានទេ!";
    (event.target as HTMLInputElement).value = "";
    return;
  }

  // Revoke the previous object URL to avoid a memory leak
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value);
  }

  errors.value.image = undefined;
  formData.value.image = file;
  imagePreview.value = URL.createObjectURL(file);
};

onBeforeUnmount(() => {
  if (imagePreview.value) {
    URL.revokeObjectURL(imagePreview.value);
  }
});

/* -------------------------------------------------- */
/* Validation                                           */
/* -------------------------------------------------- */
const requiredFields: { key: keyof FormErrors; label: string }[] = [
  { key: "userName", label: "User name is required!" },
  { key: "gender", label: "Gender is required!" },
  { key: "dateOfBirth", label: "Date of birth is required!" },
  { key: "phone", label: "Mobile phone is required!" },
  { key: "major", label: "Major is required!" },
  { key: "year", label: "Last year is required!" },
  { key: "status", label: "Status work is required!" },
  { key: "address", label: "Address work is required!" },
  { key: "image", label: "Image is required!" },
];

const validate = () => {
  errors.value = {};
  let hasError = false;

  for (const field of requiredFields) {
    const value = formData.value[field.key];
    if (!value || String(value).trim().length === 0) {
      errors.value[field.key] = field.label;
      hasError = true;
    }
  }

  return hasError;
};

/* -------------------------------------------------- */
/* Submit                                               */
/* -------------------------------------------------- */
const emit = defineEmits(["close", "submitted"]);

const submit = () => {
  if (validate()) return;

  // TODO: ភ្ជាប់ជាមួយ API (FormData + image upload)
  emit("submitted", formData.value);
};
</script>

<template>
  <div
    @click="$emit('close')"
    class="w-full justify-center items-center fixed top-0 bottom-0 right-0 left-0 bg-black/20 z-50 backdrop-blur-xs flex p-4"
  >
    <div
      class="w-1/3 max-md:w-full max-lg:w-2/3 bg-white rounded-2xl border border-slate-400"
      @click.stop
    >
      <!-- Header -->
      <div class="w-full flex justify-between items-center px-3 py-3">
        <p class="font-Inter text-slate-500">Add New Alumni</p>
        <button
          @click="$emit('close')"
          class="flex justify-center items-center rounded-md hover:text-red-500 cursor-pointer"
        >
          <CloseIcon />
        </button>
      </div>
      <hr class="text-slate-100" />

      <form @submit.prevent="submit">
        <div class="w-full flex flex-col gap-2">
          <div
            class="w-full px-4 py-2 flex flex-col gap-2 flex-1 h-auto max-h-[70vh] overflow-y-auto"
          >
            <div class="w-full grid grid-cols-2 gap-4 max-md:grid-cols-1">
              <!-- User Name / Gender -->
              <div class="w-full">
                <label class="text-xs text-slate-500">
                  User Name <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  v-model="formData.userName"
                  class="w-full px-3 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  placeholder="Enter user name"
                  :class="
                    errors.userName ? 'border-red-500' : 'border-slate-300'
                  "
                />
                <span class="text-xs text-red-500">{{ errors.userName }}</span>
              </div>

              <div class="w-full">
                <label class="text-xs text-slate-500">
                  Gender <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="formData.gender"
                  class="w-full px-3 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  :class="errors.gender ? 'border-red-500' : 'border-slate-300'"
                >
                  <option value="" disabled selected>សូមជ្រើសរើស</option>
                  <option v-for="g in genders" :key="g" :value="g">
                    {{ g.charAt(0).toUpperCase() + g.slice(1) }}
                  </option>
                </select>
                <span class="text-xs text-red-500">{{ errors.gender }}</span>
              </div>

              <!-- Date of Birth / Phone -->
              <div class="w-full">
                <label class="text-xs text-slate-500">
                  Date of Birth <span class="text-red-500">*</span>
                </label>
                <input
                  type="date"
                  v-model="formData.dateOfBirth"
                  class="w-full px-3 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  :class="
                    errors.dateOfBirth ? 'border-red-500' : 'border-slate-300'
                  "
                />
                <span class="text-xs text-red-500">{{
                  errors.dateOfBirth
                }}</span>
              </div>

              <div class="w-full">
                <label class="text-xs text-slate-500">
                  Mobile Phone <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  v-model="formData.phone"
                  class="w-full px-3 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  placeholder="000 - 000 - 0000"
                  :class="errors.phone ? 'border-red-500' : 'border-slate-300'"
                />
                <span class="text-xs text-red-500">{{ errors.phone }}</span>
              </div>

              <!-- Major / Last Year -->
              <div class="w-full">
                <label class="text-xs text-slate-500">
                  Major <span class="text-red-500">*</span>
                </label>
                <OptionsOption
                  class="z-50"
                  :items="majors"
                  :title="'Please Select Major'"
                  :active="formData.major ?? undefined"
                  :z-index="'z-50'"
                  @update:active="formData.major = $event"
                />
                <span class="text-xs text-red-500">{{ errors.major }}</span>
              </div>

              <div class="w-full">
                <label class="text-xs text-slate-500">
                  Last Year <span class="text-red-500">*</span>
                </label>
                <input
                  type="number"
                  v-model="formData.year"
                  class="w-full px-3 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  placeholder="Year"
                  :class="errors.year ? 'border-red-500' : 'border-slate-300'"
                />
                <span class="text-xs text-red-500">{{ errors.year }}</span>
              </div>

              <div class="w-full">
                <label class="text-xs text-slate-500">
                  Status Work <span class="text-red-500">*</span>
                </label>
                <select
                  v-model="formData.status"
                  class="w-full px-3 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  :class="errors.status ? 'border-red-500' : 'border-slate-300'"
                >
                  <option value="" disabled selected>សូមជ្រើសរើស</option>
                  <option v-for="w in works" :key="w" :value="w">
                    {{ w }}
                  </option>
                </select>
                <span class="text-xs text-red-500">{{ errors.status }}</span>
              </div>

              <div class="w-full">
                <label class="text-xs text-slate-500">
                  Address Work <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  v-model="formData.address"
                  class="w-full px-3 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  placeholder="Enter address"
                  :class="
                    errors.address ? 'border-red-500' : 'border-slate-300'
                  "
                />
                <span class="text-xs text-red-500">{{ errors.address }}</span>
              </div>
            </div>
            <!-- Image Upload -->
            <div class="w-full">
              <div
                class="flex justify-start items-center text-xs text-slate-500"
              >
                <p>Image</p>
                <span class="text-red-500">*</span>
              </div>
              <label for="file">
                <div
                  class="w-full relative h-20 border border-dashed border-info hover:bg-primary/5 cursor-pointer rounded-lg flex justify-center items-center overflow-hidden"
                  :class="errors.image ? 'border-red-500' : 'border-info'"
                >
                  <input
                    id="file"
                    type="file"
                    accept="image/*"
                    hidden
                    @change="handleImageChange"
                  />

                  <img
                    v-if="imagePreview"
                    :src="imagePreview"
                    alt="Preview"
                    class="h-full object-contain"
                  />
                  <div
                    v-else
                    class="text-xs text-slate-500 flex flex-col justify-center items-center"
                  >
                    <ImageIcon class="size-10 text-slate-400" />
                    <p>Max size 5MB</p>
                  </div>
                </div>
              </label>
              <span class="text-xs text-red-500">{{ errors.image }}</span>
            </div>
          </div>

          <div class="px-4 pb-2">
            <button
              type="submit"
              class="w-full bg-primary hover:bg-primary/80 py-2 rounded-md text-white text-sm my-2"
            >
              <span>Save</span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<style>
.dropdown-enter-active,
.dropdown-leave-active {
  transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

.fade-text-enter-active,
.fade-text-leave-active {
  transition: all 0.2s ease;
}

.fade-text-enter-from,
.fade-text-leave-to {
  opacity: 0;
  transform: translateY(6px);
}
</style>
