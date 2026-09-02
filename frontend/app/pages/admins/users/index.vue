<script setup lang="ts">
import AddIcon from "~/components/icons/AddIcon.vue";
import BlockIcon from "~/components/icons/BlockIcon.vue";
import CheckIcon from "~/components/icons/CheckIcon.vue";
import CloseIcon from "~/components/icons/CloseIcon.vue";
import DeleteIcon from "~/components/icons/DeleteIcon.vue";
import EditIcon from "~/components/icons/EditIcon.vue";
import EyeIcon from "~/components/icons/EyeIcon.vue";
import LoadingIcon from "~/components/icons/LoadingIcon.vue";
import SearchIcon from "~/components/icons/SearchIcon.vue";
import OptionItemsArray from "~/components/options/OptionItemsArray.vue";
import { useAlumniStore } from "~/stores/alumni";
import { useMajorStore } from "~/stores/major";
import type { AlumniModels } from "~/types/alumni.model";

definePageMeta({
  layout: "admin",
});

/*
|--------------------------------------------------------------------------
| Stores
|--------------------------------------------------------------------------
*/
const alumniStore = useAlumniStore();
const majorStore = useMajorStore();

/*
|--------------------------------------------------------------------------
| Filter State
|--------------------------------------------------------------------------
*/
const selectedMajor = ref("");

const workStatuses = [
  { id: 1, title: "Employed", status: "employed" },
  { id: 2, title: "Unemployed", status: "unemployed" },
  { id: 3, title: "Self Employed", status: "self_employed" },
  { id: 4, title: "Studying", status: "studying" },
  { id: 5, title: "Unknown", status: "unknown" }, // NOTE: id was duplicated (4) before — fixed to 5
];

/*
|--------------------------------------------------------------------------
| Debounced Fetch (search / filters / pagination)
|--------------------------------------------------------------------------
*/
let debounceTimer: ReturnType<typeof setTimeout> | null = null;

const fetchAlumni = () => {
  alumniStore.getAlumni({
    search: alumniStore.search,
    per_page: 20,
    major_id: alumniStore.selectedMajor_id,
    employment_status: alumniStore.selectedWorkStatus,
    page: alumniStore.page,
  });
};

watch(
  () => [
    alumniStore.search,
    alumniStore.per_page,
    alumniStore.page,
    alumniStore.selectedMajor_id,
    alumniStore.selectedWorkStatus,
  ],
  () => {
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(fetchAlumni, 400);
  },
);

/*
|--------------------------------------------------------------------------
| Filter Handlers
|--------------------------------------------------------------------------
*/
const handleMajor = (data: any) => {
  selectedMajor.value = data?.title ?? "";
  alumniStore.selectedMajor_id = data?.id ?? "";
};

const handleWork = (data: any) => {
  alumniStore.selectedWorkStatus = data?.status ?? "";
};

/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/
const pageDirect = (page: number) => {
  const lastPage = alumniStore.data?.last_page ?? 1;

  if (page >= 1 && page <= lastPage) {
    alumniStore.page = page;
  }
};

/*
|--------------------------------------------------------------------------
| Add / Update Forms
|--------------------------------------------------------------------------
*/
const formAddNew = ref(false);

const handleAddNewStudent = () => {
  formAddNew.value = true;
};

// update status em...
const formDataUpdate = ref<AlumniModels | null>({} as AlumniModels);
const isFormUpdate = ref(false);
const handleUpdate = (data: AlumniModels) => {
  if (data) {
    formDataUpdate.value = data;
    isFormUpdate.value = true;
  }
};
/*
|--------------------------------------------------------------------------
| Approve / Reject Status
|--------------------------------------------------------------------------
*/
const loadingId = ref<number | null>(null);
const loadingAction = ref("");

const handleUpdateStatus = async (
  id: number,
  status: "approved" | "rejected",
) => {
  loadingId.value = id;
  loadingAction.value = status;

  try {
    // TODO: replace with the real API call once the endpoint is confirmed,
    // e.g. await alumniStore.updateStatus(id, status);
    await new Promise((resolve) => setTimeout(resolve, 400));
  } finally {
    loadingId.value = null;
    loadingAction.value = "";
  }
};

/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------

*/

