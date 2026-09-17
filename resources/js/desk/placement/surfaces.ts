import type { Box, Surface, SurfaceKind } from './snap';

export function boxOf(element: Element): Box {
    const { left, top, width, height } = element.getBoundingClientRect();

    return { left, top, width, height };
}

export function surfacesIn(room: HTMLElement, exclude: Element): Surface[] {
    return Array.from(room.querySelectorAll<HTMLElement>('[data-surface]'))
        .filter((element) => !exclude.contains(element))
        .map((element) => ({
            kind: element.dataset.surface as SurfaceKind,
            box: boxOf(element),
        }));
}
