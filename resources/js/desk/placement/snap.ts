export type Box = { left: number; top: number; width: number; height: number };

export type SurfaceKind = 'area' | 'ledge';

export type Surface = { kind: SurfaceKind; box: Box };

export type Placement = { x: number; y: number };

function restingY(surface: Surface, itemBottom: number): number {
    const { top, height } = surface.box;

    return surface.kind === 'ledge'
        ? top
        : Math.min(Math.max(itemBottom, top), top + height);
}

function spans(surface: Surface, centerX: number): boolean {
    const { left, width } = surface.box;

    return centerX >= left && centerX <= left + width;
}

function clamp(value: number, min: number, max: number): number {
    return Math.min(Math.max(value, min), Math.max(min, max));
}

export function snapToSurface(
    item: Box,
    surfaces: Surface[],
    room: Box,
): Placement {
    const centerX = item.left + item.width / 2;
    const bottom = item.top + item.height;
    const candidates = surfaces.filter((surface) => spans(surface, centerX));
    const pool = candidates.length > 0 ? candidates : surfaces;

    const target = pool.reduce((best, surface) =>
        Math.abs(restingY(surface, bottom) - bottom) <
        Math.abs(restingY(best, bottom) - bottom)
            ? surface
            : best,
    );

    const x = clamp(
        centerX,
        target.box.left + item.width / 2,
        target.box.left + target.box.width - item.width / 2,
    );

    return {
        x: clamp((x - room.left) / room.width, 0, 1),
        y: clamp((restingY(target, bottom) - room.top) / room.height, 0, 1),
    };
}