const loadings = ref({
  id: 0,
  value: false,
});
const handleDelete = async (id: number) => {
  const confirmed = window.confirm(
    "Are you sure you want to delete this graduate?",
  );

  if (!confirmed) return;

  loadings.value.id = id;
  loadings.value.value = true;

  try {
    const res = await alumniStore.deleteAlumni(id);

    if (res.status === 200 || res.status === 204) {
      // Remove deleted alumni from current table
      if (alumniStore.data?.data) {
        alumniStore.data.data = alumniStore.data.data.filter(
          (item) => item.id !== id,
        );
      }
    }
  } catch (error) {
    console.error("Delete alumni failed:", error);
  } finally {
    loadings.value.value = false;
    loadings.value.id = 0;
  }
};
/*
|--------------------------------------------------------------------------
| Display Helpers
|--------------------------------------------------------------------------
*/
function statusBadgeClass(status: string) {
  if (status === "approved") return "bg-emerald-50 text-emerald-600";
  if (status === "rejected") return "bg-rose-50 text-rose-600";
  return "bg-amber-50 text-amber-600";
}

function workStatusLabel(status: string) {
  return workStatuses.find((item) => item.status === status)?.title ?? "N/A";
}

function initials(nameEn: string, nameKh: string) {
  const source = nameEn || nameKh || "?";

  return source
    .split(" ")
    .map((p) => p[0])
    .slice(0, 2)
    .join("")
    .toUpperCase();
}

// get data from add
const responseAdd = (data: any) => {
  // console.log(data);

  if ((data && data != null) || data !== "") {
    alumniStore.data?.data.unshift(data);
  }
};

/*
|--------------------------------------------------------------------------
| Lifecycle
|--------------------------------------------------------------------------
*/
onMounted(() => {
  alumniStore.getAlumni();
});
</script>

