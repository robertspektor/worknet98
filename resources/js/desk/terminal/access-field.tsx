import type { ChangeEvent } from 'react';

export function AccessField({
    id,
    label,
    value,
    error,
    onChange,
}: {
    id: string;
    label: string;
    value: string;
    error?: string;
    onChange: (value: string) => void;
}) {
    return (
        <label className="access-field" htmlFor={id}>
            <span>{label}</span>
            <input
                id={id}
                className="access-input"
                type="email"
                autoComplete="email"
                autoFocus
                value={value}
                onChange={(event: ChangeEvent<HTMLInputElement>) =>
                    onChange(event.target.value)
                }
            />
            {error !== undefined && (
                <span className="access-error">{error}</span>
            )}
        </label>
    );
}
