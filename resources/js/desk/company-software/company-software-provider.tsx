import type { ReactNode } from 'react';
import { createContext, use, useEffect, useState } from 'react';
import { show } from '@/routes/api/v1/company-software';
import type { CompanySoftware } from '@/types';
import { getJson } from '../api/game-api';
import { useEdition } from '../computer/edition-context';

const CompanySoftwareContext = createContext<CompanySoftware | null>(null);

export function CompanySoftwareProvider({ children }: { children: ReactNode }) {
    const { mailbox } = useEdition();
    const [software, setSoftware] = useState<CompanySoftware | null>(null);

    useEffect(() => {
        if (mailbox !== 'work') {
            return;
        }

        let isCurrent = true;

        void getJson<{ data: CompanySoftware }>(show.url()).then(({ data }) => {
            if (isCurrent) {
                setSoftware(data);
            }
        });

        return () => {
            isCurrent = false;
        };
    }, [mailbox]);

    return (
        <CompanySoftwareContext value={software}>
            {children}
        </CompanySoftwareContext>
    );
}

export function useCompanySoftware(): CompanySoftware | null {
    return use(CompanySoftwareContext);
}
