<script setup lang="ts">
import AddIcon from "~/components/icons/AddIcon.vue";
import CheckIcon from "~/components/icons/CheckIcon.vue";
import CloseIcon from "~/components/icons/CloseIcon.vue";
import DeleteIcon from "~/components/icons/DeleteIcon.vue";
import EditIcon from "~/components/icons/EditIcon.vue";
import EyeIcon from "~/components/icons/EyeIcon.vue";
import LoadingIcon from "~/components/icons/LoadingIcon.vue";
import SearchIcon from "~/components/icons/SearchIcon.vue";
import SpannerIcon from "~/components/icons/SpannerIcon.vue";
import OptionItemsArray from "~/components/options/OptionItemsArray.vue";

import type { UserModel } from "~/types/user.model";

definePageMeta({
  layout: "admin",
});

const userStore = useUserStore();
const majorStore = useMajorStore();

const formAddNew = ref(false);

const handleAddNewStudent = () => (formAddNew.value = !formAddNew.value);

const formUpdate = ref(false);

const dataUpdate = ref<UserModel>({} as UserModel);
const handleUpdate = (data: any) => {
  if (data) {
    formUpdate.value = true;
    dataUpdate.value = data;
    // console.log(data);
  }
};

const profileUser = ref({});
const isProfile = ref(false);

const handleProfile = (data: any) => {
  if (data) {
    profileUser.value = data;
    isProfile.value = true;
  }
};

// ----------
const pageDirect = (page: number) => {
  userStore.getUuser(userStore.search, userStore.perPage, page);
};

let timeout: number | undefined;

watch(
  () => [userStore.search, userStore.major.id, userStore.status_workd.status],
  ([search, major, status]) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
      userStore.getUuser(
        String(search),
        userStore.perPage,
        1,
        Number(major),
        String(status),
      );
    }, 400);
  },
);

/* ------------------------------------- */
const loadinStatus = ref({
  id: 0,
  status: "pending",
  title: "update",
  value: false,
});

const handleUpdateStatus = async (
  id: number,
  status: string,
  title: string,
) => {
  if (
    id &&
    (status === "approved" || status === "rejected") &&
    title === "update"
  ) {
    loadinStatus.value.id = id;
    loadinStatus.value.status = status;
    loadinStatus.value.value = true;
    try {
      const res = await userStore.updateStatus(id, status);
      if (userStore.data?.data.data) {
        userStore.data.data.data = userStore.data?.data.data.map((e) =>
          e.id === res.data.data.id ? res.data.data : e,
        );
      }
    } catch (e) {
      console.log(e);
    } finally {
      loadinStatus.value.value = false;
      loadinStatus.value.value = false;
      loadinStatus.value.id = 0;
      loadinStatus.value.status = "";
    }
  } else if (id && status == "delete" && title == "delete") {
    loadinStatus.value.id = id;
    loadinStatus.value.status = status;
    loadinStatus.value.title = title;
    loadinStatus.value.value = true;

    try {
      const res = await userStore.deleteUser(id);
      if (res.status === 204) {
        if (userStore.data?.data.data) {
          userStore.data.data.data = userStore.data.data.data.filter(
            (e) => e.id !== id,
          );
        }
      }
    } catch (e) {
      console.log(e);
    } finally {
      loadinStatus.value.value = false;
    }
  }
};
// ------------------------
onMounted(() => {
  userStore.getUuser();
});

// filter by major

const handleMajor = (data: any) => {
  userStore.major.id = data.id;
  userStore.major.title = data.title;
};

const handleWork = (data: any) => {
  userStore.status_workd.id = data.id;
  userStore.status_workd.title = data.title;
  userStore.status_workd.status = data.status;
  console.log(data);
};
</script>

