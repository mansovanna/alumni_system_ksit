export interface MajorModel {
  id: number | null;
  title: string | null;
  body: string | null;
  color_from: string | null;
  color_to: string | null;
  icon: string | null;
  updatedAt: string | null;
  users_count: string | null;
}


export interface MajorResponseAPI {
  message: string | null
  data: MajorModel[]
}