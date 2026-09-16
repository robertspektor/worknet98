export function PowerButton({
    isOn,
    isHinting,
    onPress,
}: {
    isOn: boolean;
    isHinting: boolean;
    onPress: () => void;
}) {
    return (
        <div className="power">
            <span className={`power-led ${isOn ? 'is-on' : ''}`} />
            <button
                type="button"
                className={`power-button ${isHinting ? 'is-hinting' : ''}`}
                aria-label="Power"
                aria-pressed={isOn}
                onClick={onPress}
            >
                <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2.6"
                    strokeLinecap="round"
                    aria-hidden="true"
                >
                    <path d="M12 3v8" />
                    <path d="M6.3 6.8a8 8 0 1 0 11.4 0" />
                </svg>
            </button>
        </div>
    );
}
