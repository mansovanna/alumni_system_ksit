import type { AlumniDirectoryResponse } from "~/types/index/directory";

export const useDirectoryStore = defineStore("directory", {
  state: () => ({
    data: null as AlumniDirectoryResponse | null,
    isLoading: false,
    isLoadingMore: false,
    year: "",
    major_id: "",
    search: "",
    perPage: 15,
    messageError: "",
  }),

  getters: {
    alumniList: (state) => state.data?.data.alumni.data ?? [],
    hasMore: (state) => state.data?.data.alumni.next_page_url !== null,
  },

  actions: {
    async getDirectory(
      filter: { search?: string; year?: string; major_id?: string } = {},
    ) {
      const { $api } = useNuxtApp();

      this.search = filter.search ?? "";
      this.year = filter.year ?? "";
      this.major_id = filter.major_id ?? "";
      this.isLoading = true;

      try {
        const res = await $api.get("/alumnis/directory", {
          params: {
            search: this.search,
            year: this.year,
            major_id: this.major_id,
            page: 1,
            perPage: this.perPage,
          },
        });

        this.data = res.data;
      } catch (e: any) {
        console.log(e);
        this.messageError = e.response?.data;
      } finally {
        this.isLoading = false;
      }
    },

    async loadMore() {
      const current = this.data?.data.alumni;
      if (this.isLoadingMore || this.isLoading || !current?.next_page_url) return;

      const { $api } = useNuxtApp();
      this.isLoadingMore = true;
      const nextPage = current.current_page + 1;

      try {
        const res = await $api.get("/alumnis/directory", {
          params: {
            search: this.search,
            year: this.year,
            major_id: this.major_id,
            page: nextPage,
            perPage: this.perPage,
          },
        });

        const newAlumni = res.data?.data?.alumni;
        if (newAlumni && this.data) {
          this.data.data.alumni = {
            ...newAlumni,
            data: [...this.data.data.alumni.data, ...newAlumni.data],
          };
        }
      } catch (e: any) {
        console.log(e);
        this.messageError = e.response?.data;
      } finally {
        this.isLoadingMore = false;
      }
    },
  },
});