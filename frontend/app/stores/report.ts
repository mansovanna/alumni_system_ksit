import type { ReportResponseModel } from "~/types/report.response.model";

export const useReportStore = defineStore("report", {
  state: () => ({
    data: null as ReportResponseModel | null,
    isLoading: false,
  }),

  actions: {
    async getReport(
      filters = {
        year_start: "all",
        year_end: "all",
        major_id: "all",
        status: "all",
      },
    ) {
      const { $api } = useNuxtApp();

      this.isLoading = true;

      try {
        const res = await $api.get("/reports/employment", {
          params: filters,
        });

        if (res.data?.success) {
          this.data = res.data;
        }
      } catch (e) {
        console.error("Fetch report error:", e);
      } finally {
        this.isLoading = false;
      }
    },
  },
});
