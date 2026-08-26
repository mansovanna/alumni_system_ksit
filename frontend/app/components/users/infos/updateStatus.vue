<script setup lang="ts">
import CloseIcon from "~/components/icons/CloseIcon.vue";
import { useAlumnisStore } from "~/stores/alumnis";

const alumniStores = useAlumnisStore();

const authStore = useAuthStore();

const emit = defineEmits<{
  close: [];
  submitted: [data: { status: string; notes: string }];
}>();

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const isLoading = ref(false);
const messageError = ref("");

const formData = ref({
  status: authStore.user?.data.user.alumni?.employment_status || "studying",
  notes: authStore.user?.data.user.alumni?.bio || "",
});

const errors = ref({
  status: "",
  notes: "",
});

const statusOptions = [
  "studying",
  "employed",
  "unemployed",
  "self_employed",
  "unknown",
];

/*
|--------------------------------------------------------------------------
| Submit
|--------------------------------------------------------------------------
*/

const submit = async () => {
  isLoading.value = true;
  messageError.value = "";
  const data = new FormData();
  data.append("employment_status", formData.value.status);
  data.append("bio", formData.value.notes);

  try {
    const res = await alumniStores.updateStatus(
      Number(authStore.user?.data.user.alumni?.id),
      data,
    );

    if (res) {
      emit("close");
    }
  } catch (e: any) {
    messageError.value =
      e?.message || "Something went wrong. Please try again.";
  } finally {
    isLoading.value = false;
  }
};

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

const closeModal = () => {
  if (!isLoading.value) {
    emit("close");
  }
};
</script>

<template>
  <!-- ================================================================
        Overlay
  ================================================================= -->

  <div
    class="fixed inset-0 z-100 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
    @click="closeModal"
  >
    <!-- ================================================================
          Modal
    ================================================================= -->

    <div
      class="relative w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden"
      @click.stop
    >
      <!-- ================================================================
            Header
      ================================================================= -->

      <div class="px-6 pt-6 pb-4 flex items-start justify-between">
        <div>
          <h2 class="text-lg font-bold text-slate-800">Update Status</h2>

          <p class="text-sm text-slate-500 mt-0.5">
            For
            <span class="font-bold text-slate-900">{{
              authStore.user?.data.user.name_english || "Marcus Sterling"
            }}</span>
          </p>
        </div>

        <!-- Close Button -->

        <button
          type="button"
          @click="closeModal"
          :disabled="isLoading"
          class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-lg cursor-pointer disabled:opacity-50"
        >
          <CloseIcon class="size-5" />
        </button>
      </div>

      <!-- ================================================================
            Body / Form
      ================================================================= -->

      <form @submit.prevent="submit">
        <div class="px-6 space-y-5 pb-6">
          <!-- Server Error -->

          <div
            v-if="messageError"
            class="p-3 rounded-xl border border-red-200 bg-red-50 text-red-600 text-sm"
          >
            {{ messageError }}
          </div>

          <!-- Current Status Selection -->

          <div>
            <label class="block text-sm font-bold text-slate-800 mb-2">
              Current Status
            </label>

            <div class="relative">
              <select
                v-model="formData.status"
                class="w-full h-11 px-3.5 capitalize bg-white border border-slate-300 rounded-lg text-sm text-slate-700 outline-none appearance-none transition-all focus:border-slate-400 focus:ring-1 focus:ring-slate-400 cursor-pointer"
              >
                <option
                  v-for="option in statusOptions"
                  :key="option"
                  :value="option"
                  class="capitalize"
                >
                  {{ option }}
                </option>
              </select>

              <!-- Dropdown Icon -->

              <div
                class="absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-500"
              >
                <svg class="size-4 fill-current" viewBox="0 0 20 20">
                  <path
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                  />
                </svg>
              </div>
            </div>
          </div>

          <!-- Notes / Comments -->

          <div>
            <label class="block text-sm font-bold text-slate-800 mb-2">
              Notes / Comments
            </label>

            <textarea
              v-model="formData.notes"
              rows="3"
              placeholder="Add any relevant details about this status change..."
              class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-lg text-sm text-slate-700 placeholder:text-slate-400 outline-none resize-none transition-all focus:border-slate-400 focus:ring-1 focus:ring-slate-400"
            ></textarea>
          </div>

          <!-- History Content -->
          <div class="text-xs text-slate-600">
            <p class="text-slate-700">
              Status changed from
              <span class="font-bold text-slate-900 capitalize">{{
                authStore.user?.data.user.alumni?.employment_status ||
                "studying"
              }}</span>
              to
              <span class="font-bold text-slate-900 capitalize">{{
                formData.status
              }}</span>
            </p>

            <p class="text-slate-400 mt-1 font-mono text-[11px]">
              {{
                formData.status ===
                (authStore.user?.data.user.alumni?.employment_status ||
                  "studying")
                  ? "No change yet"
                  : "Pending save"
              }}
            </p>
          </div>
        </div>

        <!-- ================================================================
              Footer
        ================================================================= -->

        <div
          class="px-6 py-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end gap-3"
        >
          <button
            type="button"
            @click="closeModal"
            :disabled="isLoading"
            class="px-4 py-2 text-sm font-semibold text-slate-600 hover:text-slate-800 transition-colors cursor-pointer disabled:opacity-50"
          >
            Cancel
          </button>

          <button
            type="submit"
            :disabled="isLoading"
            class="px-4 py-2 rounded-lg bg-black text-white text-sm font-semibold flex items-center gap-2 hover:bg-slate-800 transition-all cursor-pointer disabled:opacity-60"
          >
            <svg
              class="size-4"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"
              />
            </svg>

            <span>{{ isLoading ? "Saving..." : "Save Changes" }}</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
