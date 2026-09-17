import { usePlaceable } from '../placement/use-placeable';
export function NamePlate({ title }: { title: string }) {
    const { className, ...placeable } =
        usePlaceable<HTMLDivElement>('name-plate');
    return (
        <div className={`desk-item name-plate ${className}`} {...placeable}>
            <span className="name-plate-face">{title}</span>
        </div>
    );
}
