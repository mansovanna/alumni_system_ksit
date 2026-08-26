import { defineStore } from "pinia";
import type { EventResponseIndex } from "~/types/index/event_index";

// ======================================================
// EVENT ITEM
// ======================================================

interface EventItem {
  id: number;
  created_by: number;
  title: string;
  description: string;
  event_type: string;

  banner_image: string | null;
  banner_image_url: string | null;

  location: string | null;

  start_date: string;
  end_date: string;

  registration_deadline: string | null;
  max_participants: number | null;

  external_link: string | null;

  status: string;

  created_at: string;
  updated_at: string;

  is_registered: boolean;
}

// ======================================================
// PAGINATION
// ======================================================

interface Pagination {
  current_page: number;
  per_page: number;
  total: number;
  last_page: number;

  from: number | null;
  to: number | null;

  has_more: boolean;
}

// ======================================================
// API RESPONSE
// ======================================================

interface EventResponse {
  success: boolean;
  message: string;

  data: EventItem[];

  pagination: Pagination;
}

// ======================================================
// FILTER TYPE
// ======================================================

type EventFilter = "upcoming" | "past" | "all";

// ======================================================
// STORE
// ======================================================

export const useEventAluniStore = defineStore("alumniEventStore", {
  // ====================================================
  // STATE
  // ====================================================

  state: () => ({
    data: null as EventResponse | null,
    data_Detail: null as EventResponseIndex | null,
    isLoading: false,
    isLoadingMore: false,

    messageError: "",

    // Backend filter
    filter: "upcoming" as EventFilter,

    // IMPORTANT:
    // Backend expects per_page
    perPage: 6,

    // Registration loading
    isLoadingRegister: {
      id: 0,
      value: false,
    },
  }),

  // ====================================================
  // GETTERS
  // ====================================================

  getters: {
    // --------------------------------------------------
    // Event List
    // --------------------------------------------------

    eventList: (state): EventItem[] => {
      return state.data?.data ?? [];
    },

    // --------------------------------------------------
    // Pagination
    // --------------------------------------------------

    pagination: (state): Pagination | null => {
      return state.data?.pagination ?? null;
    },

    // --------------------------------------------------
    // Has More
    // --------------------------------------------------

    hasMore: (state): boolean => {
      return state.data?.pagination?.has_more ?? false;
    },

    // --------------------------------------------------
    // Current Page
    // --------------------------------------------------

    currentPage: (state): number => {
      return state.data?.pagination?.current_page ?? 1;
    },

    // --------------------------------------------------
    // Total
    // --------------------------------------------------

    total: (state): number => {
      return state.data?.pagination?.total ?? 0;
    },
  },

  // ====================================================
  // ACTIONS
  // ====================================================

  actions: {
    // ==================================================
    // GET EVENTS
    // ==================================================

    async getEvents(filter?: EventFilter) {
      const { $api } = useNuxtApp();

      const selectedFilter = filter ?? this.filter;

      this.isLoading = true;
      this.messageError = "";

      // Update current filter
      this.filter = selectedFilter;

      try {
        const res = await $api.get<EventResponse>("/alumnis/events", {
          params: {
            filter: this.filter,

            // Laravel backend:
            // $request->input('per_page', 6)
            per_page: this.perPage,

            page: 1,
          },
        });

        console.log("Get Events:", res.data);

        this.data = res.data;
      } catch (e: any) {
        console.error("Get Events Error:", e);

        this.messageError =
          e?.response?.data?.message ?? "Failed to load events.";
      } finally {
        this.isLoading = false;
      }
    },

    // ==================================================
    // CHANGE FILTER
    // ==================================================

    async changeFilter(filter: EventFilter) {
      /*
      |--------------------------------------------------------------------------
      | If same filter and data already exists
      |--------------------------------------------------------------------------
      */

      if (this.filter === filter && this.data !== null) {
        return;
      }

      /*
      |--------------------------------------------------------------------------
      | Get new data from backend
      |--------------------------------------------------------------------------
      */

      await this.getEvents(filter);
    },

    // ==================================================
    // LOAD MORE
    // ==================================================

    async loadMore() {
      /*
      |--------------------------------------------------------------------------
      | Prevent duplicate request
      |--------------------------------------------------------------------------
      */

      if (this.isLoadingMore) {
        return;
      }

      if (this.isLoading) {
        return;
      }

      if (!this.hasMore) {
        return;
      }

      const { $api } = useNuxtApp();

      const currentPage = this.data?.pagination?.current_page ?? 1;

      const nextPage = currentPage + 1;

      this.isLoadingMore = true;
      this.messageError = "";

      try {
        const res = await $api.get<EventResponse>("/alumnis/events", {
          params: {
            filter: this.filter,
            per_page: this.perPage,
            page: nextPage,
          },
        });

        console.log("Load More Events:", res.data);

        const newEvents = res.data?.data ?? [];

        /*
        |--------------------------------------------------------------------------
        | Append new events
        |--------------------------------------------------------------------------
        */

        if (this.data) {
          this.data = {
            ...this.data,

            data: [...this.data.data, ...newEvents],

            pagination: res.data.pagination,
          };
        } else {
          this.data = res.data;
        }
      } catch (e: any) {
        console.error("Load More Events Error:", e);

        this.messageError =
          e?.response?.data?.message ?? "Failed to load more events.";
      } finally {
        this.isLoadingMore = false;
      }
    },

    // ==================================================
    // REGISTRATION
    // ==================================================

    async registration(id: number) {
      const { $api } = useNuxtApp();

      /*
      |--------------------------------------------------------------------------
      | Loading
      |--------------------------------------------------------------------------
      */

      this.isLoadingRegister.id = id;
      this.isLoadingRegister.value = true;

      this.messageError = "";

      try {
        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        | Backend prefix = alumnis
        |--------------------------------------------------------------------------
        */

        const res = await $api.post(`/alumnis/events/${id}/register`);

        console.log("Registration:", res.data);

        /*
        |--------------------------------------------------------------------------
        | Update UI immediately
        |--------------------------------------------------------------------------
        */

        const event = this.data?.data.find((item) => item.id === id);

        if (event) {
          event.is_registered = true;
        }

        return res.data;
      } catch (e: any) {
        console.error("Registration Error:", e);

        this.messageError =
          e?.response?.data?.message ?? "Failed to register for this event.";

        throw e;
      } finally {
        this.isLoadingRegister.id = 0;
        this.isLoadingRegister.value = false;
      }
    },

    // ==================================================
    // DETAILFT
    // ==================================================
    async getDetailt(id: number) {
      const { $api } = useNuxtApp();

      this.isLoading = true;
      this.messageError = "";

      try {
        const res = await $api.get<EventResponseIndex>(`/alumnis/events/${id}`);

        this.data_Detail = res.data;
      } catch (e: any) {
        this.data_Detail = null;

        this.messageError =
          e.response?.data?.message || "Failed to retrieve event.";
      } finally {
        this.isLoading = false;
      }
    },

    // ==================================================
    // RESET
    // ==================================================

    reset() {
      this.data = null;

      this.filter = "upcoming";

      this.isLoading = false;
      this.isLoadingMore = false;

      this.messageError = "";

      this.isLoadingRegister = {
        id: 0,
        value: false,
      };
    },
  },
});
