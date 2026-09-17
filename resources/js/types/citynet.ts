export type CityOverview = {
    name: string;
    locale: string;
    residents: number;
    households: number;
    employed: number;
    unemployed: number;
    unemployment_rate: number;
    average_income: number;
    organizations: number;
    open_positions: number;
};

export type WorldEventEntry = {
    id: number;
    type: string;
    city: string;
    person: string;
    occurred_at: string;
    caused_by: string | null;
    handled_by: string | null;
};
