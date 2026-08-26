import type { MessageModel } from "~/types/message.response.model";
import type { NotificationADResponseAPI } from "~/types/notification";

export const useMessageStore = defineStore("message", {
  state: () => ({
    data: null as NotificationADResponseAPI | null,
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
        const res = await $api.get("/messages", {
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

    getMessageById(id: number){

      const { $api } = useNuxtApp();

       return $api.get(`/messages/${id}`, )
    },

    addMessage(data: FormData) {
      const { $api } = useNuxtApp();
      return $api.post("/messages", data);
    },

    updateMessage(id: number, data: FormData) {
      const { $api } = useNuxtApp();

      return $api.put(`/messages/${id}`, data, {
        headers: {
          "Content-Type": "application/json",
        },
      });
    },

    deleteMessage(id: number) {
      const { $api } = useNuxtApp();

      return $api.delete(`/messages/${id}`);
    },
  },
});
