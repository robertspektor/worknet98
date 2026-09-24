/* The hint that slides out beside a key on the tower's front panel. */

export function PanelTip({ label }: { label: string }) {
    return (
        <span className="panel-tip" aria-hidden="true">
            {label}
        </span>
    );
}
