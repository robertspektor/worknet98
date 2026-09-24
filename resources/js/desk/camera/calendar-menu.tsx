import { formatGameDate } from '../clock/game-clock';
import { useGameTime } from '../clock/use-game-time';
import { useTranslation } from '@/i18n/use-translation';
import { MenuNote } from './detail-menu';

/* The calendar has nothing to operate, it tells the player what day it is. */

export function CalendarMenu() {
    const { locale } = useTranslation();

    return <MenuNote text={formatGameDate(useGameTime(), locale)} />;
}
