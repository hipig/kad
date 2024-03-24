import axios from 'axios';

export interface ReportRecord {
    type: string;
    content: string;
}

export function reports(params) {
    return axios.get<ReportRecord[]>('reports', {params});
}
