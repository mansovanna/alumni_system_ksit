<script setup lang="ts">
import AddIcon from "~/components/icons/AddIcon.vue";
import DeleteIcon from "~/components/icons/DeleteIcon.vue";
import EditIcon from "~/components/icons/EditIcon.vue";
import KeyIcon from "~/components/icons/KeyIcon.vue";
import LoadingIcon from "~/components/icons/LoadingIcon.vue";

import SearchIcon from "~/components/icons/SearchIcon.vue";
import SpannerIcon from "~/components/icons/SpannerIcon.vue";
import type { UserModel } from "~/types/user.model";

definePageMeta({
  layout: "admin",
});

const staffStore = useStaffStore();
const authStore = useAuthStore();

const loadinStatus = ref({
  id: 0,
  status: "pending",
  title: "update",
  value: false,
});

const formAddNew = ref(false);

const handleAddNewStudent = () => (formAddNew.value = !formAddNew.value);
const formUpdate = ref(false);

const dataUpdateInfo = ref<UserModel>({} as UserModel);
const handleUpdate = (id: number, data: any) => {
  if (id) {
    formUpdate.value = true;
    dataUpdateInfo.value = data;
  }
};

const handleUpdateNew = (data: any) => {
  console.log(data);
};

const user_id = ref(0);
const isFormReset = ref(false);
const handleResetPassword = (id: number) => {
  if (id) {
    user_id.value = id;
    isFormReset.value = true;
  }
};

const handlePayloadRestPass = (data: any) => {
  console.log(data);
};


// ----------------------------

let timeout: number | undefined;

watch(
  () => [staffStore.search],
  ([search]) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
      staffStore.getStaffs()
    }, 400);
  },
);

/* ----------------------------------------- */
// ---------  Get data -------------------
onMounted(() => {
  staffStore.getStaffs();
});

// Block change pagination
const pageDirect = (page: number) => {
  staffStore.page = page;
  staffStore.getStaffs();
};

// --------------
const handleUpdateStatus = async (
  id: number,
  status: string,
  title: string,
) => {
  //

  if (id && status == "delete" && title == "delete") {
    loadinStatus.value.id = id;
    loadinStatus.value.status = status;
    loadinStatus.value.title = title;
    loadinStatus.value.value = true;

    try {
      const res = await staffStore.deleteStaff(id);

      if (res.status === 204) {
        if (staffStore.data?.data.data) {
          staffStore.data.data.data = staffStore.data.data.data.filter(
            (e) => e.id !== id,
          );
        }
      }
    } catch (e) {
      // console.log(e);
      alert("Password is can`t change");
    } finally {
      loadinStatus.value.id = 0;
      loadinStatus.value.status = "";
      loadinStatus.value.title = "";
      loadinStatus.value.value = false;
    }
  }
};
</script>

