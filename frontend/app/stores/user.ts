import type { ReponseAPIPagination } from "~/types/reponse.api.pagination.model";
import type { UserModel } from "~/types/user.model";

export const useUserStore = defineStore("user", {
  state: () => ({
    data: null as ReponseAPIPagination<UserModel> | null,

    isLoading: false,
    search: "",
    perPage: 15,
    page: 1,
    major: {
      id: 0,
      title: null,
    },
    status_workd: {
      id: 0,
      title: null,
      status: null,
    },
  }),
  getters: {
    loading: (state) => state.isLoading,
  },
  actions: {
    async getUuser(
      search: string = "",
      perPage: number = 15,
      page: number = 1,
      major: number = 0,
      status: string = "",
    ) {
      const { $api } = useNuxtApp();

      if (search !== undefined) this.search = search;
      if (perPage !== undefined) this.perPage = perPage;
      if (page !== undefined) this.page = page;
      this.isLoading = true;
      try {
        const res = await $api.get("/admin/user", {
          params: {
            search: search,
            per_page: perPage,
            page: page,
            major: major,
            work: status,
          },
        });

        this.data = res.data;
        // console.log(this.data);
      } catch (e) {
        console.log(e);
      } finally {
        this.isLoading = false;
      }
    },

    addUser(data: FormData) {
      const { $api } = useNuxtApp();
      return $api.post("/admin/user", data, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
    },
    updateUser(data: FormData, id: number) {
      const { $api } = useNuxtApp();
      return $api.put(`/admin/user/${id}`, data, {
        headers: {
          "Content-Type": "application/json",
        },
      });
    },

    updateStatus(id: number, status: string) {
      const { $api } = useNuxtApp();

      return $api.put(
        `/admin/user-status/${id}`,
        { status: status },
        {
          headers: {
            "Content-Type": "application/json",
          },
        },
      );
    },

    deleteUser(id: number) {
      const { $api } = useNuxtApp();

      return $api.delete(`/admin/user/${id}`);
    },
  },
});
