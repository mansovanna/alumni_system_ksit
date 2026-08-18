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

/*
|--------------------------------------------------------------------------
| Form State
|--------------------------------------------------------------------------
*/

const formAddNew = ref(false);
const formUpdate = ref(false);

const dataUpdate = ref<MajorModel>({} as MajorModel);

/*
|--------------------------------------------------------------------------
| Add
|--------------------------------------------------------------------------
*/

const handleAddNewStudent = () => {
  formAddNew.value = !formAddNew.value;
};

/*
|--------------------------------------------------------------------------
| Update
|--------------------------------------------------------------------------
*/

const handleUpdate = (data: MajorModel) => {
  if (data) {
    dataUpdate.value = data;
    formUpdate.value = true;
  }
};

const handleUpdateNew = (data: any) => {
  console.log(data);
};

/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

const pageDirect = (page: number) => {
  majorStore.page = page;
  majorStore.getMajors();
};

/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const loading = ref({
  id: 0,
  loading: false,
});

const handleDelete = async (id: number) => {
  if (!id) return;

  loading.value.id = id;
  loading.value.loading = true;

  try {
    const res = await majorStore.deleteMajor(id);

    if (res.status === 204) {
      if (majorStore.majors?.data?.data) {
        majorStore.majors.data.data = majorStore.majors.data.data.filter(
          (item) => item.id !== id,
        );
      }
    }
  } catch (e) {
    alert("Delete failed");
  } finally {
    loading.value.id = 0;
    loading.value.loading = false;
  }
};

/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

let timeout: ReturnType<typeof setTimeout> | undefined;

watch(
  () => majorStore.search,
  () => {
    if (timeout) {
      clearTimeout(timeout);
    }

    timeout = setTimeout(() => {
      majorStore.page = 1;
      majorStore.getMajors();
    }, 400);
  },
);

/*
|--------------------------------------------------------------------------
| Mounted
|--------------------------------------------------------------------------
*/

onMounted(() => {
  majorStore.getMajors();
});
</script>

