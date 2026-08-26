export interface ApiResponsePagination<T> {
  success: boolean;
  message: string | null;
  data: T[];
  pagination: PaginationMeta;
  filters?: Filters;
}

interface PaginationMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number | null;
  to: number | null;
}

interface Filters {
  search: string | null;
  sort_by: string;
  sort_order: 'asc' | 'desc';
}