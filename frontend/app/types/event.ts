import type { UserModel } from "./user.model";

export interface EventItem {
  id: number;
  created_by: number;
  title: string;
  description: string | null;

  event_type: "job_fair" | "reunion" | "workshop" | "seminar" | "other";

  banner_image: string | null;
  banner_image_url: string | null;
  location: string | null;

  start_date: string;
  end_date: string;

  registration_deadline: string | null;

  max_participants: number | null;

  external_link: string | null;

  status: "draft" | "published" | "ongoing" | "completed" | "cancelled";

  created_at: string;
  updated_at: string;

  registrations_count: number;
  agendas: AgendasModel[];
  speakers: SpeakerModel[];
  creator: UserModel
}

export interface EventPagination {
  current_page: number;
  data: EventItem[];
  first_page_url: string;
  from: number | null;
  last_page: number;
  last_page_url: string;

  next_page_url: string | null;
  prev_page_url: string | null;

  path: string;
  per_page: number;
  to: number | null;
  total: number;

  links: {
    url: string | null;
    label: string;
    page: number | null;
    active: boolean;
  }[];
}

export interface EventListResponseAPI {
  success: boolean;
  message: string;
  data: EventPagination;
}

export interface EventDetailResponseAPI {
  success: boolean;
  message: string;
  data: EventItem;
}

interface AgendasModel {
  id: number | null;
  event_id: number | null;
  time: string | null;
  title: string | null;
  created_at: string | null;
  updated_at: string | null;
}

interface SpeakerModel {
  id: number | null;
  event_id: number | null;
  name: string | null;
  professional_title: string | null;
  created_at: string | null;
  updated_at: string | null;
}
