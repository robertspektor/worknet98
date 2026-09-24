export function WallPoster({ city }: { city: string }) {
    return (
        <div className="wall-poster" aria-hidden="true">
            <span className="poster-art">
                <span className="poster-caption">
                    <span className="poster-city">{city}</span>
                    <span className="poster-year">1998</span>
                </span>
                <span className="poster-skyline" />
            </span>
        </div>
    );
}
