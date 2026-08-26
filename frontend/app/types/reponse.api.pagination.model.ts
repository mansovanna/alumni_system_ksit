export interface ReponseAPIPagination<T> {
  message: string | null;
  data: Pagination<T>;
}

interface Pagination<T> {
  current_page: number | null;
  data: T[];
  first_page_url: string | null;
  from: string | null;
  last_page: number | null;
  last_page_url: string | null;
  links: Links[];
  next_page_url: string | null;
  path: string | null;
  per_page: string | null;
  prev_page_url: string | null;
  to: number | null;
  total: number | null;
}

interface Links {
  url: string | null;
  label: string | null;
  page: number | null;
  active: false | true;
}
