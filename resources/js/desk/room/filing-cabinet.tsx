const DRAWERS = [0, 1, 2, 3];

/* Grey steel, four drawers, a stack of forms on top: the piece of furniture
   every office of the city owns. It stands at the edge of the picture. */

export function FilingCabinet() {
    return (
        <div className="filing-cabinet" aria-hidden="true">
            <span className="cabinet-forms" />
            <span className="cabinet-body">
                {DRAWERS.map((drawer) => (
                    <span key={drawer} className="cabinet-drawer">
                        <span className="drawer-handle" />
                        <span className="drawer-label" />
                    </span>
                ))}
            </span>
        </div>
    );
}
