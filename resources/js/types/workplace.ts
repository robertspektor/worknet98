export type RankingEntry = {
    employment_id: number;
    name: string;
    title: string;
    company: string;
    score: number;
    excellent_reviews: number;
    is_own: boolean;
};

export type EmployeeAward = {
    period: string;
    name: string;
    title: string;
    branch: string;
    is_own: boolean;
};

export type Rankings = {
    branch: RankingEntry[];
    world: RankingEntry[];
    awards: EmployeeAward[];
};

export type ForumThread = {
    id: number;
    title: string;
    author: string;
    author_title: string;
    is_own: boolean;
    posts_count: number;
    last_posted_at: string;
};

export type ForumPost = {
    id: number;
    body: string;
    author: string;
    author_title: string;
    is_own: boolean;
    posted_at: string;
};

export type Colleague = {
    position_id: number;
    name: string;
    title: string;
    address: string;
};

export type SoftwareType =
    | 'records'
    | 'scheduler'
    | 'shipments'
    | 'tours'
    | 'registry';

export type CompanySoftware = {
    company: string;
    branch: string;
    office_address: string;
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
    is_own: boolean;
    booked_by: string | null;
    booked_by_npc: string | null;
    failed: boolean;
    part: PartStatus | null;
};

export type PartStatus = {
    contents: string;
    status: 'ordered' | 'planned' | 'delivered';
    arrival: string | null;
};

export type ShipmentSize = 'parcel' | 'pallet';

export type Vehicle = 'van' | 'truck';

export type TourId = 'morning' | 'afternoon';

export type ShipmentPlan = {
    driver_id: number;
    driver: string;
    date: string;
    tour: TourId;
    is_own: boolean;
    planned_by: string | null;
    planned_by_npc: string | null;
};

export type Shipment = {
    id: number;
    contents: string;
    size: ShipmentSize;
    sender: string;
    contact: string | null;
    recipient: string;
    district: string;
    due_date: string;
    due_slot: string;
    plan: ShipmentPlan | null;
    delivered_at: string | null;
};

export type Driver = {
    id: number;
    name: string;
    vehicle: Vehicle;
    capacity: number;
    busy: string[];
};

export type TourPlan = {
    days: string[];
    tours: { id: TourId; starts: string; ends: string }[];
    drivers: Driver[];
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
