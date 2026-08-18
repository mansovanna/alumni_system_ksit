import type { MajorModel, MajorResponseAPI } from "~/types/major";
import type { ReponseAPIPagination } from "~/types/reponse.api.pagination.model";

export const useMajorStore = defineStore("major", {
  state: () => ({
    data: null as MajorResponseAPI | null,
    majors: null as ReponseAPIPagination<MajorModel> | null,
    isLoading: false,
    search: "",
    perPage: 15,
    page: 1,
  }),

  actions: {
    async getMajorAll() {
      const { $api } = useNuxtApp();

      try {
        const res = await $api.get("/depament-major");
        this.data = res.data;
        // console.log(this.data)
      } catch (e) {
        console.log(e);
      }
    },

    async getMajors() {
      const { $api } = useNuxtApp();
      this.isLoading = true;
      try {
        const res = $api.get("/admin/major", {
          params: {
            search: this.search,
            per_page: this.perPage,
            page: this.page,
          },
        });
        this.majors = (await res).data;
      } catch (e) {
        console.log(e);
      } finally {
        this.isLoading = false;
      }
    },

    addMajor(data: FormData) {
      const { $api } = useNuxtApp();
      return $api.post("/admin/major", data);
    },

    updateMajor(id: number, data: FormData) {
      const { $api } = useNuxtApp();
      return $api.put(`/admin/major/${id}`, data, {
        headers: {
          "Content-Type": "application/json",
        },
      });
    },

    deleteMajor(id: number) {
      const { $api } = useNuxtApp();
      return $api.delete(`/admin/major/${id}`);
    },
  },
});
