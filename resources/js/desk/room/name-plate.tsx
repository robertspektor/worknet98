export function NamePlate({ title }: { title: string }) {
    return (
        <div className="desk-item name-plate">
            <span className="name-plate-face">{title}</span>
        </div>
    );
}