<template>
  <!--  -->
  <AdminsStaffAdd v-if="formAddNew" @close="formAddNew = false" />
  <AdminsStaffUpdate
    v-if="formUpdate"
    :data="dataUpdateInfo ?? null"
    @close="formUpdate = false"
    @submit="handleUpdateNew"
  />
  <AdminsStaffResetPassword
    v-if="isFormReset"
    :id="user_id"
    @close="isFormReset = false"
    @submit="handlePayloadRestPass"
  />
  <!--  -->
  <div class="flex flex-col gap-4">
    <!-- Block filter production -->
    <div class="w-full flex justify-between items-center gap-6">
      <!-- Block Search bar -->

      <div class="relative min-w-1/4">
        <input
          type="text"
          v-model="staffStore.search"
          class="w-full bg-white px-4 pl-8 py-2 border border-slate-200 rounded-full focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 placeholder:text-sm ease-in-out"
          placeholder="Search...!"
        />

        <div
          class="absolute top-0 bottom-0 left-0 flex justify-center items-center px-3 text-slate-500"
        >
          <SearchIcon />
        </div>
      </div>
      <!-- End Block Serch Bar -->

      <!-- Block Option -->
      <div class="flex justify-end items-center gap-4">
        <!-- Block option 1 -->

        <button
          @click="handleAddNewStudent()"
          class="flex justify-center items-center gap-1 text-nowrap px-3 bg-primary py-2 text-white rounded-md text-sm cursor-pointer hover:bg-primary/80"
        >
          <AddIcon />
          <span>Add New</span>
        </button>
      </div>
      <!-- End Block Option -->
    </div>

    <!-- End Block filtter production -->
    <!-- Page Content -->

    <!-- Block Table -->
    <div class="w-full overflow-x-auto text-nowrap">
      <table
        class="w-full bg-white font-Inter rounded-xl overflow-clip border border-slate-400 shadow shadow-slate-500/5"
      >
        <thead class="w-full font-normal">
          <tr class="bg-ab">
            <th
              class="font-normal px-4 py-3 text-sm text-slate-600 text-left w-20"
            >
              No
            </th>
            <th class="font-normal px-4 py-3 text-sm text-slate-600 text-left">
              Name Khmer
            </th>
            <th class="font-normal px-4 py-3 text-sm text-slate-600 text-left">
              User Name (English)
            </th>

            <th class="font-normal px-4 py-3 text-sm text-slate-600 text-left">
              Role
            </th>

            <th
              class="w-45 font-normal px-4 py-3 text-sm text-slate-600 text-center"
            >
              Action
            </th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="(item, index) in staffStore.data?.data?.data || []"
            :key="item.id"
            class="hover:bg-slate-100"
          >
            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              {{ index + 1 }}
            </th>
            <th
              class="px-4 py-1.5 text-sm text-left font-kantumruy-pro font-medium"
              :class="item.name_khmer ? 'text-slate-600' : 'text-red-500'"
            >
              {{ item.name_khmer ?? "N/A" }}
            </th>

            <th
              class="font-normal px-4 py-1.5 text-sm text-left"
              :class="item.name_english ? 'text-slate-600' : 'text-red-500'"
            >
              {{ item.name_english ?? "N/A" }}
            </th>

            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left capitalize"
            >
              {{ item.role ?? "N/A" }}
            </th>

            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              <div class="flex justify-end items-center gap-2">
                <button
                  @click="handleResetPassword(item.id)"
                  class="size-6.5 border text-blue-500 border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 cursor-pointer"
                >
                  <KeyIcon />
                </button>

                <button
                  @click="handleUpdate(item.id, item)"
                  class="size-6.5 border text-blue-500 border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 cursor-pointer"
                >
                  <EditIcon />
                </button>
                <button
                  v-if="
                    item.role == 'staff' ||
                    (item.role == 'admin' && item.id !== authStore.user?.id)
                  "
                  @click="handleUpdateStatus(item.id, 'delete', 'delete')"
                  :disabled="
                    loadinStatus.id == item.id &&
                    loadinStatus.status == 'delete' &&
                    loadinStatus.title == 'delete' &&
                    loadinStatus.value
                  "
                  class="size-6.5 border text-red-500 border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 cursor-pointer"
                >
                  <SpannerIcon
                    v-if="
                      loadinStatus.id == item.id &&
                      loadinStatus.status == 'delete' &&
                      loadinStatus.title == 'delete' &&
                      loadinStatus.value
                    "
                  />
                  <DeleteIcon v-else />
                </button>
              </div>
            </th>
          </tr>
        </tbody>
      </table>

      <!--  -->
      <div v-if="staffStore.isLoading && !staffStore.data?.data?.data">
        <div class="w-full bg-white p-4 mt-2 rounded-xl">
          <div class="w-full flex justify-between items-center animate-pulse">
            <div class="flex flex-col gap-2 w-1/4">
              <div class="w-1/2 p-2 bg-slate-200 rounded-full"></div>
              <div class="w-full p-1.5 bg-slate-200 rounded-full"></div>
            </div>
            <div class="flex flex-col gap-2 w-1/4">
              <div class="w-1/2 p-2 bg-slate-200 rounded-full"></div>
              <div class="w-full p-1.5 bg-slate-200 rounded-full"></div>
            </div>
            <div class="flex flex-col gap-2 w-1/4">
              <div class="w-1/2 p-2 bg-slate-200 rounded-full"></div>
              <div class="w-full p-1.5 bg-slate-200 rounded-full"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- check if data is null -->
      <div
        v-if="!staffStore.isLoading && staffStore.data?.data?.data.length == 0"
        class="w-full p-4 flex justify-center items-center bg-white rounded-xl mt-4"
      >
        <span class="text-red-500 font-medium">Data is Available!</span>
      </div>
    </div>

    <!-- Block Pagination -->
    <!-- Block Pagination -->
    <Pagination
      :current-page="staffStore.data?.data?.current_page ?? 1"
      :last-page="staffStore.data?.data?.last_page ?? 1"
      @change="pageDirect"
    />

    <div
      v-if="staffStore.isLoading && staffStore.data?.data?.data"
      class="w-full p-2 flex justify-center items-center"
    >
      <LoadingIcon class="size-10 text-primary" />
    </div>
  </div>
</template>
