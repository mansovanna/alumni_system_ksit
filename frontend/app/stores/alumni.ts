import { defineStore } from "pinia";
import type { AlumniModels } from "~/types/alumni.model";
import type { PaginationModel } from "~/types/pagination.model";

export const useAlumniStore = defineStore("alumni", {
  state: () => ({
    data: null as PaginationModel<AlumniModels> | null,
    alumniProfile: null as AlumniModels | null,
    isLoading: false,
    search: "",
    per_page: 20,
    page: 1,
    selectedWorkStatus: "",
    selectedMajor_id: "",
  }),

  actions: {
    async getAlumni(
      filter = {
        search: "",
        per_page: 20,
        major_id: "",
        employment_status: "",
        page: 1,
      },
    ) {
      const { $api } = useNuxtApp();

      this.isLoading = true;
      try {
        const res = await $api.get("/alumni", {
          params: filter,
        });

        this.data = res.data.data;
      } catch (e) {
        console.log(e);
      } finally {
        this.isLoading = false;
      }
    },

    updateStatusAlumni(id: number, data: FormData) {
      const { $api } = useNuxtApp();

      return $api.put(`/alumni-update-state/${id}`, data, {
        headers: {
          "Content-Type": "application/json",
        },
      });
    },

    async getAlumniProfile(id: number) {
      const { $api } = useNuxtApp();
      //  ----------------------------------------
      this.isLoading = true;
      try {
        const res = await $api.get(`/alumni/${id}`);

        this.alumniProfile = res.data.data;
      } catch (e) {
        console.log(e);
      } finally {
        this.isLoading = false;
      }
    },

    deleteAlumni(id: number) {
      const { $api } = useNuxtApp();

      return $api.delete(`/alumni/${id}`, {
        headers: {
          "Content-Type": "application/json",
        },
      });
    },
  },
});
