```vue
<script setup lang="ts">
import AddIcon from "~/components/icons/AddIcon.vue";
import CheckIcon from "~/components/icons/CheckIcon.vue";
import CloseIcon from "~/components/icons/CloseIcon.vue";
import DeleteIcon from "~/components/icons/DeleteIcon.vue";
import EditIcon from "~/components/icons/EditIcon.vue";
import EyeIcon from "~/components/icons/EyeIcon.vue";
import SearchIcon from "~/components/icons/SearchIcon.vue";
import OptionItemsArray from "~/components/options/OptionItemsArray.vue";

definePageMeta({
  layout: "admin",
});

/*
|--------------------------------------------------------------------------
| Types
|--------------------------------------------------------------------------
*/

interface Major {
  id: number;
  title: string;
}

interface Alumni {
  id: number;
  name_english: string;
  name_khmer: string;
  email: string;
  phone: string;
  avatar?: string;
  major: Major;
  graduation_year: number;
  work: string;
  work_status: string;
  company: string;
  status: "pending" | "approved" | "rejected";
}

/*
|--------------------------------------------------------------------------
| Static Data
|--------------------------------------------------------------------------
*/

const alumniData = ref<Alumni[]>([
  {
    id: 1,
    name_english: "Sok Dara",
    name_khmer: "សុខ ដារ៉ា",
    email: "sokdara@gmail.com",
    phone: "012 345 678",
    major: {
      id: 1,
      title: "Information Technology",
    },
    graduation_year: 2024,
    work: "Software Developer",
    work_status: "employed",
    company: "ABA Bank",
    status: "approved",
  },

  {
    id: 2,
    name_english: "Chan Sopheak",
    name_khmer: "ចាន់ សុភ័ក្រ",
    email: "sopheak@gmail.com",
    phone: "010 222 333",
    major: {
      id: 2,
      title: "Computer Science",
    },
    graduation_year: 2023,
    work: "Web Developer",
    work_status: "employed",
    company: "Smart Axiata",
    status: "approved",
  },

  {
    id: 3,
    name_english: "Chea Vannak",
    name_khmer: "ជា វណ្ណៈ",
    email: "vannak@gmail.com",
    phone: "097 555 666",
    major: {
      id: 1,
      title: "Information Technology",
    },
    graduation_year: 2024,
    work: "Looking for a job",
    work_status: "seeking",
    company: "",
    status: "pending",
  },

  {
    id: 4,
    name_english: "Kim Sreyneang",
    name_khmer: "គឹម ស្រីនាង",
    email: "sreyneang@gmail.com",
    phone: "096 888 999",
    major: {
      id: 3,
      title: "Business Administration",
    },
    graduation_year: 2022,
    work: "Accountant",
    work_status: "employed",
    company: "Canadia Bank",
    status: "approved",
  },

  {
    id: 5,
    name_english: "Long Rithy",
    name_khmer: "ឡុង រិទ្ធី",
    email: "rithy@gmail.com",
    phone: "015 111 222",
    major: {
      id: 4,
      title: "Marketing",
    },
    graduation_year: 2021,
    work: "Freelancer",
    work_status: "employed",
    company: "Self-employed",
    status: "approved",
  },

  {
    id: 6,
    name_english: "Pich Sophea",
    name_khmer: "ពេជ្រ សុភា",
    email: "sophea@gmail.com",
    phone: "078 333 444",
    major: {
      id: 2,
      title: "Computer Science",
    },
    graduation_year: 2023,
    work: "Continuing Study",
    work_status: "continuing_study",
    company: "",
    status: "pending",
  },

  {
    id: 7,
    name_english: "Heng Pisey",
    name_khmer: "ហេង ពិសី",
    email: "pisey@gmail.com",
    phone: "011 555 777",
    major: {
      id: 3,
      title: "Business Administration",
    },
    graduation_year: 2020,
    work: "Unemployed",
    work_status: "unemployed",
    company: "",
    status: "rejected",
  },

  {
    id: 8,
    name_english: "Nhem Makara",
    name_khmer: "ញឹម មករា",
    email: "makara@gmail.com",
    phone: "092 777 888",
    major: {
      id: 1,
      title: "Information Technology",
    },
    graduation_year: 2025,
    work: "Frontend Developer",
    work_status: "employed",
    company: "Wing Bank",
    status: "approved",
  },

  {
    id: 9,
    name_english: "Seng Bopha",
    name_khmer: "សេង បូផា",
    email: "bopha@gmail.com",
    phone: "088 123 456",
    major: {
      id: 4,
      title: "Marketing",
    },
    graduation_year: 2022,
    work: "Marketing Officer",
    work_status: "employed",
    company: "Cellcard",
    status: "approved",
  },

  {
    id: 10,
    name_english: "Kong Vibol",
    name_khmer: "កុង វិបុល",
    email: "vibol@gmail.com",
    phone: "070 222 333",
    major: {
      id: 2,
      title: "Computer Science",
    },
    graduation_year: 2025,
    work: "Looking for a job",
    work_status: "seeking",
    company: "",
    status: "pending",
  },

  {
    id: 11,
    name_english: "Sokunthea Lim",
    name_khmer: "សុខុន្ធា លីម",
    email: "sokunthea@gmail.com",
    phone: "012 987 654",
    major: {
      id: 1,
      title: "Information Technology",
    },
    graduation_year: 2023,
    work: "System Administrator",
    work_status: "employed",
    company: "PPCBank",
    status: "approved",
  },

  {
    id: 12,
    name_english: "Vuthy Mean",
    name_khmer: "មាន វុទ្ធី",
    email: "vuthy@gmail.com",
    phone: "096 444 555",
    major: {
      id: 3,
      title: "Business Administration",
    },
    graduation_year: 2021,
    work: "Unemployed",
    work_status: "unemployed",
    company: "",
    status: "rejected",
  },
]);

/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const search = ref("");

const selectedMajor = ref("");
const selectedYear = ref("");
const selectedWorkStatus = ref("");

const majors = computed(() => {
  const data = alumniData.value.map((item) => item.major);

  const unique = data.filter(
    (major, index, self) =>
      index === self.findIndex((item) => item.id === major.id),
  );

  return unique;
});

const years = computed(() => {
  return [...new Set(alumniData.value.map((item) => item.graduation_year))]
    .sort((a, b) => b - a)
    .map((year) => ({
      id: year,
      title: String(year),
    }));
});

const workStatuses = [
  {
    id: 1,
    title: "Employed",
    status: "employed",
  },
  {
    id: 2,
    title: "Unemployed",
    status: "unemployed",
  },
  {
    id: 3,
    title: "Seeking Job",
    status: "seeking",
  },
  {
    id: 4,
    title: "Continuing Study",
    status: "continuing_study",
  },
];

/*
|--------------------------------------------------------------------------
| Filter Handlers
|--------------------------------------------------------------------------
*/

const handleMajor = (data: any) => {
  selectedMajor.value = data?.title ?? "";
};

const handleYear = (data: any) => {
  selectedYear.value = data?.title ?? "";
};

const handleWork = (data: any) => {
  selectedWorkStatus.value = data?.status ?? "";
};

/*
|--------------------------------------------------------------------------
| Filtered Data
|--------------------------------------------------------------------------
*/

const filteredAlumni = computed(() => {
  const keyword = search.value.toLowerCase().trim();

  return alumniData.value.filter((item) => {
    const matchesSearch =
      !keyword ||
      item.name_english.toLowerCase().includes(keyword) ||
      item.name_khmer.includes(keyword) ||
      item.work.toLowerCase().includes(keyword) ||
      item.company.toLowerCase().includes(keyword);

    const matchesMajor =
      !selectedMajor.value || item.major.title === selectedMajor.value;

    const matchesYear =
      !selectedYear.value ||
      item.graduation_year === Number(selectedYear.value);

    const matchesWorkStatus =
      !selectedWorkStatus.value ||
      item.work_status === selectedWorkStatus.value;

    return matchesSearch && matchesMajor && matchesYear && matchesWorkStatus;
  });
});

/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

const currentPage = ref(1);
const perPage = ref(5);

const lastPage = computed(() => {
  return Math.max(1, Math.ceil(filteredAlumni.value.length / perPage.value));
});

const paginatedAlumni = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;

  return filteredAlumni.value.slice(start, start + perPage.value);
});

watch([search, selectedMajor, selectedYear, selectedWorkStatus], () => {
  currentPage.value = 1;
});

const pageDirect = (page: number) => {
  if (page >= 1 && page <= lastPage.value) {
    currentPage.value = page;
  }
};

/*
|--------------------------------------------------------------------------
| Pagination Summary
|--------------------------------------------------------------------------
*/

const paginationSummary = computed(() => {
  const total = filteredAlumni.value.length;

  if (total === 0) {
    return "Showing 0 of 0 graduates";
  }

  const from = (currentPage.value - 1) * perPage.value + 1;

  const to = Math.min(currentPage.value * perPage.value, total);

  return `Showing ${from} to ${to} of ${total} graduates`;
});

/*
|--------------------------------------------------------------------------
| Add / Update / Profile
|--------------------------------------------------------------------------
*/

const formAddNew = ref(false);

const handleAddNewStudent = () => {
  formAddNew.value = true;
};

const formUpdate = ref(false);

const dataUpdate = ref<Alumni | null>(null);

const handleUpdate = (data: Alumni) => {
  dataUpdate.value = data;
  formUpdate.value = true;
};

const profileUser = ref<Alumni | null>(null);

const isProfile = ref(false);

const handleProfile = (data: Alumni) => {
  profileUser.value = data;
  isProfile.value = true;
};

/*
|--------------------------------------------------------------------------
| Status
|--------------------------------------------------------------------------
*/

const loadingId = ref<number | null>(null);
const loadingAction = ref("");

const handleUpdateStatus = (id: number, status: "approved" | "rejected") => {
  loadingId.value = id;
  loadingAction.value = status;

  setTimeout(() => {
    const item = alumniData.value.find((item) => item.id === id);

    if (item) {
      item.status = status;
    }

    loadingId.value = null;
    loadingAction.value = "";
  }, 400);
};

/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const handleDelete = (id: number) => {
  const confirmed = window.confirm(
    "Are you sure you want to delete this graduate?",
  );

  if (!confirmed) return;

  alumniData.value = alumniData.value.filter((item) => item.id !== id);

  if (currentPage.value > lastPage.value) {
    currentPage.value = lastPage.value;
  }
};

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function statusBadgeClass(status: string) {
  if (status === "approved") {
    return "bg-emerald-50 text-emerald-600";
  }

  if (status === "rejected") {
    return "bg-rose-50 text-rose-600";
  }

  return "bg-amber-50 text-amber-600";
}

function workStatusLabel(status: string) {
  const item = workStatuses.find((item) => item.status === status);

  return item?.title ?? "N/A";
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
</script>

<template>
  <!-- Add -->
  <AdminsPopupAddNewUser v-if="formAddNew" @close="formAddNew = false" />

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
          v-model="search"
          type="text"
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
            :items="majors"
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
              workStatuses.find((item) => item.status === selectedWorkStatus)
                ?.title ?? ''
            "
            title="All Work Status"
            @update:active="handleWork"
          />
        </div>

        <!-- Year -->
        <div class="min-w-40 w-full">
          <OptionItemsArray
            :items="years"
            :active="selectedYear"
            title="All Years"
            @update:active="handleYear"
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

        <tbody>
          <tr
            v-for="(item, index) in paginatedAlumni"
            :key="item.id"
            class="border-t border-slate-100 hover:bg-slate-50"
          >
            <!-- No -->
            <td class="px-4 py-3 text-sm text-slate-600">
              {{ (currentPage - 1) * perPage + index + 1 }}
            </td>

            <!-- Name -->
            <td class="px-4 py-3">
              <div class="flex items-center gap-2">
                <span
                  class="size-8 rounded-full bg-slate-100 text-slate-500 text-xs font-semibold flex items-center justify-center shrink-0"
                >
                  {{ initials(item.name_english, item.name_khmer) }}
                </span>

                <div class="min-w-0">
                  <p class="font-medium text-sm text-slate-700 truncate">
                    {{ item.name_english }}
                  </p>

                  <p class="text-xs text-slate-400 truncate">
                    {{ item.name_khmer }}
                  </p>
                </div>
              </div>
            </td>

            <!-- Major -->
            <td class="px-4 py-3 text-sm text-slate-600">
              {{ item.major.title }}
            </td>

            <!-- Year -->
            <td class="px-4 py-3 text-sm text-slate-600">
              {{ item.graduation_year }}
            </td>

            <!-- Work -->
            <td class="px-4 py-3">
              <p class="text-sm text-slate-600">
                {{ item.work }}
              </p>

              <p v-if="item.company" class="text-xs text-slate-400">
                {{ item.company }}
              </p>

              <p v-else class="text-xs text-slate-400">
                {{ workStatusLabel(item.work_status) }}
              </p>
            </td>

            <!-- Status -->
            <td class="px-4 py-3">
              <span
                :class="[
                  'px-2.5 py-1 text-xs rounded-full uppercase font-medium tracking-wide',
                  statusBadgeClass(item.status),
                ]"
              >
                {{ item.status }}
              </span>
            </td>

            <!-- Actions -->
            <td class="px-4 py-3">
              <div class="flex justify-end items-center gap-2">
                <!-- Reject -->
                <button
                  v-if="item.status === 'pending' || item.status === 'approved'"
                  :disabled="
                    loadingId === item.id && loadingAction === 'rejected'
                  "
                  @click="handleUpdateStatus(item.id, 'rejected')"
                  class="size-6.5 border text-red-500 border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 disabled:opacity-50"
                  title="Reject"
                >
                  <CloseIcon />
                </button>

                <!-- Approve -->
                <button
                  v-if="item.status === 'pending' || item.status === 'rejected'"
                  :disabled="
                    loadingId === item.id && loadingAction === 'approved'
                  "
                  @click="handleUpdateStatus(item.id, 'approved')"
                  class="size-6.5 border text-emerald-500 border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 disabled:opacity-50"
                  title="Approve"
                >
                  <CheckIcon />
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
                  class="size-6.5 border text-red-500 border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 cursor-pointer"
                  title="Delete"
                >
                  <DeleteIcon />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Empty -->
      <div
        v-if="filteredAlumni.length === 0"
        class="w-full p-8 flex justify-center items-center bg-white rounded-xl mt-2 border border-slate-200"
      >
        <span class="text-slate-400 text-sm"> No graduates found. </span>
      </div>
    </div>

    <!-- Pagination -->
    <div class="w-full flex items-center justify-between flex-wrap gap-3">
      <span class="text-sm text-slate-500">
        {{ paginationSummary }}
      </span>

      <Pagination
        :current-page="currentPage"
        :last-page="lastPage"
        @change="pageDirect"
      />
    </div>
  </div>
</template>
```
