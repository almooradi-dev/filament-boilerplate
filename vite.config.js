import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/filament/admin/theme-01.css',
                'resources/css/filament/admin/theme-02.css'
            ],
            refresh: true,
        }),
    ],
});
