import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path'
import i18n from 'laravel-vue-i18n/vite';

export default defineConfig({
    resolve: {
        alias: {
            '@images': path.resolve(__dirname, './resources/assets/images'),
            '@stores': path.resolve(__dirname, './resources/assets/stores'),
            '@Pages': path.resolve(__dirname, './resources/js/Pages'),
            '@Layouts': path.resolve(__dirname, './resources/js/Layouts'),
            '@Composables': path.resolve(__dirname, './resources/js/Composables'),
        },
    },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        i18n(),
    ],
});
