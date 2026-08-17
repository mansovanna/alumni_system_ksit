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
  <!-- ================================================================
       MODALS
  ================================================================= -->

  <AdminsStaffAdd
    v-if="formAddNew"
    @close="formAddNew = false"
  />

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


  <!-- ================================================================
       PAGE
  ================================================================= -->

  <div class="w-full flex flex-col gap-5">


    <!-- ==============================================================
         HEADER + SEARCH
    =============================================================== -->

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

              <h1
                class="text-lg font-semibold text-slate-800 tracking-tight"
              >
                Staff Management
              </h1>

              <p
                class="text-sm text-slate-400 mt-0.5"
              >
                Manage staff accounts and access permissions.
              </p>

            </div>

          </div>


          <!-- Add -->

          <button
            type="button"
            @click="handleAddNewStudent"
            class="inline-flex items-center justify-center gap-2 h-10 px-4 bg-primary text-white rounded-xl text-sm font-medium shadow-sm shadow-primary/20 hover:bg-primary/90 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-200 cursor-pointer"
          >

            <AddIcon class="size-4" />

            <span>
              Add Staff
            </span>

          </button>

        </div>

      </div>


      <!-- Search -->

      <div
        class="px-5 py-3.5 border-t border-slate-100 bg-slate-50/50"
      >

        <div
          class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
        >

          <!-- Search -->

          <div class="relative w-full sm:max-w-md">

            <input
              v-model="staffStore.search"
              type="text"
              placeholder="Search by name or username..."
              class="w-full h-10 bg-white border border-slate-200 rounded-lg pl-10 pr-10 text-sm text-slate-700 placeholder:text-slate-400 outline-none transition-all duration-200 focus:border-primary focus:ring-4 focus:ring-primary/10"
            />

            <!-- Search -->

            <div
              class="absolute left-0 top-0 bottom-0 w-10 flex items-center justify-center text-slate-400 pointer-events-none"
            >
              <SearchIcon class="size-4" />
            </div>


            <!-- Loading -->

            <div
              v-if="staffStore.isLoading"
              class="absolute right-3 top-1/2 -translate-y-1/2"
            >
              <LoadingIcon
                class="size-4 text-primary animate-spin"
              />
            </div>


            <!-- Clear -->

            <button
              v-else-if="staffStore.search"
              type="button"
              @click="staffStore.search = ''"
              class="absolute right-2 top-1/2 -translate-y-1/2 size-6 rounded-md flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition cursor-pointer"
              title="Clear search"
            >
              <span class="text-lg leading-none">
                ×
              </span>
            </button>

          </div>


          <!-- Result -->

          <div
            v-if="staffStore.data?.data"
            class="flex items-center gap-2 shrink-0"
          >

            <span
              class="size-2 rounded-full bg-emerald-500"
            ></span>

            <span
              class="text-xs font-medium text-slate-500"
            >
              {{ staffStore.data.data.total ?? 0 }}

              {{
                (staffStore.data.data.total ?? 0) === 1
                  ? "staff"
                  : "staff members"
              }}

            </span>

          </div>

        </div>

      </div>

    </section>


    <!-- ==============================================================
         STAFF LIST
    =============================================================== -->

    <section
      class="w-full bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden"
    >

      <!-- Table Header -->

      <div
        class="px-5 py-4 border-b border-slate-100"
      >

        <div>

          <h2
            class="text-sm font-semibold text-slate-800"
          >
            Staff List
          </h2>

          <p
            class="text-xs text-slate-400 mt-0.5"
          >
            All registered staff and administrator accounts
          </p>

        </div>

      </div>


      <!-- ==============================================================
           INITIAL LOADING
      =============================================================== -->

      <div
        v-if="staffStore.isLoading && !staffStore.data?.data?.data"
        class="p-5"
      >

        <div
          v-for="i in 6"
          :key="i"
          class="flex items-center gap-4 py-4 border-b border-slate-100 animate-pulse"
        >

          <div
            class="w-8 h-3 bg-slate-200 rounded-full"
          ></div>

          <div
            class="size-9 bg-slate-200 rounded-lg"
          ></div>

          <div
            class="w-40 h-3 bg-slate-200 rounded-full"
          ></div>

          <div
            class="flex-1 h-3 bg-slate-200 rounded-full"
          ></div>

          <div
            class="w-20 h-6 bg-slate-200 rounded-full"
          ></div>

          <div
            class="w-24 h-8 bg-slate-200 rounded-lg"
          ></div>

        </div>

      </div>


      <!-- ==============================================================
           TABLE
      =============================================================== -->

      <div
        v-else-if="staffStore.data?.data?.data?.length"
        class="overflow-x-auto"
      >

        <table
          class="w-full min-w-[900px]"
        >

          <!-- Header -->

          <thead>

            <tr
              class="bg-slate-50 border-b border-slate-200"
            >

              <th
                class="w-16 px-5 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide"
              >
                #
              </th>


              <th
                class="px-4 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide"
              >
                Staff
              </th>


              <th
                class="px-4 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide"
              >
                Username
              </th>


              <th
                class="px-4 py-3.5 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide"
              >
                Role
              </th>


              <th
                class="w-36 px-5 py-3.5 text-center text-xs font-semibold text-slate-500 uppercase tracking-wide"
              >
                Action
              </th>

            </tr>

          </thead>


          <!-- Body -->

          <tbody
            class="divide-y divide-slate-100"
          >

            <tr
              v-for="(item, index) in staffStore.data?.data?.data || []"
              :key="item.id"
              class="group hover:bg-slate-50 transition-colors duration-150"
            >

              <!-- Number -->

              <td
                class="px-5 py-4 text-sm text-slate-400 font-medium"
              >

                {{
                  ((staffStore.data?.data?.current_page ?? 1) - 1) *
                    (Number(staffStore.data?.data?.per_page) || 10) +
                  index +
                  1
                }}

              </td>


              <!-- Staff -->

              <td class="px-4 py-4">

                <div
                  class="flex items-center gap-3"
                >

                  <!-- Avatar -->

                  <div
                    class="size-10 shrink-0 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-semibold text-sm"
                  >

                    {{
                      item.name_english
                        ?.charAt(0)
                        ?.toUpperCase() ?? "S"
                    }}

                  </div>


                  <!-- Name -->

                  <div class="min-w-0">

                    <p
                      class="text-sm font-semibold truncate max-w-[220px]"
                      :class="
                        item.name_english
                          ? 'text-slate-700'
                          : 'text-red-500'
                      "
                    >
                      {{ item.name_english ?? "N/A" }}
                    </p>


                    <p
                      class="text-xs font-kantumruy-pro truncate max-w-[220px]"
                      :class="
                        item.name_khmer
                          ? 'text-slate-400'
                          : 'text-red-400'
                      "
                    >
                      {{ item.name_khmer ?? "N/A" }}
                    </p>

                  </div>

                </div>

              </td>


              <!-- Username -->

              <td class="px-4 py-4">

                <div
                  class="flex items-center gap-2"
                >

                  <div
                    class="size-8 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center"
                  >
                    @
                  </div>

                  <span
                    class="text-sm text-slate-600"
                  >
                    {{ item.name_english ?? "N/A" }}
                  </span>

                </div>

              </td>


              <!-- Role -->

              <td class="px-4 py-4">

                <span
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium capitalize"
                  :class="
                    item.role === 'admin'
                      ? 'bg-purple-50 text-purple-600 border border-purple-100'
                      : 'bg-blue-50 text-blue-600 border border-blue-100'
                  "
                >

                  <span
                    class="size-1.5 rounded-full"
                    :class="
                      item.role === 'admin'
                        ? 'bg-purple-500'
                        : 'bg-blue-500'
                    "
                  ></span>

                  {{ item.role ?? "N/A" }}

                </span>

              </td>


              <!-- Actions -->

              <td class="px-5 py-4">

                <div
                  class="flex items-center justify-center gap-2"
                >

                  <!-- Reset Password -->

                  <button
                    type="button"
                    @click="handleResetPassword(item.id)"
                    title="Reset Password"
                    class="size-9 rounded-lg border border-slate-200 bg-white text-amber-500 flex items-center justify-center hover:bg-amber-50 hover:border-amber-200 hover:text-amber-600 transition-all cursor-pointer"
                  >

                    <KeyIcon class="size-4" />

                  </button>


                  <!-- Edit -->

                  <button
                    type="button"
                    @click="handleUpdate(item.id, item)"
                    title="Edit Staff"
                    class="size-9 rounded-lg border border-slate-200 bg-white text-blue-500 flex items-center justify-center hover:bg-blue-50 hover:border-blue-200 hover:text-blue-600 transition-all cursor-pointer"
                  >

                    <EditIcon class="size-4" />

                  </button>


                  <!-- Delete -->

                  <button
                    v-if="
                      item.role === 'staff' ||
                      (
                        item.role === 'admin' &&
                        item.id !== authStore.user?.id
                      )
                    "
                    type="button"
                    @click="
                      handleUpdateStatus(
                        item.id,
                        'delete',
                        'delete'
                      )
                    "
                    :disabled="
                      loadinStatus.id === item.id &&
                      loadinStatus.status === 'delete' &&
                      loadinStatus.title === 'delete' &&
                      loadinStatus.value
                    "
                    title="Delete Staff"
                    class="size-9 rounded-lg border border-slate-200 bg-white text-red-500 flex items-center justify-center hover:bg-red-50 hover:border-red-200 hover:text-red-600 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                  >

                    <SpannerIcon
                      v-if="
                        loadinStatus.id === item.id &&
                        loadinStatus.status === 'delete' &&
                        loadinStatus.title === 'delete' &&
                        loadinStatus.value
                      "
                      class="size-4 animate-spin"
                    />

                    <DeleteIcon
                      v-else
                      class="size-4"
                    />

                  </button>

                </div>

              </td>

            </tr>

          </tbody>

        </table>

      </div>


      <!-- ==============================================================
           EMPTY
      =============================================================== -->

      <div
        v-else-if="
          !staffStore.isLoading &&
          staffStore.data?.data?.data?.length === 0
        "
        class="px-5 py-16 flex flex-col items-center justify-center text-center"
      >

        <div
          class="size-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mb-4"
        >

          <SpannerIcon class="size-6" />

        </div>


        <h3
          class="text-sm font-semibold text-slate-700"
        >
          No staff found
        </h3>


        <p
          class="mt-1 text-xs text-slate-400 max-w-sm"
        >
          No staff accounts match your search.
          Try another keyword or create a new staff account.
        </p>


        <button
          type="button"
          @click="handleAddNewStudent"
          class="mt-5 inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-primary/90 transition cursor-pointer"
        >

          <AddIcon class="size-4" />

          Add Staff

        </button>

      </div>


      <!-- ==============================================================
           LOADING MORE
      =============================================================== -->

      <div
        v-if="
          staffStore.isLoading &&
          staffStore.data?.data?.data
        "
        class="w-full py-4 flex justify-center items-center border-t border-slate-100"
      >

        <div
          class="flex items-center gap-2 text-sm text-slate-400"
        >

          <LoadingIcon
            class="size-5 text-primary animate-spin"
          />

          <span>
            Loading...
          </span>

        </div>

      </div>


      <!-- ==============================================================
           PAGINATION
      =============================================================== -->

      <div
        v-if="
          staffStore.data?.data &&
          Number(staffStore.data.data.last_page) > 1
        "
        class="px-5 py-4 border-t border-slate-100 bg-slate-50/50"
      >

        <Pagination
          :current-page="
            staffStore.data?.data?.current_page ?? 1
          "
          :last-page="
            staffStore.data?.data?.last_page ?? 1
          "
          @change="pageDirect"
        />

      </div>

    </section>

  </div>
</template>