export interface ChartByYearModel {
  message: string | null;
  data: DataModel;
}

interface DataModel {
  years: string[];
  series: {
    employed: Record<string, number>;
    unemployed: Record<string, number>;
    seeking: Record<string, number>;
    continuing_study: Record<string, number>;
  };
}
