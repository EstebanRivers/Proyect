import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/variables.css',
                'resources/css/base.css',
                'resources/css/layout.css',
                'resources/css/sidebar.css',
                'resources/css/components.css',
                'resources/css/responsive.css',
                'resources/js/app.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
