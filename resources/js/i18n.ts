import { useNavigatorLanguage } from '@vueuse/core';
import { createI18n } from 'vue-i18n';
import br from '../../lang/br.json';
import en from '../../lang/en.json';
import es from '../../lang/es.json';
import fr from '../../lang/fr.json';
type MessageSchema = typeof en;
type SupportedLocales = 'en' | 'fr' | 'es' | 'br';

const STORAGE_KEY = 'user_locale';

const getInitialLocale = (): SupportedLocales => {
    const savedLocale = localStorage.getItem(STORAGE_KEY) as SupportedLocales | null;
    if (savedLocale && ['en', 'fr', 'es', 'br'].includes(savedLocale)) {
        return savedLocale;
    }
    const browserLang = useNavigatorLanguage().language?.value?.slice(0, 2).toLowerCase();
    const language = ['en', 'fr', 'es', 'br'].includes(browserLang || '') ? (browserLang as SupportedLocales) : 'en';
    return language;
};

export const i18n = createI18n<[MessageSchema], SupportedLocales>({
    legacy: false,
    locale: getInitialLocale(),
    fallbackLocale: 'en',
    messages: { en, fr, es, br },
});
