import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

export const useSyncLocale = () => {
    const { locale } = useI18n();

    const syncWithBackend = () => {
        router.post(
            route('lang', { lang: locale.value }),
            {},
            {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    console.log('Locale synced with backend:', locale.value);
                },
                onError: (error) => {
                    console.error('Locale sync failed:', error);
                },
            },
        );
    };

    return { syncWithBackend };
};
