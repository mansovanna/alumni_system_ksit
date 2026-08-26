import type { DashboardOverviewResponse } from "~/types/dashboard";

export const useDashStore = defineStore("dash", {
  state: () => ({
    data: null as DashboardOverviewResponse | null,
    isLoading: false,
    isLoadingByYear: false,
    error: null as unknown,
  }),

  getters: {
    chartWorks: (state) => state.data?.data?.summary ?? {},

    chartYears: (state) =>
      state.data?.data?.employment_by_year ?? [],

    employmentTrend: (state) =>
      state.data?.data?.employment_rate_trend ?? [],
  },

  actions: {
    async getDash(
      filters = {
        year: "all",
        major: "all",
      },
    ) {
      const { $api } = useNuxtApp();

      this.isLoading = true;
      this.isLoadingByYear = true;
      this.error = null;

      try {
        const res = await $api.get("/dashboard", {
          params: filters,
        });

        // IMPORTANT:
        // API response = { success, message, data }
        this.data = res.data;

        console.log("Dashboard API:", res.data);
      } catch (e: any) {
        this.error = e;

        console.error(
          "Dashboard API Error:",
          e?.response?.data ?? e,
        );
      } finally {
        this.isLoading = false;
        this.isLoadingByYear = false;
      }
    },
  },
});