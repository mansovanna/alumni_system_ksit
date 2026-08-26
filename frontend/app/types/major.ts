export interface MajorModel {
  id: number | null;
  name: string | null;
  description: string | null;
  updatedAt: string | null;
  users_count: string | null;
}


export interface MajorResponseAPI {
  message: string | null
  data: MajorModel[]
}