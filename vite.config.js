import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
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
    ],
    // server: {
    //     host: '0.0.0.0',
    //     port: 5173,
    //     cors: true,
    //     strictPort: true,
    //     hmr: {
    //         protocol: 'ws',
    //         host: '192.168.237.158', // <-- ganti sesuai IP lokal kamu
    //         port: 5173,
    //     },
    // },
});
