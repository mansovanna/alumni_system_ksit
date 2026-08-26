import { defineStore } from "pinia";

import type {
  EventListResponseAPI,
  EventDetailResponseAPI,
  EventItem,
} from "~/types/event";

export const useEventStore = defineStore("event", {
  state: () => ({
    data: null as EventListResponseAPI | null,
    isLoading: false,
    isSubmitting: false,

    status: "" as string,
    search: "",
    page: 1,
  }),

  actions: {
    /* ------------------- List ------------------- */

    async getEventAll() {
      this.isLoading = true;

      const { $api } = useNuxtApp();

      try {
        const res = await $api.get("/events", {
          params: {
            status: this.status || undefined,
            search: this.search || undefined,
            page: this.page,
          },
        });

        this.data = res.data;
        return res;
      } finally {
        this.isLoading = false;
      }
    },

    /* ------------------- Show ------------------- */

    async getEventById(id: number) {
      const { $api } = useNuxtApp();

      const res = await $api.get<EventDetailResponseAPI>(`/events/${id}`);

      return res.data;
    },

    /* ------------------- Create ------------------- */

    async addEvent(formData: FormData) {
      const { $api } = useNuxtApp();

      this.isSubmitting = true;

      try {
        const res = await $api.post("/events", formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });

        // Add new event to current list
        if (this.data?.data) {
          this.data.data.data.unshift(res.data.data ?? res.data);
        }

        return res;
      } finally {
        this.isSubmitting = false;
      }
    },

    /* ------------------- Update ------------------- */

    async updateEvent(id: number, formData: FormData) {
      const { $api } = useNuxtApp();

      this.isSubmitting = true;

      try {
        // Laravel method spoofing for multipart/form-data
        formData.append("_method", "PUT");

        const res = await $api.post(`/events/${id}`, formData, {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        });

        // Update item in current list
        const updatedEvent = res.data.data ?? res.data;

        this.patchEvent(id, updatedEvent);

        return res;
      } finally {
        this.isSubmitting = false;
      }
    },

    /* ------------------- Delete ------------------- */

    async deleteEvent(id: number) {
      const { $api } = useNuxtApp();

      try {
        const res = await $api.delete(`/events/${id}`);

        // Remove deleted event from current list
        if (this.data?.data) {
          this.data.data.data = this.data.data.data.filter(
            (event: EventItem) => event.id !== id,
          );
        }

        return res;
      } finally {
        // Optional
      }
    },

    /* ------------------- Helper ------------------- */

    patchEvent(id: number, updated: EventItem) {
      if (!this.data?.data) return;

      const index = this.data.data.data.findIndex(
        (event: EventItem) => event.id === id,
      );

      if (index !== -1) {
        this.data.data.data[index] = updated;
      }
    },
  },
});
