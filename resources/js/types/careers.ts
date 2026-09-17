export type Company = {
    name: string;
    industry: string;
    tagline: string;
    description: string;
};

export type JobOpening = {
    id: number;
    title: string;
    description: string;
    daily_salary: number;
    company: Company;
};

export type JobApplicationStatus = 'pending' | 'accepted' | 'rejected';

export type JobApplication = {
    id: number;
    job_opening_id: number;
    status: JobApplicationStatus;
    submitted_at: string;
};
