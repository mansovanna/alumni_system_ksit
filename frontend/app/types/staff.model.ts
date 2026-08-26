export interface StaffRoleModel {
  id: number;
  name: string;
  guard_name: string;
  created_at: string;
  updated_at: string;
}

export interface StaffModel {
  id: number;
  name_khmer: string;
  name_english: string;
  role_id: number;
  mobile: string | null;
  email: string;
  status: string;
  avatar: string | null;
  profile_url: string | null;
  created_at: string;
  updated_at: string;

  role: StaffRoleModel;
}