<template>
  <!--
  |--------------------------------------------------------------------------
  | Modal - Add
  |--------------------------------------------------------------------------
  -->

  <AdminsDepartmentAdd v-if="formAddNew" @close="formAddNew = false" />

  <!--
  |--------------------------------------------------------------------------
  | Modal - Update
  |--------------------------------------------------------------------------
  -->

  <AdminsDepartmentUpdate
    v-if="formUpdate"
    :data="dataUpdate ?? null"
    @close="formUpdate = false"
    @submit="handleUpdateNew"
  />

  <!--
  |--------------------------------------------------------------------------
  | Main
  |--------------------------------------------------------------------------
  -->

  <!-- ================================================================
         Search / Filter
    ================================================================= -->

  <div class="w-full flex flex-col gap-5">
    <!-- ================================================================
         HEADER + SEARCH
    ================================================================= -->

    <section
      class="w-full bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden"
    >
      <!-- Header -->

      <div class="px-5 py-5">
        <div
          class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
        >
          <!-- Title -->

          <div class="flex items-center gap-3">
            <div
              class="size-11 shrink-0 rounded-xl bg-primary/10 text-primary flex items-center justify-center"
            >
              <SpannerIcon class="size-5" />
            </div>

            <div>
              <h1 class="text-lg font-semibold text-slate-800">
                Major Management
              </h1>

              <p class="text-sm text-slate-400 mt-0.5">
                Manage academic majors and departments.
              </p>
            </div>
          </div>

          <!-- Add Button -->

          <button
            type="button"
            @click="handleAddNewStudent"
            class="inline-flex items-center justify-center gap-2 h-10 px-4 bg-primary text-white rounded-xl text-sm font-medium shadow-sm shadow-primary/20 hover:bg-primary/90 transition-all cursor-pointer"
          >
            <AddIcon class="size-4" />

            <span> Add Major </span>
          </button>
        </div>
      </div>

      <!-- Search -->

      <div class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/50">
        <div
          class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
        >
          <!-- Search Input -->

          <div class="relative w-full sm:max-w-md">
            <input
              v-model="majorStore.search"
              type="text"
              placeholder="Search by major or department..."
              class="w-full h-10 bg-white border border-slate-200 rounded-lg pl-10 pr-10 text-sm text-slate-700 placeholder:text-slate-400 outline-none transition-all focus:border-primary focus:ring-4 focus:ring-primary/10"
            />

            <!-- Search Icon -->

            <div
              class="absolute left-0 top-0 bottom-0 w-10 flex items-center justify-center text-slate-400 pointer-events-none"
            >
              <SearchIcon class="size-4" />
            </div>

            <!-- Loading -->

            <div
              v-if="majorStore.isLoading"
              class="absolute right-3 top-1/2 -translate-y-1/2"
            >
              <LoadingIcon class="size-4 text-primary animate-spin" />
            </div>

            <!-- Clear -->

            <button
              v-else-if="majorStore.search"
              type="button"
              @click="majorStore.search = ''"
              class="absolute right-2 top-1/2 -translate-y-1/2 size-6 rounded-md flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer"
            >
              <span class="text-lg leading-none"> × </span>
            </button>
          </div>

          <!-- Result -->

          <div
            v-if="majorStore.majors?.data"
            class="flex items-center gap-2 shrink-0"
          >
            <span class="size-2 rounded-full bg-emerald-500"></span>

            <span class="text-xs font-medium text-slate-500">
              {{ majorStore.majors.data.total ?? 0 }}
              {{
                (majorStore.majors.data.total ?? 0) === 1 ? "major" : "majors"
              }}
            </span>
          </div>
        </div>
      </div>
    </section>

    <!-- ================================================================
         Table Card
    ================================================================= -->

    <div
      class="w-full bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden"
    >
      <!-- Table Header -->

      <div
        class="px-5 py-4 border-b border-slate-100 flex items-center justify-between"
      >
        <div>
          <h2 class="text-sm font-semibold text-slate-800">Major List</h2>

          <p class="text-xs text-slate-400 mt-0.5">
            All registered academic majors
          </p>
        </div>
      </div>

      <!-- ================================================================
           Loading Skeleton
      ================================================================= -->

      <div
        v-if="majorStore.isLoading && !majorStore.majors?.data?.data"
        class="p-5"
      >
        <div
          v-for="i in 6"
          :key="i"
          class="flex items-center gap-4 py-4 border-b border-slate-100 animate-pulse"
        >
          <div class="w-8 h-3 bg-slate-200 rounded-full"></div>

          <div class="w-40 h-3 bg-slate-200 rounded-full"></div>

          <div class="flex-1 h-3 bg-slate-200 rounded-full"></div>

          <div class="w-28 h-3 bg-slate-200 rounded-full"></div>

          <div class="w-28 h-3 bg-slate-200 rounded-full"></div>

          <div class="w-20 h-7 bg-slate-200 rounded-lg"></div>
        </div>
      </div>

      <!-- ================================================================
           Table
      ================================================================= -->

      <div
        v-else-if="majorStore.majors?.data?.data?.length"
        class="w-full overflow-x-auto"
      >
        <table class="w-full min-w-[1000px]">
          <!-- Head -->

          <thead>
            <tr class="bg-slate-50/80 border-b border-slate-200">
              <th
                class="px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide w-16"
              >
                #
              </th>

              <th
                class="px-4 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide"
              >
                Title
              </th>

              <th
                class="px-4 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide"
              >
                Body
              </th>

              <th
                class="px-4 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide"
              >
                Color From
              </th>

              <th
                class="px-4 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide"
              >
                Color To
              </th>

              <th
                class="px-4 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide"
              >
                Icon
              </th>

              <th
                class="px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide w-32"
              >
                Action
              </th>
            </tr>
          </thead>

          <!-- Body -->

          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="(item, index) in majorStore.majors.data.data"
              :key="index"
              class="group hover:bg-slate-50/80 transition-colors duration-150"
            >
              <!-- Number -->

              <td class="px-5 py-4 text-sm text-slate-400 font-medium">
                {{
                  ((majorStore.majors.data.current_page ?? 1) - 1) *
                    (Number(majorStore.majors.data.per_page) ?? 10) +
                  index +
                  1
                }}
              </td>

              <!-- Title -->

              <td class="px-4 py-4">
                <div class="flex items-center gap-3">
                  <div
                    class="size-9 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-semibold text-sm"
                  >
                    {{ item.title?.charAt(0)?.toUpperCase() ?? "M" }}
                  </div>

                  <div>
                    <p class="text-sm font-semibold text-slate-700">
                      {{ item.title ?? "N/A" }}
                    </p>

                    <p class="text-xs text-slate-400">Major #{{ item.id }}</p>
                  </div>
                </div>
              </td>

              <!-- Body -->

              <td class="px-4 py-4 max-w-xs">
                <p
                  class="text-sm text-slate-500 truncate"
                  :title="item.body ?? ''"
                >
                  {{ item.body ?? "N/A" }}
                </p>
              </td>

              <!-- Color From -->

              <td class="px-4 py-4">
                <div
                  class="inline-flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg bg-slate-50 border border-slate-100"
                >
                  <span
                    class="size-6 rounded-md border border-white shadow-sm ring-1 ring-slate-200"
                    :style="{
                      backgroundColor: item.color_from ?? 'transparent',
                    }"
                  ></span>

                  <span
                    class="text-xs font-medium"
                    :style="{
                      color: item.color_from ?? 'transparent',
                    }"
                  >
                    {{ item.color_from ?? "N/A" }}
                  </span>
                </div>
              </td>

              <!-- Color To -->

              <td class="px-4 py-4">
                <div
                  class="inline-flex items-center gap-2.5 px-2.5 py-1.5 rounded-lg bg-slate-50 border border-slate-100"
                >
                  <span
                    class="size-6 rounded-md border border-white shadow-sm ring-1 ring-slate-200"
                    :style="{
                      backgroundColor: item.color_to ?? 'transparent',
                    }"
                  ></span>

                  <span
                    class="text-xs font-medium"
                    :style="{
                      color: item.color_to ?? 'transparent',
                    }"
                  >
                    {{ item.color_to ?? "N/A" }}
                  </span>
                </div>
              </td>

              <!-- Icon -->

              <td class="px-4 py-4">
                <div class="inline-flex items-center gap-2">
                  <div
                    class="size-9 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center group-hover:bg-primary/10 group-hover:text-primary transition-colors"
                  >
                    <HugeiconsIcon
                      :icon="icons.find((i) => i.name === item.icon)?.icon"
                      class="size-5"
                    />
                  </div>

                  <span class="text-xs font-medium text-slate-600 capitalize">
                    {{ item.icon ?? "N/A" }}
                  </span>
                </div>
              </td>

              <!-- Actions -->

              <td class="px-5 py-4">
                <div class="flex items-center justify-center gap-2">
                  <!-- Edit -->

                  <button
                    type="button"
                    @click="handleUpdate(item)"
                    title="Edit"
                    class="size-9 rounded-lg border border-slate-200 bg-white text-blue-500 flex items-center justify-center hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 transition-all duration-150 cursor-pointer"
                  >
                    <EditIcon class="size-4" />
                  </button>

                  <!-- Delete -->

                  <button
                    type="button"
                    @click="handleDelete(Number(item.id))"
                    :disabled="loading.id === item.id && loading.loading"
                    title="Delete"
                    class="size-9 rounded-lg border border-slate-200 bg-white text-red-500 flex items-center justify-center hover:bg-red-50 hover:border-red-200 hover:text-red-600 transition-all duration-150 cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed"
                  >
                    <SpannerIcon
                      v-if="loading.id === item.id && loading.loading"
                      class="size-4 animate-spin"
                    />

                    <DeleteIcon v-else class="size-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- ================================================================
           Empty State
      ================================================================= -->

      <div
        v-else-if="
          !majorStore.isLoading && majorStore.majors?.data?.data?.length === 0
        "
        class="px-5 py-16 flex flex-col items-center justify-center text-center"
      >
        <div
          class="size-16 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mb-4"
        >
          <SpannerIcon class="size-7" />
        </div>

        <h3 class="text-sm font-semibold text-slate-700">No majors found</h3>

        <p class="text-sm text-slate-400 mt-1 max-w-sm">
          There are no majors matching your search. Try another keyword or
          create a new major.
        </p>

        <button
          type="button"
          @click="handleAddNewStudent"
          class="mt-5 inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition cursor-pointer"
        >
          <AddIcon class="size-4" />
          Add New Major
        </button>
      </div>

      <!-- ================================================================
           Bottom Loading
      ================================================================= -->

      <div
        v-if="majorStore.isLoading && majorStore.majors?.data?.data"
        class="w-full py-4 flex justify-center items-center border-t border-slate-100"
      >
        <div class="flex items-center gap-2 text-sm text-slate-400">
          <LoadingIcon class="size-5 text-primary animate-spin" />

          <span> Loading... </span>
        </div>
      </div>

      <!-- ================================================================
           Pagination
      ================================================================= -->

      <div
        v-if="
          majorStore.majors?.data &&
          Number(majorStore.majors.data.last_page) > 1
        "
        class="px-5 py-4 border-t border-slate-100 bg-slate-50/40"
      >
        <Pagination
          :current-page="majorStore.majors?.data?.current_page ?? 1"
          :last-page="majorStore.majors?.data?.last_page ?? 1"
          @change="pageDirect"
        />
      </div>
    </div>
  </div>
</template>
