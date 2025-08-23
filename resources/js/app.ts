import { createInertiaApp } from '@inertiajs/vue3';
import { useColorMode } from '@vueuse/core';
import 'abcjs/abcjs-audio.css';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import type { DefineComponent } from 'vue';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import '../css/app.css';
import { i18n } from './i18n';

const appName = import.meta.env.VITE_APP_NAME || 'Notenn';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const pinia = createPinia();
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18n)
            .use(pinia)
            .mount(el);
    },
    progress: false,
});

useColorMode();
