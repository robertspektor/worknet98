import { usePlaceable } from '../placement/use-placeable';

const BOOKS = ['is-navy', 'is-rust', 'is-olive', 'is-slate'];

export function BookStack() {
    const { className, ...placeable } =
        usePlaceable<HTMLDivElement>('book-stack');

    return (
        <div
            className={`desk-item book-stack ${className}`}
            {...placeable}
            aria-hidden="true"
        >
            {BOOKS.map((book) => (
                <span key={book} className={`book ${book}`}>
                    <span className="book-band" />
                </span>
            ))}
        </div>
    );
}
