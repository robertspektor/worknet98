export function CoffeeMug({ company }: { company: string }) {
    return (
        <div className="desk-item coffee-mug" aria-hidden="true">
            <span className="steam">
                <span />
                <span />
            </span>
            <span className="mug-handle" />
            <span className="mug-body">
                <span className="mug-print">{company}</span>
            </span>
        </div>
    );
}
