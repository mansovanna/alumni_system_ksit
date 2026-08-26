import type { ChartByWorkModel } from "~/types/chartByWorkStatus";
import type { ChartByYearModel } from "~/types/chartByYear";
import type { DashBoard } from "~/types/dashboard";

export const useDashStore = defineStore("dash", {
  state: () => ({
    data: null as DashBoard | null,
    chartWorks: null as ChartByWorkModel | null,
    chartYears: null as ChartByYearModel | null,
    isLoading: false,
    isLoadingByWork: false,
    isLoadingByYear: false,
  }),
  getters: {
    loading: (state) => state.isLoading,
  },

  actions: {
    async getDash() {
      const { $api } = useNuxtApp();
      this.isLoading = true;

      try {
        const res = await $api.get("/admin/dashboard");

        // console.log(res);
        this.data = res.data.data;
      } catch (e) {
        console.log(e);
      } finally {
        this.isLoading = false;
      }
    },

    async chartByWorkStatus() {
      const { $api } = useNuxtApp();
      this.isLoadingByWork = true;
      try {
        const res = await $api.get("/admin/chart-by-work-status");

        this.chartWorks = res.data;
      } catch (e) {
        console.log(e);
      } finally {
        this.isLoadingByWork = false;
      }
    },

    async chartByYears() {
      const { $api } = useNuxtApp();
      this.isLoadingByYear = true;
      try {
        const res = await $api.get("/admin/chart-by-year");

        this.chartYears = res.data;
      } catch (e) {
        console.log(e);
      } finally {
        this.isLoadingByYear = false;
      }
    },
  },
});
