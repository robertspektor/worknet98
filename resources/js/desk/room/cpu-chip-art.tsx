const PINS_PER_SIDE = 7;

export function CpuChipArt({ isUsed = false }: { isUsed?: boolean }) {
    return (
        <span className={`cpu-chip ${isUsed ? 'is-used' : ''}`}>
            {(['top', 'right', 'bottom', 'left'] as const).map((side) => (
                <span key={side} className={`cpu-chip-pins is-${side}`}>
                    {Array.from({ length: PINS_PER_SIDE }, (_, index) => (
                        <span key={index} className="cpu-chip-pin" />
                    ))}
                </span>
            ))}
            <span className="cpu-chip-body">
                <span className="cpu-chip-notch" />
                <span className="cpu-chip-die" />
                <span className="cpu-chip-brand">KALKULON</span>
            </span>
        </span>
    );
}
