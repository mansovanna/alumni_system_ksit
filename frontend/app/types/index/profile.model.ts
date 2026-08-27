export interface ProfileModelAlumni{
    id: number
    user_id: number
    major_id: number
    graduation_year: string 
    gpa: string
    gender: string
    dob: string
    address: string
    bio: string
    linkedin_url: string
    facebook_url: string
    profile_photo: string
    employment_status: string
    use: User
    major: MajorModel
}

interface User {
    id: number
    name_khmer: string
    name_english: string
    mobile: string
    email: string
    status: string,
    avatar: string | null
    profile_url: string | null
}

interface MajorModel {
    id: number
    name: string | null
}