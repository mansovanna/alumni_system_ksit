export interface NotificationFilters {
  graduationYear?: string;
  major?: string;
  employmentStatus?: string;
}

export interface NotificationItem {
  id: number;
  title: string;
  message: string;
  type: string;
  /** Raw JSON string from backend, or null. Parse with parseNotificationFilters(). */
  filters: string | null;
  created_at: string;
}

export interface PaginationLink {
  url: string | null;
  label: string;
  page: number | null;
  active: boolean;
}

export interface PaginatedNotifications {
  current_page: number;
  data: NotificationItem[];
  first_page_url: string;
  from: number | null;
  last_page: number;
  last_page_url: string;
  links: PaginationLink[];
  next_page_url: string | null;
  path: string;
  per_page: number;
  prev_page_url: string | null;
  to: number | null;
  total: number;
}

export interface NotificationADResponseAPI {
  success: boolean;
  message: string;
  data: PaginatedNotifications;
  totalAlumni: number;
  years: null | [];
}

/** Safely parse the `filters` JSON string into an object, or null if absent/invalid. */
export function parseNotificationFilters(
  raw: string | null,
): NotificationFilters | null {
  if (!raw) return null;
  try {
    return JSON.parse(raw) as NotificationFilters;
  } catch {
    return null;
  }
}
