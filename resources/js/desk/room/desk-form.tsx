const LINES = [0, 1, 2, 3];

/* A form somebody filled in at the counter and a pen that belongs to the
   city, chained to nothing and therefore probably gone by tomorrow. */

export function DeskForm() {
    return (
        <div className="desk-item desk-form" aria-hidden="true">
            <span className="form-sheet">
                <span className="form-head" />
                {LINES.map((line) => (
                    <span key={line} className="form-line" />
                ))}
            </span>
            <span className="form-pen" />
        </div>
    );
}
