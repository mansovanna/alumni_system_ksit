// Type =====================================================
export interface Major {
  id: number;
  name: string;
  description: string;
  created_at: string;
  updated_at: string;
}

export interface AlumniInfo {
  id: number;
  user_id: number;
  major_id: number;
  graduation_year: string;
  gpa: string | null;
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
  major: Major | null;
  employment: unknown | null; // null in sample data — shape unconfirmed
}

export interface Alumni {
  id: number;
  name_khmer: string;
  name_english: string;
  mobile: string;
  email: string;
  status: string;
  avatar: string | null;
  profile_url: string | null;
  alumni_info: AlumniInfo | null;
}

export interface AppNotification {
  id: number;
  title: string;
  message: string;
  type: string;
  filters: string; // raw JSON string — parse with JSON.parse() before use
  created_at: string;
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
  is_registered: boolean
  created_at: string;
  updated_at: string;
  banner_image_url: string;
}

export interface AlumniDashboardData {
  alumni: Alumni;
  notifications: AppNotification[];
  events: AlumniEvent[];
}

export interface AlumniDashboardResponse {
  success: boolean;
  message: string;
  data: AlumniDashboardData;
}
// ==========================================================

export const useAlumnisStore = defineStore("alumnisStore", {
  state: () => ({
    data: null as AlumniDashboardResponse | null,
    isLoading: false,
    messageError: "",
  }),

  actions: {
    async getAlumnis() {
      const { $api } = useNuxtApp();

      try {
        const res = await $api.get("/alumnis");

        this.data = res.data;
      } catch (e: any) {
        this.messageError = e.response?.data;
      } finally {
        this.isLoading = false;
      }
    },
    updateStatus(id: number, data: FormData) {
      const { $api } = useNuxtApp();

      data.append("_method", "PUT");

      return $api.put(`/alumnis/update-status/${id}`, data);
    },
  },
});
