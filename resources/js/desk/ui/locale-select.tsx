import { usePage } from '@inertiajs/react';

export function LocaleSelect({
    id,
    value,
    onChange,
}: {
    id: string;
    value: string;
    onChange: (locale: string) => void;
}) {
    const { locales } = usePage().props;

    return (
        <select
            id={id}
            className="select"
            value={value}
            onChange={(event) => onChange(event.target.value)}
        >
            {Object.entries(locales).map(([code, name]) => (
                <option key={code} value={code}>
                    {name}
                </option>
            ))}
        </select>
    );
}
