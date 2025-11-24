import { fileURLToPath } from 'node:url';
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import vueJsx from '@vitejs/plugin-vue-jsx';
import vuetify from 'vite-plugin-vuetify';
import AutoImport from 'unplugin-auto-import/vite';
import Components from 'unplugin-vue-components/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            ssr: 'resources/js/ssr.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
                compilerOptions: {
                    isCustomElement: tag => tag === 'swiper-container' || tag === 'swiper-slide',
                },
            },
        }),
        vueJsx(),
        
        // Vuetify plugin
        vuetify({
            styles: {
                configFile: 'resources/js/assets/styles/variables/_vuetify.scss',
            },
        }),

        // Auto import components
        Components({
            dirs: ['resources/js/@core/components', 'resources/js/components'],
            dts: true,
        }),

        // Auto import composables and utilities
        AutoImport({
            imports: ['vue', '@vueuse/core', 'pinia'],
            dirs: [
                'resources/js/@core/utils',
                'resources/js/@core/composable/',
                'resources/js/utils/',
            ],
            vueTemplate: true,
        }),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
            '@themeConfig': fileURLToPath(new URL('./resources/js/themeConfig.js', import.meta.url)),
            '@core': fileURLToPath(new URL('./resources/js/@core', import.meta.url)),
            '@layouts': fileURLToPath(new URL('./resources/js/@layouts', import.meta.url)),
            '@images': fileURLToPath(new URL('./resources/js/assets/images/', import.meta.url)),
            '@styles': fileURLToPath(new URL('./resources/js/assets/styles/', import.meta.url)),
            '@configured-variables': fileURLToPath(new URL('./resources/js/assets/styles/variables/_template.scss', import.meta.url)),
        },
    },
    optimizeDeps: {
        exclude: ['vuetify'],
        entries: [
            'resources/js/**/*.vue',
        ],
    },
});