<template>
  <AdminsPopupAddNewUser
    v-if="formAddNew"
    @close="formAddNew = false"
    @submitted="responseAdd($event)"
  />

  <AdminsPopupUpdateUser
    v-if="isFormUpdate"
    :data="formDataUpdate ?? undefined"
    @close="isFormUpdate = false"
  />

  <div class="flex flex-col gap-4">
    <!-- Page Header -->
    <div>
      <h1 class="text-xl font-semibold text-slate-800">Graduate Directory</h1>
      <p class="text-sm text-slate-500">
        View and manage career outcomes for the alumni network.
      </p>
    </div>

    <!-- Filters -->
    <div class="w-full flex justify-between items-center gap-6 max-md:flex-col">
      <!-- Search -->
      <div class="relative min-w-1/4 max-lg:w-full">
        <input
          v-model="alumniStore.search"
          type="search"
          class="w-full bg-white px-4 pl-8 py-2 border border-slate-200 rounded-full focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 placeholder:text-sm"
          placeholder="Search by name, year, work"
        />
        <div
          class="absolute top-0 bottom-0 left-0 flex justify-center items-center px-3 text-slate-500"
        >
          <SearchIcon />
        </div>
      </div>

      <!-- Filter Controls -->
      <div
        class="flex justify-end items-center gap-4 max-md:flex-col max-lg:w-full"
      >
        <!-- Major -->
        <div class="min-w-55 w-full">
          <OptionItemsArray
            :items="majorStore.data?.data || []"
            :active="selectedMajor"
            title="All Majors"
            @update:active="handleMajor"
          />
        </div>

        <!-- Work Status -->
        <div class="min-w-46 w-full">
          <OptionsOption
            :items="workStatuses"
            :active="
              workStatuses.find(
                (item) => item.status === alumniStore.selectedWorkStatus,
              )?.title ?? ''
            "
            title="All Work Status"
            @update:active="handleWork"
          />
        </div>

        <!-- Add -->
        <button
          @click="handleAddNewStudent"
          class="flex justify-center items-center gap-1 text-nowrap px-3 bg-primary py-2 text-white rounded-md text-sm cursor-pointer hover:bg-primary/80"
        >
          <AddIcon />
          <span>Add New Alumni</span>
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="w-full overflow-x-auto">
      <table
        class="w-full min-w-245 bg-white font-Inter rounded-xl overflow-hidden border border-slate-200 shadow shadow-slate-500/5"
      >
        <thead>
          <tr class="bg-slate-50">
            <th class="font-normal px-4 py-3 text-sm text-slate-600 text-left">
              No
            </th>
            <th class="font-normal px-4 py-3 text-sm text-slate-600 text-left">
              Name
            </th>
            <th class="font-normal px-4 py-3 text-sm text-slate-600 text-left">
              Major
            </th>
            <th class="font-normal px-4 py-3 text-sm text-slate-600 text-left">
              Grad Year
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

        <tbody v-if="alumniStore.data?.data?.length">
          <tr
            v-for="(item, index) in alumniStore.data.data"
            :key="item.id"
            class="border-t border-slate-100 hover:bg-slate-50"
          >
            <!-- No -->
            <td class="px-4 py-3 text-sm text-slate-600">
              {{ index + 1 }}
            </td>

            <!-- Name -->
            <td class="px-4 py-3">
              <div class="flex items-center gap-2">
                <span
                  class="size-8 rounded-full bg-slate-100 text-slate-500 text-xs font-semibold flex items-center justify-center shrink-0"
                >
                  {{ initials(item.user.name_english, item.user.name_khmer) }}
                </span>
                <div class="min-w-0">
                  <p class="font-medium text-sm text-slate-700 truncate">
                    {{ item.user.name_english ?? "N/A" }}
                  </p>
                  <p class="text-xs text-slate-400 truncate">
                    {{ item.user.name_khmer ?? "N/A" }}
                  </p>
                </div>
              </div>
            </td>

            <!-- Major -->
            <td class="px-4 py-3 text-sm text-slate-600">
              {{ item.major?.name ?? "N/A" }}
            </td>

            <!-- Grad Year -->
            <td class="px-4 py-3 text-sm text-slate-600">
              {{ item.graduation_year ?? "N/A" }}
            </td>

            <!-- Work -->
            <td class="px-4 py-3">
              <!-- <p class="text-sm text-slate-600">{{ item.gpa ?? "N/A" }}</p> -->
              <p class="text-xs text-slate-400">
                {{
                  item.employment?.job_title
                    ? (item.employment?.job_title ?? "N/A")
                    : workStatusLabel(item.employment_status)
                }}
              </p>
            </td>

            <!-- Status -->
            <td class="px-4 py-3">
              <span
                :class="[
                  'px-2.5 py-1 text-xs rounded-full uppercase font-medium tracking-wide',
                  statusBadgeClass(item.user.status),
                ]"
              >
                {{ item.user.status }}
              </span>
            </td>

            <!-- Actions -->
            <td class="px-4 py-3">
              <div class="flex justify-end items-center gap-2">
                <!-- Reject -->
                <button
                  v-if="false"
                  :disabled="
                    loadingId === item.id && loadingAction === 'rejected'
                  "
                  @click="handleUpdateStatus(item.id, 'rejected')"
                  class="size-6.5 border text-red-500 border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 disabled:opacity-50"
                  title="Reject"
                >
                  <BlockIcon />
                </button>

                <!-- View -->
                <button
                  @click="
                    $router.push({
                      name: 'admins-users-id',
                      params: { id: item.id },
                    })
                  "
                  class="size-6.5 border text-orange-500 border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 cursor-pointer"
                  title="View Profile"
                >
                  <EyeIcon />
                </button>

                <!-- Edit -->
                <button
                  @click="handleUpdate(item)"
                  class="size-6.5 border text-blue-500 border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 cursor-pointer"
                  title="Edit"
                >
                  <EditIcon />
                </button>

                <!-- Delete -->
                <button
                  @click="handleDelete(item.id)"
                  :disabled="loadings.value && item.id === loadings.id"
                  class="size-6.5 border text-red-500 border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                  title="Delete"
                >
                  <LoadingIcon
                    v-if="loadings.value && item.id === loadings.id"
                    class="animate-spin"
                  />

                  <DeleteIcon v-else />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Loading -->
      <div
        v-if="alumniStore.isLoading"
        class="w-full p-8 flex justify-center items-center bg-white rounded-xl mt-2 border border-slate-200 text-primary"
      >
        <LoadingIcon class="size-10" />
      </div>

      <!-- Empty (only shown once loading is finished and there's really no data) -->
      <div
        v-else-if="!alumniStore.data?.data?.length"
        class="w-full p-8 flex justify-center items-center bg-white rounded-xl mt-2 border border-slate-200"
      >
        <span class="text-slate-400 text-sm">No graduates found.</span>
      </div>
    </div>

    <!-- Pagination -->
    <div class="w-full flex items-center justify-between gap-3">
      <span class="text-sm text-slate-500">
        Showing {{ alumniStore.data?.current_page }} to
        {{ alumniStore.data?.per_page }} of
        {{ alumniStore.data?.total ?? 0 }} graduates
      </span>

      <Pagination
        :current-page="alumniStore.data?.current_page || 1"
        :last-page="alumniStore.data?.last_page || 1"
        @change="pageDirect"
      />
    </div>
  </div>
</template>
