<script setup>
import AddIcon from "~/components/icons/AddIcon.vue";
import ArrowIcon from "~/components/icons/ArrowIcon.vue";
import DeleteIcon from "~/components/icons/DeleteIcon.vue";
import EditIcon from "~/components/icons/EditIcon.vue";
import KeyIcon from "~/components/icons/KeyIcon.vue";

import SearchIcon from "~/components/icons/SearchIcon.vue";

definePageMeta({
  layout: "admin",
});

const formAddNew = ref(false);

const handleAddNewStudent = () => (formAddNew.value = !formAddNew.value);
const formUpdate = ref(false);

const handleUpdate = (id, name) => {
  if (id) {
    formUpdate.value = true;
  }
};

const handleUpdateNew = (data) => {
  console.log(data);
};

const user_id = ref(0);
const isFormReset = ref(false);
const handleResetPassword = (id) => {
  if (id) {
    user_id.value = id;
    isFormReset.value = true;
  }
};

const handlePayloadRestPass =(data)=>{
    console.log(data)
}
</script>

<template>
  <!--  -->
  <AdminsStaffAdd v-if="formAddNew" @close="formAddNew = false" />
  <AdminsStaffUpdate
    v-if="formUpdate"
    :id="1"
    :data="{
      nameKhmer: 'អ្នកគ្រប់គ្រងប្រព័ន្ធ',
      nameEnglish: 'admin',
      role: 'admin',
    }"
    @close="formUpdate = false"
    @submit="handleUpdateNew"
  />
  <AdminsStaffResetPassword v-if="isFormReset" :id="user_id" @close="isFormReset = false" @submit="handlePayloadRestPass"/>
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
    <div class="w-full">
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
          <tr v-for="index in 5" class="hover:bg-slate-100">
            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              {{ index }}
            </th>
            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              Manager System
            </th>

            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              Admin
            </th>

            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              Admin
            </th>

            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              <div class="flex justify-end items-center gap-2">
                <button
                  @click="handleResetPassword(index)"
                  class="size-6.5 border text-blue-500 border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 cursor-pointer"
                >
                  <KeyIcon />
                </button>

                <button
                  @click="handleUpdate(index, 'Computer of Technology')"
                  class="size-6.5 border text-blue-500 border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 cursor-pointer"
                >
                  <EditIcon />
                </button>

                <button
                  class="size-6.5 border text-red-500 border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 cursor-pointer"
                >
                  <DeleteIcon />
                </button>
              </div>
            </th>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Block Pagination -->
    <div class="w-full flex justify-center items-center">
      <div
        class="w-1/5 p-1 bg-white rounded-full border border-slate-300 flex justify-between items-center gap-2"
      >
        <button
          class="size-8 bg-slate-100 rounded-full border hover:bg-slate-200 cursor-pointer border-slate-200 flex justify-center items-center"
        >
          <ArrowIcon class="rotate-180" />
        </button>

        <!-- -------------------- -->
        <div class="flex justify-center items-center gap-2">
          <button
            class="size-8 bg-slate-100 text-xs rounded-full border hover:bg-slate-200 cursor-pointer border-slate-200 flex justify-center items-center"
          >
            1
          </button>
          <button
            class="size-8 bg-info text-white hover:text-info text-xs rounded-full border hover:bg-slate-200 cursor-pointer border-slate-200 flex justify-center items-center"
          >
            2
          </button>
          <button
            class="size-8 bg-info text-white hover:text-info text-xs rounded-full border hover:bg-slate-200 cursor-pointer border-slate-200 flex justify-center items-center"
          >
            3
          </button>
          <div>...</div>

          <button
            class="size-8 bg-info text-white hover:text-info text-xs rounded-full border hover:bg-slate-200 cursor-pointer border-slate-200 flex justify-center items-center"
          >
            7
          </button>
        </div>
        <!-- --------------------- -->

        <button
          class="size-8 bg-slate-100 rounded-full border hover:bg-slate-200 cursor-pointer border-slate-200 flex justify-center items-center"
        >
          <ArrowIcon class="rotate-0" />
        </button>
      </div>
    </div>
  </div>
</template>
