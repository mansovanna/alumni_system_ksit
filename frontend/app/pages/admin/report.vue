<script setup lang="ts">
definePageMeta({
  layout: "admin",
});

import { ref, reactive, computed, watch, onMounted } from "vue";

interface User {
  id: number;
  name: string;
  mobile: string;
}

const formData = reactive({
  name: "",
  mobile: "",
});

const users = ref<User[]>([]);
const search = ref("");

const isEdit = ref(false);
const editId = ref<number |null>(null);

// =======================
// Add / Update
// =======================
const submit = () => {
  if (!formData.name.trim()) {
    alert("Please enter name");
    return;
  }

  if (!formData.mobile.trim()) {
    alert("Please enter mobile");
    return;
  }

  if (isEdit.value) {
    const index = users.value.findIndex(
      (item) => item.id === editId.value
    );

    if (index !== -1) {
      users.value[index] = {
        id: editId.value!,
        name: formData.name,
        mobile: formData.mobile,
      };
    }

    isEdit.value = false;
    editId.value = null;
  } else {
    users.value.push({
      id: Date.now(),
      name: formData.name,
      mobile: formData.mobile,
    });
  }

  resetForm();
};

// =======================
// Edit
// =======================
const editUser = (user: User) => {
  formData.name = user.name;
  formData.mobile = user.mobile;

  editId.value = user.id;
  isEdit.value = true;
};

// =======================
// Delete
// =======================
const deleteUser = (id: number) => {
  if (!confirm("Delete this user?")) return;

  users.value = users.value.filter(
    (item) => item.id !== id
  );
};

// =======================
// Reset
// =======================
const resetForm = () => {
  formData.name = "";
  formData.mobile = "";
};

// =======================
// Search
// =======================
const filteredUsers = computed(() => {
  return users.value.filter((user) => {
    return (
      user.name.toLowerCase().includes(search.value.toLowerCase()) ||
      user.mobile.includes(search.value)
    );
  });
});

// =======================
// LocalStorage
// =======================
onMounted(() => {
  const storage = localStorage.getItem("users");

  if (storage) {
    users.value = JSON.parse(storage);
  }

  var name = 'Sovanna';

   name = '30'

  console.log(name)

  let age = 100
  age = 200
  console.log(age)

  const major = ['app']

  major[0] = 'Desk'
  console.log(major)

});

watch(
  users,
  () => {
    localStorage.setItem(
      "users",
      JSON.stringify(users.value)
    );
  },
  {
    deep: true,
  }
);
</script>

<template>
  <div class="max-w-5xl mx-auto p-10">

    <h1 class="text-3xl font-bold mb-8">
      User CRUD
    </h1>

    <!-- Form -->
    <form
      @submit.prevent="submit"
      class="bg-white shadow rounded-lg p-6 mb-8"
    >
      <div class="grid md:grid-cols-2 gap-4">

        <div>
          <label class="font-medium">
            Name
          </label>

          <input
            v-model="formData.name"
            type="text"
            placeholder="Enter name"
            class="w-full border rounded-md p-3 mt-2"
          />
        </div>

        <div>
          <label class="font-medium">
            Mobile
          </label>

          <input
            v-model="formData.mobile"
            type="text"
            placeholder="Enter mobile"
            class="w-full border rounded-md p-3 mt-2"
          />
        </div>

      </div>

      <div class="flex gap-3 mt-6">

        <button
          type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md"
        >
          {{ isEdit ? "Update" : "Add" }}
        </button>

        <button
          type="button"
          @click="resetForm"
          class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md"
        >
          Clear
        </button>

      </div>

    </form>

    <!-- Search -->
    <div class="mb-5">
      <input
        v-model="search"
        placeholder="Search..."
        class="w-full border rounded-md p-3"
      />
    </div>

    <!-- Table -->
    <div class="overflow-x-auto bg-white shadow rounded-lg">

      <table class="w-full">

        <thead class="bg-slate-100">

          <tr>

            <th class="p-3 text-left">
              ID
            </th>

            <th class="p-3 text-left">
              Name
            </th>

            <th class="p-3 text-left">
              Mobile
            </th>

            <th class="p-3 text-center">
              Action
            </th>

          </tr>

        </thead>

        <tbody>

          <tr
            v-for="user in filteredUsers"
            :key="user.id"
            class="border-b"
          >

            <td class="p-3">
              {{ user.id }}
            </td>

            <td class="p-3">
              {{ user.name }}
            </td>

            <td class="p-3">
              {{ user.mobile }}
            </td>

            <td class="p-3">

              <div class="flex justify-center gap-3">

                <button
                  @click="editUser(user)"
                  class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-1 rounded"
                >
                  Edit
                </button>

                <button
                  @click="deleteUser(user.id)"
                  class="bg-red-600 hover:bg-red-700 text-white px-4 py-1 rounded"
                >
                  Delete
                </button>

              </div>

            </td>

          </tr>

          <tr v-if="filteredUsers.length === 0">

            <td
              colspan="4"
              class="text-center p-6 text-gray-500"
            >
              No Data Found
            </td>

          </tr>

        </tbody>

      </table>

    </div>

  </div>
</template>