import { useI18n } from 'vue-i18n';

type RelativeTimeUnit = 'second' | 'minute' | 'hour' | 'day' | 'week' | 'month' | 'year';

export const useRelativeTime = () => {
    const { locale } = useI18n();

    const relativeTime = (formattedDate: string) => {
        if (locale.value === 'br') {
            return new Date(formattedDate).toLocaleDateString('fr');
        }
        const dateObject = new Date(formattedDate);
        const secondsDiff = Math.round((dateObject.getTime() - Date.now()) / 1000);
        const unitsInSec = [60, 3600, 86400, 86400 * 7, 86400 * 30, 86400 * 365, Infinity];
        const unitStrings: RelativeTimeUnit[] = ['second', 'minute', 'hour', 'day', 'week', 'month', 'year'];
        const unitIndex = unitsInSec.findIndex((cutoff) => cutoff > Math.abs(secondsDiff));
        const divisor = unitIndex ? unitsInSec[unitIndex - 1] : 1;
        const rtf = new Intl.RelativeTimeFormat(locale.value, { numeric: 'auto' });
        return rtf.format(Math.floor(secondsDiff / divisor), unitStrings[unitIndex] as RelativeTimeUnit);
    };

    return {
        relativeTime,
    };
};
