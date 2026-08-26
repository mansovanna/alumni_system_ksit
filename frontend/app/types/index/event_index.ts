export interface EventAgenda {
  id: number;
  event_id: number;
  time: string;
  title: string;
  created_at: string;
  updated_at: string;
}

export interface EventRegistration {
  id: number;
  event_id: number;
  alumni_id: number;
  registered_at: string;
  attendance_status: "registered" | "attended" | "absent" | "cancelled" | string;
}

export interface EventCreator {
  id: number;
  name_khmer: string;
  name_english: string;
  role_id: number;
  mobile: string;
  email: string;
  status: "active" | "inactive" | string;
  avatar: string | null;
  created_at: string;
  updated_at: string;
  profile_url: string | null;
}

export type EventType =
  | "workshop"
  | "seminar"
  | "conference"
  | "training"
  | "meeting"
  | "other"
  | string;

export type EventStatus =
  | "draft"
  | "published"
  | "ongoing"
  | "completed"
  | "cancelled"
  | string;

export interface Event {
  id: number;
  created_by: number;

  title: string;
  description: string;

  event_type: EventType;

  banner_image: string | null;
  location: string | null;

  start_date: string;
  end_date: string;
  registration_deadline: string | null;

  max_participants: number | null;

  external_link: string | null;

  status: EventStatus;

  is_registered: boolean;

  registration: EventRegistration | null;

  agendas: EventAgenda[];

  creator: EventCreator | null;

  created_at: string;
  updated_at: string;
}

export interface EventResponseIndex {
  success: boolean;
  message: string;
  data: Event;
}