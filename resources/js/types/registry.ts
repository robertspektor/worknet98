export type ApplicationKind = 'move' | 'marriage';

export type ApplicationDecision = 'approved' | 'rejected';

export type CivilApplication = {
    id: number;
    kind: ApplicationKind;
    applicant: string;
    claimed_street: string;
    claimed_district: string;
    partner: string | null;
    claimed_partner_street: string | null;
    claimed_partner_district: string | null;
    new_street: string | null;
    new_district: string | null;
    moved_on: string;
    decision: ApplicationDecision | null;
    is_own: boolean;
    decided_by: string | null;
    decided_by_npc: string | null;
};

export type RegistryPerson = {
    id: number;
    name: string;
    street: string;
    district: string;
    phone: string | null;
    household: string[];
};
