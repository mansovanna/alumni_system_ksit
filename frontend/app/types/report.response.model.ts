export interface ReportResponseModel {
  success: boolean;
  data: ReportDataModel;
}

export interface ReportDataModel {
  stats: ReportStatsModel;
  trend_chart: TrendChartModel;
  status_distribution: StatusDistributionModel;
  rows: MajorReportRowModel[];
  filters: ReportFiltersModel;
}

/* =========================
 * Stats
 * ========================= */

export interface ReportStatsModel {
  total_graduates: ReportStatItemModel;
  overall_employment: ReportStatItemModel;
  continuing_education: ReportStatItemModel;
  unemployed: ReportStatItemModel;
}

export interface ReportStatItemModel {
  value: string;
  trend: string;
  up: boolean;
}

/* =========================
 * Trend Chart
 * ========================= */

export interface TrendChartModel {
  categories: string[];
  rates: number[];
}

/* =========================
 * Status Distribution
 * ========================= */

export interface StatusDistributionModel {
  total: string;
  series: number[];
  labels: string[];
}

/* =========================
 * Major Report
 * ========================= */

export interface MajorReportRowModel {
  dept: string;
  total: number;
  employed: number;
  cont: number;
  unemployed: number;
  seeking: number;
  rate: number;
}

/* =========================
 * Filters
 * ========================= */

export interface ReportFiltersModel {
  years: string[];
  majors: ReportMajorFilterModel[];
}

export interface ReportMajorFilterModel {
  id: number;
  name: string;
}
