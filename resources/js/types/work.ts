export type ShiftState = 'off_duty' | 'on_duty';

export type ContractPeriod = {
    starts_on: string;
    ends_on: string;
    ends_at: string;
};

export type WeeklyGoal = {
    target: number;
    resolved_cases: number;
    bonus: number;
    achieved: boolean;
    ends_at: string;
};

export type ShiftStatus = {
    status: ShiftState;
    clocked_in_at: string | null;
    clocked_out_automatically: boolean;
    worked_seconds: number;
    target_seconds: number;
    full_salary: number;
    earned_salary: number;
    weekly_goal: WeeklyGoal;
    period: ContractPeriod;
};
