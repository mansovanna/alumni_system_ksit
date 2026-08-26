import type { StaffModel } from "./staff.model";

export interface StaffPaginationLink {
  url: string | null;
  label: string;
  page: number | null;
  active: boolean;
}

export interface StaffPaginationModel {
  current_page: number;

  data: StaffModel[];

  first_page_url: string;
  from: number | null;

  last_page: number;
  last_page_url: string;

  links: StaffPaginationLink[];

  next_page_url: string | null;

  path: string;

  per_page: number;

  prev_page_url: string | null;

  to: number | null;

  total: number;
}

export interface StaffResponseModel {
  message: string;
  data: StaffPaginationModel;
}