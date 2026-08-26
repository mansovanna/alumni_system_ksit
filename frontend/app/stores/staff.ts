import type { StaffResponseModel } from "~/types/staff.response.model";
import type { UserModel } from "~/types/user.model";

export const useStaffStore = defineStore("staff", {
  state: () => ({
    data: null as StaffResponseModel | null,
    loading: false,
    search: "",
    perPage: 15,
    page: 1,
  }),

  getters: {
    isLoading: (state) => state.loading,
  },
  actions: {
    async getStaffs() {
      //
      const { $api } = useNuxtApp();
      this.loading = true;

      try {
        const res = await $api.get("/admin/staff", {
          params: {
            search: this.search,
            per_page: this.perPage,
            page: this.page,
          },
        });

        this.data = res.data;
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },

    addStaff(data: any) {
      const { $api } = useNuxtApp();

      return $api.post("/admin/staff", data);
    },

    updateStaff(id: number, data: FormData) {
      const { $api } = useNuxtApp();

      return $api.put(`/admin/staff/${id}`, data);
    },

    changePassword(id: number, data: FormData) {
      const { $api } = useNuxtApp();
      return $api.put(`/admin/reset-password/${id}`, data);
    },

    deleteStaff(id: number) {
      const { $api } = useNuxtApp();

      return $api.delete(`/admin/delete-user/${id}`);
    },
  },
});
