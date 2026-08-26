export interface DashboardOverviewResponse {
  success: boolean;
  message: string;
  data: DashboardData;
}

export interface DashboardData {
  summary: DashboardSummary;
  employment_by_year: EmploymentByYear[];
  employment_rate_trend: EmploymentTrendPoint[];
  recent_updates: RecentStatusUpdate[];
  available_years: string[];
  majors: Major[];
  total_alumni: number;
}

export interface DashboardSummary {
  employed: number;
  seeking: number;
  studying: number;
  unemployed: number;

  employed_change: number;
  seeking_change: number;
  studying_change: number;
  unemployed_change: number;
}

export interface EmploymentByYear {
  year: string;
  count: number;
}

export interface EmploymentTrendPoint {
  year: number;
  rate: number;
}

export interface RecentStatusUpdate {
  name: string;
  degree: string;
  status: string;
}

export interface Major {
  id: number;
  title: string;
}