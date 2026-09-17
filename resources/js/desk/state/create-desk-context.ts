import { createContext, use } from 'react';

export function createDeskContext<T>(name: string) {
    const Context = createContext<T | null>(null);

    function useOptional(): T | null {
        return use(Context);
    }

    function useRequired(): T {
        const value = use(Context);

        if (!value) {
            throw new Error(`use${name} must be used inside ${name}Provider.`);
        }

        return value;
    }

    return { Context, useOptional, useRequired };
}
