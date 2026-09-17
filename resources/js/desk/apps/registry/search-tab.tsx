import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { index as peopleIndex } from '@/routes/api/v1/registry/people';
import type { RegistryPerson } from '@/types';
import { getJson } from '../../api/game-api';

const MIN_QUERY_LENGTH = 2;

export function SearchTab() {
    const { t } = useTranslation();
    const [query, setQuery] = useState('');
    const [results, setResults] = useState<RegistryPerson[] | null>(null);
    const [selectedId, setSelectedId] = useState<number | null>(null);
    const selected = results?.find((person) => person.id === selectedId);

    const search = async () => {
        const { data } = await getJson<{ data: RegistryPerson[] }>(
            peopleIndex.url({ query: { search: query.trim() } }),
        );
        setResults(data);
        setSelectedId(null);
    };

    return (
        <>
            <form
                className="records-search registry-search"
                onSubmit={(event) => {
                    event.preventDefault();
                    void search();
                }}
            >
                <label htmlFor="registry-search">{t('registry.search')}</label>
                <input
                    id="registry-search"
                    className="input"
                    value={query}
                    placeholder={t('registry.search_hint')}
                    onChange={(event) => setQuery(event.target.value)}
                />
                <button
                    type="submit"
                    className="button"
                    disabled={query.trim().length < MIN_QUERY_LENGTH}
                >
                    {t('registry.find')}
                </button>
            </form>
            <div className="records-body registry-body">
                <div className="records-list sunken" role="listbox">
                    {results?.length === 0 && (
                        <p className="muted records-empty">
                            {t('registry.no_people')}
                        </p>
                    )}
                    {results?.map((person) => (
                        <button
                            key={person.id}
                            type="button"
                            role="option"
                            aria-selected={person.id === selectedId}
                            className={`records-row registry-row ${person.id === selectedId ? 'is-selected' : ''}`}
                            onClick={() => setSelectedId(person.id)}
                        >
                            <span>{person.name}</span>
                            <span>{person.district}</span>
                        </button>
                    ))}
                </div>
                <div className="records-detail sunken">
                    {selected ? (
                        <>
                            <h3 className="records-detail-title">
                                {selected.name}
                            </h3>
                            <dl className="property-list">
                                <dt>{t('registry.address')}</dt>
                                <dd>
                                    {selected.street}
                                    <br />
                                    {selected.district}
                                </dd>
                                <dt>{t('records.phone')}</dt>
                                <dd>{selected.phone ?? '-'}</dd>
                                <dt>{t('registry.household')}</dt>
                                <dd>
                                    {selected.household.length > 0
                                        ? selected.household.join(', ')
                                        : t('registry.lives_alone')}
                                </dd>
                            </dl>
                        </>
                    ) : (
                        <p className="muted">{t('registry.select_person')}</p>
                    )}
                </div>
            </div>
        </>
    );
}
