// directory.model.ts

export interface AlumniUser {
  id: number
  name_khmer: string
  name_english: string
  role_id: number
  mobile: string
  email: string
  status: string
  avatar: string | null
  created_at: string
  updated_at: string
  profile_url: string | null
}

export interface AlumniMajor {
  id: number
  name: string
  description: string
  created_at: string
  updated_at: string
}

export interface AlumniEmployment {
  id: number
  alumni_id: number
  company_id: number | null
  job_title: string
  employment_type: string
  industry: string
  location: string
  salary_range: string
  start_date: string
  end_date: string | null
  is_current: boolean
  created_at: string
  updated_at: string
  company: unknown | null
}

export interface Alumni {
  id: number
  user_id: number
  major_id: number
  graduation_year: string
  gpa: number | null
  gender: string
  dob: string | null
  address: string | null
  bio: string | null
  linkedin_url: string | null
  facebook_url: string | null
  profile_photo: string | null
  employment_status: string
  created_at: string
  updated_at: string
  user: AlumniUser
  major: AlumniMajor
  employment: AlumniEmployment | null
}

// Generic Laravel paginator shape (reusable for other paginated endpoints too)
export interface LaravelPaginator<T> {
  current_page: number
  data: T[]
  first_page_url: string
  from: number | null
  last_page: number
  last_page_url: string
  links: { url: string | null; label: string; page: number | null; active: boolean }[]
  next_page_url: string | null
  path: string
  per_page: number
  prev_page_url: string | null
  to: number | null
  total: number
}

export interface AlumniDirectoryData {
  graduation_years: string[]
  alumni: LaravelPaginator<Alumni>
}

export interface AlumniDirectoryResponse {
  success: boolean
  message: string
  data: AlumniDirectoryData
}