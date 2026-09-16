export type Replacements = Record<string, string | number>;

export function translate(
    translations: Record<string, string>,
    key: string,
    replacements: Replacements = {},
): string {
    return Object.entries(replacements).reduce(
        (text, [name, value]) => text.replaceAll(`:${name}`, String(value)),
        translations[key] ?? key,
    );
}
