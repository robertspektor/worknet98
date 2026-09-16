import type { GlyphName, IconName } from './pixel-art';
import { GLYPHS, ICONS, PALETTE } from './pixel-art';
import { pixelRuns } from './pixel-runs';

function PixelArt({
    rows,
    width,
    height,
}: {
    rows: string[];
    width: number;
    height: number;
}) {
    return (
        <svg
            className="pixel-icon"
            width={width}
            height={height}
            viewBox={`0 0 ${rows[0].length} ${rows.length}`}
            shapeRendering="crispEdges"
            aria-hidden="true"
        >
            {pixelRuns(rows).map((run) => (
                <rect
                    key={`${run.x}-${run.y}`}
                    x={run.x}
                    y={run.y}
                    width={run.width}
                    height={1}
                    fill={PALETTE[run.color]}
                />
            ))}
        </svg>
    );
}

export function PixelIcon({
    name,
    size = 32,
}: {
    name: IconName;
    size?: number;
}) {
    return <PixelArt rows={ICONS[name]} width={size} height={size} />;
}

export function PixelGlyph({ name }: { name: GlyphName }) {
    return <PixelArt rows={GLYPHS[name]} width={8} height={7} />;
}
