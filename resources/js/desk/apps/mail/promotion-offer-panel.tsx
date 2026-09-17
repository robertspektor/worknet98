import { router } from '@inertiajs/react';
import { useState } from 'react';
import { useTranslation } from '@/i18n/use-translation';
import { store as acceptanceStore } from '@/routes/api/v1/promotion-offers/acceptance';
import { store as declineStore } from '@/routes/api/v1/promotion-offers/decline';
import type { PromotionOffer } from '@/types';
import { postJson } from '../../api/game-api';
import { sound } from '../../sound/sound';
import { useRefusalAlert } from '../../ui/use-refusal-alert';
import { offerOutcome } from './promotion-offer-outcome';

export function PromotionOfferPanel({ offer }: { offer: PromotionOffer }) {
    const { t } = useTranslation();
    const alertRefusal = useRefusalAlert();
    const [answer, setAnswer] = useState<PromotionOffer | null>(null);
    const [isBusy, setBusy] = useState(false);
    const current = answer?.id === offer.id ? answer : offer;

    const respond = async (url: string, body: object = {}) => {
        setBusy(true);

        try {
            const { data } = await postJson<{ data: PromotionOffer }>(
                url,
                body,
            );
            sound.click();
            setAnswer(data);
            router.reload({ only: ['workplace'] });
        } catch (error) {
            alertRefusal(t('inbox.promotion.title'), error);
        } finally {
            setBusy(false);
        }
    };

    const outcome = offerOutcome(current);

    return (
        <fieldset className="group-box promotion-offer">
            <legend>{t('inbox.promotion.title')}</legend>
            {outcome ? (
                <p className="promotion-offer-outcome">
                    {t(outcome.key, outcome.replacements)}
                </p>
            ) : (
                <>
                    <ul className="promotion-offer-options">
                        {current.options.map((option) => (
                            <li key={option.position_id}>
                                <span className="promotion-offer-title">
                                    {option.title}
                                </span>
                                <span>
                                    {t('inbox.promotion.salary', {
                                        salary: option.daily_salary,
                                    })}
                                </span>
                                <button
                                    type="button"
                                    className="button"
                                    disabled={isBusy}
                                    onClick={() =>
                                        void respond(
                                            acceptanceStore.url(current.id),
                                            { position_id: option.position_id },
                                        )
                                    }
                                >
                                    {t('inbox.promotion.accept')}
                                </button>
                            </li>
                        ))}
                    </ul>
                    <div className="app-actions">
                        <button
                            type="button"
                            className="button"
                            disabled={isBusy}
                            onClick={() =>
                                void respond(declineStore.url(current.id))
                            }
                        >
                            {t('inbox.promotion.decline')}
                        </button>
                    </div>
                </>
            )}
        </fieldset>
    );
}
