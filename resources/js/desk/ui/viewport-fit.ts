export type Span = { left: number; right: number };

export function shiftIntoView(
    span: Span,
    viewportWidth: number,
    gutter: number,
): number {
    if (span.right > viewportWidth - gutter) {
        return Math.max(
            viewportWidth - gutter - span.right,
            gutter - span.left,
        );
    }

    return span.left < gutter ? gutter - span.left : 0;
}
