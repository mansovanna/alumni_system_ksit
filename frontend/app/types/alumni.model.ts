export interface AlumniUserModel {
  id: number;
  name_khmer: string;
  name_english: string;
  role_id: number;
  mobile: string | null;
  email: string | null;
  status: string;
  avatar: string | null;
  created_at: string;
  updated_at: string;
  profile_url: string | null;
}

export interface AlumniMajorModel {
  id: number;
  name: string;
  description: string | null;
  created_at: string;
  updated_at: string;
}

export interface AlumniModels {
  id: number;
  user_id: number;
  major_id: number | null;
  graduation_year: string | null;
  gpa: number | null;
  gender: string | null;
  dob: string | null;
  address: string | null;
  bio: string | null;

  linkedin_url: string | null;
  facebook_url: string | null;
  profile_photo: string | null;

  employment_status:
    | "employed"
    | "unemployed"
    | "self_employed"
    | "studying"
    | "seeking"
    | "unknown"
    | string;

  created_at: string;
  updated_at: string;

  user: AlumniUserModel;
  major: AlumniMajorModel | null;
  employment?: Employments;
}

export interface Employments {
  id: number;
  alumni_id: number;
  company_id: number;
  job_title: string;
  employment_type: string;
  industry: string;
  location: string;
  salary_range: string;
  start_date: string;
  end_date: string;
  is_current: number;
  created_at: string;
  updated_at: string;
}
