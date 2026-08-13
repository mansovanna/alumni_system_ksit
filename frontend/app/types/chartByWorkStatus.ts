export interface ChartByWorkModel {
    message: string | null,
    data:  DataModel
}



interface DataModel {
    work: WorkModel[]
    total_user: number | null
}

interface WorkModel {
    status: string | null
    total: number | null
}