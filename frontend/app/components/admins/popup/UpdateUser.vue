<script setup lang="ts">
import CloseIcon from "~/components/icons/CloseIcon.vue";
import SpannerIcon from "~/components/icons/SpannerIcon.vue";
import type { UserModel } from "~/types/user.model";

/* -------------------------------------------------- */
const userStore = useUserStore();

const prop = defineProps<{ data: UserModel }>();
/* -------------------------------------------------- */
/* Options                                              */
/* -------------------------------------------------- */
const genders = reactive(["male", "female"]);

const majors = useMajorStore();

/* -------------------------------------------------- */
/* Form State                                           */
/* -------------------------------------------------- */
type FormData = {
  name_khmer: string | null;
  name_english: string | null;
  gender: string | null;
  dateOfBirth: string | null;
  phone: string | null;
  major: string | null;
  year: string | null;
  status: string | null;
  address: string | null;
  work_address: string | null;
};

type FormErrors = Partial<Record<keyof FormData, string>>;

const formData = ref<FormData>({
  name_khmer: prop.data.name_khmer || null,
  name_english: prop.data.name_english || null,
  gender: prop.data.gender || null,
  dateOfBirth: prop.data.user_infos_one?.date_of_birth || null,
  phone: prop.data.mobile || null,
  major: String(prop.data.user_infos_one?.major_id) || null,
  year: prop.data.user_infos_one?.last_year || null,
  status: prop.data.user_infos_one?.work || null,
  address: prop.data.user_infos_one?.address || null,
  work_address: prop.data.user_infos_one?.work_address || null,
});

const filterMajorId = computed(() => {
  return majors.data?.data.find(
    (e) => e.id == prop.data.user_infos_one?.major_id,
  );
});
const errors = ref<FormErrors>({});

/* -------------------------------------------------- */
/* Validation                                           */
/* -------------------------------------------------- */
const requiredFields: { key: keyof FormErrors; label: string }[] = [
  { key: "name_khmer", label: "User name is required!" },
  { key: "name_english", label: "User name is required!" },
  { key: "gender", label: "Gender is required!" },
  { key: "dateOfBirth", label: "Date of birth is required!" },
  { key: "phone", label: "Mobile phone is required!" },
  { key: "major", label: "Major is required!" },
  { key: "year", label: "Last year is required!" },
  { key: "status", label: "Status work is required!" },
  { key: "address", label: "Address work is required!" },
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

const majorActive = ref(null);
const handleMajor = (data: any) => {
  formData.value.major = data.id;
  majorActive.value = data.title;
};

const isLoading = ref(false);
const message = ref("");
const submit = async () => {
  if (validate()) return;

  // emit("submitted", formData.value);

  const data = new FormData();

  data.append("name_khmer", String(formData.value.name_khmer));
  data.append("name_english", String(formData.value.name_english));
  data.append("gender", String(formData.value.gender));
  data.append("date_of_birth", String(formData.value.dateOfBirth));
  data.append("major_id", String(formData.value.major));
  data.append("address", String(formData.value.address));
  data.append("work", String(formData.value.status));
  data.append("last_year", String(formData.value.year));
  data.append("mobile", String(formData.value.phone));
  data.append("work_address", String(formData.value.work_address));
  // data.append("_method", "PUT");
  if (prop.data.id) {
    isLoading.value = true;
    try {
      const res = await userStore.updateUser(data, prop.data.id);

      if (userStore.data?.data.data) {
        userStore.data.data.data = userStore.data?.data.data.map((e) =>
          e.id === res.data.data.id ? res.data.data : e,
        );
      }
      emit("close");
    } catch (e: any) {
      // console.log(e);
      message.value = e.response?.data;
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
      <!-- Header -->
      <div class="w-full flex justify-between items-center px-3 py-3">
        <p class="font-Inter text-slate-500">Update Alumni</p>
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
            <span v-if="message" class="px-4 text-xs text-red-500">{{
              message
            }}</span>
            <div
              class="w-full grid grid-cols-3 gap-3 max-md:grid-cols-1 max-lg:grid-cols-2"
            >
              <!-- User Khmer / Gender -->
              <div class="w-full">
                <label class="text-xs text-slate-500">
                  Name Khmer <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  v-model="formData.name_khmer"
                  class="w-full px-3 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  placeholder="Enter name khmer"
                  :class="
                    errors.name_khmer ? 'border-red-500' : 'border-slate-300'
                  "
                />
                <span class="text-xs text-red-500">{{
                  errors.name_khmer
                }}</span>
              </div>

              <div class="w-full">
                <label class="text-xs text-slate-500">
                  Name English <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  v-model="formData.name_english"
                  class="w-full px-3 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  placeholder="Enter name english"
                  :class="
                    errors.name_english ? 'border-red-500' : 'border-slate-300'
                  "
                />
                <span class="text-xs text-red-500">{{
                  errors.name_english
                }}</span>
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
                  :items="majors.data?.data ?? []"
                  :title="'Please Select Major'"
                  :active="majorActive ?? filterMajorId?.title ?? undefined"
                  :z-index="'z-50'"
                  @update:active="handleMajor($event)"
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
                  Status Work {{ formData.status
                  }}<span class="text-red-500">*</span>
                </label>
                <select
                  v-model="formData.status"
                  class="w-full px-3 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  :class="errors.status ? 'border-red-500' : 'border-slate-300'"
                >
                  <option value="" disabled selected>សូមជ្រើសរើស</option>
                  <option
                    v-for="(item, index) in workStatus"
                    :key="index"
                    :value="item.status"
                  >
                    {{ item.title }}
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

              <div class="w-full">
                <label class="text-xs text-slate-500"> Work Address </label>
                <input
                  type="text"
                  v-model="formData.work_address"
                  class="w-full px-3 py-2 border placeholder:text-sm placeholder:text-slate-400 rounded-md focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                  placeholder="Enter address"
                  :class="
                    errors.work_address ? 'border-red-500' : 'border-slate-300'
                  "
                />
                <span class="text-xs text-red-500">{{
                  errors.work_address
                }}</span>
              </div>
            </div>
          </div>

          <div class="px-4 pb-4 flex items-center justify-end gap-6 mt-4">
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
                <span>Please wating</span>
                <SpannerIcon />
              </div>
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
