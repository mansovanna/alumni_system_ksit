import type { MajorModel } from "./major";

export interface UserModel {
  id: number;
  name_khmer: string | null;
  name_english: string | null;
  mobile: string | null;
  email: string | null;
  role: 'admin' | 'staff' | 'alumni';
  status: 'pending' | 'approved' | 'rejected';
  gender: 'male' | 'female' | null;
  profile: string | null;
  created_at: string;
  updated_at: string;
  profile_url: string | null;
  user_infos_one: UserInfo | null;
}

export interface UserInfo {
  id: number;
  user_id: number;
  major_id: number | null;
  date_of_birth: string | null;
  address: string | null;
  work: 'unemployed' | 'seeking' | 'employed' | 'continuing_study' | null;
  work_address: string | null;
  last_year: string | null;
  created_at: string;
  updated_at: string;
  major: MajorModel
}