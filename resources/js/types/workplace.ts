export type SoftwareType = 'records' | 'scheduler';

export type CompanySoftware = {
    company: string;
    office_address: string | null;
    app_names: Partial<Record<SoftwareType, string>>;
};

export type Customer = {
    id: number;
    name: string;
    street: string;
    city: string;
    phone: string;
    email_address: string;
    notes: string;
};

export type Technician = {
    id: number;
    name: string;
    skills: string[];
    busy: string[];
};

export type Appointment = {
    id: number;
    date: string;
    slot: string;
    technician_id: number;
    customer: { id: number; name: string };
};

export type Schedule = {
    days: string[];
    slots: string[];
    technicians: Technician[];
    appointments: Appointment[];
};

export type CalendarEntry = {
    id: number;
    date: string;
    time: string;
    title: string;
};
