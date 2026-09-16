import { createContext, useContext } from 'react';
import type { Edition } from './editions';
import { HOME_EDITION } from './editions';

export const EditionContext = createContext<Edition>(HOME_EDITION);

export function useEdition(): Edition {
    return useContext(EditionContext);
}
