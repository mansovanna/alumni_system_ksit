import type { AlumniModels } from "./alumni.model";
import type { RoleModel } from "./roleModel.model";

export interface AlumniModel {
  id: number;
  // Add your actual alumni fields here
}

export interface UserModel {
  id: number;

  name_khmer: string | null;
  name_english: string | null;

  email: string | null;
  mobile: string | null;

  status: "active" | "inactive" | "pending" | "approved" | "rejected";

  avatar: string | null;
  profile_url: string | null;

  role: RoleModel;

  alumni?: AlumniModels | null;
}
