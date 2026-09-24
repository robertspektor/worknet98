/* Covers the whole page while the visitor travels from one place to another,
   so the page swap underneath never shows. */

export function Blackout({ isDark }: { isDark: boolean }) {
    return (
        <div
            className={`blackout ${isDark ? 'is-dark' : ''}`}
            aria-hidden="true"
        />
    );
}
