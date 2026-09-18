import { Monitor } from '../desk/room/monitor';
import { PixelIcon } from '../desk/ui/pixel-icon';

export type CatalogueProps = {
    action: string;
    csrf: string;
    locale: string;
    locales: Record<string, string>;
    email: string;
    ageConfirmed: boolean;
    errors: Record<string, string>;
    status: string | null;
    labels: Record<string, string>;
};

export function Catalogue({
    action,
    csrf,
    locale,
    locales,
    email,
    ageConfirmed,
    errors,
    status,
    labels,
}: CatalogueProps) {
    return (
        <Monitor isOn onPowerPress={() => undefined}>
            <div className="os catalogue-os">
                <section className="window is-focused catalogue-window">
                    <header className="title-bar">
                        <span className="title-bar-text">
                            <PixelIcon name="computer" size={16} />
                            <span>{labels.title}</span>
                        </span>
                    </header>
                    <div className="window-body catalogue-body">
                        <div className="catalogue-intro">
                            <PixelIcon name="computer" size={32} />
                            <div>
                                <h2 className="app-heading">
                                    {labels.heading}
                                </h2>
                                <p className="catalogue-note">{labels.note}</p>
                            </div>
                        </div>

                        <form method="POST" action={action} noValidate>
                            <input type="hidden" name="_token" value={csrf} />

                            <label className="field" htmlFor="order-locale">
                                <span>{labels.language}</span>
                                <select
                                    className="input"
                                    id="order-locale"
                                    name="locale"
                                    defaultValue={locale}
                                >
                                    {Object.entries(locales).map(
                                        ([code, name]) => (
                                            <option key={code} value={code}>
                                                {name}
                                            </option>
                                        ),
                                    )}
                                </select>
                            </label>

                            <label className="field" htmlFor="order-email">
                                <span>{labels.email}</span>
                                <input
                                    className="input"
                                    id="order-email"
                                    name="email"
                                    type="email"
                                    autoComplete="email"
                                    defaultValue={email}
                                />
                                {errors.email && (
                                    <span className="field-error">
                                        {errors.email}
                                    </span>
                                )}
                            </label>

                            <label className="checkbox-field">
                                <input
                                    type="checkbox"
                                    name="age_confirmed"
                                    value="1"
                                    defaultChecked={ageConfirmed}
                                />
                                <span>{labels.age}</span>
                            </label>
                            {errors.age_confirmed && (
                                <span className="field-error">
                                    {errors.age_confirmed}
                                </span>
                            )}

                            {status && (
                                <p className="catalogue-status">{status}</p>
                            )}

                            <div className="app-actions app-actions-end">
                                <button
                                    type="submit"
                                    className="button button-primary"
                                >
                                    {labels.submit}
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </Monitor>
    );
}
