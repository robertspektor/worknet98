export type ShiftState = 'off_duty' | 'on_duty' | 'done';

export type ShiftStatus = {
    status: ShiftState;
    daily_salary: number;
    clocked_in_at: string | null;
    clocked_out_at: string | null;
};
