import type { AlumniModels } from "~/types/alumni.model";

export const useAlumniProfileStore = defineStore("alumni-profile", {
  state: () => ({
    data: null as AlumniModels | null,
    isLoading: false,
    messageError: "",
  }),

  actions: {
    async getProfileID(id: number) {
      const { $api } = useNuxtApp();
      this.isLoading = true;
      try {
        const res = await $api.get(`/alumnis/profile-info/${id}`);
        // API wraps the payload as { message, data }
        this.data = res.data.data;
      } catch (e: any) {
        this.messageError = e.response?.data;
      } finally {
        this.isLoading = false;
      }
    },

    // NOTE: no update endpoint exists yet — this is a stub matching the
    // GET route's pattern. Confirm the real path/method before using it.
    async updateProfile(id: number, payload: Partial<AlumniModels>) {
      const { $api } = useNuxtApp();
      this.isLoading = true;
      try {
        const res = await $api.put(`/alumnis/profile-info/${id}`, payload);
        this.data = res.data.data;
        return true;
      } catch (e: any) {
        this.messageError = e.response?.data;
        return false;
      } finally {
        this.isLoading = false;
      }
    },

    async updateCareer(data: any) {
      const { $api } = useNuxtApp();

      return $api.post("/alumnis/profile-career", data);
    },
  },
});
