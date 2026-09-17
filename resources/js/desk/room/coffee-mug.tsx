import { usePlaceable } from '../placement/use-placeable';
export function CoffeeMug({ company }: { company: string }) {
    const { className, ...placeable } =
        usePlaceable<HTMLDivElement>('coffee-mug');
    return (
        <div
            className={`desk-item coffee-mug ${className}`}
            {...placeable}
            aria-hidden="true"
        >
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
