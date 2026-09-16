import { useEffect, useState } from 'react';
import { getJson } from './game-api';

type ApiResource<T> = {
    data: T | null;
    hasFailed: boolean;
    reload: () => void;
};

export function useApiResource<T>(url: string): ApiResource<T> {
    const [data, setData] = useState<T | null>(null);
    const [hasFailed, setFailed] = useState(false);
    const [version, setVersion] = useState(0);

    useEffect(() => {
        let isCurrent = true;

        getJson<{ data: T }>(url)
            .then((response) => {
                if (isCurrent) {
                    setData(response.data);
                    setFailed(false);
                }
            })
            .catch(() => isCurrent && setFailed(true));

        return () => {
            isCurrent = false;
        };
    }, [url, version]);

    return { data, hasFailed, reload: () => setVersion(version + 1) };
}
