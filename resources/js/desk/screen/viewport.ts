export const VIEWPORT = { width: 800, height: 600 } as const;

export const TASKBAR_HEIGHT = 30;

export const DESKTOP_BOUNDS = {
    width: VIEWPORT.width,
    height: VIEWPORT.height - TASKBAR_HEIGHT,
} as const;

export function renderScale(element: HTMLElement): number {
    return element.getBoundingClientRect().width / element.offsetWidth || 1;
}
