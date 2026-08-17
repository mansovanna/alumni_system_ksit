import type { MessageModel } from "~/types/message.response.model";
import type { ReponseAPIPagination } from "~/types/reponse.api.pagination.model";

export const useMessageStore = defineStore("message", {
  state: () => ({
    data: null as ReponseAPIPagination<MessageModel> | null,
    isLoading: false,
    search: "",
    perPage: 15,
    page: 1,
  }),
  actions: {
    async getMessageAll() {
      const { $api } = useNuxtApp();

      this.isLoading = true;

      try {
        const res = await $api.get("/admin/message", {
          params: {
            search: this.search,
            per_page: this.perPage,
            page: this.page,
          },
        });

        this.data = res.data;
      } catch (e: any) {
        console.log(e);
      } finally {
        this.isLoading = false;
      }
    },

    addMessage(data: FormData) {
      const { $api } = useNuxtApp();
      return $api.post("/admin/message", data);
    },

    updateMessage(id: number, data: FormData) {
      const { $api } = useNuxtApp();

      return $api.put(`/admin/message/${id}`, data, {
        headers: {
          "Content-Type": "application/json",
        },
      });
    },

    deleteMessage(id: number) {
      const { $api } = useNuxtApp();

      return $api.delete(`/admin/message/${id}`);
    },
  },
});
