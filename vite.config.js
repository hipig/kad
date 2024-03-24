import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import vueJsx from '@vitejs/plugin-vue-jsx';
import UnoCSS from 'unocss/vite';

export default defineConfig({
    plugins: [
        UnoCSS({
            content: {
                filesystem: ['resources/views/**/*.blade.php'],
            },
        }),
        laravel({
            input: ['resources/ts/admin/main.ts'],
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
        vueJsx()
    ],
    resolve: {
        alias: {
            '@admin': '/resources/ts/admin'
        },
    }
});
