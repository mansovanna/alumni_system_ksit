export interface AlumniUser {
  id: number;
  name_khmer: string;
  name_english: string;
  role_id: number;
  mobile: string;
  email: string;
  status: string;
  avatar: string | null;
  created_at: string;
  updated_at: string;
  profile_url: string | null;
}

export interface Major {
  id: number;
  name: string;
  description: string;
  created_at: string;
  updated_at: string;
}

export interface Employment {
  id: number;
  alumni_id: number;
  company_name: string | null;
  job_title: string | null;
  employment_type: string | null;
  industry: string | null;
  location: string | null;
  salary_range: string | null;
  start_date: string | null;
  end_date: string | null;
  is_current: boolean;
  created_at: string;
  updated_at: string;
}

export interface EventAgenda {
  id: number;
  event_id: number;
  time: string;
  title: string;
  created_at: string;
  updated_at: string;
}

export interface EventSpeaker {
  id: number;
  event_id: number;
  name: string;
  professional_title: string;
  created_at: string;
  updated_at: string;
}

export interface AlumniEvent {
  id: number;
  created_by: number;
  title: string;
  description: string;
  event_type: string;
  banner_image: string;
  location: string;
  start_date: string;
  end_date: string;
  registration_deadline: string;
  max_participants: number;
  external_link: string | null;
  status: string;
  created_at: string;
  updated_at: string;
  banner_image_url: string;
  agendas: EventAgenda[];
  speakers: EventSpeaker[];
}

export interface EventRegistration {
  id: number;
  event_id: number;
  alumni_id: number;
  registered_at: string;
  attendance_status: string;
  event: AlumniEvent;
}

export interface AlumniProfile {
  id: number;
  user_id: number;
  major_id: number;
  graduation_year: string;
  gpa: number | null;
  gender: string | null;
  dob: string | null;
  address: string | null;
  bio: string | null;
  linkedin_url: string | null;
  facebook_url: string | null;
  profile_photo: string | null;
  employment_status: string;
  created_at: string;
  updated_at: string;
  user: AlumniUser;
  major: Major;
  employment: Employment;
  event_registrations: EventRegistration[];
}

export interface AlumniProfileResponse {
  message: string;
  data: AlumniProfile;
}

export const useProfileAlumni = defineStore("profileAlumni", {
  state: () => ({
    data: null as AlumniProfile | null,
    isLoading: false,
    messageError: "",
  }),

  actions: {
    async getProfile() {
      const { $api } = useNuxtApp();
      this.isLoading = true;
      try {
        const res = await $api.get<AlumniProfileResponse>("alumnis/profile");
        this.data = res.data.data;
      } catch (e: any) {
        this.messageError = e.response?.data;
      } finally {
        this.isLoading = false;
      }
    },

    async unRegister(id: number) {
      const { $api } = useNuxtApp();

      return $api.delete(`/alumnis/unregister/${id}`);
    },
  },
});
