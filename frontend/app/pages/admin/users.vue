<script setup>
import AddIcon from "~/components/icons/AddIcon.vue";
import ArrowIcon from "~/components/icons/ArrowIcon.vue";
import CheckIcon from "~/components/icons/CheckIcon.vue";
import CloseIcon from "~/components/icons/CloseIcon.vue";
import DeleteIcon from "~/components/icons/DeleteIcon.vue";
import EditIcon from "~/components/icons/EditIcon.vue";
import EyeIcon from "~/components/icons/EyeIcon.vue";
import SearchIcon from "~/components/icons/SearchIcon.vue";

definePageMeta({
  layout: "admin",
});

const years = reactive([
  "2026",
  "2025",
  "2024",
  "2023",
  "2022",
  "2021",
  "2020",
  "2019",
  "2018",
]);

const selectedFilter = ref(null);

const majors = reactive([
  "computer of technology",
  "food of technology",
  "crope sciences",
  "animal sciences",
  "electrical of technology",
  "9+3",
]);

const selectedMajor = ref(null);

const works = reactive([
  "មានការងារធ្វើ",
  "កំពុងស្វែងរកការងារធ្វើ",
  "សិក្សាបន្ដ",
  "មិនទាន់បានការងារធ្វើ",
]);

const selectedWorks = ref(null);

const formAddNew = ref(false);

const handleAddNewStudent = () => (formAddNew.value = !formAddNew.value);

const formUpdate = ref(false)

const handleUpdate =(data)=>{
  if(data){
    formUpdate.value = true
  }
}


const profileUser = ref({})
const isProfile = ref(false)

const handleProfile = (data)=>{
  if(data){
    profileUser.value = data
    isProfile.value = true
  }
}
</script>

<template>
  <!--  -->
  <AdminsPopupAddNewUser v-if="formAddNew" @close="formAddNew = false" />
  <AdminsPopupUpdateUser v-if="formUpdate" @close="formUpdate = false"/>
  <AdminsPopupProfileUser v-if="isProfile" @close="isProfile = false" :data="profileUser" />
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
      <div class="flex justify-end items-center gap-4">
        <!-- Block option 1 -->
        <div class="min-w-30">
          <OptionsOption
            :items="years"
            :active="selectedFilter"
            :title="'ឆ្នាំទាំងអស់'"
            @update:active="selectedFilter = $event"
          />
        </div>
        <div class="min-w-55 w-full">
          <OptionsOption
            :items="majors"
            :active="selectedMajor"
            :title="'ជំនាញ់់ទាំងអស់'"
            @update:active="selectedMajor = $event"
          />
        </div>
        <div class="min-w-46 w-full">
          <OptionsOption
            :items="works"
            :active="selectedWorks"
            :title="'ស្ថានភាពការងារទាំងអស់'"
            @update:active="selectedWorks = $event"
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
    <div class="w-full">
      <table
        class="w-full bg-white font-Inter rounded-xl overflow-clip border border-slate-400 shadow shadow-slate-500/5"
      >
        <thead class="w-full font-normal">
          <tr class="bg-ab">
            <th class="font-normal px-4 py-3 text-sm text-slate-600 text-left">
              No
            </th>
            <th class="font-normal px-4 py-3 text-sm text-slate-600 text-left">
              User Name
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

        <tbody>
          <tr v-for="index in 100" class="hover:bg-slate-100">
            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              {{ index }}
            </th>
            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              Sovanna Man
            </th>
            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              បច្ចេកវិទ្យាកុំព្យូទ័រ
            </th>
            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              2026
            </th>
            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              មិនទាន់មានការធ្វើទេ
            </th>
            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              កំពុងរងចាំ
            </th>
            <th
              class="font-normal px-4 py-1.5 text-sm text-slate-600 text-left"
            >
              <div class="flex justify-end items-center gap-2">
                <button
                  class="size-6.5 border text-success border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 cursor-pointer"
                >
                  <CheckIcon />
                </button>
                <button
                  class="size-6.5 border text-red-500 border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 cursor-pointer"
                >
                  <CloseIcon />
                </button>
                <button
                  @click="handleProfile(index)"
                  class="size-6.5 border text-orange-500 border-slate-200 flex justify-center items-center rounded-md bg-slate-50 hover:bg-slate-100 cursor-pointer"
                >
                  <EyeIcon />
                </button>

                <button
                @click="handleUpdate(index)"
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
