import type { MajorModel } from "./major"
import type { WorkModel } from "./work"

export interface DashBoard{
    total_alumni: number | null
    alumni_active: number | null
    major: number | null
    majors: MajorModel[]
    work: WorkModel[]
}