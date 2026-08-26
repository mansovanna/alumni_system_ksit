import type { UserModel } from "./user.model";

export interface LoginResponseModel {
  success: boolean;
  message: string;

  data: {
    user: UserModel;
    token: string;
    token_type: "Bearer";
  };
}