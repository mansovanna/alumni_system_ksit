<script setup lang="ts">
import AddIcon from "~/components/icons/AddIcon.vue";
import DeleteIcon from "~/components/icons/DeleteIcon.vue";
import EditIcon from "~/components/icons/EditIcon.vue";
import { HugeiconsIcon } from "@hugeicons/vue";
import SearchIcon from "~/components/icons/SearchIcon.vue";
import type { MajorModel } from "~/types/major";
import SpannerIcon from "~/components/icons/SpannerIcon.vue";
import LoadingIcon from "~/components/icons/LoadingIcon.vue";

definePageMeta({
  layout: "admin",
});

const majorStore = useMajorStore();

const formAddNew = ref(false);

const handleAddNewStudent = () => (formAddNew.value = !formAddNew.value);
const formUpdate = ref(false);

const dataUpdate = ref<MajorModel>({} as MajorModel);
const handleUpdate = (data: any) => {
  if (data) {
    dataUpdate.value = data;
    formUpdate.value = true;
  }
};

const handleUpdateNew = (data: any) => {
  console.log(data);
};

const pageDirect = (page: number) => {
  majorStore.page = page;
  majorStore.getMajors();
};

const loading = ref({
  id: 0,
  loading: false,
});

const handleDelete = async (id: number) => {
  if (id) {
    loading.value.id = id;
    loading.value.loading = true;
    try {
      const res = await majorStore.deleteMajor(id);
      if (res.status === 204) {
        if (majorStore.majors?.data.data) {
          majorStore.majors.data.data = majorStore.majors?.data?.data.filter(
            (e) => e.id !== id,
          );
        }
      }
    } catch (e) {
      alert("Delete Failse");
    } finally {
      loading.value.id = 0;
      loading.value.loading = false;
    }
  }
};

let timeout: number | undefined;

watch(
  () => [majorStore.search],
  ([search]) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
      majorStore.getMajors();
    }, 400);
  },
);

// --------
onMounted(() => {
  majorStore.getMajors();
});
</script>

<template>
  <!--  -->
  <AdminsDepartmentAdd v-if="formAddNew" @close="formAddNew = false" />
  <AdminsDepartmentUpdate
    v-if="formUpdate"
    :data="dataUpdate ?? null"
    @close="formUpdate = false"
    @submit="handleUpdateNew"
  />
  <!--  -->
  <div class="flex flex-col gap-4">
    <!-- Block filter production -->
    <div class="w-full flex justify-between items-center gap-6">
      <!-- Block Search bar -->
      <div class="relative min-w-1/4">
        <input
          type="text"
          name=""
          id=""
          v-model="majorStore.search"
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
              Title
            </th>

            <th class="font-normal px-4 py-3 text-sm text-slate-600 text-left">
              Body
            </th>

            <th class="font-normal px-4 py-3 text-sm text-slate-600 text-left">
              Color From
            </th>

            <th class="font-normal px-4 py-3 text-sm text-slate-600 text-left">
              Color To
            </th>

            <th class="font-normal px-4 py-3 text-sm text-slate-600 text-left">
              Icon
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
            v-for="(item, index) in majorStore.majors?.data?.data"
            class="hover:bg-slate-100"
          >
            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              {{ index }}
            </th>
            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              {{ item.title ?? "N/A" }}
            </th>

            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              {{ item.body ?? "N/A" }}
            </th>

            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              <div class="flex gap-2 items-center">
                <div
                  class="size-6 border rounded-full border-slate-200"
                  :style="`background-color: ${item.color_from};`"
                ></div>
                <span
                  class="font-Inter font-medium"
                  :style="`color: ${item.color_from} ;`"
                  >{{ item.color_from ?? "N/A" }}</span
                >
              </div>
            </th>

            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              <div class="flex gap-2 items-center">
                <div
                  class="size-6 border rounded-full border-slate-200"
                  :style="`background-color: ${item.color_to};`"
                ></div>
                <span
                  class="font-Inter font-medium"
                  :style="`color: ${item.color_to} ;`"
                  >{{ item.color_to ?? "N/A" }}</span
                >
              </div>
            </th>

            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              <div class="flex gap-2 items-center">
                <HugeiconsIcon
                  :icon="icons.find((i) => i.name === item.icon)?.icon"
                  class="w-6 h-6"
                />

                <span class="font-Inter capitalize font-medium">{{
                  item.icon ?? "N/A"
                }}</span>
              </div>
            </th>

            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              <div class="flex justify-end items-center gap-2">
                <button
                  @click="handleUpdate(item)"
                  class="size-6.5 border text-blue-500 border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 cursor-pointer"
                >
                  <EditIcon />
                </button>

                <button
                  @click="handleDelete(Number(item.id))"
                  :disabled="loading.id == item.id && loading.loading"
                  class="size-6.5 border text-red-500 border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 cursor-pointer"
                >
                  <SpannerIcon
                    v-if="loading.id == item.id && loading.loading"
                  />
                  <DeleteIcon v-else />
                </button>
              </div>
            </th>
          </tr>
        </tbody>
      </table>
      <!--  -->
      <div v-if="majorStore.isLoading && !majorStore.majors?.data?.data">
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
        v-if="
          !majorStore.isLoading && majorStore.majors?.data?.data.length == 0
        "
        class="w-full p-4 flex justify-center items-center bg-white rounded-xl mt-4"
      >
        <span class="text-red-500 font-medium">Data is Available!</span>
      </div>
    </div>

    <!-- Block Pagination -->
    <!-- Block Pagination -->
    <Pagination
      :current-page="majorStore.majors?.data?.current_page ?? 1"
      :last-page="majorStore.majors?.data?.last_page ?? 1"
      @change="pageDirect"
    />

    <div
      v-if="majorStore.isLoading && majorStore.majors?.data?.data"
      class="w-full p-2 flex justify-center items-center"
    >
      <LoadingIcon class="size-10 text-primary" />
    </div>
  </div>
</template>