<template>
  <!--  -->
  <AdminsPopupAddNewUser v-if="formAddNew" @close="formAddNew = false" />
  <AdminsPopupUpdateUser
    v-if="formUpdate"
    @close="formUpdate = false"
    :data="dataUpdate ?? null"
  />
  <AdminsPopupProfileUser
    v-if="isProfile"
    @close="isProfile = false"
    :data="profileUser"
  />
  <!--  -->
  <div class="flex flex-col gap-4">
    <!-- Block filter production -->
    <div class="w-full flex justify-between items-center gap-6 max-md:flex-col">
      <!-- Block Search bar -->
      <div class="relative min-w-1/4 max-lg:w-full">
        <input
          type="text"
          name=""
          id=""
          v-model="userStore.search"
          class="w-full bg-white px-4 pl-8 py-2 border border-slate-200 rounded-full focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 placeholder:text-sm ease-in-out"
          placeholder="Search by name, year, work"
        />

        <div
          class="absolute top-0 bottom-0 left-0 flex justify-center items-center px-3 text-slate-500"
        >
          <SearchIcon />
        </div>
      </div>
      <!-- End Block Serch Bar -->

      <!-- Block Option -->
      <div
        class="flex justify-end items-center gap-4 max-md:flex-col max-lg:w-full"
      >
        <div class="min-w-55 w-full max-md:max-w-full">
          <OptionItemsArray
            :items="majorStore.data?.data || []"
            :active="userStore.major.title ?? ''"
            :title="'ជំនាញ់់ទាំងអស់'"
            @update:active="handleMajor($event)"
          />
        </div>
        <div class="min-w-46 w-full">
          <OptionsOption
            :items="workStatus"
            :active="userStore.status_workd.title ?? ''"
            :title="'ស្ថានភាពការងារទាំងអស់'"
            @update:active="handleWork($event)"
          />
        </div>

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
              class="font-normal px-4 w-15 py-3 text-sm text-slate-600 text-left"
            >
              No
            </th>
            <th class="font-normal px-4 py-3 text-sm text-slate-600 text-left">
              User Name(Kh)
            </th>
            <th class="font-normal px-4 py-3 text-sm text-slate-600 text-left">
              User Name (Eng)
            </th>
            <th class="font-normal px-4 py-3 text-sm text-slate-600 text-left">
              Major
            </th>
            <th class="font-normal px-4 py-3 text-sm text-slate-600 text-left">
              Last Year
            </th>
            <th class="font-normal px-4 py-3 text-sm text-slate-600 text-left">
              Work
            </th>
            <th class="font-normal px-4 py-3 text-sm text-slate-600 text-left">
              Status
            </th>
            <th
              class="w-45 font-normal px-4 py-3 text-sm text-slate-600 text-center"
            >
              Action
            </th>
          </tr>
        </thead>

        <tbody v-if="userStore.data?.data?.data">
          <tr
            v-for="(item, index) in userStore.data?.data?.data"
            :key="index"
            class="hover:bg-slate-100"
          >
            <th
              class="font-normal w-15 text-wrap line-clamp-1 px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              {{ index + 1 }}
            </th>
            <th
              class="font-normal px-4 py-1.5 text-sm text-left"
              :class="
                item.name_khmer == null || item.name_khmer == ''
                  ? 'text-red-500  font-medium'
                  : 'text-slate-600 '
              "
            >
              {{ item.name_khmer ?? "N/A" }}
            </th>
            <th
              class="font-normal px-4 py-1.5 text-sm text-left"
              :class="
                item.name_english == null || item.name_english == ''
                  ? 'text-red-500 '
                  : 'text-slate-600 '
              "
            >
              {{ item.name_english ?? "N/A" }}
            </th>
            <th
              class="font-normal px-4 py-1.5 text-sm text-left"
              :class="
                item.user_infos_one?.major?.title == null ||
                item.user_infos_one?.major?.title == ''
                  ? 'text-red-500 '
                  : 'text-slate-600 '
              "
            >
              {{ item.user_infos_one?.major?.title ?? "N/A" }}
            </th>
            <th
              class="font-normal px-4 py-1.5 text-sm text-left"
              :class="
                item.user_infos_one?.last_year == null ||
                item.user_infos_one?.last_year == ''
                  ? 'text-red-500 '
                  : 'text-slate-600 '
              "
            >
              {{ item.user_infos_one?.last_year ?? "N/A" }}
            </th>
            <th
              class="font-normal px-4 py-1.5 text-sm text-left capitalize"
              :class="
                item.user_infos_one?.work == null ||
                item.user_infos_one?.work.length <= 0
                  ? 'text-red-500 '
                  : 'text-slate-600 '
              "
            >
              {{ item.user_infos_one?.work ?? "N/A" }}
            </th>
            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              <span
                class="px-3 py-1 text-xs rounded-full capitalize font-Inter font-medium bg-slate-100 border border-slate-200 relative"
              >
                <div
                  class="size-2 absolute top-0 -right-1 rounded-full"
                  :class="
                    item.status == 'pending'
                      ? 'bg-amber-500'
                      : item.status == 'approved'
                        ? 'bg-green-500 text-white'
                        : 'bg-red-500 text-white'
                  "
                ></div>

                <div
                  class="size-2 absolute top-0 -right-1 rounded-full animate-ping"
                  :class="
                    item.status == 'pending'
                      ? 'bg-amber-500'
                      : item.status == 'approved'
                        ? 'bg-green-500 text-white'
                        : 'bg-red-500 text-white'
                  "
                ></div>
                {{ item.status ?? "N/A" }}</span
              >
            </th>
            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              <div class="flex justify-end items-center gap-2">
                <button
                  v-if="item.status == 'pending' || item.status == 'approved'"
                  :disabled="
                    loadinStatus.id == item.id &&
                    loadinStatus.status == 'rejected' &&
                    loadinStatus.title == 'update' &&
                    loadinStatus.value
                  "
                  @click="handleUpdateStatus(item.id, 'rejected', 'update')"
                  class="size-6.5 border text-red-500 border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 cursor-pointer"
                >
                  <SpannerIcon
                    v-if="
                      loadinStatus.id == item.id &&
                      loadinStatus.status == 'rejected' &&
                      loadinStatus.title == 'update' &&
                      loadinStatus.value
                    "
                  />
                  <CloseIcon v-else />
                </button>

                <button
                  v-if="item.status == 'pending' || item.status == 'rejected'"
                  :disabled="
                    loadinStatus.id == item.id &&
                    loadinStatus.status == 'approved' &&
                    loadinStatus.title == 'update' &&
                    loadinStatus.value
                  "
                  @click="handleUpdateStatus(item.id, 'approved', 'update')"
                  class="size-6.5 border text-success border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 cursor-pointer"
                >
                  <SpannerIcon
                    v-if="
                      loadinStatus.id == item.id &&
                      loadinStatus.status == 'approved' &&
                      loadinStatus.title == 'update' &&
                      loadinStatus.value
                    "
                  />
                  <CheckIcon v-else />
                </button>

                <button
                  @click="handleProfile(item)"
                  class="size-6.5 border text-orange-500 border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 cursor-pointer"
                >
                  <EyeIcon />
                </button>

                <button
                  @click="handleUpdate(item)"
                  class="size-6.5 border text-blue-500 border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 cursor-pointer"
                >
                  <EditIcon />
                </button>

                <button
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
      <div v-if="userStore.isLoading && !userStore.data?.data?.data">
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
        v-if="!userStore.isLoading && userStore.data?.data?.data.length == 0"
        class="w-full p-4 flex justify-center items-center bg-white rounded-xl mt-4"
      >
        <span class="text-red-500 font-medium">Data is Available!</span>
      </div>
    </div>

    <!-- Block Pagination -->
    <Pagination
      :current-page="userStore.data?.data?.current_page ?? 1"
      :last-page="userStore.data?.data?.last_page ?? 1"
      @change="pageDirect"
    />

    <!-- loading -->

    <div
      v-if="userStore.isLoading && userStore.data?.data?.data"
      class="w-full p-2 flex justify-center items-center"
    >
      <LoadingIcon class="size-10 text-primary" />
    </div>
  </div>
</template>
