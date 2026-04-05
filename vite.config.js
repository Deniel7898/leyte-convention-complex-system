import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: true, // ✅ BEST (auto handles IP + localhost)
        port: 5173,
        strictPort: true,
        cors: true,
        hmr: {
            host: '192.168.100.60', // ✅ NO SPACE
        },
    },
});