import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// Styles
import '@core/scss/template/index.scss';
import '@styles/styles.scss';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        // Register Inertia plugin
        app.use(plugin);

        // Register Ziggy
        app.use(ZiggyVue);

        // Register Vuetify and other plugins (Pinia, etc.)
        // This must be done synchronously before mounting
        const pluginFiles = import.meta.glob('./plugins/*.{ts,js}', { eager: true });
        const pluginIndexFiles = import.meta.glob('./plugins/*/index.{ts,js}', { eager: true });
        const allPlugins = { ...pluginFiles, ...pluginIndexFiles };
        const pluginPaths = Object.keys(allPlugins)
            .filter(path => !path.includes('.disabled')) // Exclude disabled plugins
            .sort();

        pluginPaths.forEach(path => {
            const pluginModule = allPlugins[path];
            pluginModule.default?.(app);
        });

        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
