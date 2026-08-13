import type { ReponseAPIPagination } from "~/types/reponse.api.pagination.model";
import type { UserModel } from "~/types/user.model";

export const useStaffStore = defineStore("staff", {
  state: () => ({
    data: null as ReponseAPIPagination<UserModel> | null,
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
        const res = await $api.get("/admin/admin", {
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

      return $api.post("/admin/admin", data);
    },

    updateStaff(id: number, data: FormData) {
      const { $api } = useNuxtApp();

      return $api.put(`/admin/admin/${id}`, data);
    },

    changePassword(id: number, data: FormData) {
      const { $api } = useNuxtApp();
      return $api.put(`/reset-password/${id}`, data);
    },

    deleteStaff(id: number) {
      const { $api } = useNuxtApp();

      return $api.delete(`/admin/admin/${id}`);
    },
  },
});
