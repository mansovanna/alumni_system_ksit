import type { EventModel } from "~/types/event.response.model";
import type { ReponseAPIPagination } from "~/types/reponse.api.pagination.model";

export const useEventStore = defineStore("event", {
  state: () => ({
    data: null as ReponseAPIPagination<EventModel> | null,
    isLoading: false,
    search: "",
    perPage: 15,
    page: 1,
  }),
  actions: {
    async getEvent() {
      const { $api } = useNuxtApp();
      this.isLoading = true;
      try {
        const res = await $api.get("/admin/event", {
          params: {
            search: this.search,
            per_page: this.page,
            page: this.page,
          },
        });

        this.data = res.data;
      } catch (e) {
        console.log(e);
      } finally {
        this.isLoading = false;
      }
    },
  },
});
