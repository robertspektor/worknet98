import { usePlaceable } from '../placement/use-placeable';

const PENCILS = ['is-blue', 'is-amber', 'is-red', 'is-grey'];

export function PencilCup() {
    const { className, ...placeable } =
        usePlaceable<HTMLDivElement>('pencil-cup');

    return (
        <div
            className={`desk-item pencil-cup ${className}`}
            {...placeable}
            aria-hidden="true"
        >
            <span className="pencils">
                {PENCILS.map((pencil) => (
                    <span key={pencil} className={`pencil ${pencil}`} />
                ))}
            </span>
            <span className="cup" />
        </div>
    );
}
